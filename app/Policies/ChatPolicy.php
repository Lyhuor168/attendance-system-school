<?php

namespace App\Policies;

use App\Models\ChatMessage;
use App\Models\Student;
use App\Models\User;

class ChatPolicy
{
    /**
     * Listing is always scoped by the controller to threads the user is a
     * participant in.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Only a message's sender or receiver may view it, and only if the
     * requester is still authorized for that student context (a Teacher
     * removed from a class loses access to its threads).
     */
    public function view(User $user, ChatMessage $chatMessage): bool
    {
        if ($user->id !== $chatMessage->sender_id && $user->id !== $chatMessage->receiver_id) {
            return false;
        }

        return $this->authorizedForStudent($user, $chatMessage->student);
    }

    /**
     * A Teacher may message only about students in their assigned classes.
     * A Guardian may message only about their own linked children.
     * Admins are not chat participants in this feature.
     */
    public function create(User $user, Student $student): bool
    {
        return $this->authorizedForStudent($user, $student);
    }

    public function update(User $user, ChatMessage $chatMessage): bool
    {
        return false;
    }

    public function delete(User $user, ChatMessage $chatMessage): bool
    {
        return false;
    }

    public function restore(User $user, ChatMessage $chatMessage): bool
    {
        return false;
    }

    public function forceDelete(User $user, ChatMessage $chatMessage): bool
    {
        return false;
    }

    private function authorizedForStudent(User $user, Student $student): bool
    {
        if ($user->isTeacher()) {
            return $user->teacher?->allClassIds()->contains($student->school_class_id) ?? false;
        }

        if ($user->isGuardian()) {
            return $user->guardian?->id === $student->guardian_id;
        }

        return false;
    }
}
