import { Head } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty, money } from '../../Components/ui';

type Counts = {
    new_enquiries: number | null;
    total_leads: number | null;
    follow_ups: number | null;
    active_projects: number | null;
    upcoming_appointments: number | null;
    outstanding_invoices: number | null;
    monthly_revenue_pence: number | null;
    tasks_due_today: number | null;
};

type Activity = { id: number; action: string; summary: string; created_at: string };

const labels: Array<[keyof Counts, string]> = [
    ['new_enquiries', 'New enquiries'],
    ['total_leads', 'Leads'],
    ['follow_ups', 'Follow-ups due'],
    ['active_projects', 'Active projects'],
    ['upcoming_appointments', 'Upcoming appointments'],
    ['outstanding_invoices', 'Outstanding invoices'],
    ['tasks_due_today', 'Tasks due today'],
];

export default function Dashboard({ counts, activity }: { counts: Counts; activity: Activity[] }) {
    return (
        <AdminLayout title="Dashboard">
            <Head title="Dashboard" />
            <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                {labels.map(([key, label]) =>
                    counts[key] === null ? null : (
                        <div key={key} className="rounded-md border border-hairline bg-charcoal p-4">
                            <p className="text-sm text-muted">{label}</p>
                            <p className="mt-2 text-2xl">{counts[key]}</p>
                        </div>
                    ),
                )}
                {counts.monthly_revenue_pence !== null ? (
                    <div className="rounded-md border border-hairline bg-charcoal p-4">
                        <p className="text-sm text-muted">Recorded this month</p>
                        <p className="mt-2 text-2xl text-lime">{money(counts.monthly_revenue_pence)}</p>
                    </div>
                ) : null}
            </div>
            <h2 className="mt-8 text-sm text-muted">Recent activity</h2>
            {activity.length === 0 ? (
                <div className="mt-3">
                    <Empty>No activity yet. New enquiries will appear here.</Empty>
                </div>
            ) : (
                <ul className="mt-3 divide-y divide-hairline border-y border-hairline text-sm">
                    {activity.map((entry) => (
                        <li key={entry.id} className="py-3">
                            <span className="text-muted">{entry.action}</span> {entry.summary}
                        </li>
                    ))}
                </ul>
            )}
        </AdminLayout>
    );
}
