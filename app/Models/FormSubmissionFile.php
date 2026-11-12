<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FormSubmissionFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'field_id',
        'original_name',
        'path',
        'disk',
        'mime_type',
        'size',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'submission_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'field_id');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function humanSize(): string
    {
        $bytes = $this->size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }

    public function delete(): bool
    {
        Storage::disk($this->disk)->delete($this->path);
        return parent::delete();
    }

    /**
     * Nama file deskriptif untuk export: sekolah-npsn-namafield-kodeacak.ext
     * Dipakai saat file dikumpulkan ke dalam ZIP agar mudah dibedakan.
     */
    public function exportFilename(): string
    {
        $submission = $this->submission;
        $user = $submission?->user;
        $school = $user?->school;

        $schoolName = $school?->name ? \Illuminate\Support\Str::slug($school->name) : 'sekolah';
        $npsn = $school?->npsn ?? 'tanpa-npsn';
        $fieldName = \Illuminate\Support\Str::slug($this->field->name ?? 'file');
        $extension = pathinfo($this->original_name, PATHINFO_EXTENSION);

        // Kode unik pembeda: 6 karakter dari submission_token, cukup untuk membedakan
        // antar submission dari sekolah yang sama tanpa nama file jadi terlalu panjang.
        $uniqueCode = substr($submission->submission_token, 0, 6);

        $filename = "{$schoolName}-{$npsn}-{$fieldName}-{$uniqueCode}";

        return $extension ? "{$filename}.{$extension}" : $filename;
    }
}
