<?php

namespace App\Enums;

use ReflectionClass;
use ReflectionException;

abstract class AbstractEnum
{
    /**
     * Static cache of available values, shared with all subclasses.
     *
     * @var array
     */
    protected static array $values = [];

    /**
     * AbstractEnum constructor.
     */
    private function __construct()
    {
    }

    /**
     * Gets all available keys with values.
     *
     * @throws ReflectionException
     *
     * @return array The available values, keyed by constant.
     */
    public static function getAll(): array
    {
        $class = static::class;

        if (!isset(static::$values[$class])) {
            $reflection = new ReflectionClass($class);
            static::$values[$class] = $reflection->getConstants();
        }

        return static::$values[$class];
    }

    /**
     * Gets all available values.
     *
     * @return array The available values, keyed by constant.
     * @noinspection PhpUnused*@throws \ReflectionException
     *
     * @throws ReflectionException
     * @noinspection PhpUnused
     */
    public static function getAllValues(): array
    {
        return array_values(static::getAll());
    }

    /**
     * Checks whether the provided value is defined.
     *
     * @param string $value The value.
     *
     * @return bool True if the value is defined, false otherwise.
     * @throws ReflectionException
     */
    public static function exists(string $value): bool
    {
        return in_array($value, static::getAll(), true);
    }

//    /**
//     * Glue all values
//     *
//     * @param string $glue
//     *
//     * @throws \ReflectionException
//     *
//     * @return string
//     */
//    public static function implode($glue = ',')
//    {
//        return implode($glue, static::getAll());
//    }
}
