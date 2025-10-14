<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Facility;

class ListFacilities extends Command
{
    protected $signature = 'list:facilities';
    protected $description = 'List all facilities by type';

    public function handle()
    {
        $this->info('=== PANGASINAN HEALTH FACILITIES ===');

        // District Hospitals
        $this->line('');
        $this->info('DISTRICT HOSPITALS:');
        Facility::where('type', 'District Hospital')
            ->orderBy('name')
            ->get()
            ->each(function ($facility) {
                $this->line('• ' . $facility->name);
            });

        // Provincial Hospital
        $this->line('');
        $this->info('PROVINCIAL HOSPITAL:');
        Facility::where('type', 'Provincial Hospital')
            ->orderBy('name')
            ->get()
            ->each(function ($facility) {
                $this->line('• ' . $facility->name);
            });

        // RHUs
        $this->line('');
        $this->info('RURAL HEALTH UNITS (RHU):');
        Facility::where('type', 'RHU')
            ->orderBy('name')
            ->get()
            ->each(function ($facility) {
                $this->line('• ' . $facility->name);
            });

        $this->line('');
        $this->info('SUMMARY:');
        $this->line('District Hospitals: ' . Facility::where('type', 'District Hospital')->count());
        $this->line('Provincial Hospitals: ' . Facility::where('type', 'Provincial Hospital')->count());
        $this->line('RHUs: ' . Facility::where('type', 'RHU')->count());
        $this->line('Total Facilities: ' . Facility::count());

        return 0;
    }
}
