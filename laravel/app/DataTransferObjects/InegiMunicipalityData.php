<?php

namespace App\DataTransferObjects;

use App\Support\InegiData;
use UnexpectedValueException;

final readonly class InegiMunicipalityData
{
    private const STATE_CODE = 'cve_ent';

    private const MUNICIPALITY_CODE = 'cve_mun';

    private const NAME = 'nomgeo';

    private const TOTAL_POPULATION = 'pob_total';

    private function __construct(
        public string $stateCode,
        public string $municipalityCode,
        public string $name,
        public int $totalPopulation,
    ) {}

    /**
     * @param  array<string, mixed>  $municipality
     */
    public static function fromApi(array $municipality): self
    {
        $stateCode = InegiData::code($municipality[self::STATE_CODE] ?? null, 2);
        $municipalityCode = InegiData::code($municipality[self::MUNICIPALITY_CODE] ?? null, 3);
        $name = InegiData::name($municipality[self::NAME] ?? null);
        $population = InegiData::population($municipality[self::TOTAL_POPULATION] ?? null);

        if ($stateCode === null || $municipalityCode === null || $name === null || $population === null) {
            throw new UnexpectedValueException('INEGI returned an invalid municipality record.');
        }

        return new self($stateCode, $municipalityCode, $name, $population);
    }

    /**
     * @return array{municipality_code: string, name: string, total_population: int}
     */
    public function toArray(): array
    {
        return [
            'municipality_code' => $this->municipalityCode,
            'name' => $this->name,
            'total_population' => $this->totalPopulation,
        ];
    }
}
