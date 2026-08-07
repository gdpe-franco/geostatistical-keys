<?php

namespace App\DataTransferObjects;

use App\Support\InegiData;
use UnexpectedValueException;

final readonly class InegiStateData
{
    private const STATE_CODE = 'cve_ent';

    private const NAME = 'nomgeo';

    private const SHORT_NAME = 'nom_abrev';

    private const TOTAL_POPULATION = 'pob_total';

    private const MAX_SHORT_NAME_LENGTH = 20;

    private function __construct(
        public string $stateCode,
        public string $name,
        public string $shortName,
        public int $totalPopulation,
    ) {}

    /**
     * @param  array<string, mixed>  $state
     */
    public static function fromApi(array $state): self
    {
        $code = InegiData::code($state[self::STATE_CODE] ?? null, 2);
        $name = InegiData::name($state[self::NAME] ?? null);
        $shortName = InegiData::name($state[self::SHORT_NAME] ?? null, self::MAX_SHORT_NAME_LENGTH);
        $population = InegiData::population($state[self::TOTAL_POPULATION] ?? null);

        if ($code === null || $name === null || $shortName === null || $population === null) {
            throw new UnexpectedValueException('INEGI returned an invalid state record.');
        }

        return new self($code, $name, $shortName, $population);
    }

    /**
     * @return array{name: string, short_name: string, state_code: string, total_population: int}
     */
    public function toStateAttributes(): array
    {
        return [
            'state_code' => $this->stateCode,
            'name' => $this->name,
            'short_name' => $this->shortName,
            'total_population' => $this->totalPopulation,
        ];
    }
}
