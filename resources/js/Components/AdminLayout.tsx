import { Head, Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import type { PageProps } from '../types';
import { Flash } from './ui';

const links = [
    ['Dashboard', '/admin'],
    ['Leads', '/admin/leads'],
    ['Customers', '/admin/customers'],
    ['Projects', '/admin/projects'],
    ['Services', '/admin/services'],
    ['Quotes', '/admin/quotes'],
    ['Invoices', '/admin/invoices'],
    ['Appointments', '/admin/appointments'],
    ['Marketing', '/admin/marketing'],
    ['Support', '/admin/tickets'],
    ['Files', '/admin/files'],
    ['Insights', '/admin/insights'],
    ['Users', '/admin/users'],
    ['Settings', '/admin/settings'],
    ['Audit log', '/admin/audit'],
];

export default function AdminLayout({ title, children }: { title: string; children: ReactNode }) {
    const { auth } = usePage<PageProps>().props;
    const items = auth.user?.role === 'client'
        ? [['Your work', '/portal'], ['Support', '/admin/tickets']]
        : links;

    return (
        <div className="min-h-screen bg-[#101114] text-paper">
            <Head title={title} />
            <div className="flex min-h-screen">
                <aside className="hidden w-56 shrink-0 border-r border-hairline bg-charcoal p-4 md:block">
                    <p className="px-2 text-xs tracking-[0.18em] text-muted">SUREWORK</p>
                    <nav aria-label="Admin" className="mt-4 flex flex-col gap-1 text-sm">
                        {items.map(([label, href]) => (
                            <Link key={href} href={href} className="rounded px-2 py-1.5 text-muted hover:bg-ink hover:text-paper">
                                {label}
                            </Link>
                        ))}
                    </nav>
                </aside>
                <div className="min-w-0 flex-1">
                    <header className="flex items-center justify-between border-b border-hairline px-4 py-3">
                        <h1 className="text-lg font-medium">{title}</h1>
                        <div className="flex items-center gap-3 text-sm text-muted">
                            <span>{auth.user?.name}</span>
                            <Link href="/logout" method="post" as="button">
                                Sign out
                            </Link>
                        </div>
                    </header>
                    <Flash />
                    <div className="p-4 md:p-6">{children}</div>
                </div>
            </div>
        </div>
    );
}
