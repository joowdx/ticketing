<?php

namespace App\Filament\Panels\Admin\Clusters\Resources\UserResource\Pages;

use App\Filament\Panels\Admin\Clusters\Resources\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $trashedFilterState = function (): ?bool {
            $trashedFilterState = $this->table->getLivewire()->getTableFilterState(TrashedFilter::class) ?? [];

            if (! array_key_exists('value', $trashedFilterState) || ! is_numeric($trashedFilterState['value'])) {
                return null;
            }

            return $trashedFilterState['value'];
        };

        return [
            'all' => Tab::make('Active Users')
                ->modifyQueryUsing(fn (Builder $query) => $query->verifiedEmail()->approvedAccount()->withoutDeactivated())
                ->icon('gmdi-verified-o')
                ->badge(
                    fn () => User::whereNot('id', Auth::id())
                        ->verifiedEmail()
                        ->approvedAccount()
                        ->withoutDeactivated()
                        ->when($trashedFilterState() === false, fn ($query) => $query->onlyTrashed())
                        ->when($trashedFilterState() === true, fn ($query) => $query->withTrashed())
                        ->when($trashedFilterState() === null, fn ($query) => $query->withoutTrashed())
                        ->count()
                ),
            'approval' => Tab::make('Awaiting Approval')
                ->modifyQueryUsing(fn (Builder $query) => $query->forApproval())
                ->icon('gmdi-hourglass-empty')
                ->badge(
                    fn () => User::forApproval()
                        ->whereNot('id', Auth::id())
                        ->withoutDeactivated()
                        ->when($trashedFilterState() === false, fn ($query) => $query->onlyTrashed())
                        ->when($trashedFilterState() === true, fn ($query) => $query->withTrashed())
                        ->when($trashedFilterState() === null, fn ($query) => $query->withoutTrashed())
                        ->count()
                ),
            'verification' => Tab::make('For Verification')
                ->modifyQueryUsing(fn (Builder $query) => $query->forVerification())
                ->icon('gmdi-mark-email-unread-o')
                ->badge(
                    fn () => User::forVerification()
                        ->whereNot('id', Auth::id())
                        ->withoutDeactivated()
                        ->when($trashedFilterState() === false, fn ($query) => $query->onlyTrashed())
                        ->when($trashedFilterState() === true, fn ($query) => $query->withTrashed())
                        ->when($trashedFilterState() === null, fn ($query) => $query->withoutTrashed())
                        ->count()
                ),
            'deactivated' => Tab::make('Terminated Accounts')
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyDeactivated())
                ->icon('gmdi-gpp-bad-o')
                ->badge(
                    fn () => User::whereNot('id', Auth::id())
                        ->onlyDeactivated()
                        ->when($trashedFilterState() === false, fn ($query) => $query->onlyTrashed())
                        ->when($trashedFilterState() === true, fn ($query) => $query->withTrashed())
                        ->when($trashedFilterState() === null, fn ($query) => $query->withoutTrashed())
                        ->count()
                ),
        ];
    }
}
