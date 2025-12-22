<?php

namespace Database\Seeders;

use App\Models\PreBidMeetingPlace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PreBidMeetingPlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            ['name' => 'Planedege Office'],
            ['name' => 'Site Office (Project Location)'],
            ['name' => 'Builder Office (Builder Registered Address)'],
        ];

        foreach ($inputs as $input) {
            PreBidMeetingPlace::create($input);
        }
    }
}
