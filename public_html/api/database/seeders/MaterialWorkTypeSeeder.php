<?php

namespace Database\Seeders;

use App\Models\MaterialWorkType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialWorkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workTypes = [
            [
                'name' => 'Earth Work',
                'code' => '01',
                'grade_A' => '50000000+',
                'grade_B' => '50000000-30000000',
                'grade_C' => '30000000-0',
            ],
            [
                'name' => 'RCC Work',
                'code' => '02',
                'grade_A' => '500000000+',
                'grade_B' => '500000000-200000000',
                'grade_C' => '200000000-0',
            ],
            [
                'name' => 'Masonry, Plaster Work',
                'code' => '03',
                'grade_A' => '50000000+',
                'grade_B' => '50000000-30000000',
                'grade_C' => '30000000-0',
            ],
            [
                'name' => 'Waterproofing Work',
                'code' => '04',
                'grade_A' => '50000000+',
                'grade_B' => '50000000-30000000',
                'grade_C' => '30000000-0',
            ],
            [
                'name' => 'Doors & Wooden Works',
                'code' => '05',
                'grade_A' => '50000000+',
                'grade_B' => '50000000-30000000',
                'grade_C' => '30000000-0',
            ],
            [
                'name' => 'Windows & Sliding Doors',
                'code' => '06',
                'grade_A' => '50000000+',
                'grade_B' => '50000000-30000000',
                'grade_C' => '30000000-0',
            ],
            [
                'name' => 'Flooring and Tiling works',
                'code' => '07',
                'grade_A' => '100000000+',
                'grade_B' => '100000000-51000000',
                'grade_C' => '50000000-0',
            ],
            [
                'name' => 'MS & SS  Works - Grills & Railings',
                'code' => '08',
                'grade_A' => '50000000+',
                'grade_B' => '50000000-30000000',
                'grade_C' => '30000000-0',
            ],
            [
                'name' => 'Painting & Polishing Works',
                'code' => '09',
                'grade_A' => '50000000+',
                'grade_B' => '50000000-30000000',
                'grade_C' => '30000000-0',
            ],
            [
                'name' => 'Plumbing, Drainage Work',
                'code' => '10',
                'grade_A' => '50000000+',
                'grade_B' => '50000000-30000000',
                'grade_C' => '30000000-0',
            ],
            [
                'name' => 'Electrical Work',
                'code' => '11',
                'grade_A' => '100000000+',
                'grade_B' => '100000000-51000000',
                'grade_C' => '50000000-0',
            ],
            [
                'name' => 'Lift works',
                'code' => '12',
                'grade_A' => '500000000+',
                'grade_B' => '500000000-200000000',
                'grade_C' => '200000000-0',
            ],
            [
                'name' => 'Buildings Fire Fighting Work',
                'code' => '13',
                'grade_A' => '100000000+',
                'grade_B' => '100000000-51000000',
                'grade_C' => '50000000-0',
            ],
            [
                'name' => 'Elevation, Glazing, Facade Work',
                'code' => '14',
                'grade_A' => '100000000+',
                'grade_B' => '100000000-51000000',
                'grade_C' => '50000000-0',
            ],
            [
                'name' => 'Facade Work',
                'code' => '15',
                'grade_A' => '500000000+',
                'grade_B' => '500000000-200000000',
                'grade_C' => '200000000-0',
            ],
            [
                'name' => 'Misc, Dep. Labour, Cleaning',
                'code' => '16',
                'grade_A' => '10000000+',
                'grade_B' => '10000000-5000000',
                'grade_C' => '5000000-0',
            ],
            [
                'name' => 'Building Amenities',
                'code' => '17',
                'grade_A' => '10000000+',
                'grade_B' => '10000000-5000000',
                'grade_C' => '5000000-0',
            ],
            [
                'name' => 'Core and Shell',
                'code' => '18',
                'grade_A' => '500000000+',
                'grade_B' => '500000000-200000000',
                'grade_C' => '200000000-0',

            ]
        ];

        foreach ($workTypes as $workType) {
            MaterialWorkType::create($workType);
        }

    }
}
