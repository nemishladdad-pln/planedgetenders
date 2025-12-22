<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            [
                "name"=> "PAN of Firm/Company",
            ],
            // [
            //     "name"=> "PAN of Proprietor/Partner/Director",
            // ],
            [
                "name"=> "GST registration certificate",
            ],
            // [
            //     "name"=> "Service Tax registration certificate",
            // ],
            // [
            //     "name"=> "LBT registration certificate",
            // ],
            // [
            //     "name"=> "Cancelled cheque of the vendor firm",
            // ],
            [
                "name"=> "Address proof of Firm/Company",
            ],
            [
                "name"=> "Address proof of Proprietor/Partner/Director",
            ],
        ];

        foreach ($inputs as $input) {
            DocumentType::create($input);
        }
    }
}
