<?php

/**
 * Datatype for WordPress's IDs
 *
 * WordPress use 0 to represent a guest user. It cause a lots of problems
 * in Doctrine because the user with id zero never exist. This datatype
 * convert 0 to null, make life easier.
 */

namespace Mozart\Bundle\NucleusBundle\Types;

use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Class WordpressIdType
 * @package Mozart\Bundle\NucleusBundle\Types
 */
class WordpressIdType extends BigIntType
{
    const NAME = 'wordpressid';

    /**
     * @param  mixed            $value
     * @param  AbstractPlatform $platform
     * @return mixed|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === 0) {
            return null;
        }

        return $value;
    }

    /**
     * @param  mixed            $value
     * @param  AbstractPlatform $platform
     * @return int|mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return 0;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
