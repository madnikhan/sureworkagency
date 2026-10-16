import { router, useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, buttonClass, inputClass, quietButtonClass } from '../../Components/ui';

type Task = { id: number; title: string; status: string; due_on: string | null; overdue: boolean };
type Staff = { id: number; name: string };
type Project = { id: number; title: string; status: string; customer?: { name: string } | null; milestones: Array<{ id: number; title: string }> };

export default function Project({ project, tasks, allowed, staff }: { project: Project; tasks: Task[]; allowed: string[]; staff: Staff[] }) {
    const task = useForm({ title: '', due_on: '' });
    const milestone = useForm({ title: '' });
    const member = useForm({ user_id: '' });

    return (
        <AdminLayout title={project.title}>
            <p className="text-sm text-muted">{project.customer?.name} · {project.status.replaceAll('_', ' ')}</p>
            <div className="mt-4 flex flex-wrap gap-2">
                {allowed.map((status) => (
                    <button key={status} type="button" className={quietButtonClass} onClick={() => router.post(`/admin/projects/${project.id}/status`, { status })}>Mark {status.replaceAll('_', ' ')}</button>
                ))}
            </div>
            <h2 className="mt-8 text-sm text-muted">Tasks</h2>
            <ul className="mt-2 text-sm">
                {tasks.map((item) => (
                    <li key={item.id} className={item.overdue ? 'text-red-300' : ''}>{item.title} · {item.status}{item.due_on ? ` · due ${item.due_on}` : ''}{item.overdue ? ' · overdue' : ''}</li>
                ))}
            </ul>
            <form className="mt-4 flex flex-wrap gap-2" onSubmit={(event) => { event.preventDefault(); task.post(`/admin/projects/${project.id}/tasks`, { onSuccess: () => task.reset() }); }}>
                <input className={inputClass + ' max-w-xs'} aria-label="Task title" value={task.data.title} onChange={(event) => task.setData('title', event.target.value)} />
                <input type="date" className={inputClass + ' max-w-[11rem]'} aria-label="Due date" value={task.data.due_on} onChange={(event) => task.setData('due_on', event.target.value)} />
                <button className={quietButtonClass}>Add task</button>
            </form>
            <h2 className="mt-8 text-sm text-muted">Milestones</h2>
            <ul className="mt-2 text-sm">{project.milestones.map((item) => <li key={item.id}>{item.title}</li>)}</ul>
            <form className="mt-4 flex gap-2" onSubmit={(event) => { event.preventDefault(); milestone.post(`/admin/projects/${project.id}/milestones`, { onSuccess: () => milestone.reset() }); }}>
                <input className={inputClass + ' max-w-xs'} aria-label="Milestone title" value={milestone.data.title} onChange={(event) => milestone.setData('title', event.target.value)} />
                <button className={quietButtonClass}>Add milestone</button>
            </form>
            <form className="mt-8 flex gap-2" onSubmit={(event) => { event.preventDefault(); member.post(`/admin/projects/${project.id}/members`); }}>
                <Field label="Add a team member">
                    <select className={inputClass} value={member.data.user_id} onChange={(event) => member.setData('user_id', event.target.value)}>
                        <option value="">Choose</option>
                        {staff.map((person) => <option key={person.id} value={person.id}>{person.name}</option>)}
                    </select>
                </Field>
                <button className={buttonClass}>Add</button>
            </form>
        </AdminLayout>
    );
}
