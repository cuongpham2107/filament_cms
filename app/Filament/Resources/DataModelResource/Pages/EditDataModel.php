<?php

namespace App\Filament\Resources\DataModelResource\Pages;

use App\Database\DatabaseManager;
use App\Filament\Resources\DataModelResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Common\CompareArrayCommon;

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
        
        $databaseManager = DatabaseManager::getInstance();

        $compareArray = CompareArrayCommon::compareArrays($record->schema, $data['schema']);
        if($record->name !== $data['name']){
            $result = $databaseManager->renameTable($record->name, $data['name']);
            if($result['status'] === false){
                Notification::make()
                ->title('Table is not Renamed')
                ->danger()
                ->body($result['message'])
                ->send();
            }else{
                Notification::make()
                ->title('Table is Renamed')
                ->success()
                ->body($result['message'])
                ->send();
            }
        }
        if(isset($compareArray['added']) && count($compareArray['added']) > 0)
        {
            foreach($compareArray['added'] as $key => $value){
                $result = $databaseManager->addColumn($record->name, $value);
                if($result['status'] === false){
                    Notification::make()
                    ->title('Column is not Created')
                    ->danger()
                    ->body($result['message'])
                    ->send();
                }else{
                    Notification::make()
                    ->title('Column is Created')
                    ->success()
                    ->body($result['message'])
                    ->send();
                }
            }
        }
        else if(isset($compareArray['removed']) && count($compareArray['removed']) > 0)
        {
            foreach($compareArray['removed'] as $key => $value){
                $result = $databaseManager->deleteColumn($record->name, $value);
                if($result['status'] === false){
                    Notification::make()
                    ->title('Column is not Removed')
                    ->danger()
                    ->body($result['message'])
                    ->send();
                }else{
                    Notification::make()
                    ->title('Column is Removed')
                    ->success()
                    ->body($result['message'])
                    ->send();
                }
            }
        }
        else if(isset($compareArray['updated']) && count($compareArray['updated']) > 0)
        { 
            foreach($compareArray['updated'] as $key => $value){
                $result = $databaseManager->updateColumn($record->name, $value['old'], $value['new']);
                if($result['status'] === false){
                    Notification::make()
                    ->title('Column is not Updated')
                    ->danger()
                    ->body($result['message'])
                    ->send();
                }else{
                    Notification::make()
                    ->title('Column is Updated')
                    ->success()
                    ->body($result['message'])
                    ->send();
                }
            }
        }



        $record->update($data);
        return $record;
    }
}
