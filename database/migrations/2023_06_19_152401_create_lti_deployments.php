<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lti_deployments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('client_id');
            $table->string('deployment_id');
            $table->string('auth_token_url');
            $table->string('auth_login_url');
            $table->string('keyset_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lti_deployments');
    }
};
