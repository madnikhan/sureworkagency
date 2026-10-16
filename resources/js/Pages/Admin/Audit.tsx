import AdminLayout from '../../Components/AdminLayout';
import { Pager, rows } from '../../Components/ui';
import type { Paginator } from '../../types';

type Entry = { id: number; action: string; summary: string; created_at: string; user?: { name: string } | null };

export default function Audit({ entries }: { entries: Paginator<Entry> }) {
    const list = rows(entries);

    return (
        <AdminLayout title="Audit log">
            <table className="w-full text-left text-sm">
                <thead className="text-muted"><tr><th className="py-2">When</th><th>Who</th><th>Action</th><th>Summary</th></tr></thead>
                <tbody>
                    {list.map((entry) => (
                        <tr key={entry.id} className="border-t border-hairline">
                            <td className="py-2">{entry.created_at}</td>
                            <td>{entry.user?.name ?? 'System'}</td>
                            <td>{entry.action}</td>
                            <td>{entry.summary}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <Pager links={entries.links} />
        </AdminLayout>
    );
}
