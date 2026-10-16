import { useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, buttonClass, inputClass } from '../../Components/ui';

type Role = { id: number; key: string; name: string };
type UserRow = { id: number; name: string; email: string; role?: Role | null };
type Customer = { id: number; name: string };

export default function Users({ users, roles, customers }: { users: UserRow[]; roles: Role[]; customers: Customer[] }) {
    const form = useForm({ name: '', email: '', password: '', role_id: '', customer_id: '' });

    return (
        <AdminLayout title="Users">
            <form className="grid max-w-xl gap-3" onSubmit={(event) => { event.preventDefault(); form.post('/admin/users'); }}>
                <Field label="Name" error={form.errors.name}><input className={inputClass} value={form.data.name} onChange={(event) => form.setData('name', event.target.value)} /></Field>
                <Field label="Email" error={form.errors.email}><input className={inputClass} value={form.data.email} onChange={(event) => form.setData('email', event.target.value)} /></Field>
                <Field label="Password" error={form.errors.password}><input type="password" className={inputClass} value={form.data.password} onChange={(event) => form.setData('password', event.target.value)} /></Field>
                <Field label="Role" error={form.errors.role_id}>
                    <select className={inputClass} value={form.data.role_id} onChange={(event) => form.setData('role_id', event.target.value)}>
                        <option value="">Choose</option>
                        {roles.map((role) => <option key={role.id} value={role.id}>{role.name}</option>)}
                    </select>
                </Field>
                <Field label="Customer, required for a client">
                    <select className={inputClass} value={form.data.customer_id} onChange={(event) => form.setData('customer_id', event.target.value)}>
                        <option value="">None</option>
                        {customers.map((customer) => <option key={customer.id} value={customer.id}>{customer.name}</option>)}
                    </select>
                </Field>
                <button className={buttonClass}>Create user</button>
            </form>
            <ul className="mt-6 text-sm">{users.map((user) => <li key={user.id}>{user.name} · {user.email} · {user.role?.name}</li>)}</ul>
        </AdminLayout>
    );
}
