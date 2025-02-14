<?php

namespace App\Filament\Actions\Concerns;

use App\Enums\ActionStatus;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;

trait RetractRequest
{
    protected function bootRetractRequest()
    {
        $this->name('retract-request');

        $this->icon(ActionStatus::RETRACTED->getIcon());

        $this->requiresConfirmation();

        $this->modalHeading('Retract request');

        $this->modalIcon(ActionStatus::RETRACTED->getIcon());

        $this->successNotificationTitle('Request retracted');

        $this->visible(fn (Request $request) => $request->action?->status === ActionStatus::SUBMITTED);

        $this->action(function (Request $request) {
            $request->actions()->create(['user_id' => Auth::id(), 'status' => ActionStatus::RETRACTED]);

            $this->sendSuccessNotification();
        });
    }
}
