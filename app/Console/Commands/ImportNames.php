<?php

namespace App\Console\Commands;

use App\Services\NameParser;
use Illuminate\Console\Command;

class ImportNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'import:names {file : Path to the CSV file tests/Fixtures/homeowners.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse a CSV file of names and output individual people records in JSON format';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
	    $file = (string) $this->argument('file');

	    if (! is_file($file)) {
		    $this->error('Error: File not found.');
		    return self::FAILURE;
	    }

	    // Read the file line-by-line. The provided sample behaves like a single-column CSV
	    // (each row contains a name and often ends with a trailing comma).
	    $lines = file($file, FILE_IGNORE_NEW_LINES);
	    if ($lines === false) {
		    $this->error('Error: Unable to read file.');
		    return self::FAILURE;
	    }

	    $parser = new NameParser();

	    $people = [];

	    foreach ($lines as $line) {
		    // Clean the line for tabs or extra whitespaces
		    $line = trim($line);
			// Remove the last , at the end of the line
		    $line = rtrim($line, ',');

		    if ($line === '') {
			    continue;
		    }

		    // Parse the file to the DTO
		    foreach ($parser->parse($line) as $person) {
			    $people[] = $person;
		    }
	    }

	    // Output as JSON (Person implements JsonSerializable).
	    $json = json_encode($people, JSON_PRETTY_PRINT);

	    if ($json === false) {
		    $this->error('Error: Unable to encode JSON.');
		    return self::FAILURE;
	    }

	    $this->line($json);

	    return self::SUCCESS;
    }
}
