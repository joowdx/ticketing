<?php

namespace App\Filament\Panels\Moderator\Actions\Tables;

use App\Enums\ActionStatus;
use App\Models\Request;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class QueueRequestAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('queue');

        $this->label('Queue');

        $this->color(ActionStatus::QUEUED->getColor());

        $this->icon(ActionStatus::QUEUED->getIcon());

        $this->visible(fn (Request $request) => $request->action->status === ActionStatus::SUBMITTED);

        $this->action(function (Request $request) {
            if ($request->action->status !== ActionStatus::SUBMITTED) {
                return;
            }

            $request->actions()->create()->update([
                'status' => ActionStatus::QUEUED,
                'user_id' => Auth::id(),
            ]);
        });
    }
}
