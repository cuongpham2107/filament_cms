<?php

namespace App\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TimestampType extends Type
{
    const TIMESTAMP = 'timestamp';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getDoctrineTypeMapping('timestamp');
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (new \DateTime())->setTimestamp($value);
    }

    public function getName()
    {
        return self::TIMESTAMP;
    }
}
