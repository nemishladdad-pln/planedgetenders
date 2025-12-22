<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class BackendUpgradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenders_table_has_new_columns()
    {
        $this->artisan('migrate')->run();

        $this->assertTrue(Schema::hasColumn('tenders', 'subcategory'));
        $this->assertTrue(Schema::hasColumn('tenders', 'budget_type'));
        $this->assertTrue(Schema::hasColumn('tenders', 'budget_amount'));
        $this->assertTrue(Schema::hasColumn('tenders', 'budget_file'));
        $this->assertTrue(Schema::hasColumn('tenders', 'signed_work_order'));
        $this->assertTrue(Schema::hasColumn('tenders', 'due_date'));
    }

    public function test_calendar_endpoint_returns_events()
    {
        $this->artisan('migrate')->run();

        // create a sample tender via DB
        \DB::table('tenders')->insert([
            'title' => 'Test Tender',
            'status' => 'Active',
            'due_date' => now()->addDays(5),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $resp = $this->getJson('/api/calendar');
        $resp->assertStatus(200);
        $this->assertNotEmpty($resp->json());
    }

    public function test_request_otp_endpoint()
    {
        $this->artisan('migrate')->run();

        $resp = $this->postJson('/api/auth/request-otp', ['phone' => '9999999999']);
        $resp->assertStatus(200)->assertJson(['message' => 'otp_sent']);
    }
}
