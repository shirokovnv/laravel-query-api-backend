<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueryChainLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query_chain_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_request_id')->unique();
            $table->text('client_query_data')->nullable();
            $table->string('ip', 64)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('query_mode', 16);
            $table->string('status', 16)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('query_chain_logs');
    }
}
