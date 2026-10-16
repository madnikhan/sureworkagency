import { Link, usePage } from '@inertiajs/react';
import PublicLayout from '../../Components/PublicLayout';
import type { PageProps } from '../../types';

type Faq = { question: string; answer: string };
type Service = {
    name: string;
    summary: string;
    description: string;
    delivery: string;
    pricing: string;
    delivery_time: string | null;
    features: string[];
    deliverables: string[];
    exclusions: string[];
    responsibilities: string[];
    seo_title: string;
    seo_description: string;
    faqs: Faq[];
};

function List({ title, items }: { title: string; items: string[] }) {
    if (!items?.length) {
        return null;
    }

    return (
        <section>
            <h2 className="text-h3">{title}</h2>
            <ul className="mt-3 list-disc space-y-2 pl-5 text-muted">
                {items.map((item) => (
                    <li key={item}>{item}</li>
                ))}
            </ul>
        </section>
    );
}

export default function Service({ service }: { service: Service }) {
    const { brand } = usePage<PageProps>().props;
    const delivery = service.delivery === 'both' ? 'Remote across the UK, and on-site in the listed areas.' : service.delivery === 'on_site' ? 'On-site in the listed areas.' : 'Remote across the UK.';

    return (
        <PublicLayout title={service.seo_title} description={service.seo_description}>
            <article className="mx-auto max-w-[1120px] px-5 py-20">
                <p className="text-sm text-cyan">Quote only</p>
                <h1 className="text-h1 mt-3">{service.name}</h1>
                <p className="mt-6 max-w-2xl text-lg text-muted">{service.summary}</p>
                <p className="mt-6 max-w-2xl whitespace-pre-line">{service.description}</p>
                <dl className="mt-10 grid gap-6 border-y border-hairline py-6 md:grid-cols-3">
                    <div>
                        <dt className="text-sm text-muted">Delivery</dt>
                        <dd className="mt-1">{delivery}</dd>
                    </div>
                    <div>
                        <dt className="text-sm text-muted">Pricing</dt>
                        <dd className="mt-1">{service.pricing === 'quote' ? 'Quoted after the scope is agreed' : service.pricing}</dd>
                    </div>
                    <div>
                        <dt className="text-sm text-muted">Timing</dt>
                        <dd className="mt-1">{service.delivery_time || 'Agreed in the proposal'}</dd>
                    </div>
                </dl>
                <p className="mt-6 text-sm text-muted">{brand.remote_coverage}</p>
                <div className="mt-12 grid gap-10 md:grid-cols-2">
                    <List title="Included" items={service.features} />
                    <List title="Deliverables" items={service.deliverables} />
                    <List title="Not included" items={service.exclusions} />
                    <List title="Your part" items={service.responsibilities} />
                </div>
                {service.faqs?.length ? (
                    <section className="mt-14">
                        <h2 className="text-h2">Questions</h2>
                        <dl className="mt-6 space-y-6">
                            {service.faqs.map((faq) => (
                                <div key={faq.question}>
                                    <dt className="text-h3">{faq.question}</dt>
                                    <dd className="mt-2 text-muted">{faq.answer}</dd>
                                </div>
                            ))}
                        </dl>
                    </section>
                ) : null}
                <Link href="/quote" className="mt-12 inline-flex rounded-full bg-cyan px-6 py-3 text-sm font-medium text-ink">
                    Request a quote
                </Link>
            </article>
        </PublicLayout>
    );
}
