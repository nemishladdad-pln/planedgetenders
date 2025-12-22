<?php

namespace Database\Seeders;

use App\Models\TenderDocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenderDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inputs = [
            [
                "name"=> "Tender BOQ",
                'allowed_type' => 'Excel',
            ],
            [
                "name"=> "Tender Drawings",
                'allowed_type' => 'Auto Cad',
            ],
            [
                "name"=> "Notice Inviting Tenders",
                'allowed_type' => 'PDF',
            ],
            [
                "name"=> "Guide lining",
                'allowed_type' => 'PDF',
            ],
            [
                "name"=> "Covering Letter",
                'allowed_type' => 'PDF',
            ],
            [
                "name"=> "Articles of agreement",
                'allowed_type' => 'PDF',
            ],
            [
                "name"=> "Tender Document",
                'allowed_type' => 'PDF',
            ],
            [
                "name"=> "Master Checklist",
                'allowed_type' => 'PDF',
            ],
            [
                "name"=> "Documents required for RA Bill",
                'allowed_type' => 'PDF',
            ],
            [
                "name"=> "BOCW",
                'allowed_type' => 'PDF',
            ],
        ];

        foreach ($inputs as $input) {
            TenderDocumentType::create($input);
        }
    }
}
