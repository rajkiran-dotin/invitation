<?php

namespace App\Models;

use Database\Factories\InvitationCeremonyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'invitation_id',
    'name',
    'slug',
    'date',
    'start_time',
    'end_time',
    'description',
    'dress_code',
    'image_path',
    'venue_name',
    'formatted_address',
    'google_place_id',
    'latitude',
    'longitude',
    'google_maps_url',
    'sort_order',
])]
class InvitationCeremony extends Model
{
    /** @use HasFactory<InvitationCeremonyFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'sort_order' => 'integer',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
