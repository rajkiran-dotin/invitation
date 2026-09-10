<?php

namespace App\Models;

use Database\Factories\TemplateCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'is_active', 'sort_order'])]
class TemplateCategory extends Model
{
    /** @use HasFactory<TemplateCategoryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function templates(): HasMany
    {
        return $this->hasMany(InvitationTemplate::class, 'category_id');
    }
}
