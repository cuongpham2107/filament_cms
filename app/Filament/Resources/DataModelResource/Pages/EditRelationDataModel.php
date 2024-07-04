<?php

namespace App\Filament\Resources\DataModelResource\Pages;

use App\Filament\Resources\DataModelResource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditRelationDataModel extends EditRecord
{
    protected static string $resource = DataModelResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Name')
                ->required(),
        ]);
    }
}
