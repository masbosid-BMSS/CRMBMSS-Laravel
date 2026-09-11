<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Models\Followup;
use App\Models\User;
use App\Models\WaAccount;
use App\Services\ExcelExportService;
use App\Services\NissGeneratorService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    protected NissGeneratorService $nissService;
    protected WhatsAppService $waService;
    protected ExcelExportService $excelService;

    public function __construct(NissGeneratorService $nissService, WhatsAppService $waService, ExcelExportService $excelService)
    {
        $this->nissService = $nissService;
        $this->waService = $waService;
        $this->excelService = $excelService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isMaster = $user->isMaster();

        $query = Contact::with(['owner', 'waAccount'])->active();

        // Search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('niss', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('program', 'like', "%{$search}%");
            });
        }

        // Owner filter
        $owner = $request->query('owner');
        if ($owner === 'mine') {
            $query->where('owner_id', $user->id);
        } elseif ($owner && $owner !== 'all') {
            $query->where('owner_id', $owner);
        }

        // WA filter
        $wa = $request->query('wa');
        if ($wa === 'unassigned') {
            $query->whereNull('wa_account_id');
        } elseif ($wa && $wa !== 'all') {
            $query->where('wa_account_id', $wa);
        }

        // Relation filter
        if ($rel = $request->query('relation')) {
            if ($rel !== 'all') {
                $query->where('relation_status', $rel);
            }
        }

        // Zakat status filter
        if ($zakat = $request->query('zakat')) {
            if ($zakat !== 'all') {
                $query->where('zakat_status', $zakat);
            }
        }

        // Access filter
        $access = $request->query('access');
        if ($access === 'edit') {
            if (! $isMaster) {
                $query->where('owner_id', $user->id);
            }
        } elseif ($access === 'read') {
            if (! $isMaster) {
                $query->where('owner_id', '!=', $user->id);
            } else {
                $query->whereRaw('0 = 1'); // Master can edit all
            }
        }

        // Issue drilldown filter
        $issue = $request->query('issue');
        if ($issue === 'overdue') {
            $query->whereHas('followups', fn ($q) => $q->where('status', 'Overdue'));
        } elseif ($issue === 'untrust') {
            $query->where('relation_status', 'Untrust');
        } elseif ($issue === 'bored') {
            $query->where('relation_status', 'Bosan');
        } elseif ($issue === 'blocked') {
            $query->where('relation_status', 'Blokir');
        } elseif ($issue === 'zakat-outstanding') {
            $query->whereIn('zakat_status', ['Outstanding', 'Sebagian']);
        } elseif ($issue === 'zakat-no-followup') {
            $query->whereIn('zakat_status', ['Outstanding', 'Sebagian'])
                  ->whereDoesntHave('followups', fn ($q) => $q->where('reason', 'Zakat')->where('status', '!=', 'Completed'));
        }

        $contacts = $query->orderByDesc('created_at')->paginate(50)->withQueryString();

        $admins = User::where('role', 'admin')->where('status', 'active')->get();
        $waAccounts = WaAccount::where('status', 'Aktif')->get();

        return view('contacts.index', compact('contacts', 'admins', 'waAccounts', 'issue'));
    }

    public function create()
    {
        $admins = User::where('role', 'admin')->where('status', 'active')->get();
        $waAccounts = WaAccount::where('status', 'Aktif')->get();

        return view('contacts.create', compact('admins', 'waAccounts'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);
        return redirect()->route('contacts.show', $contact);
    }

    public function show(Contact $contact)
    {
        $contact->load([
            'owner',
            'waAccount',
            'calculations' => fn ($q) => $q->orderByDesc('calculation_date'),
            'transactions' => fn ($q) => $q->orderByDesc('transaction_date'),
            'followups' => fn ($q) => $q->orderByDesc('scheduled_at'),
            'leads',
        ]);

        $canEdit = Auth::user()->can('update', $contact);
        $latestCalc = $contact->calculations->first();

        return view('contacts.show', compact('contact', 'canEdit', 'latestCalc'));
    }

    public function store(StoreContactRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $ownerId = $user->isMaster() && ! empty($data['owner_id']) ? $data['owner_id'] : $user->id;
        $waId = $data['wa_account_id'] ?? null;

        // Check WA capacity
        if ($waId && ! $this->waService->validateCapacity($waId, 1)) {
            return back()->withInput()->withErrors(['wa_account_id' => 'Kapasitas slot nomor WhatsApp ini telah penuh.']);
        }

        // NISS generator
        $niss = ! empty($data['niss']) ? $data['niss'] : $this->nissService->generate();

        $contact = Contact::create([
            'id' => (string) Str::uuid(),
            'niss' => $niss,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'city' => $data['city'] ?? null,
            'source' => $data['source'] ?? null,
            'program' => $data['program'] ?? null,
            'status' => $data['status'],
            'relation_status' => $data['relation_status'],
            'relationship_note' => $data['relationship_note'] ?? null,
            'owner_id' => $ownerId,
            'wa_account_id' => $waId,
            'tags' => ! empty($data['tags']) ? (is_array($data['tags']) ? $data['tags'] : array_map('trim', explode(',', $data['tags']))) : [],
            'notes' => $data['notes'] ?? null,
            'last_activity_at' => now(),
        ]);

        // Optional Follow-up creation
        if ($request->boolean('make_followup') && ! empty($data['followup_date'])) {
            Followup::create([
                'id' => (string) Str::uuid(),
                'contact_id' => $contact->id,
                'owner_id' => $ownerId,
                'reason' => $data['followup_reason'] ?? 'Kontak Baru',
                'title' => $data['followup_title'] ?? 'Follow-up kontak baru',
                'priority' => $data['followup_priority'] ?? 'Normal',
                'scheduled_at' => $data['followup_date'],
                'status' => 'Scheduled',
            ]);
        }

        return redirect()->route('contacts.show', $contact)->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $this->authorize('update', $contact);
        $user = Auth::user();
        $data = $request->validated();

        $ownerId = $user->isMaster() && ! empty($data['owner_id']) ? $data['owner_id'] : $contact->owner_id;
        $waId = $data['wa_account_id'] ?? null;

        if ($waId && $waId !== $contact->wa_account_id) {
            if (! $this->waService->validateCapacity($waId, 1, $contact->id)) {
                return back()->withInput()->withErrors(['wa_account_id' => 'Kapasitas slot nomor WhatsApp ini telah penuh.']);
            }
        }

        $contact->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'city' => $data['city'] ?? null,
            'source' => $data['source'] ?? null,
            'program' => $data['program'] ?? null,
            'status' => $data['status'],
            'relation_status' => $data['relation_status'],
            'relationship_note' => $data['relationship_note'] ?? null,
            'owner_id' => $ownerId,
            'wa_account_id' => $waId,
            'niss' => $data['niss'] ?? $contact->niss,
            'tags' => ! empty($data['tags']) ? (is_array($data['tags']) ? $data['tags'] : array_map('trim', explode(',', $data['tags']))) : [],
            'notes' => $data['notes'] ?? null,
            'last_activity_at' => now(),
        ]);

        return redirect()->route('contacts.show', $contact)->with('success', 'Kontak berhasil diperbarui.');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->update(['is_archived' => true]);
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil diarsipkan.');
    }

    public function exportXlsx()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'bmss_contacts_') . '.xlsx';
        $this->excelService->exportContacts(Auth::user(), $tempFile);

        return response()->download($tempFile, 'BMSS_Database_Kontak_' . date('Y-m-d') . '.xlsx')->deleteFileAfterSend(true);
    }
}
