<?php

namespace App\Filament\Panels\Auth\Pages;

use App\Enums\UserRole;
use App\Models\Office;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Events\Auth\Registered;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class Registration extends Register
{
    protected ?string $maxWidth = '2xl';

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        Notification::make()
            ->title('Registration successful')
            ->body('Please verify your email address and wait for the administrator to approve your account. It may take a while so please be patient.')
            ->success()
            ->send();

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    public function form(Form $form): Form
    {
        return $this->makeForm()
            ->schema([
                Hidden::make('role')
                    ->default('user'),
                Wizard::make([
                    Step::make('Information')
                        ->icon('heroicon-o-identification')
                        ->schema([
                            $this->getAvatarFormComponent()
                                ->hidden(),
                            $this->getNameFormComponent()
                                ->prefixIcon('heroicon-o-identification'),
                            $this->getNumberFormComponent(),
                            $this->getDesignationFormComponent(),
                            $this->getOfficeFormComponent(),
                        ]),
                    Step::make('Credentials')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent()
                                ->label('New Password')
                                ->prefixIcon('heroicon-o-lock-open'),
                            $this->getPasswordConfirmationFormComponent()
                                ->prefixIcon('heroicon-o-lock-closed'),
                        ]),
                    Step::make('Intent')
                        ->icon('heroicon-o-bolt')
                        ->schema([
                            $this->getRoleFormComponent(),
                            $this->getPurposeFormComponent(),
                        ]),
                ])
                    ->submitAction(new HtmlString(Blade::render('<x-filament::button type="submit">Submit</x-filament::button>')))
                    ->contained(false),
            ])
            ->statePath('data');
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar')
            ->alignCenter()
            ->avatar()
            ->directory('avatars');
    }

    protected function getDesignationFormComponent(): Component
    {
        return TextInput::make('designation')
            ->label('Designation')
            ->prefixIcon('heroicon-o-tag');
    }

    protected function getOfficeFormComponent(): Component
    {
        $offices = Office::pluck('code', 'id');

        return Select::make('office_id')
            ->label('Office')
            ->searchable()
            ->options($offices)
            ->disabled($offices->isEmpty())
            ->placeholder('Select Office')
            ->prefixIcon('heroicon-o-building-office-2')
            ->hint('Skip this if you can\'t find your office and tell us about it in the message.');
    }

    protected function getNumberFormComponent(): Component
    {
        return TextInput::make('number')
            ->label('Mobile number')
            ->placeholder('9xx xxx xxxx')
            ->mask('999 999 9999')
            ->prefixIcon('heroicon-o-phone')
            ->autofocus()
            ->rule(fn () => function ($a, $v, $f) {
                if (! preg_match('/^9.*/', $v)) {
                    $f('The mobile number field must follow a format of 9xx-xxx-xxxx.');
                }
            });
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->rules(['email', 'required'])
            ->unique($this->getUserModel())
            ->markAsRequired()
            ->prefixIcon('heroicon-o-at-symbol');
    }

    protected function getRoleFormComponent(): Component
    {
        $roles = collect(UserRole::cases())
            ->reject(fn (UserRole $role) => $role === UserRole::ROOT)
            ->mapWithKeys(fn (UserRole $role) => [$role->value => $role->getLabel()]);

        return Select::make('role')
            ->options($roles)
            ->default('user')
            ->helperText('Subject for approval of the organization.')
            ->required();
    }

    protected function getPurposeFormComponent(): Component
    {
        return Textarea::make('purpose')
            ->placeholder('Enter your message here.')
            ->rows(6)
            ->rule('required')
            ->markAsRequired()
            ->helperText('Tell us about the purpose of your registration to help us approve your account, and in certain cases, we may ask for additional information to verify your identity.');
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label(__('filament-panels::pages/auth/register.form.actions.register.label'))
            ->disabled()
            ->hidden();
    }
}
