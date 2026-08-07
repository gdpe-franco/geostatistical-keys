<?php

namespace Tests\Unit\Services;

use App\Services\InegiMunicipalityCatalog;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use UnexpectedValueException;

class InegiMunicipalityCatalogTest extends TestCase
{
    private const STATE_CODE = '01';

    public function test_reads_municipalities(): void
    {
        $withoutPopulation = self::municipality();
        unset($withoutPopulation['pob_total']);

        Http::fake([self::endpoint() => Http::response(['datos' => [self::municipality(), $withoutPopulation]])]);

        $this->assertSame([
            [
                'municipality_code' => '001',
                'name' => 'Aguascalientes',
                'total_population' => 948990,
            ],
            [
                'municipality_code' => '001',
                'name' => 'Aguascalientes',
                'total_population' => null,
            ],
        ], app(InegiMunicipalityCatalog::class)->forState(self::STATE_CODE));
    }

    /**
     * @param  array<string, mixed>  $response
     */
    #[DataProvider('invalidResponses')]
    public function test_rejects_invalid_response(array $response): void
    {
        Http::fake([self::endpoint() => Http::response($response)]);

        $this->expectException(UnexpectedValueException::class);

        app(InegiMunicipalityCatalog::class)->forState(self::STATE_CODE);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidResponses(): array
    {
        $missingName = self::municipality();
        unset($missingName['nomgeo']);

        $otherState = self::municipality();
        $otherState['cve_ent'] = '02';

        $invalidPopulation = self::municipality();
        $invalidPopulation['pob_total'] = 'unknown';

        return [
            'missing records' => [[]],
            'invalid record' => [['datos' => ['invalid']]],
            'missing name' => [['datos' => [$missingName]]],
            'invalid population' => [['datos' => [$invalidPopulation]]],
            'other state' => [['datos' => [$otherState]]],
        ];
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
