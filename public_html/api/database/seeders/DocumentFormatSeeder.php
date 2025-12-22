<?php

namespace Database\Seeders;

use App\Models\DocumentFormat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentFormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            ['name' => 'Excel'],
            ['name' => 'Auto Cad'],
            ['name' => 'PDF'],
        ];

        foreach ($inputs as $input) {
            DocumentFormat::create($input);
        }
    }
}
