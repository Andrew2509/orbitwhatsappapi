<?php

namespace Database\Seeders;

use App\Models\BlacklistedWord;
use Illuminate\Database\Seeder;

class BlacklistedWordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $words = [
            // Gambling / Judi Online
            ['word' => 'judi', 'category' => 'gambling', 'severity' => 'block'],
            ['word' => 'togel', 'category' => 'gambling', 'severity' => 'block'],
            ['word' => 'slot online', 'category' => 'gambling', 'severity' => 'block'],
            ['word' => 'slot gacor', 'category' => 'gambling', 'severity' => 'block'],
            ['word' => 'casino online', 'category' => 'gambling', 'severity' => 'block'],
            ['word' => 'poker online', 'category' => 'gambling', 'severity' => 'block'],
            ['word' => 'bandar bola', 'category' => 'gambling', 'severity' => 'block'],
            ['word' => 'taruhan', 'category' => 'gambling', 'severity' => 'warning'],
            
            // Scam / Penipuan
            ['word' => 'pinjol', 'category' => 'scam', 'severity' => 'warning'],
            ['word' => 'pinjaman online', 'category' => 'scam', 'severity' => 'warning'],
            ['word' => 'investasi bodong', 'category' => 'scam', 'severity' => 'block'],
            ['word' => 'money game', 'category' => 'scam', 'severity' => 'block'],
            ['word' => 'binary option', 'category' => 'scam', 'severity' => 'block'],
            ['word' => 'skema ponzi', 'category' => 'scam', 'severity' => 'block'],
            ['word' => 'cuan besar', 'category' => 'scam', 'severity' => 'warning'],
            
            // Adult Content
            ['word' => 'bokep', 'category' => 'adult', 'severity' => 'block'],
            ['word' => 'porn', 'category' => 'adult', 'severity' => 'block'],
            ['word' => 'video dewasa', 'category' => 'adult', 'severity' => 'block'],
            
            // Drugs
            ['word' => 'narkoba', 'category' => 'drugs', 'severity' => 'block'],
            ['word' => 'ganja', 'category' => 'drugs', 'severity' => 'block'],
            ['word' => 'sabu', 'category' => 'drugs', 'severity' => 'block'],
            ['word' => 'ekstasi', 'category' => 'drugs', 'severity' => 'block'],
            
            // Spam indicators
            ['word' => 'klik link ini', 'category' => 'spam', 'severity' => 'warning'],
            ['word' => 'forward ke grup', 'category' => 'spam', 'severity' => 'warning'],
            ['word' => 'bagikan ke 10 orang', 'category' => 'spam', 'severity' => 'warning'],
        ];

        foreach ($words as $wordData) {
            BlacklistedWord::firstOrCreate(
                ['word' => $wordData['word']],
                [
                    'category' => $wordData['category'],
                    'severity' => $wordData['severity'],
                    'reason' => 'Default blacklist',
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Seeded ' . count($words) . ' blacklisted words.');
    }
}
