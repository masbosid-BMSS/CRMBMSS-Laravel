<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('program')->where('status', '!=', 'Arsip')->get();
        $programs = Program::where('status', 'Aktif')->get();

        return view('campaigns.index', compact('campaigns', 'programs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'achieved_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'days_remaining' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:Aktif,Selesai,Arsip'],
            'description' => ['nullable', 'string'],
        ]);

        Campaign::create([
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'program_id' => $data['program_id'] ?? null,
            'target_amount' => $data['target_amount'],
            'achieved_amount' => $data['achieved_amount'] ?? 0,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'days_remaining' => $data['days_remaining'] ?? 30,
            'is_global' => true,
            'status' => $data['status'],
            'description' => $data['description'] ?? null,
            'accent_color' => 'navy',
        ]);

        return redirect()->route('campaigns.index')->with('success', 'Campaign global berhasil dibuat.');
    }

    public function update(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'achieved_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'days_remaining' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:Aktif,Selesai,Arsip'],
            'description' => ['nullable', 'string'],
        ]);

        $campaign->update($data);

        return redirect()->route('campaigns.index')->with('success', 'Campaign berhasil diperbarui.');
    }
}
