<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Utils;

class Sanitizer
{
    public static function sanitizePost(
        string $key,
        int $filter = FILTER_DEFAULT,
        $options = null,
        array $allowed = []
    ) {
        return self::sanitize($_POST, $key, $filter, $options, $allowed);
    }

    public static function sanitizeGet(
        string $key,
        int $filter = FILTER_DEFAULT,
        $options = null,
        array $allowed = []
    ) {
        return self::sanitize($_GET, $key, $filter, $options, $allowed);
    }

    public static function sanitize(
        array $data,
        string $key,
        int $filter = FILTER_DEFAULT,
        $options = null,
        array $allowed = []
    ) {
        $data = self::arrayGet($data, $key);
        $dataFiltered = \filter_var($data, $filter, $options ?: []);

        if ($allowed) {
            if (\is_array($dataFiltered)) {
                foreach ($dataFiltered as $data) {
                    if (false === \in_array($data, $allowed)) {
                        return false;
                    }
                }
            } elseif (false === \in_array($dataFiltered, $allowed)) {
                return false;
            }
        }

        return $dataFiltered ?: false;
    }

    private static function arrayGet(array $array, string $key)
    {
        foreach (\explode('.', $key) as $subKey) {
            $array = \is_array($array) && \array_key_exists($subKey, $array) ? $array[$subKey] : null;
        }

        return $array;
    }
}
