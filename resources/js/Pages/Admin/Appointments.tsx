import { router } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty, Pager, inputClass, quietButtonClass, rows } from '../../Components/ui';
import type { Paginator } from '../../types';

type Appointment = { id: number; name: string; email: string; status: string; starts_at: string | null; service: string | null };

const next: Record<string, string[]> = {
    requested: ['confirmed', 'cancelled'],
    confirmed: ['rescheduled', 'cancelled', 'completed'],
    rescheduled: ['confirmed', 'cancelled'],
};

export default function Appointments({ appointments }: { appointments: Paginator<Appointment> }) {
    const list = rows(appointments);

    return (
        <AdminLayout title="Appointments">
            {list.length === 0 ? <Empty>No consultation requests yet.</Empty> : (
                <ul className="space-y-4 text-sm">
                    {list.map((item) => (
                        <li key={item.id} className="rounded border border-hairline p-3">
                            <p>{item.name} · {item.email} · {item.status}</p>
                            <p className="text-muted">{item.service ?? 'No service'} · {item.starts_at ?? 'Time not set'}</p>
                            <div className="mt-2 flex flex-wrap gap-2">
                                {(next[item.status] ?? []).map((status) => (
                                    <button key={status} type="button" className={quietButtonClass} onClick={() => router.post(`/admin/appointments/${item.id}`, { status })}>{status}</button>
                                ))}
                            </div>
                            {item.status === 'confirmed' || item.status === 'rescheduled' ? (
                                <form className="mt-3 flex flex-wrap gap-2" onSubmit={(event) => {
                                    event.preventDefault();
                                    const data = new FormData(event.currentTarget);
                                    router.post(`/admin/appointments/${item.id}`, { status: 'rescheduled', starts_at: data.get('starts_at'), meeting_link: data.get('meeting_link') });
                                }}>
                                    <input type="datetime-local" name="starts_at" className={inputClass + ' max-w-xs'} aria-label="New time" />
                                    <input name="meeting_link" className={inputClass + ' max-w-xs'} placeholder="Meeting link" aria-label="Meeting link" />
                                    <button className={quietButtonClass}>Reschedule</button>
                                </form>
                            ) : null}
                        </li>
                    ))}
                </ul>
            )}
            <Pager links={appointments.links} />
        </AdminLayout>
    );
}
