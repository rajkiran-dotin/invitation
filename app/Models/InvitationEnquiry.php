<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'event_type', 'event_date', 'plan', 'message', 'status'])]
class InvitationEnquiry extends Model
{
    /** @use HasFactory<\Database\Factories\InvitationEnquiryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }
}
