<?php

namespace Database\Seeders;

use App\Models\FormContract;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            ['name' => 'Fixed Rate'],
            ['name' => 'Item Rate'],
            ['name' => 'Lump Sum'],
            ['name' => 'Percentage'],
            ['name' => 'Turn Key'],
            ['name' => 'Other'],
        ];

        foreach ($inputs as $input) {
            FormContract::create($input);
        }

    }
}
