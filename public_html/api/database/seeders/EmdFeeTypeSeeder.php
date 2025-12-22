<?php

namespace Database\Seeders;

use App\Models\EmdFeeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmdFeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            ['name' => 'Cheque'],
            ['name' => 'Demand Draft'],
            ['name' => 'Other'],
        ];

        foreach ($inputs as $input) {
            EmdFeeType::create($input);
        }
    }
}
