<?php
namespace App\Helpers;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class NepaliDateHelper {
    public static function convert($date) {
        return LaravelNepaliDate::from($date)->toNepaliDate(format: 'j F Y');
    }
}