<?php

namespace App\Filament\Actions\Concerns;

use App\Enums\ActionStatus;
use App\Models\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

trait DeleteRequest
{
    protected function bootDeleteRequest(): void
    {
        $this->successNotificationTitle('Request trashed');

        $this->action(function (): void {
            try {
                $this->beginDatabaseTransaction();

                $result = $this->process(static function (Request $request) {
                    return $request->delete() && $request->action()->create([
                        'status' => ActionStatus::TRASHED,
                        'user_id' => Auth::id(),
                    ]);
                });

                if (! $result) {
                    $this->failure();

                    return;
                }

                $this->commitDatabaseTransaction();

                $this->success();
            } catch (Exception) {
                $this->rollBackDatabaseTransaction();

                $this->failure();
            }
        });

        $this->hidden(function (Request $request) {
            if ($request->trashed()) {
                return true;
            }

            if (is_null($request->action)) {
                return false;
            }

            return ! in_array($request->action->status, [ActionStatus::RETRACTED, ActionStatus::RESTORED]);
        });
    }
}
