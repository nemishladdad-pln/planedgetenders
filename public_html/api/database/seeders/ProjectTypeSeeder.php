<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            ['name' => 'Residential'],
            ['name' => 'Commercial'],
            ['name' => 'Residential and Commercial'],
            ['name' => 'Industrial'],
            ['name' => 'Other'],
        ];

        foreach ($inputs as $input) {
            ProjectType::create($input);
        }
    }
}
