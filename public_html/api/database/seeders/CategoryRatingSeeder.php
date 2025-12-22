<?php

namespace Database\Seeders;

use App\Models\CategoryRating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            [
                'name' => 'Planning and Execution',
                'description' => 'Does Contractor Perform work as per drawing / specifications?',
                'parent_id' => null,
            ],
            [
                'name' => 'Knowledge',
                'description' => 'Is Contractor having Knowledge of work as per  IS Standard / Methodology?',
                'parent_id' => null,
            ],
            [
                'name' => 'Quality',
                'description' => null,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Quality',
                        'description' => 'Is Contractor having good Workmanship ?'
                    ],
                    [
                        'name' => 'Quality',
                        'description' => 'Is contractor good in maintaining records?'
                    ],
                    [
                        'name' => 'Quality',
                        'description' => 'How proactively does Contractor Plan work flow?'
                    ],
                    [
                        'name' => 'Quality',
                        'description' => 'How is the house keeping and cleanliness of contractor on site?'
                    ],
                    [
                        'name' => 'Quality',
                        'description' => "How does Contractor respond to Quality NC's and does contractor close NC's within the identified time-frame?"
                    ]
                ],
            ],
            [
                'name' => 'Documentation & Compliance',
                'description' => "How is Contractor's Compliance with respect to Legal /Contract Documents? (PF, ESIC, Insurance and GST",
                'parent_id' => null,
            ],
            [
                'name' => 'Timely Performance',
                'description' => null,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Timely Performance',
                        'description' => "Does Contractor complete work in time as per commitment?",
                    ],
                    [
                        'name' => 'Timely Performance',
                        'description' => "Does Contractor raise indents based on lead times?",
                    ],
                    [
                        'name' => 'Timely Performance',
                        'description' => "How is the Quality of  Contractor's Tools, Tackles, scaffolding, shuttering material on site?",
                    ],
                ]
            ],
            [
                'name' => 'Effectiveness of Management',
                'description' => null,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Effectiveness of Management',
                        'description' => "How is Contractor's Co-operation/Communication with site in-charge?",
                    ],
                    [
                        'name' => 'Effectiveness of Management',
                        'description' => "Are the labours of contractor available as per the requirement of site & Does the contractor have control over his labours?",
                    ],
                    [
                        'name' => 'Effectiveness of Management',
                        'description' => "Does Contractor attend Project Meetings as required?",
                    ],
                    [
                        'name' => 'Effectiveness of Management',
                        'description' => "Is Project team of Contractor Technically qualified with required work experience?",
                    ],
                    [
                        'name' => 'Effectiveness of Management',
                        'description' => "Does contractor have all calibrated equipments on site?",
                    ],
                ]
            ],
            [
                'name' => 'Compliance With Safety Standards',
                'description' => null,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Compliance With Safety Standards',
                        'description' => 'Does Contractor implement safety rules & regulations on site?'
                    ],
                    [
                        'name' => 'Compliance With Safety Standards',
                        'description' => "How does Contractor respond  to safety NC's and does contractor close NC's within the identified time-frame?"
                    ],
                    [
                        'name' => 'Compliance With Safety Standards',
                        'description' => "Does Contractor take Efforts in Maintaining zero accident culture on site?"
                    ],
                ]
            ],
            [
                'name' => 'Accuracy of billing',
                'description' => 'How accurate is contractor in his Billing?',
                'parent_id' => null,
            ]
        ];

        foreach ($inputs as $input) {
            $inputArr = [
                'name' => $input['name'],
                'description' => $input['description'],
                'parent_id' => null,
            ];
            $parentCategoryRating = CategoryRating::create($inputArr);
            if (isset($input['children'])) {
                foreach ($input['children'] as $child) {
                    $inputArr = [
                        'name' => $child['name'],
                        'description' => $child['description'],
                        'parent_id' => $parentCategoryRating->id,
                    ];
                    CategoryRating::create($inputArr);
                }
            }
        }
    }
}
