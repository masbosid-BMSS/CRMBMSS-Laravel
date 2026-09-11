<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isMaster = $user->isMaster();

        $query = Lead::with('owner');
        if ($owner = $request->query('owner')) {
            if ($owner === 'mine') {
                $query->where('owner_id', $user->id);
            } elseif ($owner !== 'all') {
                $query->where('owner_id', $owner);
            }
        }

        $leads = $query->get();
        $stages = ['Lead Baru', 'Contacted', 'Interested', 'Follow-up', 'Donasi'];
        $admins = User::where('role', 'admin')->get();

        return view('pipeline.index', compact('leads', 'stages', 'admins'));
    }

    public function store(StoreLeadRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        $ownerId = $user->isMaster() && ! empty($data['owner_id']) ? $data['owner_id'] : $user->id;

        Lead::create([
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'source' => $data['source'] ?? null,
            'owner_id' => $ownerId,
            'stage' => $data['stage'],
            'interest' => $data['interest'] ?? null,
            'potential_amount' => $data['potential_amount'] ?? 0,
            'status' => $data['stage'] === 'Donasi' ? 'Donasi' : 'Aktif',
        ]);

        return redirect()->route('leads.index')->with('success', 'Lead baru berhasil ditambahkan.');
    }

    public function update(StoreLeadRequest $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $data = $request->validated();
        $user = Auth::user();
        $ownerId = $user->isMaster() && ! empty($data['owner_id']) ? $data['owner_id'] : $lead->owner_id;

        $lead->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'source' => $data['source'] ?? null,
            'owner_id' => $ownerId,
            'stage' => $data['stage'],
            'interest' => $data['interest'] ?? null,
            'potential_amount' => $data['potential_amount'] ?? 0,
            'status' => $data['stage'] === 'Donasi' ? 'Donasi' : 'Aktif',
        ]);

        return redirect()->route('leads.index')->with('success', 'Lead berhasil diperbarui.');
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $data = $request->validate([
            'stage' => ['required', 'in:Lead Baru,Contacted,Interested,Follow-up,Donasi'],
        ]);

        $lead->update([
            'stage' => $data['stage'],
            'status' => $data['stage'] === 'Donasi' ? 'Donasi' : 'Aktif',
        ]);

        return response()->json(['success' => true]);
    }
}
