import { useForm } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty, buttonClass } from '../../Components/ui';

type FileRow = { id: number; original_name: string; mime: string; size: number };

export default function Files({ files }: { files: FileRow[] }) {
    const form = useForm<{ file: File | null }>({ file: null });

    return (
        <AdminLayout title="Files">
            <form className="flex items-center gap-3" onSubmit={(event) => { event.preventDefault(); form.post('/admin/files', { forceFormData: true }); }}>
                <input type="file" aria-label="Upload a file" onChange={(event) => form.setData('file', event.target.files?.[0] ?? null)} />
                <button className={buttonClass} disabled={form.processing}>Upload</button>
            </form>
            {form.errors.file ? <p role="alert" className="mt-2 text-sm text-red-300">{form.errors.file}</p> : null}
            {files.length === 0 ? <div className="mt-6"><Empty>No files stored.</Empty></div> : (
                <ul className="mt-6 text-sm">
                    {files.map((file) => <li key={file.id}><a className="text-cyan" href={`/admin/files/${file.id}`}>{file.original_name}</a> · {file.mime}</li>)}
                </ul>
            )}
        </AdminLayout>
    );
}
