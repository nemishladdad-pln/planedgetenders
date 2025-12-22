<?php

namespace Database\Seeders;

use App\Models\Quarter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuarterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            [
                'name' => 'First Quarter',
                'start_month' => 1,
                'end_month' => 3,
            ],
            [
                'name' => 'Second Quarter',
                'start_month' => 4,
                'end_month' => 6,
            ],
            [
                'name' => 'Third Quarter',
                'start_month' => 7,
                'end_month' => 9,
            ],
            [
                'name' => 'Fourth Quarter',
                'start_month' => 10,
                'end_month' => 12,
            ],
        ];

        foreach ($inputs as $input) {
            Quarter::create($input);
        }

    }
}
