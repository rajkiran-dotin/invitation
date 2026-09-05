<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category', 'theme_class', 'price', 'is_active', 'sort_order'])]
class InvitationTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\InvitationTemplateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }
}
