<?php

namespace App\Models;

use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'template_id',
    'bride_name',
    'groom_name',
    'bride_photo_path',
    'groom_photo_path',
    'hero_photo_path',
    'bride_father_name',
    'bride_mother_name',
    'groom_father_name',
    'groom_mother_name',
    'wedding_date',
    'wedding_time',
    'title',
    'message',
    'venue_name',
    'formatted_address',
    'google_place_id',
    'latitude',
    'longitude',
    'google_maps_url',
    'settings',
    'slug',
    'views',
    'status',
    'published_at',
])]
class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    public const Draft = 'draft';

    public const Published = 'published';

    public const Unpublished = 'unpublished';

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'wedding_date' => 'date',
            'published_at' => 'datetime',
            'views' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InvitationTemplate::class, 'template_id');
    }

    public function ceremonies(): HasMany
    {
        return $this->hasMany(InvitationCeremony::class)->orderBy('sort_order')->orderBy('date');
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(InvitationGalleryImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function isPublished(): bool
    {
        return $this->status === self::Published;
    }
}
