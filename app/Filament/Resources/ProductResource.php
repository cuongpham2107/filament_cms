<?php

namespace App\Filament\Resources;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                  Section::make('Product Information')
                      ->schema([
                  TextInput::make('name')->columnSpan(6),
                  TextInput::make('slug')->columnSpan(6),
                  Select::make('category_id')
                      ->columnSpan(6),
                  TextInput::make('price')->columnSpan(6),
                  FileUpload::make('image')->columnSpan(6),
                  FileUpload::make('thumbnails')->columnSpan(6),
                  Textarea::make('description')->columnSpan(12),
                  RichEditor::make('content')->columnSpan(12),
                  Select::make('status')
                      ->columnSpan(3),
                  Select::make('featured')
                      ->columnSpan(3),
                  DateTimePicker::make('created_at')->columnSpan(3),
                  DateTimePicker::make('updated_at')->columnSpan(3),
                      ])->columns(12),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
            ])
            ->filters([
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProduct::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
