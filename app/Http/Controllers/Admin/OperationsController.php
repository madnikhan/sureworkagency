<?php

namespace App\Http\Controllers\Admin;

use App\Actions\StoreUploadedFile;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BlogPost;
use App\Models\BusinessSetting;
use App\Models\Customer;
use App\Models\FileAsset;
use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceFaq;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OperationsController extends Controller
{
    public function services(): Response
    {
        abort_unless(auth()->user()->hasPermission('services.manage'), 403);

        return Inertia::render('Admin/Services', [
            'services' => Service::query()->with('category')->orderBy('sort_order')->get(),
        ]);
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('services.manage'), 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string'],
            'description' => ['required', 'string'],
            'is_published' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'delivery_time' => ['nullable', 'string', 'max:160'],
            'seo_title' => ['nullable', 'string', 'max:180'],
            'seo_description' => ['nullable', 'string', 'max:300'],
            'faq_question' => ['nullable', 'string', 'max:180'],
            'faq_answer' => ['nullable', 'string'],
        ]);
        $service->update(collect($data)->except(['faq_question', 'faq_answer'])->all());
        if (($data['faq_question'] ?? null) && ($data['faq_answer'] ?? null)) {
            ServiceFaq::query()->create([
                'service_id' => $service->id,
                'question' => $data['faq_question'],
                'answer' => $data['faq_answer'],
            ]);
        }
        Activity::record($service, 'service.updated', 'Service catalogue updated.');

        return back()->with('success', 'Service saved.');
    }

    public function files(): Response
    {
        abort_unless(auth()->user()->hasPermission('files.manage'), 403);

        return Inertia::render('Admin/Files', [
            'files' => FileAsset::query()->latest()->get(['id', 'original_name', 'mime', 'size', 'created_at']),
        ]);
    }

    public function upload(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('files.manage'), 403);
        $request->validate(['file' => ['required', 'file', 'max:10240']]);
        StoreUploadedFile::run($request->file('file'), null, null, ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'txt', 'docx']);

        return back()->with('success', 'File stored.');
    }

    public function download(FileAsset $file): StreamedResponse
    {
        abort_unless(auth()->user()->hasPermission('files.manage'), 403);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return Storage::disk($file->disk)->download($file->path, $file->original_name);
    }

    public function insights(): Response
    {
        abort_unless(auth()->user()->hasPermission('insights.publish'), 403);

        return Inertia::render('Admin/Insights', [
            'posts' => BlogPost::query()->latest()->get(),
        ]);
    }

    public function saveInsight(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('insights.publish'), 403);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'body' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
        ]);
        BlogPost::query()->updateOrCreate(['slug' => $data['slug']], $data + [
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        return back()->with('success', 'Article saved.');
    }

    public function users(): Response
    {
        abort_unless(auth()->user()->hasPermission('users.manage'), 403);

        return Inertia::render('Admin/Users', [
            'users' => User::query()->with('role:id,key,name')->get(),
            'roles' => Role::query()->where('key', '!=', 'owner')->orWhere('key', 'owner')->get(['id', 'key', 'name']),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('users.manage'), 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12'],
            'role_id' => ['required', 'exists:roles,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
        ]);
        $role = Role::query()->findOrFail($data['role_id']);
        abort_if($role->key === 'owner' && $request->user()->role?->key !== 'owner', 403);
        abort_if($role->key === 'client' && empty($data['customer_id']), 422);
        User::query()->create($data);
        Activity::record($request->user(), 'user.created', 'A user account was created.');

        return back()->with('success', 'User created.');
    }

    public function settings(): Response
    {
        abort_unless(auth()->user()->hasPermission('settings.manage') || auth()->user()->hasPermission('settings.billing'), 403);

        return Inertia::render('Admin/Settings', [
            'settings' => BusinessSetting::current(),
            'can_edit_billing' => auth()->user()->hasPermission('settings.billing'),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('settings.manage') || $request->user()->hasPermission('settings.billing'), 403);
        $setting = BusinessSetting::current();
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string'],
            'opening_hours' => ['nullable', 'string', 'max:160'],
        ]);
        if ($request->user()->hasPermission('settings.billing')) {
            $data = array_merge($data, $request->validate([
                'currency' => ['required', 'string', 'size:3'],
                'vat_rate_bps' => ['required', 'integer', 'min:0', 'max:10000'],
            ]));
        }
        $setting->update($data);
        Activity::record($setting, 'settings.updated', 'Business settings updated.');

        return back()->with('success', 'Settings saved.');
    }

    public function audit(): Response
    {
        abort_unless(auth()->user()->hasPermission('audit.view') || auth()->user()->role?->key === 'owner', 403);

        return Inertia::render('Admin/Audit', [
            'entries' => ActivityLog::query()->with('user:id,name')->latest()->paginate(40),
        ]);
    }
}
