<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceFaq;
use Illuminate\Database\Seeder;

class ServiceCatalogueSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('services_catalogue.categories') as $index => $category) {
            ServiceCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name'], 'summary' => $category['summary'], 'sort_order' => $index],
            );
        }

        foreach (config('services_catalogue.services') as $index => $service) {
            $category = ServiceCategory::query()->where('slug', $service['category'])->firstOrFail();
            $record = Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                [
                    'service_category_id' => $category->id,
                    'name' => $service['name'],
                    'summary' => $service['summary'],
                    'description' => $service['description'],
                    'delivery' => $service['delivery'],
                    'pricing' => $service['pricing'],
                    'starting_price_pence' => null,
                    'delivery_time' => $service['delivery_time'],
                    'features' => $service['features'],
                    'deliverables' => $service['deliverables'],
                    'exclusions' => $service['exclusions'],
                    'responsibilities' => $service['responsibilities'],
                    'seo_title' => $service['name'].' | '.config('brand.name'),
                    'seo_description' => $service['summary'],
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );

            $record->faqs()->delete();
            foreach ($service['faqs'] as $faqIndex => $faq) {
                ServiceFaq::query()->create([
                    'service_id' => $record->id,
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'sort_order' => $faqIndex,
                ]);
            }
        }
    }
}
