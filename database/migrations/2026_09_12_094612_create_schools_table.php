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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();

            $table->string('npsn', 20)->unique();
            $table->string('name');
            $table->text('address')->nullable();

            // Contoh: SD, SMP, SMA
            $table->string('level', 10)->nullable();

            $table->string('accreditation', 5)->nullable();

            $table->string('principal_name')->nullable();
            $table->string('principal_nip', 30)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
