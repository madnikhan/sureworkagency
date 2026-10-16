<?php

namespace App\Actions;

use App\Enums\LeadStatus;
use App\Models\Customer;
use App\Models\Lead;
use App\Support\Activity;
use Illuminate\Validation\ValidationException;

class ConvertLead
{
    public static function run(Lead $lead): Customer
    {
        if (! in_array($lead->status, LeadStatus::convertible(), true)) {
            throw ValidationException::withMessages([
                'status' => 'Only a qualified, proposed, or won lead can become a customer.',
            ]);
        }

        if ($lead->customer_id) {
            return $lead->customer;
        }

        $customer = Customer::query()->firstOrCreate(
            ['email' => $lead->email],
            [
                'name' => $lead->name,
                'company' => $lead->company,
                'phone' => $lead->phone,
            ],
        );

        $lead->forceFill([
            'customer_id' => $customer->id,
            'contact_locked' => true,
        ])->save();

        Activity::record($lead, 'lead.converted', 'Lead linked to a customer record.');

        return $customer;
    }
}
