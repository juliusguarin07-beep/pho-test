<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CaseReport;
use Illuminate\Support\Facades\DB;

class CheckBarangayData extends Command
{
    protected $signature = 'debug:barangay-data';
    protected $description = 'Check barangay data in case reports';

    public function handle()
    {
        $this->info('=== BARANGAY DATA DEBUG ===');

        $totalCases = CaseReport::count();
        $this->info("Total case reports: {$totalCases}");

        if ($totalCases > 0) {
            $this->info("\n=== RECENT CASE REPORTS ===");
            $recentCases = CaseReport::with(['disease', 'municipality'])
                ->latest()
                ->limit(5)
                ->get();

            foreach ($recentCases as $case) {
                $this->line("ID: {$case->id}");
                $this->line("Disease: " . ($case->disease?->name ?? 'NULL'));
                $this->line("Barangay: " . ($case->barangay ?? 'NULL'));
                $this->line("Municipality: " . ($case->municipality?->name ?? 'NULL'));
                $this->line("Status: {$case->status}");
                $this->line("Facility ID: {$case->reporting_facility_id}");
                $this->line("---");
            }

            $this->info("\n=== BARANGAY SUMMARY ===");
            $barangayData = CaseReport::select('barangay_id', DB::raw('count(*) as total'))
                ->with('barangay:id,name')
                ->groupBy('barangay_id')
                ->orderByDesc('total')
                ->get();

            foreach ($barangayData as $item) {
                $barangayName = $item->barangay->name ?? 'Unknown';
                $this->line("{$barangayName}: {$item->total} cases");
            }

            $this->info("\n=== VALIDATOR USER CHECK ===");
            $validator = \App\Models\User::where('id', 2)->first();
            if ($validator) {
                $this->line("Validator: {$validator->name}");
                $this->line("Facility ID: {$validator->facility_id}");
                $this->line("Municipality ID: {$validator->municipality_id}");

                if ($validator->facility_id) {
                    $facilityCases = CaseReport::where('reporting_facility_id', $validator->facility_id)->count();
                    $this->line("Cases for this facility: {$facilityCases}");
                }
            }
        } else {
            $this->warn("No case reports found in database.");
        }

        return 0;
    }
}
