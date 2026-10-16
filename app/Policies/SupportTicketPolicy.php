<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('tickets.view')
            || $user->hasPermission('tickets.manage')
            || $user->hasPermission('tickets.assigned')
            || $user->isClient();
    }

    public function view(User $user, SupportTicket $ticket): bool
    {
        if ($user->isClient()) {
            return (int) $user->customer_id === (int) $ticket->customer_id;
        }

        if ($user->hasPermission('tickets.manage') || $user->hasPermission('tickets.view')) {
            return true;
        }

        return $user->hasPermission('tickets.assigned') && (int) $ticket->assigned_to === (int) $user->id;
    }

    public function update(User $user, SupportTicket $ticket): bool
    {
        return $user->hasPermission('tickets.manage')
            || ($user->hasPermission('tickets.assigned') && (int) $ticket->assigned_to === (int) $user->id);
    }
}
