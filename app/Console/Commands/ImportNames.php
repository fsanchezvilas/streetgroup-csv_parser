<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:names {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse a CSV file of names and output individual people records in JSON format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error('Error: File not found.');
            return 1;
        }

        return 0;
    }
}
