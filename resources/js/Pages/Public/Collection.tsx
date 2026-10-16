import { Link } from '@inertiajs/react';
import PublicLayout from '../../Components/PublicLayout';

type Item = { href: string; title: string; body: string };

export default function Collection({ title, intro, items }: { title: string; intro: string; items: Item[] }) {
    return (
        <PublicLayout title={title} description={intro}>
            <div className="mx-auto max-w-[1120px] px-5 py-20">
                <h1 className="text-h1">{title}</h1>
                <p className="mt-4 max-w-2xl text-muted">{intro}</p>
                <ul className="mt-12 divide-y divide-hairline border-y border-hairline">
                    {items.map((item) => (
                        <li key={item.href}>
                            <Link href={item.href} className="block py-6">
                                <span className="text-h3">{item.title}</span>
                                <span className="mt-2 block text-sm text-muted">{item.body}</span>
                            </Link>
                        </li>
                    ))}
                </ul>
            </div>
        </PublicLayout>
    );
}
