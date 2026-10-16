<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('leads.view') || $user->hasPermission('leads.manage');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->hasPermission('leads.manage');
    }
}
