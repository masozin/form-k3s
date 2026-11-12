<?php
// app/Support/NumberFormatter.php

namespace App\Support;

class NumberFormatter
{
    public static function format(mixed $value, ?string $pattern): string
    {
        if (!$pattern || $value === null || $value === '' || !is_numeric($value)) {
            return (string) $value;
        }

        $config = self::parsePattern($pattern);
        if (!$config) return (string) $value;

        $num = (float) $value;
        $fixed = $config['decimals'] > 0 ? number_format($num, $config['decimals'], '.', '') : (string) round($num);
        [$intPart, $decPart] = array_pad(explode('.', $fixed), 2, '');

        $groupedInt = $config['thousandSep']
            ? preg_replace('/\B(?=(\d{3})+(?!\d))/', $config['thousandSep'], $intPart)
            : $intPart;

        $result = $groupedInt;
        if ($config['decimals'] > 0) {
            $result .= $config['decimalSep'] . $decPart;
        }

        return "{$config['prefix']}{$result}{$config['suffix']}";
    }

    protected static function parsePattern(string $pattern): ?array
    {
        if (!preg_match('/^([^\d#]*)([#0,.]+)([^\d#]*)$/', $pattern, $m)) {
            return null;
        }

        [, $prefix, $numberPart, $suffix] = $m;

        $lastComma = strrpos($numberPart, ',');
        $lastDot = strrpos($numberPart, '.');
        $lastSepIndex = max($lastComma === false ? -1 : $lastComma, $lastDot === false ? -1 : $lastDot);

        $thousandSep = '';
        $decimalSep = '';
        $decimals = 0;

        if ($lastSepIndex !== -1) {
            $decimalSep = $numberPart[$lastSepIndex];
            $decimals = strlen($numberPart) - $lastSepIndex - 1;
            $thousandSep = $decimalSep === ',' ? '.' : ',';
            $integerPart = substr($numberPart, 0, $lastSepIndex);
            if (!str_contains($integerPart, $thousandSep)) $thousandSep = '';
        } else {
            $thousandSep = str_contains($numberPart, ',') ? ',' : (str_contains($numberPart, '.') ? '.' : '');
        }

        return compact('prefix', 'suffix', 'thousandSep', 'decimalSep', 'decimals');
    }
}
