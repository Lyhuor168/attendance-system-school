<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        abort_unless($user->isTeacher() || $user->isGuardian(), 403);

        if ($user->isTeacher()) {
            $students = Student::whereIn('school_class_id', $user->teacher?->allClassIds() ?? collect())
                ->whereHas('guardian', fn ($query) => $query->whereNotNull('user_id'))
                ->with('guardian.user', 'schoolClass')
                ->orderBy('name')
                ->get();
        } else {
            $students = $user->guardian?->students()->with('schoolClass')->get() ?? collect();
        }

        return view('chat.index', ['students' => $students]);
    }

    public function show(Student $student): View
    {
        $this->authorize('create', [ChatMessage::class, $student]);

        $user = request()->user();
        $otherParty = $this->otherParty($user, $student);

        ChatMessage::where('student_id', $student->id)
            ->where('receiver_id', $user->id)
            ->update(['is_read' => true]);

        $messages = ChatMessage::where('student_id', $student->id)
            ->where(function ($query) use ($user, $otherParty) {
                $query->where('sender_id', $user->id)->orWhere('sender_id', $otherParty?->id);
            })
            ->orderBy('created_at')
            ->get();

        return view('chat.show', [
            'student' => $student,
            'otherParty' => $otherParty,
            'messages' => $messages,
        ]);
    }

    public function store(StoreChatMessageRequest $request, Student $student): RedirectResponse
    {
        $user = $request->user();
        $otherParty = $this->otherParty($user, $student);

        abort_if($otherParty === null, 422, 'No one to message for this student yet.');

        ChatMessage::create([
            'sender_id' => $user->id,
            'receiver_id' => $otherParty->id,
            'student_id' => $student->id,
            'message' => $request->validated('message'),
        ]);

        return redirect()->route('chat.show', $student);
    }

    /**
     * Resolve the other participant in a Teacher<->Guardian thread about
     * this student: the student's Guardian (if a Teacher is asking) or the
     * student's assigned Teacher (if a Guardian is asking).
     */
    private function otherParty(User $user, Student $student): ?User
    {
        if ($user->isTeacher()) {
            return $student->guardian?->user;
        }

        if ($user->isGuardian()) {
            $teacher = $student->schoolClass->homeroomTeacher
                ?? $student->schoolClass->assignedTeachers()->first();

            return $teacher?->user;
        }

        return null;
    }
}
