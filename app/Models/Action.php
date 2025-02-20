<?php

namespace App\Models;

use App\Enums\ActionStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Action extends Model
{
    use HasUlids;

    protected $fillable = [
        'request_id',
        'user_id',
        'status',
        'remarks',
    ];

    protected $casts = [
        'status' => ActionStatus::class,
    ];

    public function remarks(): Attribute
    {
        return Attribute::make(
            get: function (?string $remarks) {
                if ($this->status !== ActionStatus::ASSIGNED) {
                    return $remarks;
                }

                $pattern = '/\* ([a-z0-9]+)/i';

                preg_match_all($pattern, $remarks, $matches);

                $id = $matches[1] ?? [];

                if (count($id) === 1) {
                    $user = User::find($id[0]);

                    return preg_replace_callback($pattern, fn () => 'To: '.($user->name ?? '*(<u>anonymous</u>)*'), $remarks);
                }

                $users = User::whereIn('id', $id)
                    ->pluck('name', 'id');

                $mapped = preg_replace_callback($pattern, fn ($match) => '* '.($users[$match[1]] ?? '*(<u>anonymous</u>)*'), $remarks);

                return "To:\n{$mapped}";
            },
        )->shouldCache();
    }

    public function attachment(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
