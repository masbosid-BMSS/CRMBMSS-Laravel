<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use App\Models\WaAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Hanya Master Admin yang dapat mengakses manajemen user.');
        }

        $users = User::with('waAccounts')->get();

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'status' => 'active',
            'must_change_password' => true,
        ]);

        // Auto create 5 WhatsApp slots
        for ($slot = 1; $slot <= 5; $slot++) {
            WaAccount::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'slot' => $slot,
                'label' => "WA {$slot}",
                'phone' => null,
                'capacity' => 5000,
                'status' => 'Aktif',
            ]);
        }

        // Auto create KPI Target default
        $user->kpiTarget()->create([
            'id' => (string) Str::uuid(),
            'tier' => 1,
            'daily_target' => 1000000,
            'followup_target' => 150,
            'conversion_target' => 8.00,
            'retention_target' => 60.00,
            'start_date' => now()->toDateString(),
        ]);

        return redirect()->route('users.index')->with('success', 'Admin baru berhasil dibuat dengan 5 slot nomor WhatsApp.');
    }

    public function resetPassword(User $user)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        // Password acak, bukan konstanta yang bisa ditebak.
        // Tanpa simbol agar mudah disampaikan ulang ke pengguna.
        $plain = Str::password(14, symbols: false);

        $user->update([
            'password' => Hash::make($plain),
            'must_change_password' => true,
        ]);

        return redirect()->route('users.index')->with('success', "Password {$user->name} direset. Password baru: {$plain} — catat dan sampaikan sekarang, tidak akan ditampilkan lagi.");
    }

    public function transfer(Request $request, User $user)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        $data = $request->validate([
            'target_user_id' => ['required', 'exists:users,id', 'different:' . $user->id],
        ]);

        $target = User::findOrFail($data['target_user_id']);
        $firstWa = $target->waAccounts()->where('status', 'Aktif')->first();

        Contact::where('owner_id', $user->id)->update([
            'owner_id' => $target->id,
            'wa_account_id' => $firstWa?->id,
        ]);

        \App\Models\Followup::where('owner_id', $user->id)->update(['owner_id' => $target->id]);
        \App\Models\Calculation::where('owner_id', $user->id)->update(['owner_id' => $target->id]);
        \App\Models\Transaction::where('owner_id', $user->id)->update(['owner_id' => $target->id]);
        \App\Models\Lead::where('owner_id', $user->id)->update(['owner_id' => $target->id]);

        return redirect()->route('users.index')->with('success', "Seluruh database milik {$user->name} berhasil ditransfer ke {$target->name}.");
    }
}
