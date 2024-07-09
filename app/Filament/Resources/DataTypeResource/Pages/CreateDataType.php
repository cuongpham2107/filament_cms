<?php

namespace App\Filament\Resources\DataTypeResource\Pages;

use App\Filament\Resources\DataTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDataType extends CreateRecord
{
    protected static string $resource = DataTypeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        return static::getModel()::create($data);
    }
}
