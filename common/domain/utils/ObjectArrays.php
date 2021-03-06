<?php

namespace common\domain\utils;

use InvalidArgumentException;

class ObjectArrays
{

    /**
     * Create array of entity field values
     *
     * @param object[] $items
     * @param string   $fieldName
     * @param bool     $unique
     * @return array
     */
    public static function createFieldArray(array $items, string $fieldName, bool $unique = false): array
    {
        self::validateObjectField($items, $fieldName);

        $fields = array_map(function ($i) use ($fieldName) {
            return $i->$fieldName;
        }, $items);

        return $unique ? array_unique($fields) : $fields;
    }

    /**
     * Create map fieldName -> entity
     *
     * @param object[] $items
     * @param string   $fieldName
     *
     * @return array
     */
    public static function createOneToOneMapByField(array $items, string $fieldName): array
    {
        self::validateObjectField($items, $fieldName);

        $fieldMap = [];
        foreach ($items as $item) {
            $key = $item->$fieldName;
            $fieldMap[$key] = $item;
        }

        return $fieldMap;
    }

    /**
     * Create map fieldName -> entity[]
     *
     * @param object[] $items
     * @param string   $fieldName
     *
     * @return array
     */
    public static function createOneToManyMapByField(array $items, string $fieldName): array
    {
        self::validateObjectField($items, $fieldName);

        $fieldMap = [];
        foreach ($items as $item) {
            $key = $item->$fieldName;
            if (!isset($fieldMap[$key])) {
                $fieldMap[$key] = [];
            }
            $fieldMap[$key][] = $item;
        }

        return $fieldMap;
    }

    public static function filterByFalse(array $items, string $fieldName): array
    {
        self::validateObjectField($items, $fieldName);

        return self::filter($items, function ($item) use ($fieldName) {
            return $item->$fieldName === false;
        });
    }

    public static function filterEqual(array $items, string $fieldName, $value): array
    {
        self::validateObjectField($items, $fieldName);

        return self::filter($items, function ($item) use ($fieldName, $value) {
            return $item->$fieldName == $value;
        });
    }

    public static function filter(array $items, \Closure $filter)
    {
        $result = [];
        foreach ($items as $item) {
            if ($filter($item)) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * @param object[] $items
     * @param string   $fieldName
     */
    private static function validateObjectField(array $items, string $fieldName): void
    {
        $first = reset($items);
        if ($first !== false && !property_exists($first, $fieldName)) {
            throw new InvalidArgumentException("Object does not have field: $fieldName");
        }
    }
}
