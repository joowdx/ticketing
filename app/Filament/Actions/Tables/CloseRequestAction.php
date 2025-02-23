<?php

namespace App\Filament\Actions\Tables;

use App\Enums\ActionStatus;
use App\Models\Request;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class CloseRequestAction extends Action
{
    protected bool $remarksRequired = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name('close-request');

        $this->label('Close');

        $this->icon(ActionStatus::CLOSED->getIcon());

        $this->requiresConfirmation();

        $this->modalHeading('Close request');

        $this->modalDescription('Close this request to mark it as completed.');

        $this->modalWidth(MaxWidth::ExtraLarge);

        $this->form([
            MarkdownEditor::make('remarks')
                ->helperText('Please provide a brief reason for closing this request.')
                ->required(fn () => $this->remarksRequired),
        ]);

        $this->action(function (Request $request, array $data) {
            $request->actions()->create([
                'status' => ActionStatus::CLOSED,
                'user_id' => Auth::id(),
                'remarks' => $data['remarks'],
            ]);
        });

        $this->hidden(fn (Request $request) => $request->action->status->finalized());
    }

    public function requireRemarks(bool $required = true)
    {
        $this->remarksRequired = $required;

        return $this;
    }
}
