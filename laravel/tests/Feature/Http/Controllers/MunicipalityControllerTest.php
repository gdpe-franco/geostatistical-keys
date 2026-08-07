<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\State;
use App\Services\InegiMunicipalityCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MunicipalityControllerTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE = 'v1.states.municipalities.index';

    private const STATE_CODE = '01';

    public function test_lists_municipalities(): void
    {
        $state = $this->state();
        Http::fake([self::endpoint() => Http::response(['datos' => [self::municipality()]])]);

        $this->getJson(route(self::ROUTE, $state))
            ->assertOk()
            ->assertExactJson([[
                'municipality_code' => '001',
                'name' => 'Aguascalientes',
                'total_population' => 948990,
            ]]);
    }

    public function test_returns_upstream_failure(): void
    {
        $state = $this->state();
        Http::fake([self::endpoint() => Http::response([], 500)]);

        $this->getJson(route(self::ROUTE, $state))
            ->assertStatus(502)
            ->assertJson(['message' => 'Municipalities are temporarily unavailable.']);
    }

    private function state(): State
    {
        return State::query()->create([
            'state_code' => self::STATE_CODE,
            'name' => 'Aguascalientes',
            'short_name' => 'Ags.',
            'total_population' => 1425607,
        ]);
    }

    /**
     * @return array{cve_ent: string, cve_mun: string, nomgeo: string, pob_total: string}
     */
    private static function municipality(): array
    {
        return [
            'cve_ent' => self::STATE_CODE,
            'cve_mun' => '001',
            'nomgeo' => 'Aguascalientes',
            'pob_total' => '948990',
        ];
    }

    private static function endpoint(): string
    {
        return InegiMunicipalityCatalog::MUNICIPALITIES_ENDPOINT.self::STATE_CODE;
    }
}
