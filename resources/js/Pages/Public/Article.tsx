import PublicLayout from '../../Components/PublicLayout';

export default function Article({ title, excerpt, body }: { title: string; excerpt?: string; body: string }) {
    return (
        <PublicLayout title={title} description={excerpt}>
            <article className="mx-auto max-w-[760px] px-5 py-20">
                <h1 className="text-h1">{title}</h1>
                {excerpt ? <p className="mt-4 text-lg text-muted">{excerpt}</p> : null}
                <div className="mt-8 space-y-4 whitespace-pre-line text-paper/90">{body}</div>
            </article>
        </PublicLayout>
    );
}
