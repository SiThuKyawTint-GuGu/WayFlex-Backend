<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = ['English (UK)', 'Myanmar'];

        foreach ($languages as $language) {
            Language::create([
                'name' => ucfirst($language),
            ]);
        }
    }
}
