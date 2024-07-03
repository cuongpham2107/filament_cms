<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Saade\FilamentAdjacencyList\Forms\Components\AdjacencyList;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Tools';

    protected static ?string $navigationLabel = 'Menus';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                    ->required(),
                ])->columnSpan(1),
                Section::make()->schema([
                    AdjacencyList::make('menu_items')
                        ->label('Menu Items')
                        ->labelKey('name')                  // Tùy chỉnh phím nhãn theo cột của mô hình của bạn
                        ->form([                            // Xác định hình thức
                            Forms\Components\TextInput::make('name')
                                ->label('Name')
                                ->required(),
                            Forms\Components\TextInput::make('url')
                                ->label('URL')
                                ->required(),
                            Forms\Components\ColorPicker::make('color')
                                ->label('Color'),
                            Forms\Components\Select::make('target')
                                ->label('Target')
                                ->options([
                                    '_self' => 'Self',
                                    '_blank' => 'Blank',
                                ]),
                            Forms\Components\TextInput::make('order')
                                ->numeric()
                                ->label('Order'),
                            Forms\Components\Checkbox::make('is_active')
                                ->label('Is Active'),

                        ])
                    ])->columnSpan(2)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('5xl'),
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
            'index' => Pages\ListMenus::route('/'),
            // 'create' => Pages\CreateMenu::route('/create'),
            // 'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
