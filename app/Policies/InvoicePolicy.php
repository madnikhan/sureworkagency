<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('invoices.view') || $user->hasPermission('invoices.manage');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->isClient()) {
            return (int) $user->customer_id === (int) $invoice->customer_id;
        }

        return $this->viewAny($user);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasPermission('invoices.manage');
    }

    public function pay(User $user, Invoice $invoice): bool
    {
        return $user->hasPermission('payments.manage');
    }
}
