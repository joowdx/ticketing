<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): MorphMany
    {
        return $this->morphMany(Tag::class, 'taggable');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }
}
