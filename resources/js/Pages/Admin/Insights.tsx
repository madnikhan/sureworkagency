import { useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, buttonClass, inputClass } from '../../Components/ui';

type Post = { id: number; title: string; slug: string; status: string };

export default function Insights({ posts }: { posts: Post[] }) {
    const form = useForm({ title: '', slug: '', excerpt: '', body: '', status: 'draft' });

    return (
        <AdminLayout title="Insights">
            <form className="grid max-w-2xl gap-3" onSubmit={(event) => { event.preventDefault(); form.post('/admin/insights'); }}>
                <Field label="Title"><input className={inputClass} value={form.data.title} onChange={(event) => form.setData('title', event.target.value)} /></Field>
                <Field label="Slug"><input className={inputClass} value={form.data.slug} onChange={(event) => form.setData('slug', event.target.value)} /></Field>
                <Field label="Excerpt"><input className={inputClass} value={form.data.excerpt} onChange={(event) => form.setData('excerpt', event.target.value)} /></Field>
                <Field label="Body"><textarea className={inputClass} rows={8} value={form.data.body} onChange={(event) => form.setData('body', event.target.value)} /></Field>
                <Field label="Status">
                    <select className={inputClass} value={form.data.status} onChange={(event) => form.setData('status', event.target.value)}>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </Field>
                <button className={buttonClass}>Save article</button>
            </form>
            <ul className="mt-6 text-sm">{posts.map((post) => <li key={post.id}>{post.title} · {post.status}</li>)}</ul>
        </AdminLayout>
    );
}
