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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('city');
            $table->string('title');
            $table->integer('landArea');
            $table->integer('price');
            $table->integer('bedroom');
            $table->integer('bathroom');
            $table->integer('parking');
            $table->string('longDescreption');
            $table->string('shortDescreption');
            $table->integer('constructionArea');
            $table->integer('livingArea');
            $table->boolean('available')->default(true);
            $table->foreignId('property_listing_id')->constrained('listing_types')->onDelete('cascade');
            $table->foreignId('property_type_id')->constrained('property_types')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
           


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
