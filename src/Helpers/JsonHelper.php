<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Types\Json;
use JsonException;
use stdClass;

final class JsonHelper
{
    public const int ESCAPE_UNICODE = 1 << 19;

    public static function isJson(?string $data): bool
    {
        if ($data === null || $data === '') {
            return false;
        }

        try {
            self::decode($data);
            return true;
        } catch (JsonException) {
            return false;
        }
    }

    /**
     * <p>Returns a JSON encoded <code>string</code> on success or <b><code>JsonException</code></b> on failure.</p>
     *
     * @param stdClass|array<int|string, mixed> $data
     * @throws JsonException
     */
    public static function encode(array|stdClass $data, int $flags = 0): string
    {
        $flags = ($flags & self::ESCAPE_UNICODE ? 0 : JSON_UNESCAPED_UNICODE)
            | JSON_UNESCAPED_SLASHES
            | ($flags & ~self::ESCAPE_UNICODE)
            | (defined('JSON_PRESERVE_ZERO_FRACTION') ? JSON_PRESERVE_ZERO_FRACTION : 0); // since PHP 5.6.6 & PECL JSON-C 1.3.7

        $json = json_encode($data, $flags);
        if ($error = json_last_error()) {
            throw new JsonException(json_last_error_msg(), $error);
        }
        if ($json === false) {
            throw new JsonException('Unknown error during JSON encoding');
        }

        return $json;
    }

    /**
     * @template T of string|Json
     * @param T $json
     * @return array<int|string, mixed>
     * @throws (T is string ? JsonException : never)
     */
    public static function decode(string|Json $json, int $flags = 0): array
    {
        if ($json === '') {
            throw new JsonException('Empty input');
        }
        $data = $json instanceof Json ? $json->json : $json;

        $value = json_decode($data, null, 512, $flags | JSON_OBJECT_AS_ARRAY | JSON_BIGINT_AS_STRING);
        if ($error = json_last_error()) {
            throw new JsonException(message: json_last_error_msg(), code: $error);
        }

        return (array) $value;
    }
}
