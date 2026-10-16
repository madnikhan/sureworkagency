<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ConvertLead;
use App\Actions\TransitionRecord;
use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Lead::class);
        $query = Lead::query()->with('assignee:id,name');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($source = $request->string('source')->toString()) {
            $query->where('source', $source);
        }

        $sort = $request->string('sort')->toString() === 'follow_up_on' ? 'follow_up_on' : 'created_at';

        return Inertia::render('Admin/Leads', [
            'leads' => $query->orderByDesc($sort)->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'source', 'sort']),
            'staff' => User::query()->whereHas('role', fn ($q) => $q->where('key', '!=', 'client'))->get(['id', 'name']),
        ]);
    }

    public function show(Lead $lead): Response
    {
        $this->authorize('view', $lead);

        return Inertia::render('Admin/Lead', [
            'lead' => $lead->load(['notes.user:id,name', 'assignee:id,name', 'customer:id,name']),
            'statuses' => array_keys(LeadStatus::map()),
            'allowed' => LeadStatus::map()[$lead->status] ?? [],
            'staff' => User::query()->whereHas('role', fn ($q) => $q->where('key', '!=', 'client'))->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);
        $data = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_on' => ['nullable', 'date'],
            'service' => ['nullable', 'string', 'max:160'],
            'budget_band' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        if (! $lead->contact_locked) {
            $data = array_merge($data, $request->validate([
                'name' => ['sometimes', 'string', 'max:120'],
                'email' => ['sometimes', 'email', 'max:180'],
                'phone' => ['nullable', 'string', 'max:40'],
                'company' => ['nullable', 'string', 'max:160'],
            ]));
        }

        $lead->update($data);
        Activity::record($lead, 'lead.updated', 'Lead details updated.');

        return back()->with('success', 'Lead saved.');
    }

    public function status(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);
        $data = $request->validate(['status' => ['required', 'string']]);
        TransitionRecord::run($lead, $data['status'], LeadStatus::map(), 'lead.status');

        return back()->with('success', 'Status updated.');
    }

    public function note(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $lead->notes()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);
        Activity::record($lead, 'lead.note', 'A note was added.');

        return back()->with('success', 'Note added.');
    }

    public function convert(Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);
        $customer = ConvertLead::run($lead);

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Lead linked to the customer.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Lead::class);

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Company', 'Email', 'Phone', 'Service', 'Source', 'Status', 'Received']);
            Lead::query()->orderByDesc('id')->each(function (Lead $lead) use ($handle) {
                fputcsv($handle, [$lead->name, $lead->company, $lead->email, $lead->phone, $lead->service, $lead->source, $lead->status, $lead->created_at]);
            });
            fclose($handle);
        }, 'leads.csv', ['Content-Type' => 'text/csv']);
    }
}
