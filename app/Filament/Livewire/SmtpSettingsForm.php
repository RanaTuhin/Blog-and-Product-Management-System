<?php

declare(strict_types=1);

namespace App\Filament\Livewire;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Auth;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasUser;
use Joaopaulolndev\FilamentEditProfile\Livewire\BaseProfileForm;

class SmtpSettingsForm extends BaseProfileForm
{
    use HasUser;

    protected string $view = 'livewire.filament-edit-profile.smtp-settings-form';

    protected static int $sort = 35;

    public function form(Form $form): Form
    {

        return $form
            ->schema([
                Section::make('SMTP Credentials')
                    ->aside()
                    ->schema([
                        Actions::make([
                            ActionsAction::make('connect_gmail')
                                ->label(function () {
                                    $user = Auth::user();
                                    $smptpCredentials = $user->custom_fields;
                                    if (! $smptpCredentials || $smptpCredentials === []) {
                                        return 'Connect';
                                    }

                                    return 'Connected';
                                })
                                ->action(function () {
                                    $user = Auth::user();
                                    $smtpCredentials = $user->custom_fields;

                                    if (! empty($smtpCredentials)) {
                                        $user->custom_fields = null;
                                        $user->save();

                                        Notification::make()
                                            ->title('Disconnected successfully')
                                            ->success()
                                            ->send();

                                        return;
                                    }

                                    return redirect('/oauth/microsoft/redirect');
                                })
                                ->color(function () {
                                    $user = Auth::user();
                                    $smptpCredentials = $user->custom_fields;
                                    if (! $smptpCredentials || $smptpCredentials === []) {
                                        return Color::Yellow;
                                    }

                                    return Color::Green;
                                })
                                ->icon('heroicon-o-link'),
                        ]),
                    ]),
            ]);
    }
}
