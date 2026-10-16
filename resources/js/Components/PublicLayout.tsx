import { Head, Link, usePage } from '@inertiajs/react';
import { useState, type ReactNode } from 'react';
import type { PageProps } from '../types';
import { Flash } from './ui';

const links = [
    { href: '/work', label: 'Work' },
    { href: '/services', label: 'Services' },
    { href: '/about', label: 'About' },
    { href: '/insights', label: 'Insights' },
];

export default function PublicLayout({ title, description, children }: { title: string; description?: string; children: ReactNode }) {
    const { brand } = usePage<PageProps>().props;
    const [open, setOpen] = useState(false);
    const social = Array.isArray(brand.social) ? brand.social : [];

    return (
        <div className="min-h-screen bg-ink text-paper">
            <Head title={title}>
                {description ? <meta head-key="description" name="description" content={description} /> : null}
            </Head>
            <a className="skip-link" href="#content">
                Skip to content
            </a>
            <header className="sticky top-0 z-40 border-b border-hairline/80 bg-ink/80 backdrop-blur">
                <div className="mx-auto flex h-16 max-w-[1120px] items-center justify-between px-5">
                    <Link href="/" className="text-sm tracking-[0.22em]" aria-label={brand.name}>
                        SUREWORK
                    </Link>
                    <nav aria-label="Primary" className="hidden items-center gap-8 text-sm text-muted md:flex">
                        {links.map((link) => (
                            <Link key={link.href} href={link.href} className="hover:text-paper">
                                {link.label}
                            </Link>
                        ))}
                    </nav>
                    <div className="flex items-center gap-3">
                        <Link href="/contact" className="rounded-full bg-cyan px-4 py-2 text-sm font-medium text-ink">
                            Let's talk
                        </Link>
                        <button
                            type="button"
                            className="rounded-md border border-hairline px-3 py-2 text-sm md:hidden"
                            aria-expanded={open}
                            aria-controls="mobile-nav"
                            onClick={() => setOpen((value) => !value)}
                        >
                            Menu
                        </button>
                    </div>
                </div>
                {open ? (
                    <nav id="mobile-nav" aria-label="Mobile" className="flex flex-col gap-3 border-t border-hairline px-5 py-4 md:hidden">
                        {links.map((link) => (
                            <Link key={link.href} href={link.href} className="text-paper" onClick={() => setOpen(false)}>
                                {link.label}
                            </Link>
                        ))}
                    </nav>
                ) : null}
            </header>
            <Flash />
            <main id="content">{children}</main>
            <footer className="border-t border-hairline">
                <div className="mx-auto grid max-w-[1120px] gap-8 px-5 py-14 md:grid-cols-4">
                    <div>
                        <p className="tracking-[0.22em]">SUREWORK</p>
                        <p className="mt-3 text-sm text-muted">{brand.descriptor}</p>
                        <p className="mt-3 text-sm text-muted">{brand.meaning}</p>
                    </div>
                    <div>
                        <p className="text-sm text-muted">Services</p>
                        <Link href="/services" className="mt-2 block text-sm">
                            All services
                        </Link>
                        <Link href="/industries" className="mt-2 block text-sm">
                            Industries
                        </Link>
                    </div>
                    <div>
                        <p className="text-sm text-muted">Contact</p>
                        <a className="mt-2 block text-sm" href={`mailto:${brand.email}`}>
                            {brand.email}
                        </a>
                        {brand.phone ? <p className="mt-2 text-sm">{brand.phone}</p> : null}
                        {brand.opening_hours ? <p className="mt-2 text-sm text-muted">{brand.opening_hours}</p> : null}
                    </div>
                    <div>
                        <p className="text-sm text-muted">On-site areas</p>
                        <p className="mt-2 text-sm">{brand.on_site_areas.join(', ')}</p>
                        <div className="mt-4 flex gap-4 text-sm">
                            <Link href="/privacy">Privacy</Link>
                            <Link href="/terms">Terms</Link>
                            <Link href="/cookies">Cookies</Link>
                        </div>
                        {social.length > 0 ? (
                            <div className="mt-4 flex gap-4 text-sm">
                                {social.map((item) => (
                                    <a key={item.url} href={item.url}>
                                        {item.label}
                                    </a>
                                ))}
                            </div>
                        ) : null}
                    </div>
                </div>
            </footer>
        </div>
    );
}
