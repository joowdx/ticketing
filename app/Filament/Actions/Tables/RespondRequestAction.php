<?php

namespace App\Filament\Actions\Tables;

use App\Enums\ActionStatus;
use App\Enums\RequestClass;
use App\Models\Request;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class RespondRequestAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('respond-request');

        $this->label('Respond');

        $this->icon(ActionStatus::RESPONDED->getIcon());

        $this->modalIcon(ActionStatus::RESPONDED->getIcon());

        $this->modalDescription(fn (Request $request) => str('Respond to user\'s inquiry <span class="font-mono">#'.$request->code.'</span>')->toHtmlString());

        $this->slideOver();

        $this->modalWidth(MaxWidth::ExtraLarge);

        $this->successNotificationTitle(function (Request $request) {
            $pronoun = match(Filament::getCurrentPanel()->getId()) {
                'user' => 'your',
                default => 'the',
            };

            return 'You have responded to '.$pronoun.' inquiry <span class="font-mono">#'.$request->code.'</span>';
        });

        $this->form(fn (Request $request) => [
            MarkdownEditor::make('response')
                ->label('Message')
                ->required(),
            Placeholder::make('responses')
                ->hidden($request->actions()->where('status', ActionStatus::RESPONDED)->doesntExist())
                ->content(view('filament.requests.history', [
                    'request' => $request,
                    'chat' => true,
                ])),
            Placeholder::make('inquiry')
                ->content(view('filament.requests.action', [
                    'content' => $request->body,
                    'chat' => true,
                ])),
        ]);

        $this->action(function (Request $request, array $data) {
            try {
                $this->beginDatabaseTransaction();

                $request->actions()->create([
                    'remarks' => $data['response'],
                    'status' => ActionStatus::RESPONDED,
                    'user_id' => Auth::id(),
                ]);

                $this->success();

                $this->commitDatabaseTransaction();
            } catch (Exception $e) {
                $this->rollBackDatabaseTransaction();

                throw $e;
            }
        });

        $this->visible(function (Request $request) {
            $valid = $request->class === RequestClass::INQUIRY && ! $request->action->status->finalized();

            return $valid && match (Filament::getCurrentPanel()->getId()) {
                'user' => $request->action->status === ActionStatus::RESPONDED,
                'moderator', 'agent' => in_array($request->action->status, [ActionStatus::RESPONDED, ActionStatus::ASSIGNED]) &&
                    $request->assignees->contains(Auth::user()),
                default => false,
            };
        });
    }
}
