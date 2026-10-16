import { Link, useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty, Field, Pager, buttonClass, inputClass, money, rows } from '../../Components/ui';
import type { Paginator } from '../../types';

type Quote = { id: number; number: string; title: string; status: string; gross_pence: number; currency: string; customer?: { name: string } | null };
type Customer = { id: number; name: string };

export default function Quotes({ quotes, customers, tax_rate_bps }: { quotes: Paginator<Quote>; customers: Customer[]; tax_rate_bps: number }) {
    const form = useForm({
        customer_id: '',
        title: '',
        scope: '',
        items: [{ description: '', quantity: 1, unit_amount_pence: 0 }],
    });
    const list = rows(quotes);

    return (
        <AdminLayout title="Quotes">
            <p className="text-sm text-muted">Tax rate from settings: {(tax_rate_bps / 100).toFixed(2)}%. It is copied onto the quote when you create it.</p>
            <form className="mt-4 grid max-w-xl gap-3" onSubmit={(event) => { event.preventDefault(); form.post('/admin/quotes'); }}>
                <Field label="Customer" error={form.errors.customer_id}>
                    <select className={inputClass} value={form.data.customer_id} onChange={(event) => form.setData('customer_id', event.target.value)}>
                        <option value="">Choose</option>
                        {customers.map((customer) => <option key={customer.id} value={customer.id}>{customer.name}</option>)}
                    </select>
                </Field>
                <Field label="Title" error={form.errors.title}><input className={inputClass} value={form.data.title} onChange={(event) => form.setData('title', event.target.value)} /></Field>
                <Field label="Scope"><textarea className={inputClass} rows={3} value={form.data.scope} onChange={(event) => form.setData('scope', event.target.value)} /></Field>
                <Field label="Line description" error={form.errors['items.0.description']}>
                    <input className={inputClass} value={form.data.items[0].description} onChange={(event) => form.setData('items', [{ ...form.data.items[0], description: event.target.value }])} />
                </Field>
                <Field label="Amount in pence">
                    <input type="number" min={0} className={inputClass} value={form.data.items[0].unit_amount_pence} onChange={(event) => form.setData('items', [{ ...form.data.items[0], unit_amount_pence: Number(event.target.value) }])} />
                </Field>
                <button className={buttonClass} disabled={form.processing}>Create draft</button>
            </form>
            {list.length === 0 ? <div className="mt-6"><Empty>No quotes yet.</Empty></div> : (
                <ul className="mt-6 divide-y divide-hairline border-y border-hairline text-sm">
                    {list.map((quote) => (
                        <li key={quote.id} className="py-3">
                            <Link className="text-cyan" href={`/admin/quotes/${quote.id}`}>{quote.number}</Link> {quote.title} · {quote.customer?.name} · {quote.status} · {money(quote.gross_pence, quote.currency)}
                        </li>
                    ))}
                </ul>
            )}
            <Pager links={quotes.links} />
        </AdminLayout>
    );
}
