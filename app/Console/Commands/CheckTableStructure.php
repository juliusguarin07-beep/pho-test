<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckTableStructure extends Command
{
    protected $signature = 'debug:table-structure';
    protected $description = 'Check case_reports table structure';

    public function handle()
    {
        $this->info('=== CASE_REPORTS TABLE STRUCTURE ===');

        $columns = Schema::getColumnListing('case_reports');
        $this->info("Columns in case_reports table:");

        foreach ($columns as $column) {
            $this->line("- {$column}");
        }

        $this->info("\n=== SAMPLE DATA ===");
        $sampleData = DB::table('case_reports')->first();

        if ($sampleData) {
            foreach ($sampleData as $key => $value) {
                if (strlen($value) > 100) {
                    $value = substr($value, 0, 100) . '...';
                }
                $this->line("{$key}: {$value}");
            }
        } else {
            $this->warn("No data found in case_reports table");
        }

        return 0;
    }
}
