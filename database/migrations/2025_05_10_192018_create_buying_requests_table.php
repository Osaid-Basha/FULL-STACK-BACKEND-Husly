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
        Schema::create('buying_requests', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->nullable();
            $table->string('type')->nullable();
            $table->date('date')->nullable();

//            $table->unsignedBigInteger('proparty_id')->nullable();
//            $table->unsignedBigInteger('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buying_requests');
    }
};
