import { Link } from '@inertiajs/react';
import AdminLayout from '../../Components/AdminLayout';
import { Empty } from '../../Components/ui';

type Project = { id: number; title: string; status: string };
type Quote = { id: number; number: string; title: string; status: string };
type Invoice = { id: number; number: string; status: string };
type Ticket = { id: number; number: string; subject: string; status: string };

export default function Home({ projects, quotes, invoices, tickets }: { projects: Project[]; quotes: Quote[]; invoices: Invoice[]; tickets: Ticket[] }) {
    return (
        <AdminLayout title="Your work">
            <section>
                <h2 className="text-sm text-muted">Projects</h2>
                {projects.length === 0 ? <Empty>No projects have been shared yet.</Empty> : <ul className="mt-2 text-sm">{projects.map((project) => <li key={project.id}>{project.title} · {project.status.replaceAll('_', ' ')}</li>)}</ul>}
            </section>
            <section className="mt-8">
                <h2 className="text-sm text-muted">Quotes</h2>
                {quotes.length === 0 ? <Empty>No quotes are waiting.</Empty> : <ul className="mt-2 text-sm">{quotes.map((quote) => <li key={quote.id}><Link className="text-cyan" href={`/admin/quotes/${quote.id}`}>{quote.number}</Link> {quote.title}</li>)}</ul>}
            </section>
            <section className="mt-8">
                <h2 className="text-sm text-muted">Invoices</h2>
                {invoices.length === 0 ? <Empty>No invoices.</Empty> : <ul className="mt-2 text-sm">{invoices.map((invoice) => <li key={invoice.id}><Link className="text-cyan" href={`/admin/invoices/${invoice.id}`}>{invoice.number}</Link> · {invoice.status.replaceAll('_', ' ')}</li>)}</ul>}
            </section>
            <section className="mt-8">
                <h2 className="text-sm text-muted">Support</h2>
                {tickets.length === 0 ? <Empty>No tickets.</Empty> : <ul className="mt-2 text-sm">{tickets.map((ticket) => <li key={ticket.id}><Link className="text-cyan" href={`/admin/tickets/${ticket.id}`}>{ticket.number}</Link> {ticket.subject}</li>)}</ul>}
            </section>
        </AdminLayout>
    );
}
