<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Calculation;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\PaymentMethod;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isMaster = $user->isMaster();

        $query = Transaction::with(['contact', 'owner', 'recordedBy', 'campaign']);
        if (! $isMaster) {
            $query->where('owner_id', $user->id);
        }

        if ($type = $request->query('type')) {
            if ($type !== 'all') {
                $query->where('type', $type);
            }
        }

        if ($owner = $request->query('owner')) {
            if ($owner === 'mine') {
                $query->where('owner_id', $user->id);
            } elseif ($owner !== 'all') {
                $query->where('owner_id', $owner);
            }
        }

        $transactions = $query->orderByDesc('transaction_date')->paginate(50)->withQueryString();
        $admins = User::where('role', 'admin')->get();
        $paymentMethods = PaymentMethod::active()->get();
        $programs = Program::where('status', 'Aktif')->get();
        $campaigns = Campaign::where('status', 'Aktif')->get();

        return view('transactions.index', compact('transactions', 'admins', 'paymentMethods', 'programs', 'campaigns'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $contact = Contact::findOrFail($data['contact_id']);

        if (! Auth::user()->can('update', $contact)) {
            abort(403, 'Anda tidak memiliki izin mencatat transaksi untuk kontak milik admin lain.');
        }

        $amount = (float) $data['amount'];
        $calcId = $data['calculation_id'] ?? null;

        $tx = Transaction::create([
            'id' => 'TRX-' . strtoupper(Str::random(6)),
            'contact_id' => $contact->id,
            'owner_id' => $contact->owner_id,
            'recorded_by_id' => Auth::id(),
            'type' => $data['type'],
            'program' => $data['program'] ?? $data['type'],
            'campaign_id' => $data['campaign_id'] ?? null,
            'calculation_id' => $calcId,
            'amount' => $amount,
            'status' => 'Paid',
            'payment_method' => $data['payment_method'],
            'transaction_date' => $data['transaction_date'] ?? now(),
        ]);

        // Update contact LTV
        $contact->increment('ltv', $amount);
        $contact->update(['last_activity_at' => now()]);

        // If connected to calculation
        if ($calcId) {
            $calc = Calculation::find($calcId);
            if ($calc) {
                $calc->increment('paid_amount', $amount);
                $remaining = max(0, $calc->zakat_amount - $calc->paid_amount);
                $newStatus = $remaining === 0.0 ? 'Lunas' : 'Sebagian';
                $calc->update(['status' => $newStatus]);
                $contact->update(['zakat_status' => $newStatus]);
            }
        }

        // If connected to campaign
        if (! empty($data['campaign_id'])) {
            $campaign = Campaign::find($data['campaign_id']);
            if ($campaign) {
                $campaign->increment('achieved_amount', $amount);
                $campaign->increment('donors_count', 1);
            }
        }

        return redirect()->back()->with('success', 'Transaksi berhasil dicatat.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['contact', 'owner', 'recordedBy', 'campaign', 'calculation']);
        $canEdit = Auth::user()->can('update', $transaction);

        return view('transactions.show', compact('transaction', 'canEdit'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:100'],
            'program' => ['nullable', 'string', 'max:100'],
        ]);

        $newAmount = (float) $data['amount'];
        $oldAmount = (float) $transaction->amount;
        $delta = $newAmount - $oldAmount;

        $transaction->update([
            'amount' => $newAmount,
            'payment_method' => $data['payment_method'],
            'program' => $data['program'] ?? $transaction->program,
        ]);

        // Adjust LTV
        $transaction->contact->increment('ltv', $delta);

        // Adjust calculation if linked
        if ($transaction->calculation_id) {
            $calc = $transaction->calculation;
            if ($calc) {
                $calc->increment('paid_amount', $delta);
                $remaining = max(0, $calc->zakat_amount - $calc->paid_amount);
                $newStatus = $remaining === 0.0 ? 'Lunas' : ($calc->paid_amount > 0 ? 'Sebagian' : 'Outstanding');
                $calc->update(['status' => $newStatus]);
                $transaction->contact->update(['zakat_status' => $newStatus]);
            }
        }

        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui.');
    }
}
