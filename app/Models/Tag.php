<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tag extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'color',
        'office_id',
        'category_id',
        'subcategory_id',
    ];

    public static function booted()
    {
        static::addGlobalScope('non_trashed_parent', function (Builder $query) {
            $query->whereHas('office');
        });
    }

    public function name(): Attribute
    {
        return Attribute::make(
            fn (string $name) => preg_replace('/\s+/', ' ', mb_strtolower(trim($name))),
            fn (string $name) => preg_replace('/\s+/', ' ', mb_strtolower(trim($name))),
        );
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}
