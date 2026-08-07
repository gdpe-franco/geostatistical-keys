<?php

namespace App\DataTransferObjects;

use App\Support\InegiData;
use UnexpectedValueException;

final readonly class InegiStateData
{
    private const STATE_CODE = 'cve_ent';

    private const NAME = 'nomgeo';

    private const TOTAL_POPULATION = 'pob_total';

    private function __construct(
        public string $stateCode,
        public string $name,
        public int $totalPopulation,
    ) {}

    /**
     * @param  array<string, mixed>  $state
     */
    public static function fromApi(array $state): self
    {
        $code = InegiData::code($state[self::STATE_CODE] ?? null, 2);
        $name = InegiData::name($state[self::NAME] ?? null);
        $population = InegiData::population($state[self::TOTAL_POPULATION] ?? null);

        if ($code === null || $name === null || $population === null) {
            throw new UnexpectedValueException('INEGI returned an invalid state record.');
        }

        return new self($code, $name, $population);
    }

    /**
     * @return array{name: string, state_code: string, total_population: int}
     */
    public function toStateAttributes(): array
    {
        return [
            'state_code' => $this->stateCode,
            'name' => $this->name,
            'total_population' => $this->totalPopulation,
        ];
    }
}
