<?php

namespace App\Filament\Filters;

use App\Models\Office;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class OfficeFilter extends Filter
{
    protected string $model = User::class;

    public static function make(?string $name = null): static
    {
        $filterClass = static::class;

        $name ??= 'office-filter';

        $static = app($filterClass, ['name' => $name]);

        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->form(function () {
            $user = $this->model === User::class;

            $offices = Office::query()->pluck('code', 'id')->toArray();

            return [
                Select::make('office')
                    ->label($user ? 'Affiliated office' : 'Office')
                    ->options($user ? [-1 => 'Unaffiliated', ...$offices] : $offices)
                    ->placeholder($user ? 'With unaffiliated office' : 'Select an office')
                    ->searchable(),
            ];
        });

        $this->query(function (Builder $query, array $data) {
            switch (is_array($data['office'])) {
                case true:
                    $offices = collect($data['office'])->map(fn ($office) => (int) $office !== -1 ? $office : null)->toArray();

                    $query->when($offices, fn ($query, $offices) => $query->whereIn('office_id', $offices));

                    break;

                case false:
                    $office = $data['office'];

                    $query->when($office, fn ($query, $office) => $query->where('office_id', $office !== -1 ? $office : null));

                    break;
            }
        });

        $this->indicateUsing(function (array $data) {
            if (empty($data['office'])) {
                return;
            }

            $offices = is_array($data['office']) ? $data['office'] : [$data['office']];

            $offices = Office::select('code')
                ->orderBy('code')
                ->find($offices)
                ->pluck('code')
                ->when(in_array(-1, $offices), fn ($offices) => $offices->push('Unaffiliated'));

            return 'Office: '.$offices->join(', ', ', & ');
        });
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }
}
