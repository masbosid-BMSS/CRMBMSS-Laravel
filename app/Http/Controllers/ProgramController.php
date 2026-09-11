<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Program;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::withCount('campaigns')->get();
        $isMaster = Auth::user()->isMaster();

        return view('programs.index', compact('programs', 'isMaster'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Hanya Master Admin yang dapat mengelola program.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:programs,name'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Aktif,Arsip'],
        ]);

        Program::create([
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
        ]);

        return redirect()->route('programs.index')->with('success', 'Program berhasil ditambahkan.');
    }

    public function update(Request $request, Program $program)
    {
        if (! Auth::user()->isMaster()) {
            abort(403, 'Hanya Master Admin yang dapat mengelola program.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:programs,name,' . $program->id],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Aktif,Arsip'],
        ]);

        $program->update($data);

        return redirect()->route('programs.index')->with('success', 'Program berhasil diperbarui.');
    }
}
