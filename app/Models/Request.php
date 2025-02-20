<?php

namespace App\Models;

use App\Enums\ActionStatus;
use App\Enums\RequestClass;
use App\Models\Concerns\HasManyAttachmentsThroughActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use HasManyAttachmentsThroughActions, HasUlids, SoftDeletes;

    protected $fillable = [
        'class',
        'code',
        'subject',
        'body',
        'priority',
        'difficulty',
        'availability',
        'declination',
        'office_id',
        'category_id',
        'subcategory_id',
        'user_id',
    ];

    protected $casts = [
        'class' => RequestClass::class,
        'availability' => 'datetime',
        'declination' => 'boolean',
    ];

    public static function booted(): void
    {
        static::forceDeleting(fn (self $request) => $request->purge());

        static::saving(function (self $request) {
            $request->tags()->sync(
                $request->tags()
                    ->where(function (Builder $query) use ($request) {
                        $query->orWhere(fn ($query) => $query->where('taggable_type', Subcategory::class)->where('taggable_id', $request->subcategory_id));

                        $query->orWhere(fn ($query) => $query->where('taggable_type', Category::class)->where('taggable_id', $request->category_id));
                    })
                    ->pluck('tags.id')
            );
        });
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assignees', 'request_id', 'assigned_id')
            ->using(Assignee::class)
            ->withTimestamps()
            ->withPivot(['response', 'responded_at', 'assigned_id', 'assigner_id']);
    }

    public function action(): HasOne
    {
        return $this->hasOne(Action::class)
            ->ofMany(['id' => 'max'], fn ($query) => $query->whereIn('status', ActionStatus::majorActions()));
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class)
            ->latest();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user')
            ->using(User::class)
            ->withPivot(['response', 'responded_at']);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'labels')
            ->using(Label::class)
            ->orderBy('tags.name');
    }

    public function attachment(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}
