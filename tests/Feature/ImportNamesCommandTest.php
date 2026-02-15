<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class ImportNamesCommandTest extends TestCase
{
    /**
     * Test that the command fails gracefully if the file is not provided.
     */
    public function test_no_file_provided(): void
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('import:names');
    }

    /**
     * Test that the command shows an error if the file does not exist.
     */
    public function test_file_not_found(): void
    {
        $this->artisan('import:names non-existent.csv')
             ->expectsOutput('Error: File not found.')
             ->assertExitCode(1);
    }
}
