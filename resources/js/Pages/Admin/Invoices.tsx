import { Link, useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty, Field, buttonClass, inputClass, money } from '../../Components/ui';

type Invoice = { id: number; number: string; title: string; customer: string | null; status: string; gross_pence: number; currency: string };
type Customer = { id: number; name: string };

export default function Invoices({ invoices, customers }: { invoices: Invoice[]; customers: Customer[] }) {
    const form = useForm({
        customer_id: '',
        title: '',
        due_on: '',
        items: [{ description: '', quantity: 1, unit_amount_pence: 0 }],
    });

    return (
        <AdminLayout title="Invoices">
            <form className="grid max-w-xl gap-3" onSubmit={(event) => { event.preventDefault(); form.post('/admin/invoices'); }}>
                <Field label="Customer" error={form.errors.customer_id}>
                    <select className={inputClass} value={form.data.customer_id} onChange={(event) => form.setData('customer_id', event.target.value)}>
                        <option value="">Choose</option>
                        {customers.map((customer) => <option key={customer.id} value={customer.id}>{customer.name}</option>)}
                    </select>
                </Field>
                <Field label="Title" error={form.errors.title}><input className={inputClass} value={form.data.title} onChange={(event) => form.setData('title', event.target.value)} /></Field>
                <Field label="Due"><input type="date" className={inputClass} value={form.data.due_on} onChange={(event) => form.setData('due_on', event.target.value)} /></Field>
                <Field label="Line description"><input className={inputClass} value={form.data.items[0].description} onChange={(event) => form.setData('items', [{ ...form.data.items[0], description: event.target.value }])} /></Field>
                <Field label="Amount in pence"><input type="number" min={0} className={inputClass} value={form.data.items[0].unit_amount_pence} onChange={(event) => form.setData('items', [{ ...form.data.items[0], unit_amount_pence: Number(event.target.value) }])} /></Field>
                <button className={buttonClass} disabled={form.processing}>Create draft</button>
            </form>
            {invoices.length === 0 ? <div className="mt-6"><Empty>No invoices yet.</Empty></div> : (
                <ul className="mt-6 divide-y divide-hairline border-y border-hairline text-sm">
                    {invoices.map((invoice) => (
                        <li key={invoice.id} className="py-3">
                            <Link className="text-cyan" href={`/admin/invoices/${invoice.id}`}>{invoice.number}</Link> {invoice.title} · {invoice.customer} · {invoice.status.replaceAll('_', ' ')} · {money(invoice.gross_pence, invoice.currency)}
                        </li>
                    ))}
                </ul>
            )}
        </AdminLayout>
    );
}
