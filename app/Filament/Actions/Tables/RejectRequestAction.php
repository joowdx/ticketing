<?php

namespace App\Filament\Actions\Tables;

use App\Enums\ActionStatus;
use App\Models\Request;
use Exception;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class RejectRequestAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('reject-request');

        $this->label('Reject');

        $this->icon('heroicon-o-x-circle');

        $this->requiresConfirmation();

        $this->modalHeading('Reject request');

        $this->modalDescription('Reject this request to mark it as denied.');

        $this->action(function (Request $request) {
            try {
                $this->beginDatabaseTransaction();

                $request->actions()->create([
                    'status' => ActionStatus::REJECTED,
                    'user_id' => Auth::id(),
                ]);

                $this->commitDatabaseTransaction();
            } catch (Exception $e) {
                $this->rollBackDatabaseTransaction();

                throw $e;
            }

        });
    }
}
