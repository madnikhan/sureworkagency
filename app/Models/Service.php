<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'service_category_id', 'slug', 'name', 'summary', 'description', 'delivery', 'pricing',
        'starting_price_pence', 'currency', 'delivery_time', 'features', 'deliverables', 'exclusions',
        'responsibilities', 'seo_title', 'seo_description', 'is_published', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'deliverables' => 'array',
            'exclusions' => 'array',
            'responsibilities' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ServiceFaq::class);
    }
}
