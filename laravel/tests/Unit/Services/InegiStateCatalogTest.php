<?php

namespace Tests\Unit\Services;

use App\Services\InegiStateCatalog;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InegiStateCatalogTest extends TestCase
{
    public function test_reads_source(): void
    {
        Http::fake([InegiStateCatalog::STATES_ENDPOINT => Http::response([
            'metadatos' => ['Fuente_informacion_estadistica' => 'INEGI Census 2020'],
        ])]);

        $this->assertSame('INEGI Census 2020', app(InegiStateCatalog::class)->source());
    }

    /**
     * @param  array<string, mixed>  $response
     */
    #[DataProvider('unavailableResponses')]
    public function test_returns_null_when_source_is_unavailable(array $response, int $status): void
    {
        Http::fake([InegiStateCatalog::STATES_ENDPOINT => Http::response($response, $status)]);

        $this->assertNull(app(InegiStateCatalog::class)->source());
    }

    /**
     * @return array<string, array{array<string, mixed>, int}>
     */
    public static function unavailableResponses(): array
    {
        return [
            'request failed' => [[], 500],
            'missing metadata' => [[], 200],
            'missing source' => [['metadatos' => []], 200],
        ];
    }
}
