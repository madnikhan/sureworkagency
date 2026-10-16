import PublicLayout from '../../Components/PublicLayout';

type Item = {
    title: string;
    label: string;
    problem: string;
    solution: string;
    methods: string[];
    outcome: string;
};

export default function Work({ item }: { item: Item }) {
    return (
        <PublicLayout title={item.title} description={item.outcome}>
            <article className="mx-auto max-w-[760px] px-5 py-20">
                <p className="text-sm text-cyan">{item.label}</p>
                <h1 className="text-h1 mt-3">{item.title}</h1>
                <h2 className="text-h3 mt-10">Problem</h2>
                <p className="mt-2 text-muted">{item.problem}</p>
                <h2 className="text-h3 mt-8">Approach</h2>
                <p className="mt-2 text-muted">{item.solution}</p>
                <ul className="mt-4 list-disc pl-5 text-muted">
                    {item.methods.map((method) => (
                        <li key={method}>{method}</li>
                    ))}
                </ul>
                <h2 className="text-h3 mt-8">What this is</h2>
                <p className="mt-2 text-muted">{item.outcome}</p>
            </article>
        </PublicLayout>
    );
}
