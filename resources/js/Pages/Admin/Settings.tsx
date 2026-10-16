import { useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Field, buttonClass, inputClass } from '../../Components/ui';

type Settings = { company_name: string; email: string; phone: string | null; address: string | null; opening_hours: string | null; currency: string; vat_rate_bps: number };

export default function Settings({ settings, can_edit_billing }: { settings: Settings; can_edit_billing: boolean }) {
    const form = useForm({
        company_name: settings.company_name,
        email: settings.email,
        phone: settings.phone ?? '',
        address: settings.address ?? '',
        opening_hours: settings.opening_hours ?? '',
        currency: settings.currency,
        vat_rate_bps: settings.vat_rate_bps,
    });

    return (
        <AdminLayout title="Settings">
            <form className="grid max-w-xl gap-3" onSubmit={(event) => { event.preventDefault(); form.put('/admin/settings'); }}>
                <Field label="Company name"><input className={inputClass} value={form.data.company_name} onChange={(event) => form.setData('company_name', event.target.value)} /></Field>
                <Field label="Email"><input className={inputClass} value={form.data.email} onChange={(event) => form.setData('email', event.target.value)} /></Field>
                <Field label="Phone"><input className={inputClass} value={form.data.phone} onChange={(event) => form.setData('phone', event.target.value)} /></Field>
                <Field label="Address"><textarea className={inputClass} rows={3} value={form.data.address} onChange={(event) => form.setData('address', event.target.value)} /></Field>
                <Field label="Opening hours"><input className={inputClass} value={form.data.opening_hours} onChange={(event) => form.setData('opening_hours', event.target.value)} /></Field>
                {can_edit_billing ? (
                    <>
                        <Field label="Currency"><input className={inputClass} maxLength={3} value={form.data.currency} onChange={(event) => form.setData('currency', event.target.value)} /></Field>
                        <Field label="VAT basis points (2000 = 20%)"><input type="number" className={inputClass} value={form.data.vat_rate_bps} onChange={(event) => form.setData('vat_rate_bps', Number(event.target.value))} /></Field>
                    </>
                ) : (
                    <p className="text-sm text-muted">Billing settings are reserved for the owner.</p>
                )}
                <button className={buttonClass}>Save settings</button>
            </form>
        </AdminLayout>
    );
}
