<?php

namespace App\Filament\Panels\Admin\Clusters\Management\Resources\UserResource\Pages;

use App\Filament\Panels\Admin\Clusters\Management\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        $trashedFilterState = function (): ?bool {
            $trashedFilterState = $this->table->getLivewire()->getTableFilterState(TrashedFilter::class) ?? [];

            if (! array_key_exists('value', $trashedFilterState) || ! is_numeric($trashedFilterState['value'])) {
                return null;
            }

            return $trashedFilterState['value'];
        };

        $query = fn () => User::query()
            ->whereNot('id', Auth::id())
            ->where('office_id', Auth::user()->office_id)
            ->whereNotNull('office_id')
            ->when($trashedFilterState() === false, fn ($query) => $query->onlyTrashed())
            ->when($trashedFilterState() === true, fn ($query) => $query->withTrashed())
            ->when($trashedFilterState() === null, fn ($query) => $query->withoutTrashed());

        return [
            'all' => Tab::make('Active Users')
                ->modifyQueryUsing(fn (Builder $query) => $query->verifiedEmail()->approvedAccount()->withoutDeactivated())
                ->icon('gmdi-verified-o')
                ->badge(fn () => $query()->verifiedEmail()->approvedAccount()->withoutDeactivated()->count()),
            'approval' => Tab::make('Awaiting Approval')
                ->modifyQueryUsing(fn (Builder $query) => $query->forApproval())
                ->icon('gmdi-hourglass-empty')
                ->badge(fn () => $query()->forApproval()->withoutDeactivated()->count()),
            'verification' => Tab::make('For Verification')
                ->modifyQueryUsing(fn (Builder $query) => $query->forVerification())
                ->icon('gmdi-mark-email-unread-o')
                ->badge(fn () => $query()->forVerification()->withoutDeactivated()->count()),
            'deactivated' => Tab::make('Terminated Accounts')
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyDeactivated())
                ->icon('gmdi-gpp-bad-o')
                ->badge(fn () => $query()->onlyDeactivated()->count()),
        ];
    }
}
