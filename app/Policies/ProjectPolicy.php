<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('projects.manage') || $user->hasPermission('projects.assigned') || $user->hasPermission('projects.view');
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->isClient()) {
            return (int) $user->customer_id === (int) $project->customer_id;
        }

        if ($user->hasPermission('projects.manage') || $user->hasPermission('projects.view')) {
            return true;
        }

        return $user->hasPermission('projects.assigned')
            && $project->members()->where('users.id', $user->id)->exists();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.manage');
    }
}
