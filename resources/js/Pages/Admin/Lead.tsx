import { router, useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, buttonClass, inputClass, quietButtonClass } from '../../Components/ui';

type Note = { id: number; body: string; user?: { name: string } | null };
type Staff = { id: number; name: string };
type Lead = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    company: string | null;
    service: string | null;
    budget_band: string | null;
    description: string | null;
    status: string;
    source: string;
    follow_up_on: string | null;
    assigned_to: number | null;
    contact_locked: boolean;
    customer?: { id: number; name: string } | null;
    notes: Note[];
};

export default function Lead({ lead, allowed, staff }: { lead: Lead; allowed: string[]; staff: Staff[] }) {
    const details = useForm({
        assigned_to: lead.assigned_to ? String(lead.assigned_to) : '',
        follow_up_on: lead.follow_up_on ?? '',
        service: lead.service ?? '',
        budget_band: lead.budget_band ?? '',
        description: lead.description ?? '',
        name: lead.name,
        email: lead.email,
        phone: lead.phone ?? '',
        company: lead.company ?? '',
    });
    const note = useForm({ body: '' });

    return (
        <AdminLayout title={lead.name}>
            <p className="text-sm text-muted">{lead.source} · {lead.status.replaceAll('_', ' ')}</p>
            {lead.customer ? <p className="mt-2 text-sm">Customer: {lead.customer.name}</p> : null}
            <form
                className="mt-6 grid max-w-2xl gap-4"
                onSubmit={(event) => {
                    event.preventDefault();
                    details.put(`/admin/leads/${lead.id}`);
                }}
            >
                {!lead.contact_locked ? (
                    <>
                        <Field label="Name" error={details.errors.name}><input className={inputClass} value={details.data.name} onChange={(event) => details.setData('name', event.target.value)} /></Field>
                        <Field label="Email" error={details.errors.email}><input className={inputClass} value={details.data.email} onChange={(event) => details.setData('email', event.target.value)} /></Field>
                        <Field label="Phone"><input className={inputClass} value={details.data.phone} onChange={(event) => details.setData('phone', event.target.value)} /></Field>
                        <Field label="Company"><input className={inputClass} value={details.data.company} onChange={(event) => details.setData('company', event.target.value)} /></Field>
                    </>
                ) : (
                    <p className="text-sm text-muted">Contact details are locked because this lead is linked to a customer.</p>
                )}
                <Field label="Assignee">
                    <select className={inputClass} value={details.data.assigned_to} onChange={(event) => details.setData('assigned_to', event.target.value)}>
                        <option value="">Unassigned</option>
                        {staff.map((person) => <option key={person.id} value={person.id}>{person.name}</option>)}
                    </select>
                </Field>
                <Field label="Follow-up"><input type="date" className={inputClass} value={details.data.follow_up_on} onChange={(event) => details.setData('follow_up_on', event.target.value)} /></Field>
                <Field label="Service"><input className={inputClass} value={details.data.service} onChange={(event) => details.setData('service', event.target.value)} /></Field>
                <Field label="Notes"><textarea className={inputClass} rows={4} value={details.data.description} onChange={(event) => details.setData('description', event.target.value)} /></Field>
                <button className={buttonClass} disabled={details.processing}>Save</button>
            </form>
            <div className="mt-8 flex flex-wrap gap-2">
                {allowed.map((status) => (
                    <button key={status} className={quietButtonClass} type="button" onClick={() => router.post(`/admin/leads/${lead.id}/status`, { status })}>
                        Mark {status.replaceAll('_', ' ')}
                    </button>
                ))}
                {['qualified', 'proposal_sent', 'won'].includes(lead.status) && !lead.customer ? (
                    <button className={buttonClass} type="button" onClick={() => router.post(`/admin/leads/${lead.id}/convert`)}>Create customer</button>
                ) : null}
            </div>
            <h2 className="mt-10 text-sm text-muted">Notes</h2>
            <ul className="mt-3 space-y-3 text-sm">
                {lead.notes.map((item) => (
                    <li key={item.id} className="rounded border border-hairline p-3">{item.body}<span className="mt-1 block text-muted">{item.user?.name}</span></li>
                ))}
            </ul>
            <form className="mt-4 max-w-2xl space-y-3" onSubmit={(event) => { event.preventDefault(); note.post(`/admin/leads/${lead.id}/notes`, { onSuccess: () => note.reset() }); }}>
                <Field label="Add a note" error={note.errors.body}><textarea className={inputClass} rows={3} value={note.data.body} onChange={(event) => note.setData('body', event.target.value)} /></Field>
                <button className={quietButtonClass} disabled={note.processing}>Add note</button>
            </form>
        </AdminLayout>
    );
}
