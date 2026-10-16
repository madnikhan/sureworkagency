<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PortalController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user->isClient() && $user->customer_id, 403);

        return Inertia::render('Portal/Home', [
            'projects' => Project::query()->where('customer_id', $user->customer_id)->get(['id', 'title', 'status']),
            'quotes' => Quote::query()->where('customer_id', $user->customer_id)->where('status', '!=', 'draft')->get(['id', 'number', 'title', 'status']),
            'invoices' => Invoice::query()->where('customer_id', $user->customer_id)->where('status', '!=', 'draft')->get()->map(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'status' => $invoice->displayStatus(),
            ]),
            'tickets' => SupportTicket::query()->where('customer_id', $user->customer_id)->get(['id', 'number', 'subject', 'status']),
        ]);
    }
}
