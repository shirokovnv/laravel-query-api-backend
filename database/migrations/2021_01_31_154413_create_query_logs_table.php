<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('query_chain_log_id')->nullable();
            $table->string('backend_uuid')->unique();
            $table->string('query')->nullable();
            $table->string('model_class_name')->nullable();
            $table->text('client_query_data')->nullable();
            $table->string('status', 16)->nullable();
            $table->text('error_text')->nullable();
            $table->timestamps();

            $table->foreign('query_chain_log_id')
                ->references('id')
                ->on('query_chain_logs')
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
        Schema::dropIfExists('query_logs');
    }
}
