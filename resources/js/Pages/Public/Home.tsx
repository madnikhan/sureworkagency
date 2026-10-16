import { Link, usePage } from '@inertiajs/react';
import { motion, useReducedMotion } from 'motion/react';
import type { ReactNode } from 'react';
import PublicLayout from '../../Components/PublicLayout';
import type { PageProps } from '../../types';

type Chapter = { eyebrow: string; title: string; body: string; href: string };
type Step = { title: string; body: string };
type Work = { slug: string; title: string; label: string; problem: string };

function Reveal({ children }: { children: ReactNode }) {
    const reduce = useReducedMotion();

    return (
        <motion.div
            initial={reduce ? false : { opacity: 0, y: 18 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.35 }}
            transition={{ duration: 0.45 }}
        >
            {children}
        </motion.div>
    );
}

export default function Home({ chapters, process, work }: { chapters: Chapter[]; process: Step[]; work: Work[] }) {
    const { brand } = usePage<PageProps>().props;

    return (
        <PublicLayout title={brand.name} description={brand.descriptor}>
            <section className="relative flex min-h-[100svh] items-end overflow-hidden">
                <img src="/images/hero.svg" alt="" className="hero-drift absolute inset-0 h-full w-full object-cover" />
                <div className="absolute inset-0 bg-gradient-to-t from-ink via-ink/55 to-ink/20" />
                <div className="relative mx-auto w-full max-w-[1120px] px-5 pb-20 pt-32">
                    <p className="text-sm tracking-[0.18em] text-cyan">{brand.hero_kicker}</p>
                    <h1 className="text-display mt-4 max-w-5xl">{brand.hero_alternative}</h1>
                    <p className="mt-6 max-w-xl text-lg text-muted">{brand.descriptor}</p>
                    <Link href="/book" className="mt-8 inline-flex rounded-full bg-cyan px-6 py-3 text-sm font-medium text-ink">
                        Book a consultation
                    </Link>
                </div>
            </section>

            <section className="border-t border-hairline">
                <div className="mx-auto max-w-[1120px] px-5 py-24">
                    <Reveal>
                        <p className="text-sm text-muted">Proof</p>
                        <h2 className="text-h2 mt-3">What we can show today</h2>
                    </Reveal>
                    {work.length === 0 ? (
                        <p className="mt-8 text-muted">Client work will be published when it is real. There is nothing to invent in the meantime.</p>
                    ) : (
                        <div className="mt-10 grid gap-6 md:grid-cols-2">
                            {work.map((item) => (
                                <Link key={item.slug} href={`/work/${item.slug}`} className="rounded-lg border border-hairline bg-charcoal p-6">
                                    <p className="text-xs tracking-wide text-cyan">{item.label}</p>
                                    <h3 className="text-h3 mt-3">{item.title}</h3>
                                    <p className="mt-3 text-sm text-muted">{item.problem}</p>
                                </Link>
                            ))}
                        </div>
                    )}
                </div>
            </section>

            {chapters.map((chapter) => (
                <section key={chapter.eyebrow} className="flex min-h-[80svh] items-center border-t border-hairline">
                    <div className="mx-auto w-full max-w-[1120px] px-5 py-20">
                        <Reveal>
                            <p className="text-sm text-cyan">{chapter.eyebrow}</p>
                            <h2 className="text-h1 mt-4 max-w-3xl">{chapter.title}</h2>
                            <p className="mt-6 max-w-2xl text-lg text-muted">{chapter.body}</p>
                            <Link href={chapter.href} className="mt-8 inline-block text-sm text-cyan">
                                See the service
                            </Link>
                        </Reveal>
                    </div>
                </section>
            ))}

            <section className="border-t border-hairline">
                <div className="mx-auto max-w-[1120px] px-5 py-24">
                    <h2 className="text-h2">How the work proceeds</h2>
                    <ol className="mt-10 grid gap-8 md:grid-cols-5">
                        {process.map((step, index) => (
                            <li key={step.title}>
                                <p className="text-sm text-cyan">0{index + 1}</p>
                                <h3 className="text-h3 mt-2">{step.title}</h3>
                                <p className="mt-2 text-sm text-muted">{step.body}</p>
                            </li>
                        ))}
                    </ol>
                </div>
            </section>

            <section className="border-t border-hairline">
                <div className="mx-auto max-w-[1120px] px-5 py-24">
                    <h2 className="text-h2">About {brand.name}</h2>
                    <p className="mt-6 max-w-2xl text-lg text-muted">{brand.meaning}</p>
                    <p className="mt-4 max-w-2xl text-muted">{brand.remote_coverage}</p>
                    <Link href="/about" className="mt-6 inline-block text-sm text-cyan">
                        Read more
                    </Link>
                </div>
            </section>

            <section className="border-t border-hairline">
                <div className="mx-auto flex min-h-[70svh] max-w-[1120px] flex-col justify-end px-5 py-24">
                    <h2 className="text-h1 max-w-3xl">Tell us what you want to change.</h2>
                    <div className="mt-8 flex flex-wrap gap-3">
                        <Link href="/quote" className="rounded-full bg-cyan px-6 py-3 text-sm font-medium text-ink">
                            Request a quote
                        </Link>
                        <Link href="/book" className="rounded-full border border-hairline px-6 py-3 text-sm">
                            Book a consultation
                        </Link>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
