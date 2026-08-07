<?php

namespace App\Services;

use App\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;

final class InegiStateImporter
{
    public const STATES_ENDPOINT = 'https://gaia.inegi.org.mx/wscatgeo/v2/mgee/';

    private const EXPECTED_STATE_COUNT = 32;

    public function import(): int
    {
        $data = Http::acceptJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->get(self::STATES_ENDPOINT)
            ->throw()
            ->json('datos');

        if (! is_array($data) || count($data) !== self::EXPECTED_STATE_COUNT) {
            throw new UnexpectedValueException('INEGI returned an invalid state list.');
        }

        $states = array_map($this->mapState(...), $data);

        if (count(array_unique(array_column($states, 'state_code'))) !== self::EXPECTED_STATE_COUNT) {
            throw new UnexpectedValueException('INEGI returned duplicate state codes.');
        }

        $timestamp = now();
        $records = array_map(
            static fn (array $state): array => $state + [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'deleted_at' => null,
            ],
            $states,
        );

        DB::transaction(fn () => State::withTrashed()->upsert(
            $records,
            ['state_code'],
            ['name', 'total_population', 'updated_at', 'deleted_at'],
        ));

        return count($records);
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array{name: string, state_code: string, total_population: int}
     */
    private function mapState(array $state): array
    {
        $code = $state['cve_ent'] ?? null;
        $name = $state['nomgeo'] ?? null;
        $population = $state['pob_total'] ?? null;

        if (! is_string($code) || ! preg_match('/^\d{2}$/', $code)
            || ! is_string($name) || ($name = trim($name)) === '' || mb_strlen($name) > 120
            || (! is_string($population) && ! is_int($population)) || ! ctype_digit((string) $population)) {
            throw new UnexpectedValueException('INEGI returned an invalid state record.');
        }

        return [
            'state_code' => $code,
            'name' => $name,
            'total_population' => (int) $population,
        ];
    }
}
