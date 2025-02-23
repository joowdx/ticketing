<?php

namespace App\Filament\Filters;

use App\Models\Office;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class OfficeFilter extends Filter
{
    protected Closure|string|null $placeholder = null;

    protected bool $withUnaffiliated = true;

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
            $offices = Office::query()->pluck('code', 'id')->toArray();

            return [
                Select::make('office')
                    ->label($this->evaluate($this->label))
                    ->options($this->withUnaffiliated ? [-1 => 'Unaffiliated', ...$offices] : $offices)
                    ->placeholder($this->placeholder ? $this->evaluate($this->placeholder) : ($this->withUnaffiliated ? 'With unaffiliated office' : 'Select an office'))
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

                    $query->when($office, fn ($query, $office) => $query->where('office_id', (int) $office !== -1 ? $office : null));

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

    public function placeholder(string|Closure $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function withUnaffiliated(bool $withUnaffiliated = true): static
    {
        $this->withUnaffiliated = $withUnaffiliated;

        return $this;
    }
}
