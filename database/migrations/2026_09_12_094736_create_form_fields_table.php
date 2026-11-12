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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')
                ->constrained('forms')
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('form_sections')
                ->cascadeOnDelete();

            // Contoh:
            // text, textarea, number, select, radio, checkbox,
            // date, email, file, heading, paragraph
            $table->string('type', 30);

            // Nama internal field.
            // Contoh: nama_lengkap, email, kelas
            $table->string('name');

            // Label yang ditampilkan kepada pengguna
            $table->string('label');

            // Penjelasan tambahan di bawah label
            $table->text('description')->nullable();

            /*
            Semua konfigurasi tambahan disimpan di JSON.

            Contoh:
            {
                "placeholder": "Masukkan nama lengkap",
                "help_text": "Sesuai kartu keluarga",
                "options": [
                    {"label": "Laki-laki", "value": "L"},
                    {"label": "Perempuan", "value": "P"}
                ],
                "validation": {
                    "min_length": 3,
                    "max_length": 100
                },
                "appearance": {
                    "width": "full"
                }
            }
            */
            $table->json('config')->nullable();

            // Required dibuat sebagai kolom karena merupakan
            // properti umum hampir semua field.
            $table->boolean('is_required')->default(false);

            // Urutan field di dalam form
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // Nama field tidak boleh sama dalam satu form
            $table->unique(['form_id', 'name']);

            $table->index(['form_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
