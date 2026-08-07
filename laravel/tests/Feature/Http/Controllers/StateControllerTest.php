<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StateControllerTest extends TestCase
{
    use RefreshDatabase;

    private const PATH = '/api/states';

    public function test_lists_states(): void
    {
        $this->state('01', 'Alpha', 10);
        $this->state('02', 'Bravo', 20);
        $this->state('03', 'Albatross', 30);
        $this->state('04', 'Deleted', 40)->delete();

        $this->getJson($this->url([
            'draw' => 4,
            'start' => 1,
            'length' => 1,
            'search' => ['value' => 'Al'],
            'order' => [['column' => 2, 'dir' => 'desc']],
        ]))
            ->assertOk()
            ->assertExactJson([
                'draw' => 4,
                'recordsTotal' => 3,
                'recordsFiltered' => 2,
                'data' => [[
                    'state_code' => '01',
                    'name' => 'Alpha',
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

    private function state(string $code, string $name, int $population): State
    {
        return State::query()->create([
            'state_code' => $code,
            'name' => $name,
            'total_population' => $population,
        ]);
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function url(array $query): string
    {
        return self::PATH.'?'.http_build_query($query);
    }
}
