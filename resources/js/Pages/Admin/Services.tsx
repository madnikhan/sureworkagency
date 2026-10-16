import { useForm } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, buttonClass, inputClass } from '../../Components/ui';

type Service = {
    id: number;
    name: string;
    summary: string;
    description: string;
    is_published: boolean;
    sort_order: number;
    delivery_time: string | null;
    seo_title: string | null;
    seo_description: string | null;
};

export default function Services({ services }: { services: Service[] }) {
    const [selected, setSelected] = useState(services[0]?.id ?? 0);
    const current = services.find((service) => service.id === selected) ?? services[0];
    const form = useForm({
        name: current?.name ?? '',
        summary: current?.summary ?? '',
        description: current?.description ?? '',
        is_published: current?.is_published ?? false,
        sort_order: current?.sort_order ?? 0,
        delivery_time: current?.delivery_time ?? '',
        seo_title: current?.seo_title ?? '',
        seo_description: current?.seo_description ?? '',
        faq_question: '',
        faq_answer: '',
    });

    function choose(id: number) {
        const service = services.find((item) => item.id === id);
        setSelected(id);
        if (!service) {
            return;
        }
        form.setData({
            name: service.name,
            summary: service.summary,
            description: service.description,
            is_published: service.is_published,
            sort_order: service.sort_order,
            delivery_time: service.delivery_time ?? '',
            seo_title: service.seo_title ?? '',
            seo_description: service.seo_description ?? '',
            faq_question: '',
            faq_answer: '',
        });
    }

    if (!current) {
        return <AdminLayout title="Services"><p>No services have been seeded.</p></AdminLayout>;
    }

    return (
        <AdminLayout title="Services">
            <label className="block max-w-md text-sm">
                <span className="text-muted">Service</span>
                <select className={inputClass + ' mt-1'} value={current.id} onChange={(event) => choose(Number(event.target.value))}>
                    {services.map((service) => <option key={service.id} value={service.id}>{service.name}</option>)}
                </select>
            </label>
            <form className="mt-6 grid max-w-2xl gap-3" onSubmit={(event) => { event.preventDefault(); form.put(`/admin/services/${current.id}`); }}>
                <Field label="Name"><input className={inputClass} value={form.data.name} onChange={(event) => form.setData('name', event.target.value)} /></Field>
                <Field label="Summary"><textarea className={inputClass} rows={2} value={form.data.summary} onChange={(event) => form.setData('summary', event.target.value)} /></Field>
                <Field label="Description"><textarea className={inputClass} rows={5} value={form.data.description} onChange={(event) => form.setData('description', event.target.value)} /></Field>
                <label className="flex items-center gap-2 text-sm"><input type="checkbox" checked={form.data.is_published} onChange={(event) => form.setData('is_published', event.target.checked)} /> Published on the public site</label>
                <Field label="Sort order"><input type="number" className={inputClass} value={form.data.sort_order} onChange={(event) => form.setData('sort_order', Number(event.target.value))} /></Field>
                <Field label="FAQ question"><input className={inputClass} value={form.data.faq_question} onChange={(event) => form.setData('faq_question', event.target.value)} /></Field>
                <Field label="FAQ answer"><textarea className={inputClass} rows={2} value={form.data.faq_answer} onChange={(event) => form.setData('faq_answer', event.target.value)} /></Field>
                <button className={buttonClass} disabled={form.processing}>Save service</button>
            </form>
        </AdminLayout>
    );
}
