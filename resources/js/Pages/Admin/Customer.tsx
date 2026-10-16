import { Link, useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, buttonClass, inputClass } from '../../Components/ui';

type Customer = {
    id: number;
    name: string;
    company: string | null;
    email: string;
    phone: string | null;
    billing_address: string | null;
    staff_notes: string | null;
    projects: Array<{ id: number; title: string; status: string }>;
    invoices: Array<{ id: number; number: string; status: string }>;
};

export default function Customer({ customer }: { customer: Customer }) {
    const form = useForm({
        name: customer.name,
        company: customer.company ?? '',
        email: customer.email,
        phone: customer.phone ?? '',
        billing_address: customer.billing_address ?? '',
        staff_notes: customer.staff_notes ?? '',
    });

    return (
        <AdminLayout title={customer.name}>
            <form className="grid max-w-2xl gap-4" onSubmit={(event) => { event.preventDefault(); form.put(`/admin/customers/${customer.id}`); }}>
                <Field label="Name" error={form.errors.name}><input className={inputClass} value={form.data.name} onChange={(event) => form.setData('name', event.target.value)} /></Field>
                <Field label="Company"><input className={inputClass} value={form.data.company} onChange={(event) => form.setData('company', event.target.value)} /></Field>
                <Field label="Email" error={form.errors.email}><input className={inputClass} value={form.data.email} onChange={(event) => form.setData('email', event.target.value)} /></Field>
                <Field label="Phone"><input className={inputClass} value={form.data.phone} onChange={(event) => form.setData('phone', event.target.value)} /></Field>
                <Field label="Billing address"><textarea className={inputClass} rows={3} value={form.data.billing_address} onChange={(event) => form.setData('billing_address', event.target.value)} /></Field>
                <Field label="Staff notes"><textarea className={inputClass} rows={3} value={form.data.staff_notes} onChange={(event) => form.setData('staff_notes', event.target.value)} /></Field>
                <button className={buttonClass} disabled={form.processing}>Save</button>
            </form>
            <h2 className="mt-8 text-sm text-muted">Projects</h2>
            <ul className="mt-2 text-sm">{customer.projects.map((project) => <li key={project.id}><Link className="text-cyan" href={`/admin/projects/${project.id}`}>{project.title}</Link> · {project.status}</li>)}</ul>
            <h2 className="mt-6 text-sm text-muted">Invoices</h2>
            <ul className="mt-2 text-sm">{customer.invoices.map((invoice) => <li key={invoice.id}><Link className="text-cyan" href={`/admin/invoices/${invoice.id}`}>{invoice.number}</Link> · {invoice.status}</li>)}</ul>
        </AdminLayout>
    );
}
