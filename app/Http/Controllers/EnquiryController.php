<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Mail\AppointmentUpdated;
use App\Mail\EnquiryReceived;
use App\Models\Appointment;
use App\Models\Lead;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class EnquiryController extends Controller
{
    public function quote(): Response
    {
        return Inertia::render('Public/Enquiry', [
            'mode' => 'quote',
            'services' => $this->serviceNames(),
        ]);
    }

    public function book(): Response
    {
        return Inertia::render('Public/Enquiry', [
            'mode' => 'consultation',
            'services' => $this->serviceNames(),
        ]);
    }

    public function storeQuote(StoreEnquiryRequest $request): RedirectResponse
    {
        $lead = $this->lead($request->validated(), 'quote');
        Mail::to(config('brand.email'))->send(new EnquiryReceived($lead));

        return back()->with('success', 'Your quote request has been recorded. We will reply by email.');
    }

    public function storeBooking(StoreEnquiryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $lead = $this->lead($data, 'consultation');
        $appointment = Appointment::query()->create([
            'lead_id' => $lead->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'service' => $data['service'] ?? null,
            'starts_at' => $data['preferred_date'] ?? null,
            'timezone' => $data['timezone'] ?? 'Europe/London',
            'status' => 'requested',
            'notes' => $data['description'] ?? null,
        ]);
        Mail::to($appointment->email)->send(new AppointmentUpdated($appointment));
        Mail::to(config('brand.email'))->send(new EnquiryReceived($lead));

        return back()->with('success', 'Your consultation request has been recorded.');
    }

    private function lead(array $data, string $source): Lead
    {
        $lead = Lead::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'service' => $data['service'] ?? null,
            'budget_band' => $data['budget_band'] ?? null,
            'description' => $data['description'] ?? null,
            'source' => $source,
            'status' => 'new',
            'marketing_consent' => (bool) ($data['marketing_consent'] ?? false),
        ]);
        Activity::record($lead, 'lead.created', 'Enquiry received from the website.');

        return $lead;
    }

    private function serviceNames(): array
    {
        return collect(config('services_catalogue.services'))->pluck('name')->all();
    }
}
