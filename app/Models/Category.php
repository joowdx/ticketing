<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'office_id',
    ];

    public static function booted()
    {
        static::addGlobalScope('non_trashed_parent', function (Builder $query) {
            $query->whereHas('office');
        });

        static::addGlobalScope('category_order', function (Builder $query) {
            $query->orderByRaw("CASE WHEN categories.name like 'others' THEN 1 ELSE 0 END")
                ->orderBy('name');
        });
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }
}
