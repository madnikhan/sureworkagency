import { Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import type { PageProps, Paginator } from '../types';

export function money(pence: number, currency = 'GBP'): string {
    const pounds = Math.trunc(pence / 100);
    const remainder = Math.abs(pence % 100).toString().padStart(2, '0');
    return `${currency} ${pounds.toLocaleString('en-GB')}.${remainder}`;
}

export function Flash() {
    const { flash } = usePage<PageProps>().props;
    if (!flash.success && !flash.error) {
        return null;
    }

    return (
        <p role="status" className="border-b border-hairline bg-charcoal px-4 py-3 text-sm text-paper">
            {flash.success || flash.error}
        </p>
    );
}

export function Empty({ children }: { children: ReactNode }) {
    return <p className="rounded-md border border-dashed border-hairline px-4 py-8 text-sm text-muted">{children}</p>;
}

export function Field({ label, error, children }: { label: string; error?: string; children: ReactNode }) {
    return (
        <label className="block text-sm">
            <span className="mb-1.5 block text-muted">{label}</span>
            {children}
            {error ? (
                <span role="alert" className="mt-1 block text-sm text-red-300">
                    {error}
                </span>
            ) : null}
        </label>
    );
}

export const inputClass = 'w-full rounded-md border border-hairline bg-ink px-3 py-2 text-paper placeholder:text-muted';

export const buttonClass =
    'inline-flex items-center justify-center rounded-full bg-cyan px-5 py-2.5 text-sm font-medium text-ink disabled:opacity-60';

export const quietButtonClass =
    'inline-flex items-center justify-center rounded-md border border-hairline px-3 py-2 text-sm text-paper disabled:opacity-60';

export function rows<T>(value: Paginator<T> | T[] | undefined): T[] {
    if (!value) {
        return [];
    }
    return Array.isArray(value) ? value : value.data;
}

export function Pager({ links }: { links?: Array<{ url: string | null; label: string; active: boolean }> }) {
    if (!links || links.length <= 3) {
        return null;
    }

    return (
        <nav aria-label="Pagination" className="mt-4 flex flex-wrap gap-2 text-sm">
            {links.map((link, index) =>
                link.url ? (
                    <Link key={index} href={link.url} className={link.active ? 'text-cyan' : 'text-muted'} preserveScroll>
                        <span dangerouslySetInnerHTML={{ __html: link.label }} />
                    </Link>
                ) : (
                    <span key={index} className="text-muted" dangerouslySetInnerHTML={{ __html: link.label }} />
                ),
            )}
        </nav>
    );
}
