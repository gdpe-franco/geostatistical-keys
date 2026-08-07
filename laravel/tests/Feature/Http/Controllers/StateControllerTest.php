<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\State;
use App\Services\InegiStateCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StateControllerTest extends TestCase
{
    use RefreshDatabase;

    private const STATES_ROUTE = 'v1.states.index';

    private const SUMMARY_ROUTE = 'v1.summary';

    public function test_summarizes_states(): void
    {
        $this->state('01', 'Alpha', 'A', 10);
        $this->state('02', 'Deleted', 'D', 20)->delete();
        Http::fake([InegiStateCatalog::STATES_ENDPOINT => Http::response([
            'metadatos' => ['Fuente_informacion_estadistica' => 'INEGI Census 2020'],
        ])]);

        $this->getJson(route(self::SUMMARY_ROUTE))
            ->assertOk()
            ->assertExactJson([
                'total' => 1,
                'source' => 'INEGI Census 2020',
            ]);
    }

    public function test_lists_states(): void
    {
        $this->state('01', 'Alpha', 'A', 10);
        $this->state('02', 'Bravo', 'B', 20);
        $this->state('03', 'Albatross', 'Al', 30);
        $this->state('04', 'Deleted', 'D', 40)->delete();

        $this->getJson($this->url([
            'draw' => 4,
            'start' => 1,
            'length' => 1,
            'search' => ['value' => 'Al'],
            'order' => [['column' => 3, 'dir' => 'desc']],
        ]))
            ->assertOk()
            ->assertExactJson([
                'draw' => 4,
                'recordsTotal' => 3,
                'recordsFiltered' => 2,
                'data' => [[
                    'state_code' => '01',
                    'name' => 'Alpha',
                    'short_name' => 'A',
                    'total_population' => 10,
                ]],
            ]);
    }

    /**
     * @param  array<string, int>  $query
     */
    #[DataProvider('invalidLengths')]
    public function test_rejects_invalid_length(array $query): void
    {
        $this->getJson($this->url($query))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('length');
    }

    /**
     * @return array<string, array{array<string, int>}>
     */
    public static function invalidLengths(): array
    {
        return [
            'zero' => [['length' => 0]],
            'too large' => [['length' => 101]],
        ];
    }

    private function state(string $code, string $name, string $shortName, int $population): State
    {
        return State::query()->create([
            'state_code' => $code,
            'name' => $name,
            'short_name' => $shortName,
            'total_population' => $population,
        ]);
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function url(array $query): string
    {
        return route(self::STATES_ROUTE, $query);
    }
}
