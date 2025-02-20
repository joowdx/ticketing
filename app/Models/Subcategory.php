<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
    ];

    public static function booted()
    {
        static::addGlobalScope('non_trashed_parent', function (Builder $query) {
            $query->whereHas('category', function (Builder $query) {
                $query->whereHas('office');
            });
        });

        static::addGlobalScope('subcategory_order', function (Builder $query) {
            $query->orderByRaw("CASE WHEN name like 'others' THEN 1 ELSE 0 END")
                ->orderBy('name');
        });
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }
}
