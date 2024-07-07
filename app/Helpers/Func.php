<?php
use Illuminate\Support\Facades\File;

if (!function_exists('formatRelationshipMethod')) {
    function formatRelationshipMethod($method)
    {
        $parts = preg_split('/(?=[A-Z])/', $method, -1, PREG_SPLIT_NO_EMPTY);
        $formatted = implode(' ', $parts);
        return ucfirst(strtolower($formatted));
    }
}

if (!function_exists('singularize_and_capitalize_words')) {
    function singularize_and_capitalize_words($input)
    {
        $words = explode(', ', $input);
        $result = array();
        foreach ($words as $word) {
            $sub_words = explode('_', $word);
            $singular_sub_words = array_map('singularize', $sub_words);
            $camel_case_word = convert_to_camel_case($singular_sub_words);
            $result[] = ucfirst($camel_case_word);
        }
        return implode(', ', $result);
    }
    function convert_to_camel_case($words)
    {
        $camelCase = '';
        foreach ($words as $index => $word) {
            $camelCase .= $index === 0 ? lcfirst($word) : ucfirst($word);
        }
        return $camelCase;
    }
    function singularize($word)
    {
        $singular = array(
            '/(s|x|z|ch|sh)es$/i' => '$1',
            '/ies$/i' => 'y',
            '/ves$/i' => 'f',
            '/men$/i' => 'man',
            '/children$/i' => 'child',
            '/s$/i' => ''
        );
        foreach ($singular as $pattern => $replacement) {
            if (preg_match($pattern, $word)) {
                return preg_replace($pattern, $replacement, $word);
            }
        }
        return $word;
    }
}

if (!function_exists('create_migration_model')) {
    function create_migration_model($tableName){
        $traitRalationPath =  generateModel($tableName);
        return $traitRalationPath;
    }
}

if(!function_exists('create_trait_relations')){
    function create_trait_relations($data,$record){
        $trait = "<?php\n\n";
        $trait .= "namespace App\Models\Traits;\n\n";
        $trait .= "trait ".singularize_and_capitalize_words($record->name)."RelationTrait\n";
        $trait .= "{\n";
        foreach ($data['relations'] as $relation) {
            $trait .= "    public function {$relation['name']}()\n    {\n";
            if($relation['type_ralation'] === 'belongsToMany'){
                $trait .= "        return \$this->{$relation['type_ralation']}(\\{$relation['model']}::class, '{$relation['pivot_table']}', '{$relation['foreign_key_on_current_model']}', '{$relation['foreign_key_on_related_model']}');\n";
            }
            else{
                $trait .= "        return \$this->{$relation['type_ralation']}(\\{$relation['model']}::class, '{$relation['foreign_key']}', '{$relation['local_key']}');\n";
            }
            $trait .= "    }\n";
        }
        $trait .= "}\n";
        $traitContent =  $trait;
        $directory = app_path('Models/Traits');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $traitPath = app_path('Models/Traits/' . singularize_and_capitalize_words($record->name) . 'RelationTrait.php');
        if(file_exists($traitPath)){
            unlink($traitPath);
        }
        file_put_contents($traitPath, $traitContent);
        
    }
}
if(!function_exists('craete_resource'))
{
    function craete_resource($data,$record){

        $resource = "<?php\n\n";
        $resource .= "namespace App\Filament\Resources;\n";
        $resource .= "use App\Filament\Resources\\".singularize_and_capitalize_words($record->name)."Resource\Pages;\n";
        $resource .= "use App\Filament\Resources\\".singularize_and_capitalize_words($record->name)."Resource\RelationManagers;\n";
        $resource .= "use App\Models\\".singularize_and_capitalize_words($record->name).";\n";
        $resource .= "use Filament\Forms\Form;\n";
        $resource .= "use Filament\Resources\Resource;\n";
        $resource .= "use Filament\Tables;\n";
        $resource .= "use Filament\Tables\Table;\n";
        $resource .= "use Filament\Forms\Components\TextInput;\n";
        $resource .= "use Filament\Forms\Components\Select;\n";
        $resource .= "use Filament\Forms\Components\Checkbox;\n";
        $resource .= "use Filament\Forms\Components\Toggle;\n";
        $resource .= "use Filament\Forms\Components\CheckboxList;\n";
        $resource .= "use Filament\Forms\Components\Radio;\n";
        $resource .= "use Filament\Forms\Components\DatePicker;\n";
        $resource .= "use Filament\Forms\Components\DateTimePicker;\n";
        $resource .= "use Filament\Forms\Components\TimePicker;\n";
        $resource .= "use Filament\Forms\Components\FileUpload;\n";
        $resource .= "use Filament\Forms\Components\RichEditor;\n";
        $resource .= "use Filament\Forms\Components\MarkdownEditor;\n";
        $resource .= "use Filament\Forms\Components\Repeater;\n";
        $resource .= "use Filament\Forms\Components\Builder;\n";
        $resource .= "use Filament\Forms\Components\TagsInput;\n";
        $resource .= "use Filament\Forms\Components\Textarea;\n";
        $resource .= "use Filament\Forms\Components\KeyValue;\n";
        $resource .= "use Filament\Forms\Components\ColorPicker;\n";
        $resource .= "use Filament\Forms\Components\ToggleButtons;\n";
        $resource .= "use Filament\Forms\Components\Hidden;\n";
        $resource .= "use Filament\Forms\Components\Section;\n";

        $resource .= "class ".singularize_and_capitalize_words($record->name)."Resource extends Resource\n";
        $resource .= "{\n";

        $resource .= "    protected static ?string \$model = ".singularize_and_capitalize_words($record->name)."::class;\n";
        $resource .= "    protected static ?string \$navigationIcon = 'heroicon-o-rectangle-stack';\n";
        $resource .= "    public static function form(Form \$form): Form\n";
        $resource .= "    {\n";
        $resource .= "        return \$form\n";
        $resource .= "            ->schema([\n";
        $resource .= "                  Section::make('".singularize_and_capitalize_words($record->name)." Information')\n";
        $resource .= "                      ->schema([\n";
        
        foreach ($data['resource'] as $field) {
            switch ($field['options']) {
                case 'text':
                    $resource .= "                TextInput::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'select':
                    $resource .= "                Select::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'checkbox':
                    $resource .= "                Checkbox::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'toggle':
                    $resource .= "                Toggle::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'checkbox_list':
                    $resource .= "                CheckboxList::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'radio':
                    $resource .= "                Radio::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'date':
                    $resource .= "                DatePicker::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'datetime':
                    $resource .= "                DateTimePicker::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'time':
                    $resource .= "                TimePicker::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'file':
                    $resource .= "                FileUpload::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'rich_text':
                    $resource .= "                RichEditor::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'markerdown_editor':
                    $resource .= "                MarkdownEditor::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'repeater':
                    $resource .= "                Repeater::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'builder':
                    $resource .= "                Builder::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'tags_input':
                    $resource .= "                TagsInput::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'textarea':
                    $resource .= "                Textarea::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'key_value':
                    $resource .= "                KeyValue::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'color_picker':
                    $resource .= "                ColorPicker::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'toggle_button':
                    $resource .= "                ToggleButtons::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                case 'hidden':
                    $resource .= "                Hidden::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
                default:
                    $resource .= "                TextInput::make('{$field['name']}')->columnSpan({$field['column']}),\n";
                    break;
            }
        }
        $resource .= "                      ])->columns(12),\n";
        $resource .= "            ]);\n";
        $resource .= "    }\n";
        $resource .= "    public static function table(Table \$table): Table\n";
        $resource .= "    {\n";
        $resource .= "        return \$table\n";
        $resource .= "            ->columns([\n";
        $resource .= "                \n";
        $resource .= "            ])\n";
        $resource .= "            ->filters([\n";
        $resource .= "                \n";
        $resource .= "            ])\n";
        $resource .= "            ->actions([\n";
        $resource .= "                Tables\Actions\EditAction::make(),\n";
        $resource .= "            ])\n";
        $resource .= "            ->bulkActions([\n";
        $resource .= "                Tables\Actions\BulkActionGroup::make([\n";
        $resource .= "                    Tables\Actions\DeleteBulkAction::make(),\n";
        $resource .= "                ]),\n";
        $resource .= "            ]);\n";
        $resource .= "    }\n";
        $resource .= "    public static function getRelations(): array\n";
        $resource .= "    {\n";
        $resource .= "        return [\n";
        $resource .= "            //\n";
        $resource .= "        ];\n";
        $resource .= "    }\n";
        $resource .= "    public static function getPages(): array\n";
        $resource .= "    {\n";
        $resource .= "        return [\n";
        $resource .= "            'index' => Pages\List".singularize_and_capitalize_words($record->name)."::route('/'),\n";
        $resource .= "            'create' => Pages\Create".singularize_and_capitalize_words($record->name)."::route('/create'),\n";
        $resource .= "            'edit' => Pages\Edit".singularize_and_capitalize_words($record->name)."::route('/{record}/edit'),\n";
        $resource .= "        ];\n";
        $resource .= "    }\n";
        $resource .= "}\n";
        $resourceContent =  $resource;
        $directory = app_path('Filament/Resources');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $resourcePath = app_path('Filament/Resources/' . singularize_and_capitalize_words($record->name) . 'Resource.php');
        if(file_exists($resourcePath)){
            unlink($resourcePath);
        }
        file_put_contents($resourcePath, $resourceContent);

        create_page($record);
        edit_page($record);
        list_page($record);
    }
    function create_page($record){
        $page = "<?php\n\n";
        $page .= "namespace App\Filament\Resources\\".singularize_and_capitalize_words($record->name)."Resource\Pages;\n";
        $page .= "use App\Filament\Resources\\".singularize_and_capitalize_words($record->name)."Resource;\n";
        $page .= "use Filament\Actions;\n";
        $page .= "use Filament\Resources\Pages\CreateRecord;\n";
        $page .= "class Create".singularize_and_capitalize_words($record->name)." extends CreateRecord\n";
        $page .= "{\n";
        $page .= "    protected static string \$resource = ".singularize_and_capitalize_words($record->name)."Resource::class;\n";
        $page .= " }\n";
        $pageContent =  $page;
        $directory = app_path('Filament/Resources/'.singularize_and_capitalize_words($record->name).'Resource/Pages');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $pagePath = app_path('Filament/Resources/'.singularize_and_capitalize_words($record->name).'Resource/Pages/Create'.singularize_and_capitalize_words($record->name).'.php');
        if(file_exists($pagePath)){
            unlink($pagePath);
        }
        file_put_contents($pagePath, $pageContent);
        
    }
    function edit_page($record){
        $page = "<?php\n\n";
        $page .= "namespace App\Filament\Resources\\".singularize_and_capitalize_words($record->name)."Resource\Pages;\n";
        $page .= "use App\Filament\Resources\\".singularize_and_capitalize_words($record->name)."Resource;\n";
        $page .= "use Filament\Actions;\n";
        $page .= "use Filament\Resources\Pages\EditRecord;\n";
        $page .= "use App\Common\CompareArrayCommon;\n";
        $page .= "class Edit".singularize_and_capitalize_words($record->name)." extends EditRecord\n";
        $page .= "{\n";
        $page .= "    protected static string \$resource = ".singularize_and_capitalize_words($record->name)."Resource::class;\n";
        $page .= "    protected function getHeaderActions(): array\n";
        $page .= "    {\n";
        $page .= "        return [\n";
        $page .= "            Actions\DeleteAction::make()\n";
        $page .= "        ];\n";
        $page .= "    }\n";
        $page .= "}";

        $pageContent =  $page;
        $directory = app_path('Filament/Resources/'.singularize_and_capitalize_words($record->name).'Resource/Pages');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $pagePath = app_path('Filament/Resources/'.singularize_and_capitalize_words($record->name).'Resource/Pages/Edit'.singularize_and_capitalize_words($record->name).'.php');
        if(file_exists($pagePath)){
            unlink($pagePath);
        }
        file_put_contents($pagePath, $pageContent);
    }
    function list_page($record){
        $page = "<?php\n\n";
        $page .= "namespace App\Filament\Resources\\".singularize_and_capitalize_words($record->name)."Resource\Pages;\n";
        $page .= "use App\Filament\Resources\\".singularize_and_capitalize_words($record->name)."Resource;\n";
        $page .= "use Filament\Actions;\n";
        $page .= "use Filament\Resources\Pages\ListRecords;\n";
        $page .= "class List".singularize_and_capitalize_words($record->name)." extends ListRecords\n";
        $page .= "{\n";
        $page .= "    protected static string \$resource = ".singularize_and_capitalize_words($record->name)."Resource::class;\n";
        $page .= "    protected function getHeaderActions(): array\n";
        $page .= "    {\n";
        $page .= "        return [\n";
        $page .= "            Actions\CreateAction::make(),\n";
        $page .= "        ];\n";
        $page .= "    }\n";
        $page .= "}\n";
        $pageContent =  $page;
        $directory = app_path('Filament/Resources/'.singularize_and_capitalize_words($record->name).'Resource/Pages');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $pagePath = app_path('Filament/Resources/'.singularize_and_capitalize_words($record->name).'Resource/Pages/List'.singularize_and_capitalize_words($record->name).'.php');
        if(file_exists($pagePath)){
            unlink($pagePath);
        }
        file_put_contents($pagePath, $pageContent);
    }
}

if(!function_exists('generateModel')){
    function generateModel($modelName){
        // dd($modelName);
        $modelName = singularize_and_capitalize_words($modelName);
        
        $model = "<?php\n\n";
        $model .= "namespace App\Models;\n\n";
        $model .= "use Illuminate\Database\Eloquent\Factories\HasFactory;\n";
        $model .= "use Illuminate\Database\Eloquent\Model;\n";
        $model .= "use Illuminate\Database\Eloquent\SoftDeletes;\n\n";
        $model .= "use App\Models\Traits\\{$modelName}RelationTrait;\n\n";
        $model .= "class $modelName extends Model\n{\n";
        $model .= "    use HasFactory,{$modelName}RelationTrait;\n\n";
        $model .= "     /**\n";
        $model .= "     * The attributes that are mass assignable.\n";
        $model .= "     *\n";
        $model .= "     * @var array\n";
        $model .= "     */\n";
        $model .= "     protected \$guarded = [];\n";
        $model .= "}\n";
        $modelContent =  $model;
        $modelPath = app_path('Models/' . $modelName . '.php');
        file_put_contents($modelPath, $modelContent);


        $trait = "<?php\n\n";
        $trait .= "namespace App\Models\Traits;\n\n";
        $trait .= "trait {$modelName}RelationTrait\n{\n";
        $trait .= "}\n";
        $traitContent =  $trait;
        $directory = app_path('Models/Traits');
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        $traitPath = app_path('Models/Traits/' . $modelName . 'RelationTrait.php');
        file_put_contents($traitPath, $traitContent);
        return 'Models\\Traits\\' . $modelName . 'RelationTrait.php';
    }
}
