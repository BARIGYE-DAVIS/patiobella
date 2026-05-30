<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($number)
    {
        $number = round($number, 2);
        $whole = floor($number);
        $cents = round(($number - $whole) * 100);

        $words = self::convertNumberToWords($whole);

        if ($cents > 0) {
            $words .= ' and ' . self::convertNumberToWords($cents) . ' Cents';
        }

        return ucfirst($words);
    }

    private static function convertNumberToWords($number)
    {
        $words = '';

        $units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        if ($number < 20) {
            $words = $units[$number];
        } elseif ($number < 100) {
            $words = $tens[floor($number / 10)] . ($number % 10 ? ' ' . $units[$number % 10] : '');
        } elseif ($number < 1000) {
            $words = $units[floor($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . self::convertNumberToWords($number % 100) : '');
        } elseif ($number < 1000000) {
            $words = self::convertNumberToWords(floor($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . self::convertNumberToWords($number % 1000) : '');
        } elseif ($number < 1000000000) {
            $words = self::convertNumberToWords(floor($number / 1000000)) . ' Million' . ($number % 1000000 ? ' ' . self::convertNumberToWords($number % 1000000) : '');
        } else {
            $words = self::convertNumberToWords(floor($number / 1000000000)) . ' Billion' . ($number % 1000000000 ? ' ' . self::convertNumberToWords($number % 1000000000) : '');
        }

        return $words;
    }
}
