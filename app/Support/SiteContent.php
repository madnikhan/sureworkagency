<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Service;
use Illuminate\Support\Collection;

class SiteContent
{
    /**
     * @return Collection<int, Service|\stdClass>
     */
    public static function services(): Collection
    {
        if (self::databaseReady()) {
            $published = Service::query()->where('is_published', true)->orderBy('sort_order')->get();
            if ($published->isNotEmpty()) {
                return $published;
            }
        }

        return collect(config('services_catalogue.services'))->values()->map(fn (array $service) => (object) $service);
    }

    public static function service(string $slug): Service|\stdClass|null
    {
        return self::services()->first(function ($service) use ($slug) {
            return $service->slug === $slug;
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function articles(): array
    {
        if (self::databaseReady()) {
            $posts = BlogPost::query()->where('status', 'published')->orderByDesc('published_at')->get();
            if ($posts->isNotEmpty()) {
                return $posts->map(fn (BlogPost $post) => [
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'body' => $post->body,
                ])->all();
            }
        }

        return config('site.articles');
    }

    public static function article(string $slug): ?array
    {
        foreach (self::articles() as $article) {
            if ($article['slug'] === $slug) {
                return $article;
            }
        }

        return null;
    }

    private static function databaseReady(): bool
    {
        try {
            return Service::query()->getConnection()->getSchemaBuilder()->hasTable('services');
        } catch (\Throwable) {
            return false;
        }
    }
}
