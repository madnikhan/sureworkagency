<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('quotes.manage');
    }

    public function view(User $user, Quote $quote): bool
    {
        if ($user->isClient()) {
            return $quote->status !== 'draft' && (int) $user->customer_id === (int) $quote->customer_id;
        }

        return $user->hasPermission('quotes.manage');
    }

    public function update(User $user, Quote $quote): bool
    {
        return $user->hasPermission('quotes.manage');
    }
}
