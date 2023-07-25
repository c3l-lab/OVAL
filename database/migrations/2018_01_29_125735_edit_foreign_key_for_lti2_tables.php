<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditForeignKeyForLti2Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lti2_tool_proxy', function (Blueprint $table) {
            $table->dropForeign(['consumer_pk']);
        });
        Schema::table('lti2_tool_proxy', function (Blueprint $table) {
            $table->foreign('consumer_pk')
                    ->references('consumer_pk')->on('lti2_consumer')
                    ->onDelete('cascade');
        });

        Schema::table('lti2_nonce', function (Blueprint $table) {
            $table->dropForeign(['consumer_pk']);
        });
        Schema::table('lti2_nonce', function (Blueprint $table) {
            $table->foreign('consumer_pk')
                    ->references('consumer_pk')->on('lti2_consumer')
                    ->onDelete('cascade');
        });

        Schema::table('lti2_context', function (Blueprint $table) {
            $table->dropForeign(['consumer_pk']);
        });
        Schema::table('lti2_context', function (Blueprint $table) {
            $table->foreign('consumer_pk')
                    ->references('consumer_pk')->on('lti2_consumer')
                    ->onDelete('cascade');
        });

        Schema::table('lti2_resource_link', function (Blueprint $table) {
            $table->dropForeign(['context_pk']);
            $table->dropForeign(['primary_resource_link_pk']);
            $table->dropForeign(['consumer_pk']);
        });
        Schema::table('lti2_resource_link', function (Blueprint $table) {
            $table->foreign('context_pk')
                    ->references('context_pk')->on('lti2_context')
                    ->onDelete('cascade');
            $table->foreign('primary_resource_link_pk')
                    ->references('resource_link_pk')->on('lti2_resource_link')
                    ->onDelete('cascade');
            $table->foreign('consumer_pk')
                    ->references('consumer_pk')->on('lti2_consumer')
                    ->onDelete('cascade');
        });

        Schema::table('lti2_user_result', function (Blueprint $table) {
            $table->dropForeign(['resource_link_pk']);
        });
        Schema::table('lti2_user_result', function (Blueprint $table) {
            $table->foreign('resource_link_pk')
                    ->references('resource_link_pk')->on('lti2_resource_link')
                    ->onDelete('cascade');
        });

        Schema::table('lti2_share_key', function (Blueprint $table) {
            $table->dropForeign(['resource_link_pk']);
        });
        Schema::table('lti2_share_key', function (Blueprint $table) {
            $table->foreign('resource_link_pk')
                    ->references('resource_link_pk')->on('lti2_resource_link')
                    ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lti2_tool_proxy', function (Blueprint $table) {
            $table->dropForeign(['consumer_pk']);
        });
        Schema::table('lti2_tool_proxy', function (Blueprint $table) {
            $table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
        });

        Schema::table('lti2_nonce', function (Blueprint $table) {
            $table->dropForeign(['consumer_pk']);
        });
        Schema::table('lti2_nonce', function (Blueprint $table) {
            $table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
        });

        Schema::table('lti2_context', function (Blueprint $table) {
            $table->dropForeign(['consumer_pk']);
        });
        Schema::table('lti2_context', function (Blueprint $table) {
            $table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
        });

        Schema::table('lti2_resource_link', function (Blueprint $table) {
            $table->dropForeign(['context_pk']);
            $table->dropForeign(['primary_resource_link_pk']);
            $table->dropForeign(['consumer_pk']);
        });
        Schema::table('lti2_resource_link', function (Blueprint $table) {
            $table->foreign('context_pk')->references('context_pk')->on('lti2_context');
            $table->foreign('primary_resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
            $table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
        });

        Schema::table('lti2_user_result', function (Blueprint $table) {
            $table->dropForeign(['resource_link_pk']);
        });
        Schema::table('lti2_user_result', function (Blueprint $table) {
            $table->foreign('resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
        });

        Schema::table('lti2_share_key', function (Blueprint $table) {
            $table->dropForeign(['resource_link_pk']);
        });
        Schema::table('lti2_share_key', function (Blueprint $table) {
            $table->foreign('resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
        });
    }
}
