<?php

namespace App\Filament\Resources\DataRowResource\Pages;

use App\Filament\Resources\DataRowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataRow extends EditRecord
{
    protected static string $resource = DataRowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
