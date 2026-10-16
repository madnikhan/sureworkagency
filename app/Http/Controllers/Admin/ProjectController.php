<?php

namespace App\Http\Controllers\Admin;

use App\Actions\TransitionRecord;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Project::class);
        $query = Project::query()->with('customer:id,name');
        $user = $request->user();
        if (! $user->hasPermission('projects.manage') && ! $user->hasPermission('projects.view')) {
            $query->whereHas('members', fn ($members) => $members->where('users.id', $user->id));
        }

        return Inertia::render('Admin/Projects', [
            'projects' => $query->latest()->paginate(20),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('projects.manage'), 403);
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'title' => ['required', 'string', 'max:160'],
            'service_category' => ['nullable', 'string', 'max:160'],
            'starts_on' => ['nullable', 'date'],
            'target_on' => ['nullable', 'date'],
            'budget_pence' => ['nullable', 'integer', 'min:0'],
        ]);
        $project = Project::query()->create($data + ['status' => 'planning', 'manager_id' => $request->user()->id]);
        $project->members()->syncWithoutDetaching([$request->user()->id]);
        Activity::record($project, 'project.created', 'Project created.');

        return redirect()->route('admin.projects.show', $project);
    }

    public function show(Project $project): Response
    {
        $this->authorize('view', $project);
        $project->load(['customer', 'tasks', 'milestones', 'members:id,name']);

        return Inertia::render('Admin/Project', [
            'project' => $project,
            'tasks' => $project->tasks->map(fn (ProjectTask $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'due_on' => $task->due_on?->toDateString(),
                'overdue' => $task->isOverdue(),
            ]),
            'allowed' => ProjectStatus::map()[$project->status] ?? [],
            'staff' => User::query()->whereHas('role', fn ($q) => $q->where('key', '!=', 'client'))->get(['id', 'name']),
        ]);
    }

    public function status(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);
        TransitionRecord::run($project, $request->validate(['status' => ['required', 'string']])['status'], ProjectStatus::map(), 'project.status');

        return back()->with('success', 'Project status updated.');
    }

    public function task(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'due_on' => ['nullable', 'date'],
            'parent_id' => ['nullable', 'exists:project_tasks,id'],
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);
        $project->tasks()->create($data + ['status' => 'open']);

        return back()->with('success', 'Task added.');
    }

    public function milestone(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);
        $project->milestones()->create($request->validate([
            'title' => ['required', 'string', 'max:160'],
            'due_on' => ['nullable', 'date'],
        ]));

        return back()->with('success', 'Milestone added.');
    }

    public function member(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);
        $data = $request->validate(['user_id' => ['required', 'exists:users,id']]);
        $project->members()->syncWithoutDetaching([$data['user_id']]);
        Activity::record($project, 'project.member', 'A team member was added.');

        return back()->with('success', 'Member added.');
    }
}
