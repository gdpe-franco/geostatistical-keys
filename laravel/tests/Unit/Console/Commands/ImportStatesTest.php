<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\ImportStates;
use App\Services\InegiStateImporter;
use RuntimeException;
use Tests\TestCase;

class ImportStatesTest extends TestCase
{
    private const IMPORTED_COUNT = 32;

    public function test_imports_states(): void
    {
        $importer = $this->mock(InegiStateImporter::class);
        $importer->shouldReceive('import')->once()->andReturn(self::IMPORTED_COUNT);

        $this->artisan(ImportStates::SIGNATURE)
            ->expectsOutput('Imported 32 states.')
            ->assertSuccessful();
    }

    public function test_reports_import_failure(): void
    {
        $importer = $this->mock(InegiStateImporter::class);
        $importer->shouldReceive('import')->once()->andThrow(new RuntimeException);

        $this->artisan(ImportStates::SIGNATURE)
            ->expectsOutput('State import failed.')
            ->assertFailed();
    }
}
