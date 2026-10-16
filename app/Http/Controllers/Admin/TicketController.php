<?php

namespace App\Http\Controllers\Admin;

use App\Actions\TransitionRecord;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SupportTicket;
use App\Models\User;
use App\Support\Activity;
use App\Support\Numbers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', SupportTicket::class);
        $query = SupportTicket::query()->with('customer:id,name');
        $user = $request->user();
        if ($user->isClient()) {
            $query->where('customer_id', $user->customer_id);
        } elseif (! $user->hasPermission('tickets.manage') && ! $user->hasPermission('tickets.view')) {
            $query->where('assigned_to', $user->id);
        }

        return Inertia::render('Admin/Tickets', [
            'tickets' => $query->latest()->paginate(20),
            'customers' => $user->isClient() ? [] : Customer::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('tickets.manage') || $request->user()->isClient(), 403);
        $customerId = $request->user()->isClient()
            ? $request->user()->customer_id
            : $request->validate(['customer_id' => ['required', 'exists:customers,id']])['customer_id'];
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['nullable', 'in:low,normal,high'],
        ]);
        $ticket = SupportTicket::query()->create($data + [
            'customer_id' => $customerId,
            'number' => Numbers::next(SupportTicket::class, 'number', 'T'),
            'priority' => $data['priority'] ?? 'normal',
            'status' => 'open',
        ]);
        Activity::record($ticket, 'ticket.created', 'Support ticket opened.');

        return redirect()->route('admin.tickets.show', $ticket);
    }

    public function show(Request $request, SupportTicket $ticket): Response
    {
        $this->authorize('view', $ticket);
        $messages = $ticket->messages()->latest()->get();
        if ($request->user()->isClient()) {
            $messages = $messages->where('visibility', 'client')->values();
        }

        return Inertia::render('Admin/Ticket', [
            'ticket' => $ticket->load('customer:id,name'),
            'messages' => $messages,
            'allowed' => TicketStatus::map()[$ticket->status] ?? [],
            'staff' => User::query()->whereHas('role', fn ($q) => $q->where('key', 'developer'))->get(['id', 'name']),
        ]);
    }

    public function message(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('view', $ticket);
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'visibility' => ['nullable', 'in:internal,client'],
        ]);
        $visibility = $request->user()->isClient() ? 'client' : ($data['visibility'] ?? 'client');
        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'visibility' => $visibility,
            'body' => $data['body'],
        ]);

        return back()->with('success', 'Reply saved.');
    }

    public function status(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);
        $data = $request->validate([
            'status' => ['required', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'resolution_summary' => ['nullable', 'string', 'max:5000'],
        ]);
        if (array_key_exists('assigned_to', $data)) {
            $ticket->assigned_to = $data['assigned_to'];
            $ticket->save();
        }
        if ($data['resolution_summary'] ?? null) {
            $ticket->resolution_summary = $data['resolution_summary'];
            $ticket->save();
        }
        if ($data['status'] !== $ticket->status) {
            TransitionRecord::run($ticket, $data['status'], TicketStatus::map(), 'ticket.status');
        }

        return back()->with('success', 'Ticket updated.');
    }
}
