<?php
// app/Services/FormFileExportService.php

namespace App\Services;

use App\Models\Form;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class FormFileExportService
{
    /**
     * Membuat ZIP berisi semua file upload dari seluruh submission form ini.
     * File ditata per folder sekolah, dengan nama deskriptif per file.
     *
     * @return string Path absolut file ZIP yang dibuat (sementara, di storage/app/tmp)
     */
    public function export(Form $form): string
    {
        $files = $form->submissions()
            ->with('files.field', 'user.school')
            ->get()
            ->flatMap->files
            ->filter(fn($file) => Storage::disk($file->disk)->exists($file->path));

        $zipFilename = Str::slug($form->slug) . '-files-' . now()->format('Ymd-His') . '.zip';
        $zipPath = storage_path("app/tmp/{$zipFilename}");

        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($files->isEmpty()) {
            // Tetap buat ZIP kosong dengan catatan, supaya tidak error di sisi controller
            $zip->addFromString('tidak-ada-file.txt', 'Belum ada file yang diunggah untuk form ini.');
            $zip->close();
            return $zipPath;
        }

        foreach ($files as $file) {
            $school = $file->submission?->user?->school;
            $folderName = $school ? Str::slug($school->name) : 'tanpa-sekolah';

            $fullPath = Storage::disk($file->disk)->path($file->path);
            $zip->addFile($fullPath, "{$folderName}/{$file->exportFilename()}");
        }

        $zip->close();

        return $zipPath;
    }
}
