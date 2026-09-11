<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use App\Models\Contact;
use App\Services\ZakatCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZakatController extends Controller
{
    protected ZakatCalculatorService $calculatorService;

    public function __construct(ZakatCalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isMaster = $user->isMaster();

        $query = Calculation::with(['contact', 'owner']);
        if (! $isMaster) {
            $query->where('owner_id', $user->id);
        }

        $totalConsultations = (clone $query)->count();
        $wajibCount = (clone $query)->where('zakat_amount', '>', 0)->count();
        $outstandingAmount = (float) (clone $query)->whereIn('status', ['Outstanding', 'Sebagian'])->get()->sum('remaining_amount');
        $paidAmount = (float) (clone $query)->sum('paid_amount');

        // Actionable issue items
        $overdueCalcs = (clone $query)->whereIn('status', ['Outstanding', 'Sebagian'])
            ->whereDoesntHave('contact.followups', fn ($q) => $q->where('reason', 'Zakat')->where('status', '!=', 'Completed'))
            ->count();

        $todayFollowupCalcs = (clone $query)->whereIn('status', ['Outstanding', 'Sebagian'])
            ->whereHas('contact.followups', fn ($q) => $q->where('reason', 'Zakat')->where('status', 'Today'))
            ->count();

        $partialCalcs = (clone $query)->where('status', 'Sebagian')->count();

        return view('zakat.index', compact(
            'totalConsultations',
            'wajibCount',
            'outstandingAmount',
            'paidAmount',
            'overdueCalcs',
            'todayFollowupCalcs',
            'partialCalcs'
        ));
    }

    public function calculator(Request $request)
    {
        $user = Auth::user();
        $selectedContactId = $request->query('contact_id');
        $selectedContact = $selectedContactId ? Contact::find($selectedContactId) : null;

        $contacts = Contact::active()
            ->when(! $user->isMaster(), fn ($q) => $q->where('owner_id', $user->id))
            ->orderBy('name')
            ->get();

        $defaultGoldPrice = (float) config('crm.default_gold_price', 2200000);

        return view('zakat.calculator', compact('contacts', 'selectedContact', 'defaultGoldPrice'));
    }

    public function calculateAjax(Request $request)
    {
        $data = $request->validate([
            'assets_total' => ['required', 'numeric', 'min:0'],
            'deductions_total' => ['nullable', 'numeric', 'min:0'],
            'gold_price' => ['nullable', 'numeric', 'min:1'],
            'year_method' => ['required', 'in:h,m'],
            'haul_status' => ['required', 'in:yes,no,na'],
        ]);

        $result = $this->calculatorService->calculate($data);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_id' => ['required', 'exists:contacts,id'],
            'type' => ['required', 'string'],
            'calculation_date' => ['required', 'date'],
            'gold_price' => ['nullable', 'numeric', 'min:1'],
            'year_method' => ['required', 'in:h,m'],
            'haul_status' => ['required', 'in:yes,no,na'],
            'assets_total' => ['required', 'numeric', 'min:0'],
            'deductions_total' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'finalize' => ['nullable', 'boolean'],
            'existing_id' => ['nullable', 'exists:calculations,id'],
        ]);

        $contact = Contact::findOrFail($data['contact_id']);
        if (! Auth::user()->isMaster() && Auth::id() !== $contact->owner_id) {
            abort(403, 'Anda tidak memiliki izin menghitung zakat untuk kontak milik admin lain.');
        }

        $calc = $this->calculatorService->saveCalculation($contact, Auth::user(), $data, $data['existing_id'] ?? null);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'calculation' => $calc->load('contact', 'owner'),
                'message' => $request->boolean('finalize') ? 'Perhitungan selesai difinalisasi' : 'Draft perhitungan zakat disimpan',
            ]);
        }

        return redirect()->route('zakat.history')->with('success', 'Perhitungan zakat berhasil disimpan.');
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $isMaster = $user->isMaster();

        $query = Calculation::with(['contact', 'owner']);
        if (! $isMaster) {
            $query->where('owner_id', $user->id);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhereHas('contact', fn ($cq) => $cq->where('name', 'like', "%{$search}%"))
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($owner = $request->query('owner')) {
            if ($owner === 'mine') {
                $query->where('owner_id', $user->id);
            } elseif ($owner !== 'all') {
                $query->where('owner_id', $owner);
            }
        }

        $calculations = $query->orderByDesc('calculation_date')->paginate(50)->withQueryString();
        $admins = \App\Models\User::where('role', 'admin')->get();

        return view('zakat.history', compact('calculations', 'admins'));
    }

    public function show(Calculation $calculation)
    {
        $calculation->load('contact', 'owner', 'transactions');
        $explanation = $this->calculatorService->generateExplanation(
            (float) $calculation->net_amount,
            (float) $calculation->nisab_amount,
            (float) $calculation->rate,
            $calculation->haul_status,
            (float) $calculation->zakat_amount
        );

        return view('zakat.show', compact('calculation', 'explanation'));
    }
}
