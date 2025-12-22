<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            [
                'name' => 'A',
                'min' => 500,
                'max' => 15500
            ],
            [
                'name' => 'B',
                'min' => 200,
                'max' => 500
            ],
            [
                'name' => 'C',
                'min' => 100,
                'max' => 200
            ],
        ];
        foreach ($inputs as $input) {
            Grade::create($input);
        }
    }
}
