<?php

namespace App\Database\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
class TypeRegister
{
   public static function registerTypes($connection)
   {
       $platform = $connection->getDatabasePlatform();
       switch (true) {
           case $platform instanceof SqlitePlatform:
               self::registerSqliteTypes($connection);
               break;
           case $platform instanceof MySqlPlatform:
                self::registerMysqlTypes($connection);
               break;
       }
   }
   private static function registerSqliteTypes($connection)
   {
       $types = [
            'timestamp' => 'App\Database\Types\Mysql\TimestampType',
       ];
       self::registerTypesFromArray($connection, $types);
   }
   private static function registerMysqlTypes($connection)
   {
       $types = [
           'timestamp' => 'App\Database\Types\Sqlite\TimestampType',
       ];
       self::registerTypesFromArray($connection, $types);
   }
   private static function registerTypesFromArray($connection, array $types)
    {
        foreach ($types as $typeName => $typeClass) {
            if (!Type::hasType($typeName)) {
                Type::addType($typeName, $typeClass);
                $connection->getDatabasePlatform()->markDoctrineTypeCommented(Type::getType($typeName));
            }
        }
    }
}