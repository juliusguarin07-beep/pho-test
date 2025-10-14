<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OutbreakAlert;

class UpdateChickenPoxAlert extends Command
{
    protected $signature = 'fix:chicken-pox-alert';
    protected $description = 'Update Chicken Pox alert with proper content and case numbers';

    public function handle()
    {
        $this->info('=== UPDATING CHICKEN POX ALERT ===');

        // Find the published Chicken Pox alert in Lingayen
        $alert = OutbreakAlert::where('id', 4)->first();

        if (!$alert) {
            $this->error('Chicken Pox alert not found!');
            return 1;
        }

        $this->info("Found alert ID: {$alert->id}");

        // Update with comprehensive content
        $alert->update([
            'alert_title' => 'Chicken Pox Outbreak',
            'alert_details' => 'Multiple cases of Chicken Pox (Varicella) have been reported in Lingayen. The outbreak appears to be centered around schools and childcare facilities. Parents and guardians are advised to monitor children for symptoms and seek medical attention promptly.',
            'affected_areas' => 'Central Lingayen, School Districts, Residential Areas',
            'number_of_cases' => 18,
            'preventive_measures' => "• Keep infected children at home until all blisters have scabbed over
• Maintain good hand hygiene - wash hands frequently with soap and water
• Avoid close contact with infected individuals
• Ensure children are up to date with varicella vaccination
• Clean and disinfect surfaces and objects that may be contaminated
• Cover mouth and nose when coughing or sneezing
• Avoid sharing personal items like towels, utensils, or clothing",
            'dos_and_donts' => "DO:
• Isolate infected children until all lesions are crusted over (usually 5-7 days)
• Keep fingernails short to prevent scratching and secondary infection
• Use calamine lotion or cool baths to relieve itching
• Give acetaminophen for fever (avoid aspirin in children)
• Seek medical attention for high fever, difficulty breathing, or signs of infection

DON'T:
• Send infected children to school or daycare
• Use aspirin in children with chicken pox (risk of Reye's syndrome)
• Scratch or pick at the blisters
• Ignore signs of secondary bacterial infection
• Allow infected children to be around pregnant women or immunocompromised individuals",
            'emergency_contacts' => "Lingayen District Hospital: (075) 542-xxxx
Municipal Health Office: (075) 515-xxxx
Provincial Health Office: (075) 515-4046
Pediatric Emergency: 911

For school closure inquiries:
• Department of Education - Lingayen
• Municipal Mayor's Office

Report cases to your local health unit immediately.",
            'health_advisory' => 'Parents should check children daily for fever and rash. Chicken pox is highly contagious from 1-2 days before the rash appears until all blisters have formed scabs. Children with symptoms should stay home and seek medical attention. Vaccination is the best prevention.'
        ]);

        $this->info('Chicken Pox alert updated successfully!');
        $this->info('Updated fields:');
        $this->line('- Alert title: Chicken Pox Outbreak');
        $this->line('- Number of cases: 18');
        $this->line('- Affected areas: Central Lingayen, School Districts');
        $this->line('- Comprehensive prevention and care guidelines added');

        return 0;
    }
}
