<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFollowupRequest;
use App\Models\Contact;
use App\Models\Followup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FollowupController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isMaster = $user->isMaster();

        $query = Followup::with(['contact', 'owner']);
        if (! $isMaster) {
            $query->where('owner_id', $user->id);
        }

        $allFollowups = $query->orderBy('scheduled_at')->get();

        $now = Carbon::now('Asia/Jakarta');
        // Update statuses dynamically
        foreach ($allFollowups as $f) {
            if ($f->status !== 'Completed' && $f->scheduled_at) {
                if ($f->scheduled_at->isPast() && ! $f->scheduled_at->isToday()) {
                    $f->update(['status' => 'Overdue']);
                } elseif ($f->scheduled_at->isToday()) {
                    $f->update(['status' => 'Today']);
                }
            }
        }

        $activeList = $allFollowups->where('status', '!=', 'Completed');
        $overdueCount = $activeList->where('status', 'Overdue')->count();
        $todayCount = $activeList->where('status', 'Today')->count();
        $scheduledCount = $activeList->where('status', 'Scheduled')->count();

        $contacts = Contact::active()
            ->when(! $isMaster, fn ($q) => $q->where('owner_id', $user->id))
            ->where('relation_status', '!=', 'Blokir')
            ->orderBy('name')
            ->get();

        return view('followups.index', compact(
            'activeList',
            'overdueCount',
            'todayCount',
            'scheduledCount',
            'contacts',
            'isMaster'
        ));
    }

    public function store(StoreFollowupRequest $request)
    {
        $data = $request->validated();
        $contact = Contact::findOrFail($data['contact_id']);

        if (! Auth::user()->can('update', $contact)) {
            abort(403, 'Akses ditolak.');
        }

        if ($contact->relation_status === 'Blokir') {
            return back()->withErrors(['contact_id' => 'Kontak berstatus BLOKIR. Follow-up tidak dapat dibuat.']);
        }

        $scheduled = Carbon::parse($data['scheduled_at']);
        $status = $scheduled->isPast() && ! $scheduled->isToday() ? 'Overdue' : ($scheduled->isToday() ? 'Today' : 'Scheduled');

        Followup::create([
            'id' => (string) Str::uuid(),
            'contact_id' => $contact->id,
            'owner_id' => $contact->owner_id,
            'reason' => $data['reason'],
            'title' => $data['title'],
            'priority' => $data['priority'],
            'scheduled_at' => $scheduled,
            'status' => $status,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Follow-up berhasil dijadwalkan.');
    }

    public function complete(Followup $followup)
    {
        $this->authorize('update', $followup);
        $followup->update([
            'status' => 'Completed',
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Follow-up ditandai selesai.');
    }

    public function reschedule(Request $request, Followup $followup)
    {
        $this->authorize('update', $followup);
        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
        ]);

        $scheduled = Carbon::parse($data['scheduled_at']);
        $status = $scheduled->isPast() && ! $scheduled->isToday() ? 'Overdue' : ($scheduled->isToday() ? 'Today' : 'Scheduled');

        $followup->update([
            'scheduled_at' => $scheduled,
            'status' => $status,
        ]);

        return redirect()->back()->with('success', 'Follow-up berhasil dijadwalkan ulang.');
    }
}
