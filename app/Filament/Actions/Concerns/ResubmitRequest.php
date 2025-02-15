<?php

namespace App\Filament\Actions\Concerns;

use App\Enums\ActionStatus;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;

trait ResubmitRequest
{
    protected function bootResubmitRequest()
    {
        $this->name('resubmit-request');

        $this->icon('gmdi-publish-o');

        $this->requiresConfirmation();

        $this->modalHeading('Resubmit request');

        $this->modalIcon('gmdi-publish-o');

        $this->successNotificationTitle('Request resubmitted');

        $this->hidden(fn (Request $request): bool => $request->trashed());

        $this->action(function (Request $request): void {
            $request->actions()->create(['user_id' => Auth::id(), 'status' => ActionStatus::SUBMITTED]);

            $this->sendSuccessNotification();
        });

        $this->visible(fn (Request $request): bool => is_null($request->action) ?: in_array($request->action?->status, [
            ActionStatus::RETRACTED,
            ActionStatus::RESTORED,
        ]));
    }
}
