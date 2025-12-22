<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectRoleTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('project_role_assignments')) {
            Schema::create('project_role_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id')->index();
                $table->unsignedBigInteger('role_id')->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                // lightweight foreign keys if tables exist
                // Do not enforce constraints to avoid deployment issues
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_role_assignments');
    }
}
