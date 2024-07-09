<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataTypeResource\Pages;
use App\Filament\Resources\DataTypeResource\RelationManagers;
use App\Models\DataType;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataTypeResource extends Resource
{
    protected static ?string $model = DataType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Migration Table')
                    ->description('Create a new migration file for the model. The migration file will be created in the database/migrations directory.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Table Name')
                            ->live()
                            ->required()
                            ->columnSpan(1)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $set('slug', Str::slug($state));
                                $set('display_name', Str::title(Str::singular($state)));
                                $set('model_name', 'App\Models\\' . Str::studly(Str::singular($state)));
                                $set('trait_name', 'App\Models\Trait\\' . Str::studly(Str::singular($state)) . 'Trait');
                            }),
                            Hidden::make('slug'),
                            Hidden::make('display_name'),
                            Hidden::make('model_name'),
                            Hidden::make('trait_name'),
                        TableRepeater::make('rows')
                            ->relationship('rows')
                            ->orderColumn('order')
                            ->label('Table Columns')
                            ->addActionLabel('Add New Column')
                            ->cloneable()
                            ->default([
                                [
                                    'field' => 'id',
                                    'type' => 'integer',
                                    'length' => 11,
                                    'notnull' => true,
                                    'unsigned' => true,
                                    'autoincrement' => true,
                                    'index' => 'primary',
                                    'default' => null,
                                    'required' => true,
                                    'display_name' => 'ID',
                                    'show' => ['index','view','edit','add','delete'],
                                ],
                            ])
                            ->headers([
                                Header::make('field')
                                    ->label('Field Name')
                                    ->width('150px'),
                                Header::make('type')
                                    ->width('150px'),
                                Header::make('length')
                                    ->width('100px'),
                                Header::make('is_nullable')
                                    ->label('Not Null')
                                    ->width('70px'),
                                Header::make('is_unsigned')
                                    ->label('Unsigned')
                                    ->width('50px'),
                                Header::make('is_auto_increment')
                                    ->label('Auto Increment')
                                    ->width('70px'),
                                Header::make('index')
                                    ->width('100px'),
                                Header::make('default')
                                    ->width('150px'),
                            ])
                            ->schema([
                                TextInput::make('field')
                                ->placeholder('Field Name')
                                ->live()
                                ->required()
                                ->afterStateUpdated(function (Set $set, ?string $state) {
                                    $set('display_name', Str::title($state));
                                }),
                                Select::make('type')
                                    ->placeholder('Choose Type')
                                    ->live()
                                    ->searchable()
                                    ->options(
                                        config('custom.type_options')
                                    )
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        if ($state == 'date' || $state == 'datetime' || $state == 'time' || $state == 'timestamp' || $state == 'year') {
                                            $set('length', null);
                                        }
                                        if ($state == 'integer' || $state == 'mediumint' || $state == 'bigint' || $state == 'smallint' || $state == 'tinyint') {
                                            $set('length', 11);
                                        }
                                        if ($state == 'float' || $state == 'double' || $state == 'decimal') {
                                            $set('length', 10,2);
                                        }
                                        if ($state == 'char' || $state == 'string' || $state == 'binary' || $state == 'varbinary') {
                                            $set('length', 255);
                                        }

                                    })
                                    ->required(),
                                TextInput::make('length')
                                    ->placeholder('Length')
                                    ->nullable()
                                    ->numeric(),
                                Checkbox::make('notnull')
                                    ->live()
                                    ->default(false)
                                    ->afterStateUpdated(function (Set $set, ?bool $state) {
                                        $set('required', $state);
                                    }),
                                Checkbox::make('unsigned')
                                    ->default(false),
                                Checkbox::make('autoincrement')
                                    ->default(false),
                                Select::make('index')
                                    ->placeholder('Choose')
                                    ->options(config('custom.index_options'))
                                    ->default('none'),
                                TextInput::make('default')
                                    ->default(null),
                                Hidden::make('required')
                                    ->default(false),
                                Hidden::make('display_name')
                                    ->default(null),
                                Hidden::make('show')
                                    ->default(['index','view','edit','add','delete']),
                            ])
                            ->extraActions([
                                Action::make('addTimestamps')
                                    ->icon('heroicon-m-clock')
                                    ->action(function (TableRepeater $component): void {
                                        $state = $component->getState();
                                        $field = [
                                            'created_at',
                                            'updated_at',
                                        ];
                                        foreach ($field as $item) {
                                            $state[Str::uuid()->toString()] = [
                                                'field' => $item,
                                                'type' => 'timestamp',
                                                'length' => null,
                                                'notnull' => false,
                                                'unsigned' => false,
                                                'autoincrement' => false,
                                                'index' => 'none',
                                                'default' => null,
                                                'required' => false,
                                                'display_name' => Str::title($item),
                                                'show' => ['index','view','edit','add','delete'],
                                            ];
                                        }

                                        $component->state($state);
                                    }),
                                Action::make('addSoftDeletes')
                                    ->icon('heroicon-m-x-mark')
                                    ->action(function (TableRepeater $component): void {
                                        $state = $component->getState();
                                        $field = [
                                            'deleted_at',
                                        ];
                                        foreach ($field as $item) {
                                            $state[Str::uuid()->toString()] = [
                                                'field' => $item,
                                                'type' => 'timestamp',
                                                'length' => null,
                                                'notnull' => true,
                                                'unsigned' => false,
                                                'autoincrement' => false,
                                                'index' => 'none',
                                                'default' => null,
                                                'required' => false,
                                                'display_name' => Str::title($item),
                                                'show' => ['index','view','edit','add','delete'],
                                            ];
                                        }
                                        $component->state($state);
                                    }),
                            ])
                            ->columnSpanFull()
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->button()
                ->outlined(),
                Tables\Actions\Action::make('resource')
                    ->label(fn(DataType $record) => 'Create '. $record->display_name .'Resource')
                    ->icon('heroicon-o-code-bracket')
                    ->button()
                    ->outlined()
                    ->color('success')
                    ->url(fn(DataType $record) => route(Pages\CreateResource::getRouteName(), $record->id)),
                    
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataTypes::route('/'),
            'create' => Pages\CreateDataType::route('/create'),
            'edit' => Pages\EditDataType::route('/{record}/edit'),
            'create-resource' => Pages\CreateResource::route('/{record}/create-resource'),
        ];
    }
}
