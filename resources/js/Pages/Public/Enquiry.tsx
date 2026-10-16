import { useForm } from '@inertiajs/react';
import PublicLayout from '../../Components/PublicLayout';
import { Field, buttonClass, inputClass } from '../../Components/ui';

export default function Enquiry({ mode, services }: { mode: 'quote' | 'consultation'; services: string[] }) {
    const form = useForm({
        name: '',
        email: '',
        phone: '',
        company: '',
        service: '',
        budget_band: '',
        description: '',
        preferred_date: '',
        timezone: 'Europe/London',
        marketing_consent: false,
    });
    const title = mode === 'quote' ? 'Request a quote' : 'Book a consultation';

    return (
        <PublicLayout title={title} description="Tell us what you need. We reply by email.">
            <div className="mx-auto max-w-[720px] px-5 py-20">
                <h1 className="text-h1">{title}</h1>
                <p className="mt-4 text-muted">
                    {mode === 'quote'
                        ? 'Describe the work. A quotation follows once the scope is clear. There is no card payment on this form.'
                        : 'Ask for a time to talk. We confirm the appointment by email.'}
                </p>
                <form
                    className="mt-10 space-y-5"
                    onSubmit={(event) => {
                        event.preventDefault();
                        form.post(mode === 'quote' ? '/quote' : '/book');
                    }}
                >
                    <Field label="Name" error={form.errors.name}>
                        <input className={inputClass} value={form.data.name} onChange={(event) => form.setData('name', event.target.value)} required />
                    </Field>
                    <Field label="Email" error={form.errors.email}>
                        <input type="email" className={inputClass} value={form.data.email} onChange={(event) => form.setData('email', event.target.value)} required />
                    </Field>
                    <Field label="Phone" error={form.errors.phone}>
                        <input className={inputClass} value={form.data.phone} onChange={(event) => form.setData('phone', event.target.value)} />
                    </Field>
                    <Field label="Company" error={form.errors.company}>
                        <input className={inputClass} value={form.data.company} onChange={(event) => form.setData('company', event.target.value)} />
                    </Field>
                    <Field label="Service" error={form.errors.service}>
                        <select className={inputClass} value={form.data.service} onChange={(event) => form.setData('service', event.target.value)}>
                            <option value="">Choose if you know</option>
                            {services.map((service) => (
                                <option key={service} value={service}>
                                    {service}
                                </option>
                            ))}
                        </select>
                    </Field>
                    {mode === 'quote' ? (
                        <Field label="Budget band" error={form.errors.budget_band}>
                            <input className={inputClass} value={form.data.budget_band} onChange={(event) => form.setData('budget_band', event.target.value)} />
                        </Field>
                    ) : (
                        <Field label="Preferred date" error={form.errors.preferred_date}>
                            <input type="datetime-local" className={inputClass} value={form.data.preferred_date} onChange={(event) => form.setData('preferred_date', event.target.value)} />
                        </Field>
                    )}
                    <Field label="What do you need?" error={form.errors.description}>
                        <textarea className={inputClass} rows={5} value={form.data.description} onChange={(event) => form.setData('description', event.target.value)} />
                    </Field>
                    <label className="flex items-start gap-3 text-sm text-muted">
                        <input type="checkbox" checked={form.data.marketing_consent} onChange={(event) => form.setData('marketing_consent', event.target.checked)} />
                        <span>You may email me about later offers. This is separate from the reply to this enquiry.</span>
                    </label>
                    <button className={buttonClass} disabled={form.processing}>
                        {form.processing ? 'Sending…' : 'Send'}
                    </button>
                </form>
            </div>
        </PublicLayout>
    );
}
