<?php

namespace App\Filament\Resources\DataTypeResource\Pages;

use App\Filament\Resources\DataTypeResource;
use App\Models\DataRow;
use App\Models\DataType;
use Awcodes\TableRepeater\Components\TableRepeater;
use Filament\Forms\Components\Actions\Action;
use Awcodes\TableRepeater\Header;
use Filament\Actions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;
use Illuminate\Support\Collection;

class CreateResource extends EditRecord
{
    protected static string $resource = DataTypeResource::class;
    // public function mount(int | string $record): void
    // {
    //     $this->record = $this->resolveRecord($record);
    //     dd(json_decode($this->record->rows[1]->default)->column);
    // }

    protected function fillForm(): void
    {
        $this->fillFormWithDataAndCallHooks($this->getRecord());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Make Resource')
            ->description('Create a new resource file for the model. The resource file will be created in the app/Filament/Resources directory.')
            ->schema([
                TableRepeater::make('rows')
                    ->relationship('rows')
                    ->label('Table Resource')
                    ->orderColumn('order')
                    ->addable(false)
                    ->headers([
                        Header::make('field')
                            ->width('100px'),
                        Header::make('show')
                            ->label('Hiển thị')
                            ->width('150px'),
                        Header::make('type_filament')
                            ->label('Kiểu dữ liệu')
                            ->width('150px'),
                        Header::make('display_name')
                            ->label('Tiêu đề')
                            ->width('150px'),
                        Header::make('default')
                            ->width('180px'),
                    ])
                    ->schema([
                        TextInput::make('field')
                            ->readOnly()
                            ->required(),
                        CheckboxList::make('show')
                            ->options([
                                'index' => 'Danh sách',
                                'view' => 'Xem',
                                'edit' => 'Chỉnh sửa',
                                'add' => 'Thêm mới',
                                'delete' => 'Xóa',
                            ]),
                        Select::make('type_filament')
                            ->searchable()
                            ->placeholder('Chọn kiểu dữ liệu')
                            ->default('text')
                            ->options(config('custom.type_filament'))
                            ->required(),
                        TextInput::make('display_name')
                            ->required(),
                        CodeEditor::make('default'),
                    ])
                    ->extraActions([
                        Action::make('add_relations')
                            ->label('Relations')
                            ->form([
                                Section::make('Create Relations')->schema([
                                    TextInput::make('name')
                                        ->placeholder('Function')
                                        ->required(),
                                    Select::make('type')
                                        ->placeholder('Type')
                                        ->options([
                                            'hasOne' => 'Has One',
                                            'hasMany' => 'Has Many',
                                            'belongsTo' => 'Belongs To',
                                            'belongsToMany' => 'Belongs To Many',
                                        ])
                                        ->required(),
                                    Select::make('model')
                                        ->placeholder('Model')
                                        ->options(DataType::all()->pluck('display_name', 'slug')->toArray())
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ( Set $set, ?string $state): void {
                                            $data_type = DataType::query()->where('slug', $state)->first();
                                            $data_type = DataRow::query()->where('data_type_id', $data_type->id)->get()->pluck('field', 'field')->toArray();
                                            $set('foreign_key', $data_type);
                                        })
                                        ->required(),
                                    Select::make('foreign_key')
                                        ->label('Which column from the Room table will be used to link to the Book_room table?')
                                        ->options(function (Get $get) {
                                            return $get('foreign_key') ?? [];
                                        })
                                        ->columnSpanFull()
                                ])->columns(3),
                                
                            ])
                            ->action(function (TableRepeater $component): void {
                                
                            }),
                    ]),
            ])
        ]);
    }
}
