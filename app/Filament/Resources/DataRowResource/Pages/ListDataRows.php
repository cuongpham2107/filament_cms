<?php

namespace App\Filament\Resources\DataRowResource\Pages;

use App\Filament\Resources\DataRowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataRows extends ListRecords
{
    protected static string $resource = DataRowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
