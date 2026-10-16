import { router, useForm, usePage } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, inputClass, quietButtonClass } from '../../Components/ui';
import type { PageProps } from '../../types';

type Message = { id: number; body: string; visibility: string };
type Ticket = { id: number; number: string; subject: string; description: string; status: string };
type Staff = { id: number; name: string };

export default function Ticket({ ticket, messages, allowed, staff }: { ticket: Ticket; messages: Message[]; allowed: string[]; staff: Staff[] }) {
    const { auth } = usePage<PageProps>().props;
    const reply = useForm({ body: '', visibility: 'client' });

    return (
        <AdminLayout title={ticket.number}>
            <h2 className="text-xl">{ticket.subject}</h2>
            <p className="mt-3 max-w-2xl text-sm">{ticket.description}</p>
            <p className="mt-2 text-sm text-muted">{ticket.status.replaceAll('_', ' ')}</p>
            {auth.user?.role !== 'client' ? (
                <div className="mt-4 flex flex-wrap gap-2">
                    {allowed.map((status) => <button key={status} type="button" className={quietButtonClass} onClick={() => router.post(`/admin/tickets/${ticket.id}/status`, { status })}>{status.replaceAll('_', ' ')}</button>)}
                    <form className="flex gap-2" onSubmit={(event) => { event.preventDefault(); const data = new FormData(event.currentTarget); router.post(`/admin/tickets/${ticket.id}/status`, { status: ticket.status, assigned_to: data.get('assigned_to') }); }}>
                        <select name="assigned_to" className={inputClass} aria-label="Assign developer">
                            <option value="">Assign</option>
                            {staff.map((person) => <option key={person.id} value={person.id}>{person.name}</option>)}
                        </select>
                        <button className={quietButtonClass}>Assign</button>
                    </form>
                </div>
            ) : null}
            <ul className="mt-8 space-y-3 text-sm">
                {messages.map((message) => (
                    <li key={message.id} className="rounded border border-hairline p-3">
                        <span className="text-xs text-muted">{message.visibility === 'internal' ? 'Internal note' : 'Reply'}</span>
                        <p className="mt-1">{message.body}</p>
                    </li>
                ))}
            </ul>
            <form className="mt-4 max-w-xl space-y-3" onSubmit={(event) => { event.preventDefault(); reply.post(`/admin/tickets/${ticket.id}/messages`, { onSuccess: () => reply.reset('body') }); }}>
                <Field label="Reply" error={reply.errors.body}><textarea className={inputClass} rows={4} value={reply.data.body} onChange={(event) => reply.setData('body', event.target.value)} /></Field>
                {auth.user?.role !== 'client' ? (
                    <label className="flex items-center gap-2 text-sm text-muted">
                        <input type="checkbox" checked={reply.data.visibility === 'internal'} onChange={(event) => reply.setData('visibility', event.target.checked ? 'internal' : 'client')} />
                        Internal note, hidden from the client
                    </label>
                ) : null}
                <button className={quietButtonClass}>Save reply</button>
            </form>
        </AdminLayout>
    );
}
