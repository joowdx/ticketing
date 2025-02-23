<?php

namespace App\Filament\Panels\Agent\Actions\Tables;

use App\Enums\ActionStatus;
use App\Enums\RequestClass;
use App\Models\Request;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class StartRequestAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('start-request');

        $this->label('Start');

        $this->icon(ActionStatus::STARTED->getIcon());

        $this->requiresConfirmation();

        $this->modalHeading('Start processing request');

        $this->modalDescription('Start this request to begin processing. Once started, the request will be marked as in progress.');

        $this->visible(fn (Request $request) => $request->class === RequestClass::TICKET && ! $request->action->status->finalized());

        $this->action(function (Request $request) {
            if ($request->action->status === ActionStatus::STARTED) {
                return;
            }

            $request->actions()->create([
                'status' => ActionStatus::STARTED,
                'user_id' => Auth::id(),
            ]);
        });
    }
}
