<?php

namespace Database\Seeders;

use App\Models\TenderType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            ['name' => 'Limited'],
            ['name' => 'Open Tender'],
            ['name' => 'Restricted'],
            ['name' => 'Other'],
        ];

        foreach ($inputs as $input) {
            TenderType::create($input);
        }
    }
}
