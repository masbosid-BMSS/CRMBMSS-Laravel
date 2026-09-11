<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        $org = Setting::get('org_name', 'Baitulmaal Sejuta Santri');
        $target = (float) Setting::get('monthly_target', 1000000000);
        $email = Setting::get('org_email', 'admin@bmss.local');
        $contactsCount = Contact::active()->count();

        return view('settings.index', compact('org', 'target', 'email', 'contactsCount'));
    }

    public function update(Request $request)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Akses ditolak.');
        }

        $data = $request->validate([
            'org_name' => ['required', 'string', 'max:255'],
            'monthly_target' => ['required', 'numeric', 'min:1'],
            'org_email' => ['required', 'email', 'max:255'],
        ]);

        Setting::set('org_name', $data['org_name']);
        Setting::set('monthly_target', $data['monthly_target']);
        Setting::set('org_email', $data['org_email']);

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
