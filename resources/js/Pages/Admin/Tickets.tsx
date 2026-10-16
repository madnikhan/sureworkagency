import { Link, useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty, Field, Pager, buttonClass, inputClass, rows } from '../../Components/ui';
import type { Paginator } from '../../types';

type Ticket = { id: number; number: string; subject: string; status: string; customer?: { name: string } | null };
type Customer = { id: number; name: string };

export default function Tickets({ tickets, customers }: { tickets: Paginator<Ticket>; customers: Customer[] }) {
    const form = useForm({ customer_id: '', subject: '', description: '', priority: 'normal' });
    const list = rows(tickets);

    return (
        <AdminLayout title="Support">
            <form className="grid max-w-xl gap-3" onSubmit={(event) => { event.preventDefault(); form.post('/admin/tickets'); }}>
                {customers.length > 0 ? (
                    <Field label="Customer">
                        <select className={inputClass} value={form.data.customer_id} onChange={(event) => form.setData('customer_id', event.target.value)}>
                            <option value="">Choose</option>
                            {customers.map((customer) => <option key={customer.id} value={customer.id}>{customer.name}</option>)}
                        </select>
                    </Field>
                ) : null}
                <Field label="Subject" error={form.errors.subject}><input className={inputClass} value={form.data.subject} onChange={(event) => form.setData('subject', event.target.value)} /></Field>
                <Field label="Description" error={form.errors.description}><textarea className={inputClass} rows={4} value={form.data.description} onChange={(event) => form.setData('description', event.target.value)} /></Field>
                <button className={buttonClass}>Open ticket</button>
            </form>
            {list.length === 0 ? <div className="mt-6"><Empty>No tickets.</Empty></div> : (
                <ul className="mt-6 divide-y divide-hairline border-y border-hairline text-sm">
                    {list.map((ticket) => (
                        <li key={ticket.id} className="py-3"><Link className="text-cyan" href={`/admin/tickets/${ticket.id}`}>{ticket.number}</Link> {ticket.subject} · {ticket.customer?.name} · {ticket.status.replaceAll('_', ' ')}</li>
                    ))}
                </ul>
            )}
            <Pager links={tickets.links} />
        </AdminLayout>
    );
}
