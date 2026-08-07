<?php

namespace App\Support;

final class InegiData
{
    private const MAX_NAME_LENGTH = 120;

    public static function code(mixed $value, int $length): ?string
    {
        return is_string($value) && preg_match("/^\\d{{$length}}$/", $value) === 1
            ? $value
            : null;
    }

    public static function name(mixed $value): ?string
    {
        if (! is_string($value) || ($value = trim($value)) === '' || mb_strlen($value) > self::MAX_NAME_LENGTH) {
            return null;
        }

        return $value;
    }

    public static function population(mixed $value): ?int
    {
        return (is_string($value) || is_int($value)) && ctype_digit((string) $value)
            ? (int) $value
            : null;
    }
}
