<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentCalendarItem;
use App\Models\GbpTask;
use App\Models\MarketingCampaign;
use App\Models\SeoRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function index(): Response
    {
        abort_unless(auth()->user()->hasPermission('marketing.manage'), 403);

        return Inertia::render('Admin/Marketing', [
            'campaigns' => MarketingCampaign::query()->latest()->get(),
            'seo' => SeoRecord::query()->latest()->get(),
            'calendar' => ContentCalendarItem::query()->orderBy('scheduled_for')->get(),
            'gbp' => GbpTask::query()->latest()->get(),
            'notice' => 'These records are entered by staff. They are not live rankings, impressions, or Google data.',
        ]);
    }

    public function campaign(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('marketing.manage'), 403);
        MarketingCampaign::query()->create($request->validate([
            'name' => ['required', 'string', 'max:160'],
            'channel' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string'],
            'utm_source' => ['nullable', 'string', 'max:80'],
            'utm_medium' => ['nullable', 'string', 'max:80'],
            'utm_campaign' => ['nullable', 'string', 'max:80'],
        ]) + ['data_source' => 'manual']);

        return back()->with('success', 'Campaign recorded.');
    }

    public function seo(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('marketing.manage'), 403);
        SeoRecord::query()->create($request->validate([
            'title' => ['required', 'string', 'max:160'],
            'checklist' => ['nullable', 'string'],
            'keyword_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]) + ['data_source' => 'manual', 'recorded_on' => today()]);

        return back()->with('success', 'SEO note recorded.');
    }

    public function calendar(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('marketing.manage'), 403);
        ContentCalendarItem::query()->create($request->validate([
            'title' => ['required', 'string', 'max:160'],
            'channel' => ['nullable', 'string', 'max:80'],
            'scheduled_for' => ['nullable', 'date'],
            'body' => ['nullable', 'string'],
        ]) + ['data_source' => 'manual']);

        return back()->with('success', 'Calendar item added.');
    }

    public function gbp(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('marketing.manage'), 403);
        GbpTask::query()->create($request->validate([
            'title' => ['required', 'string', 'max:160'],
            'notes' => ['nullable', 'string'],
        ]) + ['data_source' => 'manual']);

        return back()->with('success', 'Profile task added.');
    }

    public function utm(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('marketing.manage'), 403);
        $data = $request->validate([
            'url' => ['required', 'url'],
            'utm_source' => ['required', 'string', 'max:80'],
            'utm_medium' => ['required', 'string', 'max:80'],
            'utm_campaign' => ['required', 'string', 'max:80'],
        ]);
        $link = $data['url'].(str_contains($data['url'], '?') ? '&' : '?').http_build_query([
            'utm_source' => $data['utm_source'],
            'utm_medium' => $data['utm_medium'],
            'utm_campaign' => $data['utm_campaign'],
        ]);

        return back()->with('success', $link);
    }
}
