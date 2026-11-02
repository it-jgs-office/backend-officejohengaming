<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingInvitation;
use App\Models\Notification;
use App\Models\Room;
use App\Models\User;
use App\Services\MeetingQueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MeetingRequestIntegrationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->ensureIntegrationAccount($request);

        $query = Meeting::with(['requester:id,name,username', 'room:id,name', 'approver:id,name'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = match ($request->string('status')->toString()) {
                    'disetujui' => ['approved', 'confirmed', 'in_progress', 'completed'],
                    'ditolak' => ['rejected'],
                    'pending' => ['pending'],
                    default => null,
                };
                if ($status) $query->whereIn('status', $status);
            })
            ->when($request->filled('requester_username'), function ($query) use ($request) {
                $requester = User::where('username', $request->string('requester_username'))->firstOrFail();
                $query->where('requested_by', $requester->id);
            })
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->string('search').'%'))
            ->orderByDesc('created_at');

        $meetings = $query->paginate(min(max($request->integer('per_page', 10), 1), 100));

        return response()->json([
            'data' => $meetings->getCollection()->map(fn (Meeting $meeting) => $this->format($meeting)),
            'pagination' => [
                'current_page' => $meetings->currentPage(),
                'last_page' => $meetings->lastPage(),
                'per_page' => $meetings->perPage(),
                'total' => $meetings->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensureIntegrationAccount($request);

        $validated = $request->validate([
            'requester_username' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'room' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'why' => ['nullable', 'string', 'max:10000'],
            'what' => ['nullable', 'string', 'max:10000'],
            'how' => ['nullable', 'string', 'max:10000'],
        ]);

        $requester = User::where('username', $validated['requester_username'])->first();
        if (! $requester) {
            throw ValidationException::withMessages(['requester_username' => 'Akun pemohon belum terhubung dengan akun meeting.']);
        }
        if (! $requester->team_id) {
            throw ValidationException::withMessages(['requester_username' => 'Akun pemohon belum memiliki tim pada sistem meeting.']);
        }

        $room = Room::where('name', $validated['room'])->where('is_active', true)->first();
        if (! $room || $room->is_weekly_only) {
            throw ValidationException::withMessages(['room' => 'Ruangan tidak ditemukan atau khusus Weekly Meeting.']);
        }

        if ($room->isSmokingArea() && Room::lunchBreakOverlaps($validated['start_time'], $validated['end_time'])) {
            throw ValidationException::withMessages(['room' => 'Smoking Area tidak tersedia pada jam istirahat (12.00-13.00 WIB).']);
        }

        $meeting = Meeting::create([
            'title' => $validated['title'],
            'room_id' => $room->id,
            'requested_by' => $requester->id,
            'team_id' => $requester->team_id,
            'why' => $validated['why'] ?? '',
            'what' => $validated['what'] ?? '',
            'how_expected' => $validated['how'] ?? '',
            'meeting_date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Pengajuan meeting berhasil dikirim.', 'data' => $this->format($meeting->load(['requester', 'room', 'approver']))], 201);
    }

    public function approve(Request $request, Meeting $meeting, MeetingQueueService $queue): JsonResponse
    {
        $actor = $this->ensureIntegrationAccount($request);

        if ($meeting->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan sudah diproses.'], 409);
        }

        DB::transaction(function () use ($meeting, $actor, $queue) {
            $meeting->update(['status' => 'approved', 'approved_by' => $actor->id, 'approved_at' => now()]);
            $queue->assignQueue($meeting);
            $members = User::whereIn('team_id', $meeting->allTeamIds())->get();
            foreach ($members as $member) {
                MeetingInvitation::firstOrCreate(['meeting_id' => $meeting->id, 'user_id' => $member->id], ['is_read' => false]);
            }
            $label = MeetingQueueService::queueLabel($meeting->fresh()->queue_position);
            Notification::send($meeting->requested_by, 'activity', 'Meeting Disetujui', 'Meeting "'.$meeting->title.'" telah disetujui. Status: '.$label, route('koordinator.meetings.show', $meeting));
        });

        return response()->json(['message' => 'Pengajuan meeting disetujui.', 'data' => $this->format($meeting->fresh(['requester', 'room', 'approver']))]);
    }

    public function reject(Request $request, Meeting $meeting): JsonResponse
    {
        $actor = $this->ensureIntegrationAccount($request);
        $validated = $request->validate(['notes' => ['required', 'string', 'max:10000']]);

        if ($meeting->status !== 'pending') {
            return response()->json(['message' => 'Pengajuan sudah diproses.'], 409);
        }

        $meeting->update(['status' => 'rejected', 'approved_by' => $actor->id, 'reject_reason' => $validated['notes']]);
        Notification::send($meeting->requested_by, 'activity', 'Meeting Ditolak', 'Meeting "'.$meeting->title.'" ditolak. Alasan: '.$validated['notes'], route('koordinator.meetings.show', $meeting));

        return response()->json(['message' => 'Pengajuan meeting ditolak.', 'data' => $this->format($meeting->fresh(['requester', 'room', 'approver']))]);
    }

    private function ensureIntegrationAccount(Request $request): User
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'admin', 403, 'Akun API integrasi tidak diizinkan.');

        return $user;
    }

    private function format(Meeting $meeting): array
    {
        return [
            'id' => $meeting->id,
            'title' => $meeting->title,
            'date' => $meeting->meeting_date?->format('Y-m-d'),
            'room' => $meeting->room?->name,
            'start_time' => $meeting->start_time,
            'end_time' => $meeting->end_time,
            'status' => in_array($meeting->status, ['approved', 'confirmed', 'in_progress', 'completed'], true) ? 'disetujui' : ($meeting->status === 'rejected' ? 'ditolak' : $meeting->status),
            'why' => $meeting->why,
            'what' => $meeting->what,
            'how' => $meeting->how_expected,
            'notes' => $meeting->reject_reason,
            'employee' => $meeting->requester ? ['nama' => $meeting->requester->name, 'username' => $meeting->requester->username] : null,
            'approver' => $meeting->approver ? ['name' => $meeting->approver->name] : null,
            'created_at' => $meeting->created_at?->toDateTimeString(),
        ];
    }
}

