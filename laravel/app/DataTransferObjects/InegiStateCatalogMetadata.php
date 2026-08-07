<?php

namespace App\DataTransferObjects;

use UnexpectedValueException;

final readonly class InegiStateCatalogMetadata
{
    private const SOURCE = 'Fuente_informacion_estadistica';

    private function __construct(public string $source) {}

    /**
     * @param  array<string, mixed>  $metadata
     */
    public static function fromApi(array $metadata): self
    {
        $source = $metadata[self::SOURCE] ?? null;

        if (! is_string($source) || ($source = trim($source)) === '' || mb_strlen($source) > 255) {
            throw new UnexpectedValueException('INEGI returned invalid catalog metadata.');
        }

        return new self($source);
    }
}
