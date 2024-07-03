<?php

namespace App\Filament\Resources\DataModelResource\Pages;

use App\Filament\Resources\DataModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataModel extends EditRecord
{
    protected static string $resource = DataModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
