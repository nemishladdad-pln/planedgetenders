<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TenderMaterialWork>
 */
class TenderMaterialWorkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => "Earth Work",
            "description"=> "Backfilling in plinth, floors and other areas with non compatible granular soil (murrum of approved quality) using approved material from cutting on site  in layers of not more than 23cm in thickness before compaction, including levelling, watering and compaction by mechanical means or manually  to  95% of Standard Proctor density and as directed by the Site Engineer Incharge etc complete.",
            "unit"=> 'cum',
            "quantity"=> "9730.5",
        ];
    }
}
