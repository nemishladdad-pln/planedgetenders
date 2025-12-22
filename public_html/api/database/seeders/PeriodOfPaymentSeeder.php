<?php

namespace Database\Seeders;

use App\Models\PeriodOfPayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodOfPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            ['name' => 'Payment Within 15 days'],
            ['name' => 'Payment Between 15-30 days'],
            ['name' => 'Payment after 30 days & above'],
        ];

        foreach ($inputs as $input) {
            PeriodOfPayment::create($input);
        }
    }
}
