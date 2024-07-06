<?php

namespace App\Filament\Resources\DataModelResource\Pages;

use App\Database\DatabaseManager;
use App\Database\Schema\SchemaManager;
use App\Database\Schema\Table;
use App\Database\Types\Type;
use App\Filament\Resources\DataModelResource;
use Exception;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


class CreateDataModel extends CreateRecord
{
    protected static string $resource = DataModelResource::class;
    protected function beforeCreate(): void
    {
         
        $data = $this->data;
        $databaseManager = DatabaseManager::getInstance();
        $result = $databaseManager->createTable($data['name'], $data['schema']);
        if($result['status'] == true){
            Notification::make()
            ->title('Table Created')
            ->success()
            ->body($result['message'])
            ->send();
        }
        else{
            Notification::make()
            ->title('Table is not Created')
            ->danger()
            ->body($result['message'])
            ->send();
           $this->halt();
        }
    }
    protected function handleRecordCreation(array $data): Model
    {
        $traitRalationPath =  create_migration_model($data['name']);
        $data['trait_ralation_path'] = $traitRalationPath;
        return static::getModel()::create($data);
    }
}
