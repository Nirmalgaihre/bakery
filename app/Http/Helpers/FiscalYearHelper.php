<?php

namespace App\Helpers;

class FiscalYearHelper
{
    public static function getFiscalYear($bsDate) 
    {
        // $bsDate को format "YYYY-MM-DD" हुनुपर्छ
        $parts = explode('-', $bsDate);
        if (count($parts) < 2) return '';

        $year = (int)$parts[0];
        $month = (int)$parts[1];

        // श्रावण (०४) वा सोभन्दा पछि: चालु वर्ष/आगामी वर्ष (जस्तै २०८२/८३)
        if ($month >= 4) {
            $nextYear = substr($year + 1, 2);
            return "{$year}/{$nextYear}";
        } 
        // वैशाख, जेठ, असार (०१, ०२, ०३): अघिल्लो वर्ष/चालु वर्ष (जस्तै २०८२/८३)
        else {
            $prevYear = $year - 1;
            $currYear = substr($year, 2);
            return "{$prevYear}/{$currYear}";
        }
    }
}