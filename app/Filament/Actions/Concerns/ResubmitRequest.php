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

        $this->visible(fn (Request $request) => is_null($request->action) ?: $request->action?->status === ActionStatus::RETRACTED);

        $this->action(function (Request $request) {
            $request->actions()->create(['user_id' => Auth::id(), 'status' => ActionStatus::SUBMITTED]);

            $this->sendSuccessNotification();
        });
    }
}
