<?php
// app/Exports/FormSubmissionsExport.php

namespace App\Exports;

use App\Models\Form;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Support\NumberFormatter;
use App\Support\DateFormatter;

class FormSubmissionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $answerableFields;

    public function __construct(protected Form $form)
    {
        // Semua field yang punya jawaban (bukan heading/paragraph), urut sesuai tampilan form
        $this->answerableFields = $form->fields->merge($form->sections->flatMap->fields)
            ->whereNotIn('type', ['heading', 'paragraph'])
            ->sortBy('sort_order')
            ->values();
    }

    public function collection()
    {
        return $this->form->submissions()
            ->with('user.school', 'files.field')
            ->latest('submitted_at')
            ->get();
    }

    public function title(): string
    {
        // Nama sheet Excel maksimal 31 karakter, potong kalau judul form panjang
        return substr($this->form->title, 0, 31);
    }

    public function headings(): array
    {
        $headings = ['No', 'Nama Pengisi', 'Email', 'Sekolah', 'Waktu Pengisian'];

        foreach ($this->answerableFields as $field) {
            $headings[] = $field->label;
        }

        return $headings;
    }

    public function map($submission): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $row = [
            $rowNumber,
            $submission->user?->name ?? '—',
            $submission->user?->email ?? '—',
            $submission->user?->school?->name ?? '—',
            $submission->submitted_at?->translatedFormat('d M Y H:i') ?? '—',
        ];

        foreach ($this->answerableFields as $field) {
            $row[] = $this->formatAnswer($field, $submission);
        }

        return $row;
    }

    protected function formatAnswer($field, $submission): string
    {
        if ($field->type === 'file') {
            $file = $submission->files->firstWhere('field_id', $field->id);
            return $file ? $file->url() : '—';
        }

        $value = $submission->data[$field->name] ?? null;

        if ($field->type === 'date' && $value) {
            return DateFormatter::format($value, $field->config['date_format'] ?? null);
        }

        if ($field->type === 'number' && $value !== null) {
            return NumberFormatter::format($value, $field->config['number_format'] ?? null);
        }

        if (is_array($value)) return $value ? implode(', ', $value) : '—';

        return $value !== null && $value !== '' ? (string) $value : '—';
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 5, 'B' => 22, 'C' => 26, 'D' => 26, 'E' => 20];

        // Kolom jawaban dinamis, mulai dari F
        $col = 'F';
        foreach ($this->answerableFields as $field) {
            $widths[$col] = 28;
            $col++;
        }

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // Header: bold, background brand-ish, teks putih
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial', 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Seluruh isi tabel: font Arial, border tipis
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E4E4E7']],
            ],
        ]);

        // Kolom "No" rata tengah
        $sheet->getStyle("A2:A{$lastRow}")->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->freezePane('A2');

        return [];
    }
}
