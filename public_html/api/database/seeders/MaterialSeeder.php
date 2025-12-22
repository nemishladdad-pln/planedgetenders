<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            ['name' => 'Labour With Material'],
            ['name' => 'Part Material'],
            ['name' => 'Labour Only'],
        ];
        foreach ($inputs as $input) {
            Material::create($input);
        }
    }
}
