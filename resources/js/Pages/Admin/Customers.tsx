import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty, Pager, inputClass, quietButtonClass, rows } from '../../Components/ui';
import type { Paginator } from '../../types';

type Customer = { id: number; name: string; company: string | null; email: string };

export default function Customers({ customers }: { customers: Paginator<Customer> }) {
    const [search, setSearch] = useState('');
    const list = rows(customers);

    return (
        <AdminLayout title="Customers">
            <form className="flex gap-2" onSubmit={(event) => { event.preventDefault(); router.get('/admin/customers', { search }, { preserveState: true }); }}>
                <input className={inputClass + ' max-w-xs'} aria-label="Search customers" value={search} onChange={(event) => setSearch(event.target.value)} />
                <button className={quietButtonClass}>Search</button>
            </form>
            {list.length === 0 ? <div className="mt-6"><Empty>No customers yet. Convert a qualified lead to create one.</Empty></div> : (
                <table className="mt-6 w-full text-left text-sm">
                    <thead className="text-muted"><tr><th className="py-2">Name</th><th>Company</th><th>Email</th></tr></thead>
                    <tbody>
                        {list.map((customer) => (
                            <tr key={customer.id} className="border-t border-hairline">
                                <td className="py-2"><Link className="text-cyan" href={`/admin/customers/${customer.id}`}>{customer.name}</Link></td>
                                <td>{customer.company ?? '—'}</td>
                                <td>{customer.email}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            )}
            <Pager links={customers.links} />
        </AdminLayout>
    );
}
