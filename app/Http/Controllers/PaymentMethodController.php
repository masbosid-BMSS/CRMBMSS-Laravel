<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    public function index()
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Hanya Master Admin yang dapat mengelola metode pembayaran.');
        }

        $methods = PaymentMethod::all();
        $activeCount = $methods->where('is_active', true)->count();
        $usedCount = Transaction::whereNotNull('payment_method')->distinct('payment_method')->count('payment_method');

        return view('payment-methods.index', compact('methods', 'activeCount', 'usedCount'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:payment_methods,name'],
            'category' => ['required', 'string', 'max:50'],
            'detail' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);

        PaymentMethod::create([
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'category' => $data['category'],
            'detail' => $data['detail'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:payment_methods,name,' . $paymentMethod->id],
            'category' => ['required', 'string', 'max:50'],
            'detail' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);

        $paymentMethod->update($data);

        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function toggle(PaymentMethod $paymentMethod)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        if ($paymentMethod->is_active && PaymentMethod::where('is_active', true)->count() <= 1) {
            return back()->withErrors(['error' => 'Minimal harus ada 1 metode pembayaran aktif.']);
        }

        $paymentMethod->update(['is_active' => ! $paymentMethod->is_active]);

        return redirect()->route('payment-methods.index')->with('success', 'Status metode berhasil diubah.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        if ($paymentMethod->is_active && PaymentMethod::where('is_active', true)->count() <= 1) {
            return back()->withErrors(['error' => 'Metode aktif terakhir tidak boleh dihapus.']);
        }

        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
