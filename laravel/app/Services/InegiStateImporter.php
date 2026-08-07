<?php

namespace App\Services;

use App\DataTransferObjects\InegiStateData;
use App\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;

class InegiStateImporter
{
    public const STATES_ENDPOINT = InegiStateCatalog::STATES_ENDPOINT;

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

        $states = array_map(InegiStateData::fromApi(...), $data);

        if (count(array_unique(array_map(static fn (InegiStateData $state): string => $state->stateCode, $states))) !== self::EXPECTED_STATE_COUNT) {
            throw new UnexpectedValueException('INEGI returned duplicate state codes.');
        }

        $timestamp = now();
        $records = array_map(
            static fn (InegiStateData $state): array => $state->toStateAttributes() + [
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
}
