<?php

namespace App\Filament\Resources\DataModelResource\Pages;

use App\Filament\Resources\DataModelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;


class CreateDataModel extends CreateRecord
{
    protected static string $resource = DataModelResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        //create migration and create table database
        $traitRalationPath =  create_migration_model($data['schema'], $data['name']);
        $data['trait_ralation_path'] = $traitRalationPath;
        return static::getModel()::create($data);
    }
}
