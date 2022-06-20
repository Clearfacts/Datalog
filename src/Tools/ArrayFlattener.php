<?php

namespace Datalog\Tools;

class ArrayFlattener
{
    /**
     * @return mixed[]
     */
    public static function getFlat(array $notFlat, &$result = [], $prefix = ''): array
    {
        foreach ($notFlat as $key => $mystery) {
            if (is_array($mystery)) {
                self::getFlat($mystery, $result, self::getPrefix($prefix) . $key);
            } else {
                $result[self::getPrefix($prefix) . $key] = $mystery;
            }
        }

        return $result;
    }

    public static function getFlatKeyValueString(array $notFlat): string
    {
        $result = '';
        foreach (self::getFlat($notFlat) as $key => $value) {
            $strippedValue = '"' . str_replace('"', '', (string) $value) . '"';
            $result .= "{$key}={$strippedValue} ";
        }

        return trim($result);
    }

    private static function getPrefix($prefix): string
    {
        return $prefix ? $prefix . '.' : '';
    }
}
