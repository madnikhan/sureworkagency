<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\PaymentRecord;
use App\Models\Project;
use App\Models\ProjectTask;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();
        abort_if($user->isClient(), 403);

        $leads = $user->hasPermission('leads.view') || $user->hasPermission('leads.manage');
        $projects = $user->hasPermission('projects.view') || $user->hasPermission('projects.manage') || $user->hasPermission('projects.assigned');
        $appointments = $user->hasPermission('appointments.manage');
        $invoices = $user->hasPermission('invoices.view') || $user->hasPermission('invoices.manage');
        $assignedOnly = $projects && ! $user->hasPermission('projects.manage') && ! $user->hasPermission('projects.view');
        $projectQuery = Project::query()->when($assignedOnly, fn ($query) => $query->whereHas('members', fn ($members) => $members->where('users.id', $user->id)));
        $taskQuery = ProjectTask::query()->when($assignedOnly, fn ($query) => $query->whereHas('project', fn ($project) => $project->whereHas('members', fn ($members) => $members->where('users.id', $user->id))));

        return Inertia::render('Admin/Dashboard', [
            'counts' => [
                'new_enquiries' => $leads ? Lead::query()->where('status', 'new')->count() : null,
                'total_leads' => $leads ? Lead::query()->count() : null,
                'follow_ups' => $leads ? Lead::query()->whereDate('follow_up_on', '<=', today())->whereNotIn('status', ['won', 'lost'])->count() : null,
                'active_projects' => $projects ? (clone $projectQuery)->whereIn('status', ['planning', 'in_progress', 'review'])->count() : null,
                'upcoming_appointments' => $appointments ? Appointment::query()->where('starts_at', '>=', now())->whereNotIn('status', ['cancelled', 'completed'])->count() : null,
                'outstanding_invoices' => $invoices ? Invoice::query()->whereIn('status', ['sent', 'part_paid'])->count() : null,
                'monthly_revenue_pence' => $invoices ? (int) PaymentRecord::query()->whereBetween('paid_on', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount_pence') : null,
                'tasks_due_today' => $projects ? (clone $taskQuery)->whereDate('due_on', today())->where('status', '!=', 'done')->count() : null,
            ],
            'activity' => ActivityLog::query()->latest()->limit(8)->get(['id', 'action', 'summary', 'created_at']),
        ]);
    }
}
