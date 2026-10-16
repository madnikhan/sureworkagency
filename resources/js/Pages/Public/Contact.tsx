import { Link, usePage } from '@inertiajs/react';
import PublicLayout from '../../Components/PublicLayout';
import type { PageProps } from '../../types';

export default function Contact() {
    const { brand } = usePage<PageProps>().props;

    return (
        <PublicLayout title="Contact" description="Book a consultation or request a quote.">
            <div className="mx-auto max-w-[1120px] px-5 py-20">
                <h1 className="text-h1">Let's talk</h1>
                <p className="mt-4 max-w-2xl text-muted">{brand.remote_coverage}</p>
                <p className="mt-4 text-sm text-muted">On-site: {brand.on_site_areas.join(', ')} and nearby.</p>
                <div className="mt-10 flex flex-wrap gap-3">
                    <Link href="/book" className="rounded-full bg-cyan px-6 py-3 text-sm font-medium text-ink">
                        Book a consultation
                    </Link>
                    <Link href="/quote" className="rounded-full border border-hairline px-6 py-3 text-sm">
                        Request a quote
                    </Link>
                </div>
                <a className="mt-8 block" href={`mailto:${brand.email}`}>
                    {brand.email}
                </a>
            </div>
        </PublicLayout>
    );
}
