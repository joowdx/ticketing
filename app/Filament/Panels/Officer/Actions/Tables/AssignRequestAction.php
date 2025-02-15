<?php

namespace App\Filament\Panels\Officer\Actions\Tables;

use App\Enums\ActionStatus;
use App\Models\Request;
use App\Models\User;
use Exception;
use Filament\Forms\Components\CheckboxList;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class AssignRequestAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('assign');

        $this->label(fn (Request $request) => $request->action->status === ActionStatus::ASSIGNED ? 'Reassign' : 'Assign');

        $this->icon(ActionStatus::ASSIGNED->getIcon());

        $this->slideOver();

        $this->modalIcon(ActionStatus::ASSIGNED->getIcon());

        $this->modalHeading(fn (Request $request) => ($request->action->status === ActionStatus::ASSIGNED ? 'Reassign' : 'Assign').' request');

        $this->modalDescription('Please select support users to assign this request to.');

        $this->modalContent(fn (Request $request) => $request->office->users()->support()->doesntExist() ? str('No support users found')->toHtmlString() : null);

        $this->modalWidth(MaxWidth::Large);

        $this->modalSubmitAction(fn (Request $request) => $request->office->users()->support()->exists() ? null : false);

        $this->modalSubmitActionLabel('Assign');

        $this->successNotificationTitle(fn (Request $request) => $request->action->status === ActionStatus::ASSIGNED ? 'Request reassigned' : 'Request assigned');

        $this->fillForm(fn (Request $request) => [
            'assignees' => $request->assignees->pluck('id')->toArray(),
        ]);

        $this->form(fn (Request $request) => $request->office->users()->support()->exists() ? [
            CheckboxList::make('assignees')
                ->required()
                ->searchable()
                ->exists('users', 'id')
                ->options($request->office->users()->support()->pluck('name', 'id')->toArray())
                ->descriptions($request->office->users()->support()->pluck('designation', 'id')->toArray()),
        ] : []);

        $this->action(function (Request $request, array $data) {
            if (
                $request->assignees->pluck('id')->diff($data['assignees'])->isEmpty() &&
                collect($data['assignees'])->diff($request->assignees->pluck('id')->toArray())->isEmpty()
            ) {
                return;
            }

            try {
                $this->beginDatabaseTransaction();

                $request->actions()->create([
                    'status' => ActionStatus::ASSIGNED,
                    'user_id' => Auth::id(),
                    'remarks' => User::select('id')
                        ->find($data['assignees'])
                        ->map(fn (User $user) => ['id' => "* {$user->id}"])
                        ->implode('id', "\n"),
                ]);

                $request->assignees()->sync(
                    collect($data['assignees'])->mapWithKeys(function (string $assigned) use ($request) {
                        $assignee = $request->assignees->first(fn (User $assignee) => $assignee->id === $assigned);

                        return [$assigned => [
                            'assigner_id' => Auth::id(),
                            'response' => $assignee?->pivot->response,
                            'responded_at' => $assignee?->pivot->responded_at,
                            'created_at' => $assignee?->pivot->created_at ?? now(),
                        ]];
                    }),
                );

                $this->success();

                $this->commitDatabaseTransaction();
            } catch (Exception $exception) {
                $this->rollBackDatabaseTransaction();

                $this->failure();

                throw $exception;
            }
        });

        $this->visible(fn (Request $request) => in_array($request->action->status, [ActionStatus::SUBMITTED, ActionStatus::ASSIGNED]));
    }
}
