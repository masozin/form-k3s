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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();

            // Form dapat dimiliki oleh sekolah tertentu
            $table->foreignId('school_id')
                ->nullable()
                ->constrained('schools')
                ->nullOnDelete();

            // User yang membuat form
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('title');

            // URL form, misalnya:
            // /form/pendaftaran-siswa-baru
            $table->string('slug')->unique();

            $table->text('description')->nullable();

            // Lokasi banner/gambar form
            $table->string('banner_image')->nullable();

            // Periode form dibuka
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();

            // draft, published, closed
            $table->string('status', 20)->default('draft');

            // Apakah satu user boleh mengisi lebih dari satu kali?
            $table->boolean('allow_multiple_submissions')
                ->default(false);

            $table->boolean('require_login')->default(true);
            $table->string('target_role', 30)->nullable()->default('operator');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
