<?php

namespace Tests\Unit\Services;

use App\Models\State;
use App\Services\InegiStateImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use UnexpectedValueException;

class InegiStateImporterTest extends TestCase
{
    use RefreshDatabase;

    private const STATE_COUNT = 32;

    public function test_imports_states(): void
    {
        Http::fake([InegiStateImporter::STATES_ENDPOINT => Http::response(['datos' => self::states()])]);

        $importer = app(InegiStateImporter::class);

        $this->assertSame(self::STATE_COUNT, $importer->import());

        State::query()->where('state_code', '01')->firstOrFail()->delete();
        $states = self::states();
        $states[0]['pob_total'] = '1';
        Http::fake([InegiStateImporter::STATES_ENDPOINT => Http::response(['datos' => $states])]);

        $this->assertSame(self::STATE_COUNT, $importer->import());
        $this->assertDatabaseCount('states', self::STATE_COUNT);
        $this->assertSame(1, State::query()->where('state_code', '01')->value('total_population'));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('invalidResponses')]
    public function test_rejects_invalid_response(array $payload): void
    {
        State::query()->create([
            'state_code' => '01',
            'name' => 'Existing state',
            'total_population' => 1,
        ]);
        Http::fake([InegiStateImporter::STATES_ENDPOINT => Http::response($payload)]);

        try {
            app(InegiStateImporter::class)->import();
            $this->fail('Expected an invalid INEGI response to fail.');
        } catch (UnexpectedValueException) {
            $this->assertDatabaseCount('states', 1);
            $this->assertDatabaseHas('states', ['state_code' => '01', 'name' => 'Existing state']);
        }
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function invalidResponses(): array
    {
        $missingName = self::states();
        unset($missingName[0]['nomgeo']);

        $invalidPopulation = self::states();
        $invalidPopulation[0]['pob_total'] = 'unknown';

        return [
            'missing records' => [[]],
            'missing name' => [['datos' => $missingName]],
            'invalid population' => [['datos' => $invalidPopulation]],
        ];
    }

    /**
     * @return list<array{cve_ent: string, nomgeo: string, pob_total: string}>
     */
    private static function states(): array
    {
        return array_map(
            static fn (int $number): array => [
                'cve_ent' => str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'nomgeo' => "State {$number}",
                'pob_total' => (string) $number,
            ],
            range(1, self::STATE_COUNT),
        );
    }
}
