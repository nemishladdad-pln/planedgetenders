<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'name' => 'site_name',
                'value' => 'Planedge Contractor Management System',
                'field_type' => 'text',
                'setting_type_id' => 1,
            ],
            [
                'name' => 'site_slogan',
                'value' => 'We Build Better',
                'field_type' => 'text',
                'setting_type_id' => 1,
            ],
            [
                'name' => 'site_url',
                'value' => 'http://localhost:8000',
                'field_type' => 'text',
                'setting_type_id' => 1,
            ],
            [
                'name' => 'site_logo',
                'value' => 'storage/settings/logo.png',
                'field_type' => 'file',
                'setting_type_id' => 1,
            ],
            [
                'name' => 'allow_registration_from_frontend',
                'value' => true,
                'field_type' => 'checkbox',
                'setting_type_id' => 1,
            ],
            [
                'name' => 'basic_contractor_registration_fees',
                'value' => 1000,
                'field_type' => 'text',
                'setting_type_id' => 1,
            ],
            [
                'name' => 'allow_payment_during_contractor_registration',
                'value' => false,
                'field_type' => 'checkbox',
                'setting_type_id' => 1,
            ],
            [
                'name' => 'administration_email_address',
                'value' => 'pallavi@meritest.in',
                'field_type' => 'text',
                'setting_type_id' => 1,
            ],
            [
                'name' => 'payment_method',
                'value' => 'Razor Pay',
                'field_type' => 'text',
                'setting_type_id' => 2,
            ],
            [
                'name' => 'payment_url',
                'value' => 'razorpay.com',
                'field_type' => 'text',
                'setting_type_id' => 2,
            ],
            [
                'name' => 'sms_server',
                'value' => 'smtp.hostinger.in',
                'field_type' => 'text',
                'setting_type_id' => 3,
            ],
            [
                'name' => 'login',
                'value' => 'pallavi@meritest.in',
                'field_type' => 'text',
                'setting_type_id' => 3,
            ],
            [
                'name' => 'password',
                'value' => '123456789',
                'field_type' => 'text',
                'setting_type_id' => 3,
            ],
            [
                'name' => 'mail_server',
                'value' => 'smtp.hostinger.in',
                'field_type' => 'text',
                'setting_type_id' => 4,
            ],
            [
                'name' => 'login',
                'value' => 'pallavi@meritest.in',
                'field_type' => 'text',
                'setting_type_id' => 4,
            ],
            [
                'name' => 'password',
                'value' => '123456789',
                'field_type' => 'text',
                'setting_type_id' => 4,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
