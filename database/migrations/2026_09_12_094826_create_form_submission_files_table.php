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
        Schema::create('form_submission_files', function (Blueprint $table) {
            $table->id();

            // Submission yang memiliki file ini
            $table->foreignId('submission_id')
                ->constrained('form_submissions')
                ->cascadeOnDelete();

            // Field upload yang digunakan
            $table->foreignId('field_id')
                ->constrained('form_fields')
                ->cascadeOnDelete();

            // Nama asli file dari pengguna
            // Contoh: Kartu_Keluarga.pdf
            $table->string('original_name');

            // Lokasi file di storage
            // Contoh: forms/2026/abc123.pdf
            $table->string('path');

            // Storage disk yang digunakan
            $table->string('disk')->default('public');

            // Contoh: application/pdf, image/jpeg
            $table->string('mime_type')->nullable();

            // Ukuran file dalam byte
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();

            $table->index(['submission_id', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submission_files');
    }
};
