<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionRoleUserSeeder::class,
            SettingTypeSeeder::class,
            SettingSeeder::class,
            MaterialWorkTypeSeeder::class,
            ProjectTypeSeeder::class,
            TenderTypeSeeder::class,
            FormContractSeeder::class,
            TenderCategorySeeder::class,
            EmdFeeTypeSeeder::class,
            PreBidMeetingPlaceSeeder::class,
            UnitSeeder::class,
            DocumentFormatSeeder::class,
            TenderDocumentTypeSeeder::class,
            DocumentTypeSeeder::class,
            MaterialSeeder::class,
            GradeSeeder::class,
            CategoryRatingSeeder::class,
            QuarterSeeder::class,
            PeriodOfPaymentSeeder::class,
        ]);
    }
}
