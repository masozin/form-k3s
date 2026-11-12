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
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('form_id')
                ->constrained('forms')
                ->cascadeOnDelete();

            // Nullable karena nanti form bisa saja dibuka
            // untuk pengguna yang belum login.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            Token publik untuk mengidentifikasi submission.
            Sebaiknya jangan menggunakan ID:
            /submission/123
            tetapi misalnya:
            /submission/550e8400-e29b-41d4-a716-446655440000
            */
            $table->uuid('submission_token')->unique();

            /*
            Jawaban non-file disimpan di JSON.
            Contoh:
            {
                "nama_lengkap": "Budi Santoso",
                "nik": "167xxxxxxxxxxx",
                "jenis_kelamin": "L",
                "alamat": "Belitang"
            }
            */
            $table->json('data');

            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->index(['form_id', 'submitted_at']);
            $table->index(['user_id', 'form_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
