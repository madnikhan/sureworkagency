import { router, useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, buttonClass, inputClass, money, quietButtonClass } from '../../Components/ui';

type Invoice = {
    id: number;
    number: string;
    title: string;
    status: string;
    currency: string;
    tax_rate_bps: number;
    net_pence: number;
    tax_pence: number;
    gross_pence: number;
    items: Array<{ id: number; description: string; quantity: number; unit_amount_pence: number }>;
    payments: Array<{ id: number; amount_pence: number; method: string; paid_on: string }>;
    customer?: { name: string } | null;
};

export default function Invoice({ invoice, display_status, balance_pence, allowed, can_record_payment }: { invoice: Invoice; display_status: string; balance_pence: number; allowed: string[]; can_record_payment: boolean }) {
    const payment = useForm({ amount_pence: balance_pence, paid_on: '', method: 'bank_transfer', reference: '' });

    return (
        <AdminLayout title={invoice.number}>
            <p className="text-sm text-muted">{invoice.customer?.name} · stored {invoice.status} · shown as {display_status.replaceAll('_', ' ')}</p>
            <ul className="mt-4 text-sm">{invoice.items.map((item) => <li key={item.id}>{item.description} × {item.quantity}</li>)}</ul>
            <p className="mt-4 text-sm">Total {money(invoice.gross_pence, invoice.currency)} · Balance {money(balance_pence, invoice.currency)} · Tax snapshot {(invoice.tax_rate_bps / 100).toFixed(2)}%</p>
            <div className="mt-4 flex flex-wrap gap-2">
                {allowed.map((status) => <button key={status} type="button" className={quietButtonClass} onClick={() => router.post(`/admin/invoices/${invoice.id}/status`, { status })}>Mark {status.replaceAll('_', ' ')}</button>)}
                <a className={quietButtonClass} href={`/admin/invoices/${invoice.id}/pdf`}>Download PDF</a>
            </div>
            <h2 className="mt-8 text-sm text-muted">Payments</h2>
            <ul className="mt-2 text-sm">{invoice.payments.map((item) => <li key={item.id}>{money(item.amount_pence, invoice.currency)} · {item.method.replaceAll('_', ' ')} · {item.paid_on}</li>)}</ul>
            {can_record_payment ? (
                <form className="mt-4 grid max-w-md gap-3" onSubmit={(event) => { event.preventDefault(); payment.post(`/admin/invoices/${invoice.id}/payments`); }}>
                    <Field label="Amount in pence" error={payment.errors.amount_pence}><input type="number" min={1} className={inputClass} value={payment.data.amount_pence} onChange={(event) => payment.setData('amount_pence', Number(event.target.value))} /></Field>
                    <Field label="Paid on" error={payment.errors.paid_on}><input type="date" className={inputClass} value={payment.data.paid_on} onChange={(event) => payment.setData('paid_on', event.target.value)} /></Field>
                    <Field label="Method">
                        <select className={inputClass} value={payment.data.method} onChange={(event) => payment.setData('method', event.target.value)}>
                            <option value="bank_transfer">Bank transfer</option>
                            <option value="cash">Cash</option>
                            <option value="other">Other</option>
                        </select>
                    </Field>
                    <Field label="Reference"><input className={inputClass} value={payment.data.reference} onChange={(event) => payment.setData('reference', event.target.value)} /></Field>
                    <button className={buttonClass} disabled={payment.processing}>Record payment</button>
                </form>
            ) : null}
        </AdminLayout>
    );
}
