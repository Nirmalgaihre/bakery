<?php

namespace App\Helpers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class FiscalYearHelper
{
    public static function getCurrentFiscalYear()
    {
        // आजको मिति लिने (Nepali Date मा)
        $today = LaravelNepaliDate::from(date('Y-m-d'))->toNepaliDate(format: 'Y-m-d');
        return self::getFiscalYearFromDate($today);
    }

    public static function getFiscalYearFromDate($bsDate) 
    {
        $parts = explode('-', $bsDate);
        $year = (int)$parts[0];
        $month = (int)$parts[1];

        // नेपाली महिना ४ (श्रावण) देखि नयाँ आर्थिक वर्ष सुरु हुन्छ
        if ($month >= 4) {
            return $year . '/' . (substr($year + 1, 2));
        } else {
            return ($year - 1) . '/' . (substr($year, 2));
        }
    }

    // सबै सम्भावित आर्थिक वर्षहरूको सूची बनाउने (फिल्टरको लागि)
    public static function getFiscalYearList()
    {
        $currentFiscalYear = self::getCurrentFiscalYear();
        $currentYear = (int) explode('/', $currentFiscalYear)[0];
        $list = [];
        for ($i = 0; $i < 5; $i++) {
            $y = $currentYear - $i;
            $list[] = $y . '/' . (substr($y + 1, 2));
        }
        return $list;
    }

    public static function getFiscalYearDateRange(?string $fiscalYear = null): array
    {
        $fiscalYear = $fiscalYear ?: self::getCurrentFiscalYear();
        $startYear = self::getFiscalYearStartYear($fiscalYear);
        $endYear = $startYear + 1;

        $bsStart = $startYear . '-04-01';
        $bsEnd = $endYear . '-03-' . self::getLastDayOfNepaliMonth($endYear, 3);

        return [
            'fiscal_year' => $startYear . '/' . substr((string) $endYear, 2),
            'bs_start' => $bsStart,
            'bs_end' => $bsEnd,
            'ad_start' => self::bsToAd($bsStart),
            'ad_end' => self::bsToAd($bsEnd),
        ];
    }

    public static function getFiscalYearStartYear(string $fiscalYear): int
    {
        return (int) explode('/', $fiscalYear)[0];
    }

    public static function bsToAd(string $bsDate): string
    {
        return LaravelNepaliDate::from($bsDate, 'Y-m-d', 'np')->toEnglishDate('Y-m-d');
    }

    public static function getLastDayOfNepaliMonth(int $year, int $month): int
{
    // Start from 32 and work downwards to find the first valid date
    for ($day = 32; $day >= 28; $day--) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

        // Use a try-catch block to prevent the exception from crashing the app
        try {
            if (LaravelNepaliDate::validateNepali($date, 'Y-m-d')) {
                return $day;
            }
        } catch (\Exception $e) {
            // If invalid, continue to the next day
            continue;
        }
    }

    return 28; // Fallback
}
}
