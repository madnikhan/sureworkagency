import { router } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { money, quietButtonClass } from '../../Components/ui';

type Item = { id: number; description: string; quantity: number; unit_amount_pence: number };
type Quote = {
    id: number;
    number: string;
    title: string;
    status: string;
    scope: string | null;
    currency: string;
    tax_rate_bps: number;
    net_pence: number;
    tax_pence: number;
    gross_pence: number;
    items: Item[];
    customer?: { name: string } | null;
};

export default function Quote({ quote, allowed }: { quote: Quote; allowed: string[] }) {
    return (
        <AdminLayout title={quote.number}>
            <p className="text-sm text-muted">{quote.customer?.name} · {quote.status}</p>
            <h2 className="mt-4 text-xl">{quote.title}</h2>
            {quote.scope ? <p className="mt-3 max-w-2xl text-sm">{quote.scope}</p> : null}
            <ul className="mt-6 text-sm">
                {quote.items.map((item) => <li key={item.id}>{item.description} × {item.quantity} · {money(item.unit_amount_pence * item.quantity, quote.currency)}</li>)}
            </ul>
            <p className="mt-4 text-sm">Net {money(quote.net_pence, quote.currency)} · Tax {money(quote.tax_pence, quote.currency)} ({(quote.tax_rate_bps / 100).toFixed(2)}%) · Total {money(quote.gross_pence, quote.currency)}</p>
            <div className="mt-6 flex flex-wrap gap-2">
                {allowed.map((status) => (
                    <button key={status} type="button" className={quietButtonClass} onClick={() => router.post(`/admin/quotes/${quote.id}/status`, { status })}>Mark {status}</button>
                ))}
                <a className={quietButtonClass} href={`/admin/quotes/${quote.id}/pdf`}>Download PDF</a>
            </div>
        </AdminLayout>
    );
}
