<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

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
    function create_migration_model($columns, $tableName){
        generateMigration($columns, $tableName);
        Artisan::call('migrate');
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

if(!function_exists('generateMigration')){
    function generateMigration($columns,$tableName)
    {
        $migration = "<?php\n\n";
        $migration .= "use Illuminate\Database\Migrations\Migration;\n";
        $migration .= "use Illuminate\Database\Schema\Blueprint;\n";
        $migration .= "use Illuminate\Support\Facades\Schema;\n\n";
        $migration .= "return new class extends Migration\n{\n";
        $migration .= "    /**\n";
        $migration .= "     * Run the migrations.\n";
        $migration .= "     */\n";
        $migration .= "    public function up(): void\n    {\n";
        $migration .= "        Schema::create('$tableName', function (Blueprint \$table) {\n";
        foreach ($columns as $column) {
            $line = "            \$table";
            // Type and length
            if($column['name'] === 'id'){
                $line .= "->id();\n";
            } 
            else{
                switch ($column['type']) {
                    case 'integer':
                    case 'bigint':
                    case 'mediumint':
                    case 'smallint':
                    case 'tinyint':
                        $line .= "->{$column['type']}('{$column['name']}')";
                        if ($column['is_unsigned']) {
                            $line .= "->unsigned()";
                        }
                        if ($column['is_auto_increment']) {
                            $line .= "->autoIncrement()";
                        }
                        break;
                    case 'decimal':
                    case 'double':
                    case 'float':
                        $line .= "->{$column['type']}('{$column['name']}')";
                        break;
                    case 'string':
                    case 'char':
                        $line .= "->{$column['type']}('{$column['name']}', {$column['length']})";
                        break;
                    case 'text':
                    case 'mediumtext':
                    case 'longtext':
                    case 'tinytext':
                    case 'binary':
                    case 'varbinary':
                    case 'blob':
                    case 'mediumblob':
                    case 'longblob':
                    case 'tinyblob':
                    case 'bit':
                        $line .= "->{$column['type']}('{$column['name']}')";
                        break;
                    case 'date':
                    case 'datetime':
                    case 'time':
                    case 'timestamp':
                    case 'year':
                        $line .= "->{$column['type']}('{$column['name']}')";
                        break;
                }
    
                // Nullability
                if ($column['is_nullable']) {
                    $line .= "->nullable()";
                }
    
                // Default value
                if ($column['default'] !== null) {
                    $line .= "->default('{$column['default']}')";
                }
                // Indexes
                if ($column['index'] !== 'none') {
                    if ($column['index'] === 'primary') {
                        $line .= "->primary()";
                    } elseif ($column['index'] === 'unique') {
                        $line .= "->unique()";
                    } elseif ($column['index'] === 'index') {
                        $line .= "->index()";
                    }
                }
    
                $line .= ";\n";
            }
            $migration .= $line;
        }
        $migration .= "        });\n";
        $migration .= "    }\n\n";
        $migration .= "    /**\n";
        $migration .= "     * Reverse the migrations.\n";
        $migration .= "     */\n";
        $migration .= "    public function down(): void\n    {\n";
        $migration .= "        Schema::dropIfExists('$tableName');\n";
        $migration .= "    }\n";
        $migration .= "};\n";
        $migrationContent =  $migration;
        $path = database_path('migrations/' . date('Y_m_d_His') . '_create_' . $tableName . '_table.php');
        file_put_contents($path, $migrationContent);
    }
}
if(!function_exists('generateModel')){
    function generateModel($modelName){
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
if(!function_exists('update_table_name')){
    function update_table_name($oldNameTable, $newNameTable){
        Schema::rename($oldNameTable, $newNameTable);
    }
}
if(!function_exists('update_table_schema')){
    function update_table_schema($tableName, $data){
        Schema::table($tableName, function ($table) use ($data) {
            foreach ($data as $column) {
                if($column['id'] === 'id'){
                    $table->id();
                }
                else{
                    switch ($column['type']) {
                        case 'integer':
                        case 'bigint':
                        case 'mediumint':
                        case 'smallint':
                        case 'tinyint':
                            $table->{$column['type']}($column['name']);
                            if ($column['is_unsigned']) {
                                $table->unsigned();
                            }
                            if ($column['is_auto_increment']) {
                                $table->autoIncrement();
                            }
                            break;
                        case 'decimal':
                        case 'double':
                        case 'float':
                            $table->{$column['type']}($column['name']);
                            break;
                        case 'string':
                        case 'char':
                            $table->{$column['type']}($column['name'], $column['length']);
                            break;
                        case 'text':
                        case 'mediumtext':
                        case 'longtext':
                        case 'tinytext':
                        case 'binary':
                        case 'varbinary':
                        case 'blob':
                        case 'mediumblob':
                        case 'longblob':
                        case 'tinyblob':
                        case 'bit':
                            $table->{$column['type']}($column['name']);
                            break;
                        case 'date':
                        case 'datetime':
                        case 'time':
                        case 'timestamp':
                        case 'year':
                            $table->{$column['type']}($column['name']);
                            break;
                    }
                    if ($column['is_nullable']) {
                        $table->nullable();
                    }
                    if ($column['default'] !== null) {
                        $table->default($column['default']);
                    }
                    if ($column['index'] !== 'none') {
                        if ($column['index'] === 'primary') {
                            $table->primary();
                        } elseif ($column['index'] === 'unique') {
                            $table->unique();
                        } elseif ($column['index'] === 'index') {
                            $table->index();
                        }
                    }
                }
            }
        });
        Artisan::call('migrate');
    }
}