<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'taggable_type',
        'taggable_id',
    ];

    public function name(): Attribute
    {
        return Attribute::make(
            fn (string $name) => preg_replace('/\s+/', ' ', mb_strtolower(trim($name))),
            fn (string $name) => preg_replace('/\s+/', ' ', mb_strtolower(trim($name))),
        );
    }

    public function category(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'taggable');
    }

    public function subcategory(): MorphToMany
    {
        return $this->morhpedByMany(Subcategory::class, 'taggable');
    }

    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
