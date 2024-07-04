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
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        dd(array_diff_assoc($data['schema'], $record->schema));//
        if($record->name !== $data['name']){
            update_table_name($record->name, $data['name']);
        }

        if(count($record->schema) !== count($data['schema'])){
            update_table_schema($data['name'], $data['schema']);
        }
        // $record->update($data);
        // return $record;
    }

}
