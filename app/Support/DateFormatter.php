<?php
// app/Support/DateFormatter.php

namespace App\Support;

use Carbon\Carbon;

class DateFormatter
{
    protected static array $monthsId = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];

    protected static array $daysId = [
        0 => 'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
    ];

    public static function format(?string $isoDate, ?string $pattern): string
    {
        if (!$isoDate) return '—';

        $pattern = $pattern ?: 'DD/MM/YYYY';
        $date = Carbon::parse($isoDate);

        $replacements = [
            'YYYY' => $date->year,
            'YY' => substr((string) $date->year, -2),
            'MMMM' => self::$monthsId[(int) $date->month],
            'MMM' => substr(self::$monthsId[(int) $date->month], 0, 3),
            'MM' => str_pad($date->month, 2, '0', STR_PAD_LEFT),
            'DD' => str_pad($date->day, 2, '0', STR_PAD_LEFT),
            'dddd' => self::$daysId[(int) $date->dayOfWeek],
        ];

        // Urutan token panjang ke pendek supaya tidak salah replace
        return preg_replace_callback(
            '/YYYY|YY|MMMM|MMM|MM|DD|dddd/',
            fn($m) => $replacements[$m[0]] ?? $m[0],
            $pattern
        );
    }
}
