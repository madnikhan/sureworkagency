<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\SiteContent;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SiteController extends Controller
{
    public function home(): InertiaResponse
    {
        return Inertia::render('Public/Home', [
            'chapters' => config('site.chapters'),
            'process' => config('site.process'),
            'work' => config('site.work'),
        ]);
    }

    public function services(): InertiaResponse
    {
        return Inertia::render('Public/Services', [
            'categories' => config('services_catalogue.categories'),
            'services' => SiteContent::services()->map(fn ($service) => [
                'slug' => $service->slug,
                'name' => $service->name,
                'summary' => $service->summary,
                'category' => $service instanceof Service ? $service->category?->slug : $service->category,
            ]),
        ]);
    }

    public function service(string $slug): InertiaResponse
    {
        $service = SiteContent::service($slug);
        abort_if($service === null, 404);

        $faqs = method_exists($service, 'faqs') ? $service->faqs()->orderBy('sort_order')->get() : collect($service->faqs ?? []);

        return Inertia::render('Public/Service', [
            'service' => [
                'name' => $service->name,
                'summary' => $service->summary,
                'description' => $service->description,
                'delivery' => $service->delivery,
                'pricing' => $service->pricing,
                'delivery_time' => $service->delivery_time,
                'features' => $service->features,
                'deliverables' => $service->deliverables,
                'exclusions' => $service->exclusions,
                'responsibilities' => $service->responsibilities,
                'seo_title' => $service->seo_title ?? $service->name,
                'seo_description' => $service->seo_description ?? $service->summary,
                'faqs' => $faqs,
            ],
        ]);
    }

    public function industries(): InertiaResponse
    {
        return Inertia::render('Public/Collection', [
            'title' => 'Industries',
            'intro' => 'The same standard of work, described for the kinds of businesses we are set up to help.',
            'items' => collect(config('site.industries'))->map(fn ($item) => [
                'href' => '/industries/'.$item['slug'],
                'title' => $item['name'],
                'body' => $item['body'],
            ]),
        ]);
    }

    public function industry(string $slug): InertiaResponse
    {
        $industry = collect(config('site.industries'))->firstWhere('slug', $slug);
        abort_if($industry === null, 404);

        return Inertia::render('Public/Article', [
            'title' => $industry['name'],
            'excerpt' => $industry['body'],
            'body' => $industry['body']."\n\n".config('brand.remote_coverage'),
        ]);
    }

    public function work(): InertiaResponse
    {
        return Inertia::render('Public/Collection', [
            'title' => 'Selected work',
            'intro' => 'Demonstration projects are labelled as demonstrations. Client work is added only when it is real.',
            'items' => collect(config('site.work'))->map(fn ($item) => [
                'href' => '/work/'.$item['slug'],
                'title' => $item['title'],
                'body' => $item['label'].' — '.$item['problem'],
            ]),
        ]);
    }

    public function workShow(string $slug): InertiaResponse
    {
        $item = collect(config('site.work'))->firstWhere('slug', $slug);
        abort_if($item === null, 404);

        return Inertia::render('Public/Work', ['item' => $item]);
    }

    public function about(): InertiaResponse
    {
        return Inertia::render('Public/Article', [
            'title' => 'About '.config('brand.name'),
            'excerpt' => config('brand.meaning'),
            'body' => config('brand.name')." helps small businesses and growing companies with websites, software, marketing, and technology support. ".config('brand.remote_coverage')."\n\nThe name means work you can rely on. We quote the work we can describe, and we do not invent testimonials, awards, or results.",
        ]);
    }

    public function contact(): InertiaResponse
    {
        return Inertia::render('Public/Contact');
    }

    public function insights(): InertiaResponse
    {
        return Inertia::render('Public/Collection', [
            'title' => 'Insights',
            'intro' => 'Short guides for people deciding what to do next.',
            'items' => collect(SiteContent::articles())->map(fn ($article) => [
                'href' => '/insights/'.$article['slug'],
                'title' => $article['title'],
                'body' => $article['excerpt'],
            ]),
        ]);
    }

    public function insight(string $slug): InertiaResponse
    {
        $article = SiteContent::article($slug);
        abort_if($article === null, 404);

        return Inertia::render('Public/Article', $article);
    }

    public function legal(string $page): InertiaResponse
    {
        $pages = [
            'privacy' => $this->privacy(),
            'terms' => $this->terms(),
            'cookies' => $this->cookies(),
        ];
        abort_unless(isset($pages[$page]), 404);

        return Inertia::render('Public/Article', $pages[$page]);
    }

    public function sitemap(): Response
    {
        $urls = ['/', '/services', '/industries', '/work', '/about', '/contact', '/quote', '/book', '/insights', '/privacy', '/terms', '/cookies'];
        foreach (SiteContent::services() as $service) {
            $urls[] = '/services/'.$service->slug;
        }
        foreach (config('site.industries') as $industry) {
            $urls[] = '/industries/'.$industry['slug'];
        }
        foreach (config('site.work') as $item) {
            $urls[] = '/work/'.$item['slug'];
        }
        foreach (SiteContent::articles() as $article) {
            $urls[] = '/insights/'.$article['slug'];
        }

        $base = rtrim(config('app.url'), '/');
        $xml = collect($urls)->map(fn ($url) => '<url><loc>'.e($base.$url).'</loc></url>')->implode('');

        return response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$xml.'</urlset>', 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    private function privacy(): array
    {
        return [
            'title' => 'Privacy notice',
            'excerpt' => 'A draft for the business to approve. This is not legal advice.',
            'body' => "This notice describes the personal information the site collects so the business can reply to enquiries and deliver agreed work.\n\nWe collect the name, email, telephone number, company, and message you submit. The lawful basis for that processing is the steps you ask us to take before a contract, and the contract itself once work is agreed. Marketing email is sent only if you tick the separate consent box, and you can withdraw that consent.\n\nWe keep enquiry records while they are needed for the conversation, the project, or a legal obligation, and then delete them. You can ask for a copy, a correction, or deletion of your information by emailing ".config('brand.email').".\n\nWe do not sell personal information, and we do not store payment card numbers.",
        ];
    }

    private function terms(): array
    {
        return [
            'title' => 'Terms and conditions',
            'excerpt' => 'A draft for the business to approve before it is used with customers.',
            'body' => "Work starts when a quotation is accepted. The quotation states the scope, the price, what is excluded, and the expected timing.\n\nPrices are quotation-only until the business publishes packages. Tax treatment is shown on the document from the rate configured in the business settings at the time it is issued.\n\nCancellation and refund terms will be added when the business confirms them. Until then, do not treat this page as a finished customer contract.",
        ];
    }

    private function cookies(): array
    {
        return [
            'title' => 'Cookies',
            'excerpt' => 'The site uses the cookies it needs to keep a signed-in session secure.',
            'body' => "Essential cookies keep a staff or client session signed in. Optional analytics cookies are not set. A consent banner will be added only if a non-essential analytics tool is switched on.",
        ];
    }
}
