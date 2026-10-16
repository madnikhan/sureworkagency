<?php

namespace App\Http\Controllers\Admin;

use App\Actions\DocumentTotals;
use App\Actions\TransitionRecord;
use App\Enums\InvoiceStatus;
use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PaymentRecord;
use App\Models\Quote;
use App\Support\Activity;
use App\Support\Numbers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CommerceController extends Controller
{
    public function quotes(): Response
    {
        $this->authorize('viewAny', Quote::class);

        return Inertia::render('Admin/Quotes', [
            'quotes' => Quote::query()->with('customer:id,name')->latest()->paginate(20),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            'tax_rate_bps' => BusinessSetting::current()->vat_rate_bps,
        ]);
    }

    public function storeQuote(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Quote::class);
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'title' => ['required', 'string', 'max:160'],
            'scope' => ['nullable', 'string'],
            'exclusions' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'timeline' => ['nullable', 'string', 'max:160'],
            'valid_until' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_amount_pence' => ['required', 'integer', 'min:0'],
        ]);

        $setting = BusinessSetting::current();
        $quote = Quote::query()->create([
            'customer_id' => $data['customer_id'],
            'number' => Numbers::next(Quote::class, 'number', 'Q'),
            'title' => $data['title'],
            'scope' => $data['scope'] ?? null,
            'exclusions' => $data['exclusions'] ?? null,
            'terms' => $data['terms'] ?? null,
            'timeline' => $data['timeline'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
            'currency' => $setting->currency,
            'tax_rate_bps' => $setting->vat_rate_bps,
            'status' => 'draft',
        ]);
        foreach ($data['items'] as $index => $item) {
            $quote->items()->create($item + ['sort_order' => $index]);
        }
        DocumentTotals::quote($quote);
        Activity::record($quote, 'quote.created', 'Draft quote created.');

        return redirect()->route('admin.quotes.show', $quote);
    }

    public function showQuote(Quote $quote): Response
    {
        $this->authorize('view', $quote);

        return Inertia::render('Admin/Quote', [
            'quote' => $quote->load('items', 'customer'),
            'allowed' => QuoteStatus::map()[$quote->status] ?? [],
        ]);
    }

    public function quoteStatus(Request $request, Quote $quote): RedirectResponse
    {
        $this->authorize('update', $quote);
        $status = $request->validate(['status' => ['required', 'string']])['status'];
        TransitionRecord::run($quote, $status, QuoteStatus::map(), 'quote.status');
        if ($status === 'sent') {
            $quote->forceFill(['issued_at' => now()])->save();
        }

        return back()->with('success', 'Quote updated.');
    }

    public function quotePdf(Quote $quote): HttpResponse
    {
        $this->authorize('view', $quote);

        return Pdf::loadView('pdf.document', [
            'title' => 'Proposal '.$quote->number,
            'document' => $quote->load('items', 'customer'),
            'kind' => 'Proposal',
        ])->download($quote->number.'.pdf');
    }

    public function invoices(): Response
    {
        $this->authorize('viewAny', Invoice::class);

        return Inertia::render('Admin/Invoices', [
            'invoices' => Invoice::query()->with('customer:id,name')->latest()->get()->map(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'title' => $invoice->title,
                'customer' => $invoice->customer?->name,
                'status' => $invoice->displayStatus(),
                'gross_pence' => $invoice->gross_pence,
                'currency' => $invoice->currency,
            ]),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $this->authorize('update', new Invoice);
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'title' => ['required', 'string', 'max:160'],
            'due_on' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_amount_pence' => ['required', 'integer', 'min:0'],
        ]);
        $setting = BusinessSetting::current();
        $invoice = Invoice::query()->create([
            'customer_id' => $data['customer_id'],
            'number' => Numbers::next(Invoice::class, 'number', 'INV'),
            'title' => $data['title'],
            'due_on' => $data['due_on'] ?? null,
            'currency' => $setting->currency,
            'tax_rate_bps' => $setting->vat_rate_bps,
            'status' => 'draft',
        ]);
        foreach ($data['items'] as $index => $item) {
            $invoice->items()->create($item + ['sort_order' => $index]);
        }
        DocumentTotals::invoice($invoice);
        Activity::record($invoice, 'invoice.created', 'Draft invoice created.');

        return redirect()->route('admin.invoices.show', $invoice);
    }

    public function showInvoice(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        return Inertia::render('Admin/Invoice', [
            'invoice' => $invoice->load('items', 'customer', 'payments'),
            'display_status' => $invoice->displayStatus(),
            'balance_pence' => $invoice->balancePence(),
            'allowed' => InvoiceStatus::map()[$invoice->status] ?? [],
            'can_record_payment' => auth()->user()->hasPermission('payments.manage'),
        ]);
    }

    public function invoiceStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        $status = $request->validate(['status' => ['required', 'string']])['status'];
        TransitionRecord::run($invoice, $status, InvoiceStatus::map(), 'invoice.status');
        if ($status === 'sent') {
            $invoice->forceFill(['issued_at' => now()])->save();
        }

        return back()->with('success', 'Invoice updated.');
    }

    public function payment(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('pay', $invoice);
        $data = $request->validate([
            'amount_pence' => ['required', 'integer', 'min:1'],
            'paid_on' => ['required', 'date'],
            'method' => ['required', 'in:bank_transfer,cash,other'],
            'reference' => ['nullable', 'string', 'max:120'],
        ]);
        PaymentRecord::query()->create($data + ['invoice_id' => $invoice->id]);
        $balance = $invoice->fresh()->balancePence();
        $next = $balance === 0 ? 'paid' : 'part_paid';
        if (in_array($next, InvoiceStatus::map()[$invoice->status] ?? [], true)) {
            TransitionRecord::run($invoice, $next, InvoiceStatus::map(), 'invoice.payment');
        }
        Activity::record($invoice, 'invoice.payment', 'A payment was recorded.');

        return back()->with('success', 'Payment recorded.');
    }

    public function invoicePdf(Invoice $invoice): HttpResponse
    {
        $this->authorize('view', $invoice);

        return Pdf::loadView('pdf.document', [
            'title' => 'Invoice '.$invoice->number,
            'document' => $invoice->load('items', 'customer'),
            'kind' => 'Invoice',
        ])->download($invoice->number.'.pdf');
    }
}
