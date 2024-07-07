<?php

namespace App\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Schema\Index;
use Illuminate\Support\Facades\Config;
use App\Database\Types\TypeRegister;
use App\Common\MessageCommon;
use Doctrine\DBAL\Schema\ColumnDiff;

class DatabaseManager
{
    private static $instance = null;
    private $connection;
    private $connectionConfig;
    private $connectionParams;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->connectionConfig = new Configuration();
        $this->connectionParams = $this->getConnectionParams();
        $this->connection = DriverManager::getConnection($this->connectionParams, $this->connectionConfig);
        TypeRegister::registerTypes($this->connection);
    }
    /**
     * Get instance
     *
     * @return DatabaseManager
     */
    public static function getInstance(): DatabaseManager
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseManager();
        }
        return self::$instance;
    }

    private function checkConnection(): bool
    {
        try {
            $this->connection->connect();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Get connection params
     *
     * @return array
     */
    private function getConnectionParams(): array
    {
        switch (env('DB_CONNECTION')) {
            case 'mysql':
                return [
                    'dbname' => env('DB_DATABASE', 'forge'),
                    'user' => env('DB_USERNAME', 'forge'),
                    'password' => env('DB_PASSWORD', ''),
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'driver' => 'pdo_mysql',
                ];
            case 'sqlite':
                return [
                    'path' => database_path('database.sqlite'),
                    'driver' => 'pdo_sqlite',
                ];
            default:
                return [];
        }
    }
    /**
     * Get schema manager
     *
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private function getSchemaManager()
    {
        return $this->connection->createSchemaManager();
    }
    /**
     * Create success message
     *
     * @param string $message
     * @return array
     */
    private function createSuccessMessage(string $message): array
    {
        return MessageCommon::createMessage(true, $message);
    }
    /**
     * Create error message
     *
     * @param string $message
     * @return array
     */
    private function createErrorMessage(string $message): array
    {
        return MessageCommon::createMessage(false, $message);
    }
    /**
     * Create table
     *
     * @param string $tableName
     * @param array $columns
     * @return array
     */
    public function createTable(string $tableName, array $columns): array
    {
        try {
            if (!$this->checkConnection())
                return $this->createErrorMessage('Database connection error');

            $schemaManager = $this->getSchemaManager();
            if ($schemaManager->tablesExist($tableName))
                return $this->createErrorMessage('Table (' . $tableName . ') already exists');

            if (count($columns) <= 0)
                return $this->createErrorMessage('Columns are empty');

            $table = new Table($tableName);
            foreach ($columns as $column) {
                $options = $this->convertColumnOptions($column);
                $table->addColumn($column['name'], $column['type'], $options);
                $table = $this->checkTableIndex($table, $column);
            }
            $schemaManager->createTable($table);
            return $this->createSuccessMessage('Table (' . $tableName . ') created successfully');
        } catch (\Throwable $th) {
            return $this->createErrorMessage($th->getMessage());
        }
    }
    /**
     * Add column
     *
     * @param string $tableName
     * @param array $column
     * @return array
     */
    public function addColumn(string $tableName, array $column): array
    {
        try {
            if (!$this->checkConnection())
                return $this->createErrorMessage('Database connection error');

            $schemaManager = $this->getSchemaManager();
            if (!$schemaManager->tablesExist($tableName))
                return $this->createErrorMessage('Table (' . $tableName . ') does not exist');

            $tableDiff = new TableDiff($tableName);
            $options = $this->convertColumnOptions($column);
            $newColumn = new Column($column['name'], Type::getType($column['type']), $options);
            $tableDiff = $this->checkTableDiffIndex($tableDiff, $column);
            $tableDiff->addedColumns[] = $newColumn;
            $schemaManager->alterTable($tableDiff);
            return $this->createSuccessMessage('Column (' . $column['name'] . ') added to table (' . $tableName . ') successfully');
        } catch (\Throwable $th) {
            return $this->createErrorMessage($th->getMessage());
        }
    }
    /**
     * Update column
     *
     * @param string $tableName
     * @param array $oldColumn
     * @param array $newColumn
     * @return array
     */
    public function updateColumn(string $tableName, array $oldColumn, array $newColumn): array
    {
        try {
            if (!$this->checkConnection())
                return $this->createErrorMessage('Database connection error');

            $schemaManager = $this->getSchemaManager();
            if (!$schemaManager->tablesExist($tableName))
                return $this->createErrorMessage('Table (' . $tableName . ') does not exist');

            $table = $schemaManager->introspectTable($tableName);
            if (!$table->hasColumn($oldColumn['name']))
                return $this->createErrorMessage('Column (' . $oldColumn['name'] . ') does not exist in table (' . $tableName . ')');

            $options = $this->convertColumnOptions($newColumn);
            $newColumnObject = new Column($newColumn['name'], Type::getType($newColumn['type']), $options);
            $oldColumnObject = $table->getColumn($oldColumn['name']);
            $columnDiff = new ColumnDiff($oldColumn['name'], $newColumnObject, array_keys($options), $oldColumnObject);

            $tableDiff = new TableDiff($tableName);
            $tableDiff->fromTable = $table;
            $tableDiff->changedColumns[$oldColumn['name']] = $columnDiff;

            $schemaManager->alterTable($tableDiff);
            return $this->createSuccessMessage('Column (' . $oldColumn['name'] . ') updated to (' . $newColumn['name'] . ') in table (' . $tableName . ') successfully');
        } catch (\Throwable $th) {
            return $this->createErrorMessage($th->getMessage());
        }
    }
    /**
     * Delete table
     *
     * @param string $tableName
     * @return array
     */
    public function deleteTable(string $tableName): array
    {
        try {
            $schemaManager = $this->getSchemaManager();
            if (!$schemaManager->tablesExist($tableName))
                return $this->createErrorMessage('Table (' . $tableName . ') does not exist');
            $schemaManager->dropTable($tableName);
            return $this->createSuccessMessage('Table (' . $tableName . ') deleted successfully');
        } catch (\Throwable $th) {
            return $this->createErrorMessage($th->getMessage());
        }
    }
    /**
     * Delete column
     *
     * @param string $tableName
     * @param array $column
     * @return array
     */
    public function deleteColumn(string $tableName, array $column): array
    {
        try {
            if (!$this->checkConnection())
                return $this->createErrorMessage('Database connection error');

            $schemaManager = $this->getSchemaManager();
            if (!$schemaManager->tablesExist($tableName))
                return $this->createErrorMessage('Table (' . $tableName . ') does not exist');

            $table = $schemaManager->introspectTable($tableName);
            if (!$table->hasColumn($column['name']))
                return $this->createErrorMessage('Column (' . $column['name'] . ') does not exist in table (' . $tableName . ')');

            $tableDiff = new TableDiff($tableName);
            $tableDiff->fromTable = $table;
            $tableDiff->removedColumns[$column['name']] = $table->getColumn($column['name']);

            $schemaManager->alterTable($tableDiff);
            return $this->createSuccessMessage('Column (' . $column['name'] . ') deleted from table (' . $tableName . ') successfully');
        } catch (\Throwable $th) {
            return $this->createErrorMessage($th->getMessage());
        }
    }
    /**
     * Rename table
     *
     * @param string $oldTableName
     * @param string $newTableName
     * @return array
     */
    public function renameTable(string $oldTableName, string $newTableName): array
    {
        try {
            if (!$this->checkConnection())
                return $this->createErrorMessage('Database connection error');

            $schemaManager = $this->getSchemaManager();
            if (!$schemaManager->tablesExist($oldTableName))
                return $this->createErrorMessage('Table (' . $oldTableName . ') does not exist');

            if ($schemaManager->tablesExist($newTableName))
                return $this->createErrorMessage('Table (' . $newTableName . ') already exists');

            $schemaManager->renameTable($oldTableName, $newTableName);
            return $this->createSuccessMessage('Table (' . $oldTableName . ') renamed to (' . $newTableName . ') successfully');
        } catch (\Throwable $th) {
            return $this->createErrorMessage($th->getMessage());
        }
    }
    /**
     * Check table index
     *
     * @param Table $table
     * @param array $column
     * @return Table
     */
    private function checkTableIndex(Table $table, array $column): Table
    {
        if (isset($column['index'])) {
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
            }
        }
        return $table;
    }
    /**
     * Check table diff index
     *
     * @param TableDiff $tableDiff
     * @param array $column
     * @return TableDiff
     */
    private function checkTableDiffIndex(TableDiff $tableDiff, array $column): TableDiff
    {
        if (isset($column['index'])) {
            switch ($column['index']) {
                case 'primary':
                    $tableDiff->addedIndexes[] = new Index('primary', [$column['name']], true, true);
                    break;
                case 'unique':
                    $tableDiff->addedIndexes[] = new Index($column['name'] . '_unique', [$column['name']], true);
                    break;
                case 'index':
                    $tableDiff->addedIndexes[] = new Index($column['name'] . '_index', [$column['name']]);
                    break;
            }
        }
        return $tableDiff;
    }
    /**
     * Convert column options to array
     *
     * @param array $column
     * @return array
     */
    private function convertColumnOptions(array $column): array
    {
        $options = [];
        if (isset($column['length'])) $options['length'] = $column['length'];
        if (isset($column['notnull'])) $options['notnull'] = $column['notnull'];
        if (isset($column['default'])) $options['default'] = $column['default'];
        if (isset($column['autoincrement'])) $options['autoincrement'] = $column['autoincrement'];
        if (isset($column['comment'])) $options['comment'] = $column['comment'];
        if (isset($column['platformOptions'])) $options['platformOptions'] = $column['platformOptions'];
        return $options;
    }
}
