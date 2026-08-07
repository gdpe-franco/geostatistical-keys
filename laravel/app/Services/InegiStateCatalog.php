<?php

namespace App\Services;

use App\DataTransferObjects\InegiStateCatalogMetadata;
use Illuminate\Support\Facades\Http;
use Throwable;

class InegiStateCatalog
{
    public const STATES_ENDPOINT = 'https://gaia.inegi.org.mx/wscatgeo/v2/mgee/';

    public function source(): ?string
    {
        try {
            $metadata = Http::acceptJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->get(self::STATES_ENDPOINT)
                ->throw()
                ->json('metadatos');

            return is_array($metadata)
                ? InegiStateCatalogMetadata::fromApi($metadata)->source
                : null;
        } catch (Throwable) {
            return null;
        }
    }
}
