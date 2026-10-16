import { useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, buttonClass, inputClass } from '../../Components/ui';

type Row = { id: number; name?: string; title?: string; data_source: string };

export default function Marketing({ campaigns, seo, calendar, gbp, notice }: { campaigns: Row[]; seo: Row[]; calendar: Row[]; gbp: Row[]; notice: string }) {
    const campaign = useForm({ name: '', channel: '', notes: '', utm_source: '', utm_medium: '', utm_campaign: '' });
    const seoForm = useForm({ title: '', checklist: '', keyword_notes: '', notes: '' });
    const calendarForm = useForm({ title: '', channel: '', scheduled_for: '', body: '' });
    const gbpForm = useForm({ title: '', notes: '' });
    const utm = useForm({ url: '', utm_source: '', utm_medium: '', utm_campaign: '' });

    return (
        <AdminLayout title="Marketing">
            <p className="rounded border border-hairline bg-charcoal p-3 text-sm">{notice}</p>
            <div className="mt-8 grid gap-8 lg:grid-cols-2">
                <form className="space-y-3" onSubmit={(event) => { event.preventDefault(); campaign.post('/admin/marketing/campaigns'); }}>
                    <h2 className="text-sm text-muted">Campaign</h2>
                    <Field label="Name"><input className={inputClass} value={campaign.data.name} onChange={(event) => campaign.setData('name', event.target.value)} /></Field>
                    <Field label="Channel"><input className={inputClass} value={campaign.data.channel} onChange={(event) => campaign.setData('channel', event.target.value)} /></Field>
                    <button className={buttonClass}>Save campaign</button>
                    <ul className="text-sm">{campaigns.map((item) => <li key={item.id}>{item.name} · {item.data_source}</li>)}</ul>
                </form>
                <form className="space-y-3" onSubmit={(event) => { event.preventDefault(); seoForm.post('/admin/marketing/seo'); }}>
                    <h2 className="text-sm text-muted">SEO note</h2>
                    <Field label="Title"><input className={inputClass} value={seoForm.data.title} onChange={(event) => seoForm.setData('title', event.target.value)} /></Field>
                    <Field label="Checklist"><textarea className={inputClass} rows={3} value={seoForm.data.checklist} onChange={(event) => seoForm.setData('checklist', event.target.value)} /></Field>
                    <Field label="Keywords"><textarea className={inputClass} rows={2} value={seoForm.data.keyword_notes} onChange={(event) => seoForm.setData('keyword_notes', event.target.value)} /></Field>
                    <button className={buttonClass}>Save SEO note</button>
                    <ul className="text-sm">{seo.map((item) => <li key={item.id}>{item.title} · entered by staff</li>)}</ul>
                </form>
                <form className="space-y-3" onSubmit={(event) => { event.preventDefault(); calendarForm.post('/admin/marketing/calendar'); }}>
                    <h2 className="text-sm text-muted">Content calendar</h2>
                    <Field label="Title"><input className={inputClass} value={calendarForm.data.title} onChange={(event) => calendarForm.setData('title', event.target.value)} /></Field>
                    <Field label="Date"><input type="date" className={inputClass} value={calendarForm.data.scheduled_for} onChange={(event) => calendarForm.setData('scheduled_for', event.target.value)} /></Field>
                    <button className={buttonClass}>Add item</button>
                    <ul className="text-sm">{calendar.map((item) => <li key={item.id}>{item.title} · {item.data_source}</li>)}</ul>
                </form>
                <form className="space-y-3" onSubmit={(event) => { event.preventDefault(); gbpForm.post('/admin/marketing/gbp'); }}>
                    <h2 className="text-sm text-muted">Business Profile task</h2>
                    <Field label="Title"><input className={inputClass} value={gbpForm.data.title} onChange={(event) => gbpForm.setData('title', event.target.value)} /></Field>
                    <Field label="Notes"><textarea className={inputClass} rows={2} value={gbpForm.data.notes} onChange={(event) => gbpForm.setData('notes', event.target.value)} /></Field>
                    <button className={buttonClass}>Add task</button>
                    <ul className="text-sm">{gbp.map((item) => <li key={item.id}>{item.title} · {item.data_source}</li>)}</ul>
                </form>
            </div>
            <form className="mt-8 max-w-xl space-y-3" onSubmit={(event) => { event.preventDefault(); utm.post('/admin/marketing/utm'); }}>
                <h2 className="text-sm text-muted">UTM link</h2>
                <Field label="URL"><input className={inputClass} value={utm.data.url} onChange={(event) => utm.setData('url', event.target.value)} /></Field>
                <Field label="Source"><input className={inputClass} value={utm.data.utm_source} onChange={(event) => utm.setData('utm_source', event.target.value)} /></Field>
                <Field label="Medium"><input className={inputClass} value={utm.data.utm_medium} onChange={(event) => utm.setData('utm_medium', event.target.value)} /></Field>
                <Field label="Campaign"><input className={inputClass} value={utm.data.utm_campaign} onChange={(event) => utm.setData('utm_campaign', event.target.value)} /></Field>
                <button className={buttonClass}>Build link</button>
            </form>
        </AdminLayout>
    );
}
