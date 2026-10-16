import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty, Pager, inputClass, quietButtonClass, rows } from '../../Components/ui';
import type { Paginator } from '../../types';

type Lead = { id: number; name: string; email: string; company: string | null; source: string; status: string; follow_up_on: string | null };

export default function Leads({ leads, filters }: { leads: Paginator<Lead>; filters: { search?: string; status?: string; source?: string; sort?: string } }) {
    const [search, setSearch] = useState(filters.search ?? '');
    const list = rows(leads);

    return (
        <AdminLayout title="Leads">
            <form
                className="flex flex-wrap gap-2"
                onSubmit={(event) => {
                    event.preventDefault();
                    router.get('/admin/leads', { ...filters, search }, { preserveState: true });
                }}
            >
                <input className={inputClass + ' max-w-xs'} value={search} onChange={(event) => setSearch(event.target.value)} placeholder="Search name, email, company" aria-label="Search leads" />
                <select className={inputClass + ' max-w-[10rem]'} aria-label="Status" value={filters.status ?? ''} onChange={(event) => router.get('/admin/leads', { ...filters, status: event.target.value }, { preserveState: true })}>
                    <option value="">Any status</option>
                    {['new', 'contacted', 'qualified', 'proposal_sent', 'won', 'lost'].map((status) => (
                        <option key={status} value={status}>{status.replace('_', ' ')}</option>
                    ))}
                </select>
                <select className={inputClass + ' max-w-[10rem]'} aria-label="Source" value={filters.source ?? ''} onChange={(event) => router.get('/admin/leads', { ...filters, source: event.target.value }, { preserveState: true })}>
                    <option value="">Any source</option>
                    <option value="quote">quote</option>
                    <option value="consultation">consultation</option>
                </select>
                <button className={quietButtonClass}>Search</button>
                <a className={quietButtonClass} href="/admin/leads/export">Export CSV</a>
            </form>
            {list.length === 0 ? (
                <div className="mt-6"><Empty>No leads match these filters.</Empty></div>
            ) : (
                <table className="mt-6 w-full text-left text-sm">
                    <thead className="text-muted">
                        <tr><th className="py-2">Name</th><th>Email</th><th>Source</th><th>Status</th><th>Follow-up</th></tr>
                    </thead>
                    <tbody>
                        {list.map((lead) => (
                            <tr key={lead.id} className="border-t border-hairline">
                                <td className="py-2"><Link href={`/admin/leads/${lead.id}`} className="text-cyan">{lead.name}</Link></td>
                                <td>{lead.email}</td>
                                <td>{lead.source}</td>
                                <td>{lead.status.replaceAll('_', ' ')}</td>
                                <td>{lead.follow_up_on ?? '—'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            )}
            <Pager links={leads.links} />
        </AdminLayout>
    );
}
