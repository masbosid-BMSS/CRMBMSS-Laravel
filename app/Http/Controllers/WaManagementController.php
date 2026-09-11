<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use App\Models\WaAccount;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaManagementController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    public function index()
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Hanya Master Admin yang dapat mengakses manajemen nomor WhatsApp.');
        }

        $admins = User::with(['waAccounts.contacts'])->where('role', 'admin')->where('status', 'active')->get();
        $totalContacts = Contact::active()->count();
        $totalCapacity = WaAccount::where('status', 'Aktif')->sum('capacity');
        $registeredNumbers = WaAccount::whereNotNull('phone')->where('phone', '!=', '')->count();

        return view('whatsapp.index', compact('admins', 'totalContacts', 'totalCapacity', 'registeredNumbers'));
    }

    public function updateAccount(Request $request, WaAccount $waAccount)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        $data = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
            'capacity' => ['required', 'integer', 'min:100'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        $assigned = $waAccount->assigned_count;
        if ($data['capacity'] < $assigned) {
            return back()->withErrors(['capacity' => "Kapasitas tidak boleh lebih kecil dari {$assigned} kontak yang sudah ditugaskan."]);
        }

        $waAccount->update($data);

        return redirect()->route('whatsapp.index')->with('success', 'Nomor WhatsApp berhasil diperbarui.');
    }

    public function assignContact(Request $request)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        $data = $request->validate([
            'contact_id' => ['required', 'exists:contacts,id'],
            'owner_id' => ['required', 'exists:users,id'],
            'wa_account_id' => ['nullable', 'exists:wa_accounts,id'],
        ]);

        $contact = Contact::findOrFail($data['contact_id']);
        $waId = $data['wa_account_id'] ?? null;

        if ($waId) {
            $wa = WaAccount::find($waId);
            if ($wa->user_id !== $data['owner_id']) {
                return back()->withErrors(['wa_account_id' => 'Nomor WA harus milik CS yang dipilih.']);
            }
            if (! $this->waService->validateCapacity($waId, 1, $contact->id)) {
                return back()->withErrors(['wa_account_id' => 'Kapasitas nomor WA tujuan sudah penuh.']);
            }
        }

        $oldOwnerId = $contact->owner_id;
        $contact->update([
            'owner_id' => $data['owner_id'],
            'wa_account_id' => $waId,
        ]);

        // Reassign related items
        $contact->followups()->update(['owner_id' => $data['owner_id']]);
        $contact->calculations()->update(['owner_id' => $data['owner_id']]);
        $contact->transactions()->update(['owner_id' => $data['owner_id']]);

        return redirect()->back()->with('success', 'Kontak dan seluruh riwayatnya berhasil dipindahkan ke CS tujuan.');
    }
}
