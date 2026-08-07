<?php

namespace App\Console\Commands;

use App\Services\InegiStateImporter;
use Illuminate\Console\Command;
use Throwable;

class ImportStates extends Command
{
    public const SIGNATURE = 'states:import';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::SIGNATURE;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import states from INEGI';

    /**
     * Execute the console command.
     */
    public function handle(InegiStateImporter $importer): int
    {
        try {
            $count = $importer->import();
        } catch (Throwable) {
            $this->error('State import failed.');

            return self::FAILURE;
        }

        $this->info("Imported {$count} states.");

        return self::SUCCESS;
    }
}
