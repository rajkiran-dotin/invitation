<?php

namespace App\Models;

use Database\Factories\InvitationTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'category', 'category_id', 'description', 'preview_image', 'demo_url', 'features', 'is_premium', 'theme_class', 'view_name', 'price', 'is_active', 'sort_order'])]
class InvitationTemplate extends Model
{
    /** @use HasFactory<InvitationTemplateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_active' => 'boolean',
            'is_premium' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (InvitationTemplate $template): void {
            if (filled($template->slug)) {
                return;
            }

            $baseSlug = Str::slug($template->name) ?: 'template';
            $slug = $baseSlug;
            $suffix = 2;

            while (self::query()->where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$suffix;
                $suffix++;
            }

            $template->slug = $slug;
        });
    }

    public function templateCategory(): BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class, 'category_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
