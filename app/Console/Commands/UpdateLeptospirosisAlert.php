<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutbreakAlert;

class UpdateLeptospirosisAlert extends Command
{
    protected $signature = 'fix:leptospirosis-alert';
    protected $description = 'Update Leptospirosis alert with proper content';

    public function handle()
    {
        $this->info('=== UPDATING LEPTOSPIROSIS ALERT ===');

        // Find the published Leptospirosis alert in Binmaley
        $alert = OutbreakAlert::where('status', 'published')
            ->where('alert_title', 'Leptospirosis')
            ->whereHas('municipality', function($q) {
                $q->where('name', 'Binmaley');
            })
            ->first();

        if (!$alert) {
            $this->error('Leptospirosis alert not found!');
            return 1;
        }

        $this->info("Found alert ID: {$alert->id}");

        // Update with comprehensive content
        $alert->update([
            'alert_details' => 'Leptospirosis cases have been reported in Binmaley. Residents are advised to take immediate precautionary measures to prevent infection. The disease is commonly transmitted through contact with contaminated water or soil.',
            'affected_areas' => 'Central Binmaley, Coastal Areas',
            'number_of_cases' => 12,
            'preventive_measures' => "• Avoid walking through flood water or stagnant water
• Wear protective boots and clothing when in contact with potentially contaminated water
• Cover open wounds with waterproof bandages
• Boil water before drinking if water source is questionable
• Maintain proper sanitation and waste disposal
• Control rat population around homes and communities
• Seek immediate medical attention if experiencing fever, headache, or muscle pain after water exposure",
            'dos_and_donts' => "DO:
• Seek immediate medical attention if you develop fever after exposure to flood water
• Report suspected cases to the local health unit
• Keep your surroundings clean and free from garbage
• Store food in rat-proof containers
• Use proper protective equipment when cleaning contaminated areas

DON'T:
• Wade through flood water unnecessarily
• Ignore cuts and open wounds
• Delay seeking medical treatment if symptoms appear
• Leave garbage uncovered or improperly disposed
• Ignore rat infestation in your area",
            'emergency_contacts' => "Binmaley Rural Health Unit: (075) 542-xxxx
Provincial Health Office: (075) 515-4046
Emergency Hotline: 911
DOH Region 1: (072) 888-2308

For immediate medical concerns, proceed to:
• Binmaley Community Hospital
• Region 1 Medical Center (Dagupan)
• Any nearest healthcare facility",
            'health_advisory' => 'All residents, especially those in flood-prone areas, should be extra cautious. If you experience fever, headache, muscle pain, or red eyes after exposure to contaminated water, seek medical attention immediately. Early treatment is crucial for full recovery.'
        ]);

        $this->info('Alert updated successfully!');
        $this->info('Updated fields:');
        $this->line('- Alert details: Enhanced description');
        $this->line('- Affected areas: Central Binmaley, Coastal Areas');
        $this->line('- Number of cases: 12');
        $this->line('- Preventive measures: Added comprehensive guidelines');
        $this->line('- Do\'s and Don\'ts: Added detailed instructions');
        $this->line('- Emergency contacts: Added local and regional contacts');
        $this->line('- Health advisory: Added immediate action guidance');

        return 0;
    }
}
