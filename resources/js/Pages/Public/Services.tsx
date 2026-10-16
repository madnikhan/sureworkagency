import { Link } from '@inertiajs/react';
import PublicLayout from '../../Components/PublicLayout';

type Category = { slug: string; name: string; summary: string };
type Service = { slug: string; name: string; summary: string; category: string | null };

export default function Services({ categories, services }: { categories: Category[]; services: Service[] }) {
    return (
        <PublicLayout title="Services" description="Websites, software, marketing, and technology support. Every price is quoted.">
            <div className="mx-auto max-w-[1120px] px-5 py-20">
                <h1 className="text-h1">Services</h1>
                <p className="mt-4 max-w-2xl text-muted">Pricing is quoted for the agreed scope. Nothing on this site is a published package price.</p>
                <div className="mt-14 space-y-16">
                    {categories.map((category) => (
                        <section key={category.slug}>
                            <h2 className="text-h2">{category.name}</h2>
                            <p className="mt-3 max-w-2xl text-muted">{category.summary}</p>
                            <ul className="mt-6 divide-y divide-hairline border-y border-hairline">
                                {services
                                    .filter((service) => service.category === category.slug)
                                    .map((service) => (
                                        <li key={service.slug}>
                                            <Link href={`/services/${service.slug}`} className="block py-5">
                                                <span className="text-h3">{service.name}</span>
                                                <span className="mt-1 block text-sm text-muted">{service.summary}</span>
                                            </Link>
                                        </li>
                                    ))}
                            </ul>
                        </section>
                    ))}
                </div>
            </div>
        </PublicLayout>
    );
}
