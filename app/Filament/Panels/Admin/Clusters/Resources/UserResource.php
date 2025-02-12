<?php

namespace App\Filament\Panels\Admin\Clusters\Resources;

use App\Enums\UserRole;
use App\Filament\Panels\Admin\Actions\Tables\ApproveAccountAction;
use App\Filament\Panels\Admin\Actions\Tables\ApproveAccountBulkAction;
use App\Filament\Panels\Admin\Actions\Tables\DeactivateAccessAction;
use App\Filament\Panels\Admin\Clusters\Organization;
use App\Filament\Panels\Admin\Clusters\Resources\UserResource\Filters\OfficeFilter;
use App\Filament\Panels\Admin\Clusters\Resources\UserResource\Filters\RoleFilter;
use App\Filament\Panels\Admin\Clusters\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?int $navigationSort = -3;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'gmdi-person-pin-o';

    protected static ?string $cluster = Organization::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Information')
                    ->disabled(fn (User $user) => $user->exists && (! $user->hasActiveAccess() || ! $user->hasVerifiedEmail() || $user->trashed()))
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->avatar()
                            ->alignCenter()
                            ->directory('avatars'),
                        Forms\Components\Group::make()
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->unique(ignoreRecord: true)
                                    ->markAsRequired()
                                    ->rule('required')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('designation')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Select::make('office_id')
                            ->relationship('office', 'name')
                            ->columnSpan([
                                'sm' => 1,
                                'md' => 3,
                            ]),
                        Forms\Components\Select::make('role')
                            ->options(UserRole::class)
                            ->prefixIcon('gmdi-shield-o')
                            ->default('user')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label(__('filament-panels::pages/auth/register.form.email.label'))
                            ->maxLength(255)
                            ->rules(['email', 'required'])
                            ->unique(ignoreRecord: true)
                            ->markAsRequired()
                            ->prefixIcon('heroicon-o-at-symbol'),
                        Forms\Components\TextInput::make('number')
                            ->label('Mobile number')
                            ->placeholder('9xx xxx xxxx')
                            ->mask('999 999 9999')
                            ->prefixIcon('heroicon-o-phone')
                            ->rule(fn () => function ($a, $v, $f) {
                                if (! preg_match('/^9.*/', $v)) {
                                    $f('The mobile number field must follow a format of 9xx-xxx-xxxx.');
                                }
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->extraImgAttributes(['loading' => 'lazy']),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('office.code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deactivatedBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deactivated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                OfficeFilter::make(),
                RoleFilter::make(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (HasTable $livewire) => in_array($livewire->activeTab, ['all', 'approval', 'deactivated'])),
                ApproveAccountAction::make()
                    ->label('Approve'),
                Tables\Actions\ActionGroup::make([
                    DeactivateAccessAction::make()
                        ->label(fn (User $user) => $user->deactivated_at ? 'Reactivate' : 'Deactivate')
                        ->visible(fn (HasTable $livewire) => in_array($livewire->activeTab, ['all', 'deactivated'])),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn (HasTable $livewire) => in_array($livewire->activeTab, ['all', 'approval', 'confirmation'])),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ApproveAccountBulkAction::make()
                        ->visible(fn (HasTable $livewire) => in_array($livewire->activeTab, ['approval'])),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->deselectAllRecordsWhenFiltered();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNot('id', Auth::id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
