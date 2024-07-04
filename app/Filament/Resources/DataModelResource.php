<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataModelResource\Pages;
use App\Models\DataModel;
use App\Tables\Columns\TableRepeaterColumn;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DataModelResource extends Resource
{
    protected static ?string $model = DataModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Tools';

    protected static ?string $navigationLabel = 'Database';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Migration Table')
                    ->description('Create a new migration file for the model. The migration file will be created in the database/migrations directory.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Table Name')
                            ->required()
                            ->columnSpan(1),
                        TableRepeater::make('schema')
                            ->default([
                                [
                                    'name' => 'id',
                                    'type' => 'integer',
                                    'length' => 11,
                                    'is_nullable' => true,
                                    'is_unsigned' => true,
                                    'is_auto_increment' => true,
                                    'index' => 'primary',
                                    'default' => null,
                                ],
                            ])
                            ->label('Table Columns')
                            ->addActionLabel('Add New Column')
                            ->cloneable()
                            ->headers([
                                Header::make('name')->width('150px'),
                                Header::make('type')->width('150px'),
                                Header::make('length')->width('150px'),
                                Header::make('is_nullable')
                                    ->label('Not Null')
                                    ->width('50px'),
                                Header::make('is_unsigned')
                                    ->label('Unsigned')
                                    ->width('50px'),
                                Header::make('is_auto_increment')
                                    ->label('Auto Increment')
                                    ->width('50px'),
                                Header::make('index')->width('150px'),
                                Header::make('default')->width('150px'),
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->placeholder('Name')
                                    ->required(),
                                Select::make('type')
                                    ->placeholder('Choose Type')
                                    ->live()
                                    ->searchable()
                                    ->options([
                                        'Numbers' => [
                                            'decimal' => 'DECIMAL',
                                            'double' => 'DOUBLE',
                                            'float' => 'FLOAT',
                                            'integer' => 'INTEGER',
                                            'mediumint' => 'MEDIUMINT',
                                            'bigint' => 'BIGINT',
                                            'smallint' => 'SMALLINT',
                                            'tinyint' => 'TINYINT',
                                        ],
                                        'Binary' => [
                                            'binary' => 'BINARY',
                                            'varbinary' => 'VARBINARY',
                                            'blob' => 'BLOB',
                                            'mediumblob' => 'MEDIUMBLOB',
                                            'longblob' => 'LONGBLOB',
                                            'tinyblob' => 'TINYBLOB',
                                            'bit' => 'BIT',
                                        ],
                                        'Strings' => [
                                            'char' => 'CHAR',
                                            'string' => 'VARCHAR',
                                            'text' => 'TEXT',
                                            'mediumtext' => 'MEDIUMTEXT',
                                            'longtext' => 'LONGTEXT',
                                            'tinytext' => 'TINYTEXT',
                                        ],
                                        'Date and Time' => [
                                            'date' => 'DATE',
                                            'datetime' => 'DATETIME',
                                            'time' => 'TIME',
                                            'timestamp' => 'TIMESTAMP',
                                            'year' => 'YEAR',
                                        ],
                                    ])
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        if ($state == 'date' || $state == 'datetime' || $state == 'time' || $state == 'timestamp' || $state == 'year') {
                                            $set('length', null);
                                        }
                                        if ($state == 'integer' || $state == 'mediumint' || $state == 'bigint' || $state == 'smallint' || $state == 'tinyint') {
                                            $set('length', 11);
                                        }
                                        if ($state == 'float' || $state == 'double' || $state == 'decimal') {
                                            $set('length', '10,2');
                                        }
                                        if ($state == 'char' || $state == 'string' || $state == 'binary' || $state == 'varbinary') {
                                            $set('length', 255);
                                        }

                                    }),
                                TextInput::make('length')
                                    ->default(null)
                                    ->numeric(),
                                Checkbox::make('is_nullable')
                                    ->default(false),
                                Checkbox::make('is_unsigned')
                                    ->default(false),
                                Checkbox::make('is_auto_increment')
                                    ->default(false),
                                Select::make('index')
                                    ->placeholder('Choose')
                                    ->options([
                                        'none' => 'None',
                                        'primary' => 'Primary',
                                        'unique' => 'Unique',
                                        'index' => 'Index',
                                    ])
                                    ->default('none'),
                                TextInput::make('default')
                                    ->default(null),

                            ])
                            ->extraActions([
                                Action::make('addTimestamps')
                                    ->icon('heroicon-m-clock')
                                    ->action(function (TableRepeater $component): void {
                                        $state = $component->getState();
                                        $name = [
                                            'created_at',
                                            'updated_at',
                                        ];
                                        foreach ($name as $item) {
                                            $state[Str::uuid()->toString()] = [
                                                'name' => $item,
                                                'type' => 'timestamp',
                                                'length' => null,
                                                'is_nullable' => false,
                                                'is_unsigned' => false,
                                                'is_auto_increment' => false,
                                                'index' => 'none',
                                                'default' => null,
                                            ];
                                        }

                                        $component->state($state);
                                    }),
                                Action::make('addSoftDeletes')
                                    ->icon('heroicon-m-x-mark')
                                    ->action(function (TableRepeater $component): void {
                                        $state = $component->getState();
                                        $name = [
                                            'deleted_at',
                                        ];
                                        foreach ($name as $item) {
                                            $state[Str::uuid()->toString()] = [
                                                'name' => $item,
                                                'type' => 'timestamp',
                                                'length' => null,
                                                'is_nullable' => true,
                                                'is_unsigned' => false,
                                                'is_auto_increment' => false,
                                                'index' => 'none',
                                                'default' => null,
                                            ];
                                        }

                                        $component->state($state);
                                    }),
                            ])
                            ->columnSpan('full'),

                    ])->columns(3),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                TableRepeaterColumn::make('schema'),
                TableRepeaterColumn::make('relations'),
                   
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\ReplicateAction::make(),
                    Tables\Actions\Action::make('relations')
                    ->label(function (DataModel $record) {
                       return "Create Relations ($record->name)";
                    })
                    ->icon('heroicon-m-paper-clip')
                    ->modalWidth(MaxWidth::ScreenTwoExtraLarge)
                    ->fillForm(fn (DataModel $record): array => [
                        'relations' => $record->relations,
                    ])
                    ->form([
                        Section::make('relations')
                            ->description('Relationships define the relations between models. Foreign keys are automatically inferred, so they don’t need to be added as attributes explicitly.')
                            ->schema([
                                TableRepeater::make('relations')
                                    ->label('Table Relations')
                                    ->headers([
                                        Header::make('name')->width('180px'),
                                        Header::make('type')->width('180px'),
                                        Header::make('model')
                                            ->label('Model Relation')
                                            ->width('180px'),
                                        Header::make(''),
                                        Header::make(''),
                                        Header::make(''),
                                    ])
                                    ->schema([
                                        TextInput::make('name')
                                            ->placeholder('Name Relation')
                                            ->required(),
                                        Select::make('type_ralation')
                                            ->placeholder('Choose Type')
                                            ->default('hasOne')
                                            ->live()
                                            ->options([
                                                'hasOne' => 'Has One',
                                                'hasMany' => 'Has Many',
                                                'belongsTo' => 'Belongs To',
                                                'belongsToMany' => 'Belongs To Many',
                                            ])
                                            ->required(),
                                        Select::make('model')
                                            ->options(function () {
                                                $models = [];
                                                $files = scandir(app_path('Models'));
                                                foreach ($files as $file) {
                                                    if ($file != '.' && $file != '..') {
                                                        $model = 'App\Models\\' . str_replace('.php', '', $file);
                                                        if (class_exists($model)) {
                                                            $models[$model] = str_replace('.php', '', $file);
                                                        }
                                                    }
                                                }
                                                return $models;
                                            })
                                            ->required(),
                                        TextInput::make('foreign_key')
                                            ->placeholder('Foreign Key')
                                            ->visible(function (Get $get) {
                                                 return $get('type_ralation') !== 'belongsToMany' && $get('type_ralation') !== null;
                                            }),
                                        TextInput::make('local_key')
                                            ->placeholder('Local Key')
                                            ->visible(function (Get $get) {
                                                 return $get('type_ralation') !== 'belongsToMany' && $get('type_ralation') !== null;
                                            })
                                            ->required(),
                                        TextInput::make('pivot_table')
                                            ->placeholder('Pivot Table')
                                            ->visible(function (Get $get) {
                                                 return $get('type_ralation') === 'belongsToMany' && $get('type_ralation') !== null;
                                            }),
                                        TextInput::make('foreign_key_on_current_model')
                                            ->placeholder('Foreign Key On Current Model')
                                            ->visible(function (Get $get) {
                                                 return $get('type_ralation') === 'belongsToMany' && $get('type_ralation') !== null;
                                            }),
                                        TextInput::make('foreign_key_on_related_model')
                                            ->placeholder('Foreign Key On Related Model')
                                            ->visible(function (Get $get) {
                                                 return $get('type_ralation') === 'belongsToMany' && $get('type_ralation') !== null;
                                            }),
                                    ])->columnSpan('full'),
                            ])
                    ])
                    ->action(function (array $data, DataModel $record): void {
                        create_trait_relations( $data,$record);
                        $record->relations = $data['relations'];
                        $record->save();
                    }),
                ])
                    ->icon('heroicon-m-ellipsis-horizontal-circle')
                    ->size(ActionSize::Large)
                    ->color('primary'),
                    
                
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
            'index' => Pages\ListDataModels::route('/'),
            'create' => Pages\CreateDataModel::route('/create'),
            'edit' => Pages\EditDataModel::route('/{record}/edit'),
            // 'edit-ralations' => Pages\EditRelationDataModel::route('/{record}/edit/ralations'),
        ];
    }

    // public static function getRecordSubNavigation(Page $page): array
    // {
    //     return $page->generateNavigationItems([
    //         Pages\EditDataModel::class,
    //         Pages\EditRelationDataModel::class,
    //     ]);
    // }
}
