<?php

namespace Database\Seeders;

use App\Models\TenderCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenderCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            ['name' => 'Material'],
            ['name' => 'Labour'],
            ['name' => 'Labour and Material'],
        ];

        foreach ($inputs as $input) {
            TenderCategory::create($input);
        }
    }
}
