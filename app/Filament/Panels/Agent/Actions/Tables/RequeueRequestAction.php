<?php

namespace App\Filament\Panels\Agent\Actions\Tables;

use App\Enums\ActionStatus;
use App\Models\Request;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class RequeueRequestAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('requeue');

        $this->slideOver();

        $this->color(ActionStatus::QUEUED->getColor());

        $this->icon(ActionStatus::QUEUED->getIcon());

        $this->modalIcon(ActionStatus::QUEUED->getIcon());

        $this->modalDescription('Please provide a valid reason for requeueing this request.');

        $this->modalWidth(MaxWidth::ExtraLarge);

        $this->form([
            MarkdownEditor::make('remarks')
                ->label('Reason')
                ->required(),
        ]);

        $this->action(function (Request $request, array $data) {
            if ($request->action->status !== ActionStatus::ASSIGNED) {
                return;
            }

            $request->assignees()->detach(Auth::id());

            $request->actions()->create([
                'remarks' => $data['remarks'],
                'status' => ActionStatus::QUEUED,
                'user_id' => Auth::id(),
            ]);
        });

        $this->closeModalByClickingAway(false);

        $this->visible(fn (Request $request) => $request->declination === true &&
            $request->assignees()->count() === 1 &&
            ActionStatus::canTransitionTo($request->action->status, ActionStatus::QUEUED) &&
            $request->action->status === ActionStatus::ASSIGNED,
        );
    }
}
