<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ActionStatus: string implements HasColor, HasDescription, HasIcon, HasLabel
{
    case STALE = 'stale';
    case RESTORED = 'restored';
    case TRASHED = 'trashed';
    case UPDATED = 'updated';
    case APPROVED = 'approved';
    case DECLINED = 'declined';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case STARTED = 'started';
    case SUBMITTED = 'submitted';
    case RETRACTED = 'retracted';
    case QUEUED = 'queued';
    case RESOLVED = 'resolved';
    case SUSPENDED = 'suspended';
    case ASSIGNED = 'assigned';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case ADJUSTED = 'adjusted';
    case SCHEDULED = 'scheduled';
    case COMPLIED = 'complied';
    case VERIFIED = 'verified';
    case DENIED = 'denied';

    public static function allowedTransitions(): array
    {
        return [
            static::SUBMITTED->value => [
                static::RETRACTED,
                static::UPDATED,
                static::QUEUED,
            ],
            static::QUEUED->value => [
                static::ASSIGNED,
                static::REJECTED,
            ],
            static::ASSIGNED->value => [
                static::STARTED,
                static::QUEUED,
                static::REJECTED,
                static::ASSIGNED,
            ],
            static::STARTED->value => [
                static::COMPLETED,
                static::SUSPENDED,
            ],
        ];
    }

    public static function canTransitionTo(self $from, self $to): bool
    {
        return in_array($to, static::allowedTransitions()[$from->value] ?? [], true);
    }

    public static function majorActions(): array
    {
        return array_filter(self::cases(), fn($case) => in_array($case->value, [
            self::STALE->value,
            self::QUEUED->value,
            self::ASSIGNED->value,
            self::APPROVED->value,
            self::DECLINED->value,
            self::COMPLETED->value,
            self::CANCELLED->value,
            self::STARTED->value,
            self::SUSPENDED->value,
            self::SUBMITTED->value,
            self::RETRACTED->value,
            self::RESOLVED->value,
            self::DENIED->value,
        ], true));
    }

    public static function minorActions(): array
    {
        return array_filter(self::cases(), fn($case) => !in_array($case, self::majorActions(), true));
    }

    public function getColor(): ?string
    {
        return match ($this) {
            static::RESTORED,
            static::TRASHED => 'gray',
            static::UPDATED => 'info',
            static::APPROVED => 'success',
            static::DECLINED => 'danger',
            static::COMPLETED => 'success',
            static::CANCELLED => 'danger',
            static::STARTED => 'info',
            static::SUSPENDED => 'warning',
            static::SUBMITTED => 'success',
            static::RETRACTED => 'warning',
            static::ACCEPTED => 'success',
            static::REJECTED => 'danger',
            static::ASSIGNED  ,
            static::ADJUSTED ,
            static::QUEUED,
            static::RESOLVED,
            static::SCHEDULED => 'info',
            static::COMPLIED => 'warning',
            static::DENIED => 'danger',
            default => 'gray'
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            static::QUEUED => 'The request has been queued and is awaiting processing.',
            static::RESTORED => 'The request has been restored after being trashed.',
            static::TRASHED => 'The request has been trashed.',
            static::UPDATED => 'The request has been updated.',
            static::ACCEPTED => 'The request has been accepted.',
            static::DECLINED => 'The request has been declined.',
            static::COMPLETED => 'The request has been completed.',
            static::CANCELLED => 'The request has been cancelled and will not be processed further.',
            static::STARTED => 'The request has been taken up and is in progress.',
            static::SUSPENDED => 'The request has been suspended and is awaiting further action.',
            static::SUBMITTED => 'The request has been published by the user',
            static::RETRACTED => 'The request has been retracted by the requestor and is waiting to be republished.',
            static::RESOLVED => 'The request has been completed fully and will no longer receive updates',
            static::COMPLIED => 'The user submitted the lacking documents',
            static::DENIED => 'The user has rejected the completion of the request',
            static::APPROVED => 'The request has been accepted and is being processed',
            default => null
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            static::QUEUED => 'gmdi-start-o',
            static::RESTORED => 'gmdi-restore-o',
            static::TRASHED => 'gmdi-delete-o',
            static::UPDATED => 'gmdi-update-o',
            static::APPROVED => 'gmdi-verified-o',
            static::DECLINED => 'gmdi-block-o',
            static::COMPLETED => 'gmdi-task-alt-o',
            static::RESOLVED => 'gmdi-approval-tt',
            static::CANCELLED => 'gmdi-disabled-by-default-o',
            static::STARTED => 'gmdi-alarm-o',
            static::SUSPENDED => 'gmdi-front-hand-o',
            static::SUBMITTED => 'gmdi-publish-o',
            static::RETRACTED => 'gmdi-settings-backup-restore-o',
            static::ASSIGNED => 'gmdi-supervisor-account-o',
            static::ACCEPTED => 'gmdi-published-with-changes-o',
            static::REJECTED => 'gmdi-person-off-o',
            static::ADJUSTED => 'gmdi-scale-o',
            static::SCHEDULED => 'gmdi-event-o',
            static::COMPLIED => 'gmdi-task-r',
            static::DENIED => 'gmdi-do-not-disturb-on-total-silence',
            default => 'gmdi-circle-o',
        };
    }

    public function getLabel(?string $type = null, ?bool $capitalize = true): ?string
    {
        $label = match ($type) {
            'nounForm' => match ($this->value) {
                'updated' => 'update',
                'approved' => 'approval',
                'declined' => 'declination',
                'completed' => 'completion',
                'cancelled' => 'cancellation',
                'initiated' => 'initiation',
                'suspended' => 'suspension',
                'published' => 'publication',
                'retracted' => 'retraction',
                'assigned' => 'assignment',
                'accepted' => 'acceptance',
                'rejected' => 'rejection',
                'adjusted' => 'adjustment',
                'scheduled' => 'scheduling',
                'extended' => 'extension',
                'verified' => 'verification',
                'denied' => 'denial',
                'ammended' => 'alter',
                'surveyed' => 'surveying',
                default => $this->value,
            },
            'presentTense' => match ($this->value) {
                'approved' => 'approve',
                'cancelled' => 'cancel',
                'declined' => 'decline',
                'scheduled' => 'schedule',
                default => substr($this->value, 0, -2),
            },
            default => $this->value,
        };

        return $capitalize ? ucfirst($label) : $label;
    }

    public function major()
    {
        return in_array($this, static::majorActions(), true);
    }

    public function minor()
    {
        return in_array($this, static::minorActions(), true);
    }
}
