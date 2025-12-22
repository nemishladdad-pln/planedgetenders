<?php

namespace Database\Seeders;

use App\Models\SettingType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Site',
            ],
            [
                'name' => 'Payment',
            ],
            [
                'name' => 'SMS',
            ],
            [
                'name' => 'Mailer',
            ],
        ];

        foreach ($types as $type) {
            SettingType::create($type);
        }
    }
}
