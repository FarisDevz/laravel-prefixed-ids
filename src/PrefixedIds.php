<?php

namespace Spatie\PrefixedIds;

use Illuminate\Database\Eloquent\Model;

class PrefixedIds
{
    static $registeredModels = [];

    public static function registerModels(array $registerModels): void
    {
        foreach ($registerModels as $prefix => $model) {
            self::registerModel($prefix, $model);
        }
    }

    public static function registerModel(string $prefix, string $modelClass): void
    {
        static::$registeredModels[$prefix] = $modelClass;
    }

    public static function clearRegisteredModels(): void
    {
        static::$registeredModels = [];
    }

    public static function getPrefixForModel(string $modelClass): ?string
    {
        $keyedByModelClass = array_flip(static::$registeredModels);

        return $keyedByModelClass[$modelClass] ?? null;
    }

    public static function find(string $prefixedId): ?Model
    {
        if (! $modelClass = static::getModelClass($prefixedId)) {
            return null;
        }

        return $modelClass::findByPrefixedId($prefixedId);
    }

    public static function getModelClass(string $prefixedId): ?string
    {
        foreach (static::$registeredModels as $prefix => $modelClass) {
            if (static::str_starts_with($prefixedId, $prefix)) {
                return $modelClass;
            }
        }

        return null;
    }

    public static function str_starts_with(string $haystack, string $needle): bool
    {
        return 0 === strncmp($haystack, $needle, \strlen($needle));
    }

}
