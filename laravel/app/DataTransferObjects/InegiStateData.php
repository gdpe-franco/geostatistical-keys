<?php

namespace App\DataTransferObjects;

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
        $code = $state[self::STATE_CODE] ?? null;
        $name = $state[self::NAME] ?? null;
        $population = $state[self::TOTAL_POPULATION] ?? null;

        if (! is_string($code) || ! preg_match('/^\d{2}$/', $code)
            || ! is_string($name) || ($name = trim($name)) === '' || mb_strlen($name) > 120
            || (! is_string($population) && ! is_int($population)) || ! ctype_digit((string) $population)) {
            throw new UnexpectedValueException('INEGI returned an invalid state record.');
        }

        return new self($code, $name, (int) $population);
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
