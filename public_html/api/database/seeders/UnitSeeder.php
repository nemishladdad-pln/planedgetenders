<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            [
                'name' => 'Cubic Meter',
                'code' => 'Cum',
            ],
            [
                'name' => 'Kilo Gram',
                'code' => 'Kg',
            ],
            [
                'name' => 'Square Meter',
                'code' => 'Sqm',
            ],
        ];

        foreach ($inputs as $input) {
            Unit::create($input);
        }
    }
}
