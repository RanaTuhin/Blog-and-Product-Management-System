<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class Settings extends Page
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::WrenchScrewdriver;

    protected static ?int $navigationSort = 3;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Action::make('connect_gmail')
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
            ]);
    }
}
