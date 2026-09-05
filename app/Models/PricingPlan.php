<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'price', 'features', 'is_popular', 'is_active', 'sort_order'])]
class PricingPlan extends Model
{
    /** @use HasFactory<\Database\Factories\PricingPlanFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'price' => 'decimal:2',
        ];
    }
}
