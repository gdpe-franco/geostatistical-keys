<?php

namespace App\Services;

use App\DataTransferObjects\InegiMunicipalityData;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;

class InegiMunicipalityCatalog
{
    public const MUNICIPALITIES_ENDPOINT = 'https://gaia.inegi.org.mx/wscatgeo/v2/mgem/';

    /**
     * @return list<array{municipality_code: string, name: string, total_population: int|null}>
     */
    public function forState(string $stateCode): array
    {
        $data = Http::acceptJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->get(self::MUNICIPALITIES_ENDPOINT.$stateCode)
            ->throw()
            ->json('datos');

        if (! is_array($data) || ! array_is_list($data)) {
            throw new UnexpectedValueException('INEGI returned an invalid municipality list.');
        }

        $municipalities = [];

        foreach ($data as $record) {
            if (! is_array($record)) {
                throw new UnexpectedValueException('INEGI returned an invalid municipality record.');
            }

            $municipality = InegiMunicipalityData::fromApi($record);

            if ($municipality->stateCode !== $stateCode) {
                throw new UnexpectedValueException('INEGI returned municipalities for another state.');
            }

            $municipalities[] = $municipality;
        }

        return array_map(static fn (InegiMunicipalityData $municipality): array => $municipality->toArray(), $municipalities);
    }
}
