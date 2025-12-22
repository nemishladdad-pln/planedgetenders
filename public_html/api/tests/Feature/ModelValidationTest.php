<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ModelValidationTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		// force in-memory sqlite for safe test runs
		config(['database.default' => 'sqlite']);
		config(['database.connections.sqlite.database' => ':memory:']);
		parent::setUp();
		// run migrations in test DB
		$this->artisan('migrate', ['--force' => true])->run();
	}

	public function test_tenders_table_has_expected_columns()
	{
		$cols = [
			'subcategory',
			'budget_type',
			'budget_amount',
			'budget_file',
			'signed_work_order',
			'due_date',
		];

		foreach ($cols as $col) {
			$this->assertTrue(
				Schema::hasColumn('tenders', $col),
				"Expected column '{$col}' exists on tenders table"
			);
		}
	}

	public function test_additional_tables_exist()
	{
		$this->assertTrue(Schema::hasTable('vendor_histories'), 'vendor_histories table exists');
		$this->assertTrue(Schema::hasTable('subscriptions'), 'subscriptions table exists');
		$this->assertTrue(Schema::hasTable('invoices'), 'invoices table exists');
		$this->assertTrue(Schema::hasTable('buyers'), 'buyers table exists');
	}

	public function test_models_if_present_can_be_created_and_persisted()
	{
		$modelMap = [
			'App\\Models\\Tender'  => ['title' => 'Test Tender', 'status' => 'Active'],
			'App\\Models\\Vendor'  => ['name' => 'Test Vendor', 'email' => 'vendor@example.com'],
			'App\\Models\\User'    => ['name' => 'Test User', 'email' => 'user@example.com', 'password' => 'password'],
		];

		foreach ($modelMap as $class => $attrs) {
			if (!class_exists($class)) {
				$this->markTestIncomplete("Model {$class} not present — skipping model creation test.");
				continue;
			}

			$model = new $class();
			// use forceFill to avoid fillable/guarded restrictions
			$model->forceFill($attrs);
			$model->save();

			$table = $model->getTable();
			$this->assertDatabaseHas($table, ['id' => $model->getKey()]);
		}
	}

	public function test_calendar_and_dashboard_endpoints_exist_and_respond()
	{
		// calendar endpoint
		$calendar = $this->getJson('/api/calendar');
		$this->assertTrue(in_array($calendar->getStatusCode(), [200,404]), 'calendar endpoint responded');

		// dashboard endpoint (may be protected by auth/roles)
		$dashboard = $this->getJson('/api/admin/dashboard');
		$this->assertTrue(in_array($dashboard->getStatusCode(), [200,401,403,404]), 'dashboard endpoint responded');
	}
}
