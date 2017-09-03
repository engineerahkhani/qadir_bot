<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);
        Schema::create('stages', function (Blueprint $table) {
            $table->collation = 'utf8mb4_persian_ci';
            $table->charset = 'utf8mb4';
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title')->unique();
            $table->longText('passage');
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
        Schema::dropIfExists('stages');

    }
}
