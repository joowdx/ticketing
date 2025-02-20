<?php

namespace App\Models\Concerns;

use App\Models\Action;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

trait HasManyAttachmentsThroughActions
{
    public function sanitize(): void
    {
        $this->attachments()->lazyById()->each(fn (Attachment $attachment) => $attachment->sanitize());
    }

    public function purge(): void
    {
        $this->files()->each(fn ($file) => Storage::delete($file));

        $this->attachments()->delete();
    }

    public function files(): LazyCollection
    {
        $directory = 'attachments';

        if (! is_dir(Storage::path('public/'.$directory))) {
            return LazyCollection::make();
        }

        return LazyCollection::make(function () use ($directory) {

            $handle = opendir(Storage::path('public/'.$directory));

            $actions = $this->actions->pluck('id');

            if ($handle) {
                while (($file = readdir($handle)) !== false) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }

                    @[$type, $id] = explode('-', $file);

                    if ($type === 'request' && $id !== $this->id || $type === 'action' && $actions->doesntContain($id)) {
                        continue;
                    }

                    yield "public/$directory/$file";
                }

                closedir($handle);
            }
        });
    }

    public function attachments(): HasManyThrough
    {
        $through = $this->newRelatedThroughInstance(Action::class);

        $firstKey = $this->getForeignKey();

        $secondKey = 'attachable_id';

        return $this->hasManyAttachmentsThroughActions(
            $this->newRelatedInstance(Attachment::class)->newQuery(),
            $this,
            $through,
            $firstKey,
            $secondKey,
            $this->getKeyName(),
            $through->getKeyName(),
        );
    }

    public function hasManyAttachmentsThroughActions(
        Builder $query,
        Model $farParent,
        Model $throughParent,
        $firstKey,
        $secondKey,
        $localKey,
        $secondLocalKey
    ): HasManyThrough {
        return new class($query, $farParent, $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey) extends HasManyThrough
        {
            public function addEagerConstraints(array $models)
            {
                $whereIn = $this->whereInMethod($this->farParent, $this->localKey);

                $keys = $this->getKeys($models, $this->localKey);

                $this->whereInEager(
                    $whereIn,
                    $this->getQualifiedFirstKeyName(),
                    $keys
                );

                $this->query->orWhere(function (Builder $query) use ($keys) {
                    $query->where('attachable_type', $this->farParent->getMorphClass())
                        ->whereIn('attachable_id', $keys);
                });

                $keys = implode(', ', array_map(fn ($id) => "'".$id."'", $keys));

                $this->query->select('attachments.*');

                $this->query->addSelect($this->raw(<<<SQL
                    CASE
                        WHEN attachments.attachable_type = 'App\Models\Request'
                            AND attachments.attachable_id IN ($keys)
                        THEN attachments.attachable_id
                        ELSE actions.request_id
                    END AS laravel_direct_key
                SQL));
            }

            public function addConstraints()
            {
                $localValue = $this->farParent[$this->localKey];

                $this->performJoin();

                if (self::$constraints) {
                    $this->query->where($this->getQualifiedFirstKeyName(), '=', $localValue);

                    $this->query->orWhere(function (Builder $query) {
                        $query->where('attachable_type', $this->farParent->getMorphClass())
                            ->where('attachable_id', $this->farParent->getKey());
                    });
                }
            }

            protected function performJoin(?Builder $query = null)
            {
                $query = $query ?: $this->query;

                $farKey = $this->getQualifiedFarKeyName();

                $query->leftJoin($this->throughParent->getTable(), function ($join) use ($farKey) {
                    $join->on($this->getQualifiedParentKeyName(), '=', $farKey);

                    $join->where('attachable_type', $this->throughParent->getMorphClass());
                });

                if ($this->throughParentSoftDeletes()) {
                    $query->withGlobalScope('SoftDeletableHasManyThrough', function ($query) {
                        $query->whereNull($this->throughParent->getQualifiedDeletedAtColumn());
                    });
                }
            }

            protected function buildDictionary(Collection $results)
            {
                $dictionary = [];

                foreach ($results as $result) {
                    $dictionary[$result->laravel_through_key ?? $result->laravel_direct_key][] = $result;
                }

                return $dictionary;
            }
        };
    }
}
