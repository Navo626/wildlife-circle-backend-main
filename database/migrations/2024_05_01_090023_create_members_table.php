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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('honorary_title');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('category')->nullable();
            $table->string('image_path')->nullable();
            $table->string('email')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_researchgate')->nullable();
            $table->string('social_scholar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
