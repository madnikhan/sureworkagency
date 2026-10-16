<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('customers.view') || $user->hasPermission('customers.manage');
    }

    public function view(User $user, Customer $customer): bool
    {
        if ($user->isClient()) {
            return (int) $user->customer_id === (int) $customer->id;
        }

        return $this->viewAny($user);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->hasPermission('customers.manage');
    }
}
