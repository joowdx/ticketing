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
    case CLOSED = 'closed';
    case RECLASSIFIED = 'reclassified';
    case RESPONDED = 'responded';

    case IN_PROGRESS = 'in_progress'; // Placeholder only

    public static function allowedTransitions(): array
    {
        return [
            self::SUBMITTED->value => [
                self::RETRACTED,
                self::UPDATED,
                self::QUEUED,
            ],
            self::QUEUED->value => [
                self::ASSIGNED,
                self::REJECTED,
            ],
            self::ASSIGNED->value => [
                self::STARTED,
                self::QUEUED,
                self::REJECTED,
                self::ASSIGNED,
            ],
            self::STARTED->value => [
                self::COMPLETED,
                self::SUSPENDED,
            ],
            self::SUSPENDED->value => [
                self::COMPLIED,
            ],
            self::COMPLIED->value => [
                self::SUSPENDED,
                self::COMPLETED,
            ],
        ];
    }

    public static function canTransitionTo(self $from, self $to): bool
    {
        return in_array($to, self::allowedTransitions()[$from->value] ?? [], true);
    }

    public static function majorActions(): array
    {
        return array_filter(self::cases(), fn ($case) => in_array($case->value, [
            self::SUBMITTED->value,
            self::QUEUED->value,
            self::ASSIGNED->value,
            self::APPROVED->value,
            self::DECLINED->value,
            self::COMPLETED->value,
            self::CANCELLED->value,
            self::STARTED->value,
            self::SUSPENDED->value,
            self::RETRACTED->value,
            self::RESOLVED->value,
            self::DENIED->value,
            self::CLOSED->value,
            self::REJECTED->value,
            self::RESPONDED->value,
            self::STALE->value,
        ], true));
    }

    public static function minorActions(): array
    {
        return array_filter(self::cases(), fn ($case) => ! in_array($case, self::majorActions(), true));
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::RESTORED,
            self::TRASHED => 'gray',
            self::UPDATED => 'info',
            self::APPROVED => 'success',
            self::DECLINED => 'danger',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
            self::STARTED => 'info',
            self::SUSPENDED => 'warning',
            self::SUBMITTED => 'success',
            self::RETRACTED => 'warning',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
            self::ASSIGNED  ,
            self::ADJUSTED ,
            self::QUEUED,
            self::RESOLVED,
            self::SCHEDULED => 'info',
            self::COMPLIED => 'warning',
            self::DENIED,
            self::CLOSED => 'danger',
            default => 'gray'
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::QUEUED => 'The request has been queued and is awaiting processing.',
            self::RESTORED => 'The request has been restored after being trashed.',
            self::TRASHED => 'The request has been trashed.',
            self::UPDATED => 'The request has been updated.',
            self::ACCEPTED => 'The request has been accepted.',
            self::DECLINED => 'The request has been declined.',
            self::COMPLETED => 'The request has been completed.',
            self::CANCELLED => 'The request has been cancelled and will not be processed further.',
            self::STARTED => 'The request has been taken up and is in progress.',
            self::SUSPENDED => 'The request has been suspended and is awaiting further action.',
            self::SUBMITTED => 'The request has been published by the user',
            self::RETRACTED => 'The request has been retracted by the requestor and is waiting to be republished.',
            self::RESOLVED => 'The request has been completed fully and will no longer receive updates',
            self::COMPLIED => 'The user submitted the lacking documents',
            self::DENIED => 'The user has rejected the completion of the request',
            self::APPROVED => 'The request has been accepted and is being processed',
            default => null
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::QUEUED => 'gmdi-start-o',
            self::RESTORED => 'gmdi-restore-o',
            self::TRASHED => 'gmdi-delete-o',
            self::UPDATED => 'gmdi-update-o',
            self::APPROVED => 'gmdi-verified-o',
            self::DECLINED => 'gmdi-block-o',
            self::COMPLETED => 'gmdi-task-alt-o',
            self::RESOLVED => 'gmdi-approval-tt',
            self::CANCELLED => 'gmdi-disabled-by-default-o',
            self::STARTED => 'gmdi-alarm-o',
            self::SUSPENDED => 'gmdi-front-hand-o',
            self::SUBMITTED => 'gmdi-publish-o',
            self::RETRACTED => 'gmdi-settings-backup-restore-o',
            self::ASSIGNED => 'gmdi-supervisor-account-o',
            self::ACCEPTED => 'gmdi-published-with-changes-o',
            self::REJECTED => 'gmdi-person-off-o',
            self::ADJUSTED => 'gmdi-scale-o',
            self::SCHEDULED => 'gmdi-event-o',
            self::COMPLIED => 'gmdi-task-r',
            self::DENIED => 'gmdi-do-not-disturb-on-total-silence',
            self::RECLASSIFIED => 'gmdi-swap-horizontal-circle-o',
            self::CLOSED => 'gmdi-cancel-o',
            self::RESPONDED => 'gmdi-chat-o',
            self::IN_PROGRESS => 'gmdi-sync-o',
            default => 'gmdi-circle-o',
        };
    }

    public function getLabel(?string $type = null, ?bool $capitalize = true): ?string
    {
        if (in_array($this, [self::IN_PROGRESS], true)) {
            return match ($this) {
                self::IN_PROGRESS => 'In Progress',
                default => null,
            };
        }

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
                'responded' => 'response',
                'reclassified' => 'reclassification',
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
        return in_array($this, self::majorActions(), true);
    }

    public function minor()
    {
        return in_array($this, self::minorActions(), true);
    }

    public function finalized()
    {
        return in_array($this, [
            self::COMPLETED,
            self::CANCELLED,
            self::CLOSED,
            self::DENIED,
            self::REJECTED,
        ], true);
    }
}
