<?php

namespace App\Filament\Pages;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.settings';

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                TableRepeater::make('users')
                ->headers([
                    Header::make('name')->width('150px'),
                ])
                ->schema([
                    TextInput::make('name')
                        ->placeholder('Name')
                        ->required(),
                ])
                ->columnSpan('full')
            ])
        ]);
    }
}
