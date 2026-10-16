import { useForm, usePage } from '@inertiajs/react';
import { Field, Flash, buttonClass, inputClass, quietButtonClass } from '../../Components/ui';
import type { PageProps } from '../../types';

export default function Login() {
    const { brand } = usePage<PageProps>().props;
    const password = useForm({ email: '', password: '' });
    const magic = useForm({ email: '' });

    return (
        <div className="mx-auto flex min-h-screen max-w-md flex-col justify-center px-5">
            <p className="text-xs tracking-[0.2em] text-muted">SUREWORK</p>
            <h1 className="mt-3 text-2xl">Sign in</h1>
            <p className="mt-2 text-sm text-muted">Staff and client accounts are created by {brand.name}. There is no public signup.</p>
            <Flash />
            <form
                className="mt-8 space-y-4"
                onSubmit={(event) => {
                    event.preventDefault();
                    password.post('/login');
                }}
            >
                <Field label="Email" error={password.errors.email}>
                    <input type="email" className={inputClass} value={password.data.email} onChange={(event) => password.setData('email', event.target.value)} required />
                </Field>
                <Field label="Password" error={password.errors.password}>
                    <input type="password" className={inputClass} value={password.data.password} onChange={(event) => password.setData('password', event.target.value)} required />
                </Field>
                <button className={buttonClass} disabled={password.processing}>
                    {password.processing ? 'Signing in…' : 'Sign in'}
                </button>
            </form>
            <form
                className="mt-10 space-y-4 border-t border-hairline pt-8"
                onSubmit={(event) => {
                    event.preventDefault();
                    magic.post('/login/magic');
                }}
            >
                <Field label="Email a sign-in link" error={magic.errors.email}>
                    <input type="email" className={inputClass} value={magic.data.email} onChange={(event) => magic.setData('email', event.target.value)} required />
                </Field>
                <button className={quietButtonClass} disabled={magic.processing}>
                    {magic.processing ? 'Sending…' : 'Send link'}
                </button>
            </form>
        </div>
    );
}
