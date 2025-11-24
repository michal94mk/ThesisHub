<?php

namespace App\Policies;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ThesisPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view theses (filtered by role in controller)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Thesis $thesis): bool
    {
        // Admin can view all theses
        if ($user->isAdmin()) {
            return true;
        }
        
        // Student can view their own theses
        if ($user->isStudent() && $thesis->student_id === $user->id) {
            return true;
        }
        
        // Supervisor can view theses they supervise
        if ($user->isSupervisor() && $thesis->supervisor_id === $user->id) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only students can create theses
        return $user->isStudent();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Thesis $thesis): bool
    {
        // Admin can update all theses
        if ($user->isAdmin()) {
            return true;
        }
        
        // Student can update their own thesis if it's in draft or returned for corrections
        if ($user->isStudent() && $thesis->student_id === $user->id) {
            return in_array($thesis->status, [
                Thesis::STATUS_DRAFT,
                Thesis::STATUS_RETURNED_FOR_CORRECTIONS
            ]);
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Thesis $thesis): bool
    {
        // Admin can delete any thesis
        if ($user->isAdmin()) {
            return true;
        }
        
        // Student can delete their own thesis only if it's in draft status
        if ($user->isStudent() && $thesis->student_id === $user->id && $thesis->isDraft()) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Thesis $thesis): bool
    {
        // Only admin can restore
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Thesis $thesis): bool
    {
        // Only admin can permanently delete
        return $user->isAdmin();
    }
}
