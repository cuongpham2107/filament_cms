<?php

namespace App\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Configuration;
use Illuminate\Support\Facades\Config;
use App\Database\Types\TypeRegister;
use App\Common\MessageCommon;

class DatabaseManager
{
    private static $instance = null;
    public $connection;
    private $connectionConfig;
    private $connectionParams;

    public function __construct()
    {   
        $this->connectionConfig = new Configuration();
        $this->connectionParams = $this->checkDBConnection();
        $this->connection = DriverManager::getConnection($this->connectionParams, $this->connectionConfig);

        TypeRegister::registerTypes($this->connection);
    }
    // Singleton pattern
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseManager();
        }

        return self::$instance;
    }
    /**
     * Check connection
     * @return boolean
     */
    public function checkConnection()
    {
        try {
            $this->connection->connect();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /** 
     * check DB_CONNECTION in .env file
    */
    public function checkDBConnection(): array
    {
        if(env('DB_CONNECTION') == 'mysql') return array(
            'dbname' => env('DB_DATABASE', 'forge'),
            'user' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'host' => env('DB_HOST', '127.0.0.1'),
            'driver' => 'pdo_mysql',
        );
        if(env('DB_CONNECTION') == 'sqlite') return array(
            'path' => database_path('database.sqlite'),
            'driver' => 'pdo_sqlite',
        
        );
        return [];
    }
    /**
     * Create table
     * @param string $tableName
     * @param array $columns
     * @return string
     */
    public function createTable($tableName, $columns): array
    {
        try {
            // check connection
            if (!$this->checkConnection()) return MessageCommon::createMessage(false,'Connection database error');
            
            $schemaManager = $this->connection->createSchemaManager();
            //check table exists
            if ($schemaManager->tablesExist($tableName)) return MessageCommon::createMessage(false,'Table (' .$tableName.') exists');
            
            $table = new Table($tableName);

            if(count($columns)<=0) return MessageCommon::createMessage(false,'Columns is empty');

            foreach ($columns as $column) {
                $options = [
                    'length' => $column['length'] ?? null,
                    'notnull' => $column['notnull'] ? true : false,
                    'unsigned' => $column['unsigned'] ?? false,
                    'autoincrement' => $column['autoincrement'] ?? false,
                    'default' => $column['default'] ?? null,
                ];
                $table->addColumn($column['name'], $column['type'], $options);
                if(isset($column['index'])){
                    switch ($column['index']) {
                        case 'primary':
                            $table->setPrimaryKey([$column['name']]);
                            break;
                        case 'unique':
                            $table->addUniqueIndex([$column['name']]);
                            break;
                        case 'index':
                            $table->addIndex([$column['name']]);
                            break;
                        default:
                            break;
                    }
                }
            }
            $schemaManager->createTable($table);
            return MessageCommon::createMessage(true,'Create table (' . $tableName . ') success');
        } catch (\Throwable $th) {
            return MessageCommon::createMessage(false,$th->getMessage());
        }

    }
    /**
     * Summary of deleteTable
     * @param mixed $tableName
     * @return array
     */
    public function deleteTable($tableName): array
    {
        try{
            $schemaManager = $this->connection->createSchemaManager();
            if (!$schemaManager->tablesExist($tableName)) return MessageCommon::createMessage(false,'Table (' .$tableName.') is not exists');
            $schemaManager->dropTable($tableName);
            return MessageCommon::createMessage(true,'Delete table (' . $tableName . ') success');

        }catch(\Throwable $th){
            return MessageCommon::createMessage(false,$th->getMessage());
        }
    }

    /**
     * Delete column
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    public function deleteColumn($tableName, $columnName): bool{
        $schemaManager = $this->connection->createSchemaManager();

        if(!$schemaManager->tablesExist($tableName)) return false;

        $table = $schemaManager->introspectTable($tableName);

        if(!$table->hasColumn($columnName)) return false;
        $table->dropColumn($columnName);
        $schemaManager->dropAndCreateTable($table);
        return true;
    }


    public function listTables()
    {
        $schemaManager = $this->connection->createSchemaManager();
        return $schemaManager->listTableNames();
    }
}