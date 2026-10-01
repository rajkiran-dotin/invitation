<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'invitation_id',
    'guest_name',
    'attending',
    'functions_attending',
    'message',
])]
class Rsvp extends Model
{
    protected function casts(): array
    {
        return [
            'attending' => 'boolean',
            'functions_attending' => 'array',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
