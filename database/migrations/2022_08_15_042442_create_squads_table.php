<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('squads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->boolean('requires_approval')->nullable()->default(0);
            $table->string('country')->nullable();
            $table->string('rank')->nullable();
            $table->unsignedInteger('active_members')->nullable()->default(1);
            $table->text('description')->nullable();
            $table->string('link')->nullable();
            $table->boolean('featured')->nullable()->default(0);
            $table->boolean('verified')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('squads');
    }
};
