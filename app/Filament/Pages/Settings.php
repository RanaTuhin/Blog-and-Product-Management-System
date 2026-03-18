<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

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
                Action::make('site_name')
                    ->label('Site Name')
                    ->action(function (array $data) {

                        // TODO
                        Notification::make()
                            ->title('Settings saved')
                            ->body('The site name has been updated to:')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
