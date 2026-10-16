import { Link, useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty, Field, Pager, buttonClass, inputClass, rows } from '../../Components/ui';
import type { Paginator } from '../../types';

type Project = { id: number; title: string; status: string; customer?: { name: string } | null };
type Customer = { id: number; name: string };

export default function Projects({ projects, customers }: { projects: Paginator<Project>; customers: Customer[] }) {
    const form = useForm({ customer_id: '', title: '', service_category: '', budget_pence: '' });
    const list = rows(projects);

    return (
        <AdminLayout title="Projects">
            <form className="grid max-w-xl gap-3" onSubmit={(event) => { event.preventDefault(); form.post('/admin/projects'); }}>
                <Field label="Customer" error={form.errors.customer_id}>
                    <select className={inputClass} value={form.data.customer_id} onChange={(event) => form.setData('customer_id', event.target.value)}>
                        <option value="">Choose</option>
                        {customers.map((customer) => <option key={customer.id} value={customer.id}>{customer.name}</option>)}
                    </select>
                </Field>
                <Field label="Title" error={form.errors.title}><input className={inputClass} value={form.data.title} onChange={(event) => form.setData('title', event.target.value)} /></Field>
                <button className={buttonClass} disabled={form.processing}>Create project</button>
            </form>
            {list.length === 0 ? <div className="mt-6"><Empty>No projects yet.</Empty></div> : (
                <ul className="mt-6 divide-y divide-hairline border-y border-hairline text-sm">
                    {list.map((project) => (
                        <li key={project.id} className="py-3">
                            <Link className="text-cyan" href={`/admin/projects/${project.id}`}>{project.title}</Link>
                            <span className="text-muted"> · {project.customer?.name} · {project.status.replaceAll('_', ' ')}</span>
                        </li>
                    ))}
                </ul>
            )}
            <Pager links={projects.links} />
        </AdminLayout>
    );
}
