<?php

namespace App\Filament\Resources\DataModelResource\Pages;

use App\Database\DatabaseManager;
use App\Filament\Resources\DataModelResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDataModel extends EditRecord
{
    protected static string $resource = DataModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    $databaseManager = DatabaseManager::getInstance();
                    $result = $databaseManager->deleteTable($record->name);
                    if ($result) {
                        Notification::make()
                            ->title('Table deleted successfully')
                            ->success()
                            ->body($result['message'])
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Table deleted failed')
                            ->error()
                            ->body($result['message'])
                            ->send();
                    }
                }),
        ];
    }
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // if($record->name !== $data['name']){
        //     update_table_name($record->name, $data['name']);
        // }
        // dd($this->compareArrays($record->schema, $data['schema']));
        dd($record->schema, $data['schema']);

        if (count($this->compareArrays($record->schema, $data['schema'])) !== 0) {

            // Notification::make()
            // ->title($message)
            // ->success()
            // ->send();
        }
        // $record->update($data);
        // return $record;
    }

    function compareArrays($original, $modified)
    {
        $changedValues = [];
        $addedValues = [];
        $removedValues = [];

        // Check for added or changed values in $modified
        foreach ($modified as $key => $value) {
            if (is_array($value)) {
                if (!isset($original[$key])) {
                    $addedValues[$key] = $value;
                } else {
                    $subChanges = $this->compareArrays($original[$key], $value);
                    if (!empty($subChanges['changed']) || !empty($subChanges['added']) || !empty($subChanges['removed'])) {
                        $changedValues[$key] = $subChanges;
                    }
                }
            } else {
                if (!array_key_exists($key, $original) || $original[$key] !== $value) {
                    $changedValues[$key] = $value;
                }
            }
        }

        // Check for removed values in $original
        foreach ($original as $key => $value) {
            if (!array_key_exists($key, $modified)) {
                $removedValues[$key] = $value;
            }
        }

        // Kết hợp kết quả
        $result = [
            'changed' => $changedValues,
            'added' => $addedValues,
            'removed' => $removedValues,
        ];

        return $result;
    }


}
