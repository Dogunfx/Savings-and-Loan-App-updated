<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 7/23/19
 * Time: 3:22 PM
 */

use Modules\Setting\Entities\Setting;

if (!function_exists('determine_period_interest_rate')) {

    /**
     * @param $default_interest_rate
     * @param $repayment_frequency_type
     * @param $interest_rate_type
     * @param int $days_in_year
     * @param int $days_in_month
     * @param int $weeks_in_year
     * @param int $weeks_in_month
     * @return float
     */
    function determine_period_interest_rate($default_interest_rate, $repayment_frequency_type, $interest_rate_type, $repayment_frequency = 1, $days_in_year = 365, $days_in_month = 30, $weeks_in_year = 52, $weeks_in_month = 4)
    {
        $interest_rate = $default_interest_rate;
        if ($repayment_frequency_type == "days") {
            if ($interest_rate_type == 'year') {
                $interest_rate = $interest_rate / $days_in_year;
            }
            if ($interest_rate_type == 'month') {
                $interest_rate = $interest_rate / $days_in_month;
            }
            if ($interest_rate_type == 'week') {
                $interest_rate = $interest_rate / 7;
            }
        }
        if ($repayment_frequency_type == "weeks") {
            if ($interest_rate_type == 'year') {
                $interest_rate = $interest_rate / $days_in_year;
            }
            if ($interest_rate_type == 'month') {
                $interest_rate = $interest_rate / $weeks_in_month;
            }
            if ($interest_rate_type == 'day') {
                $interest_rate = $interest_rate * 7;
            }
        }
        if ($repayment_frequency_type == "months") {
            if ($interest_rate_type == 'year') {
                $interest_rate = $interest_rate / 12;
            }
            if ($interest_rate_type == 'week') {
                $interest_rate = $interest_rate * $weeks_in_month;
            }
            if ($interest_rate_type == 'day') {
                $interest_rate = $interest_rate * $days_in_month;
            }
        }
        if ($repayment_frequency_type == "years") {
            if ($interest_rate_type == 'month') {
                $interest_rate = $interest_rate * 12;
            }
            if ($interest_rate_type == 'week') {
                $interest_rate = $interest_rate * $weeks_in_year;
            }
            if ($interest_rate_type == 'day') {
                $interest_rate = $interest_rate * $days_in_year;
            }
        }
        return $interest_rate * $repayment_frequency / 100;
    }
}
if (!function_exists('determine_amortized_payment')) {

    /**
     * @param $default_interest_rate
     * @param $repayment_frequency_type
     * @param $interest_rate_type
     * @param int $days_in_year
     * @param int $days_in_month
     * @param int $weeks_in_year
     * @param int $weeks_in_month
     * @return float
     */
    function determine_amortized_payment($interest_rate, $balance, $period)
    {

        return ($interest_rate * $balance * pow((1 + $interest_rate), $period)) / (pow((1 + $interest_rate),
                    $period) - 1);
    }
}
if (!function_exists('compare_multi_dimensional_array')) {
    function compare_multi_dimensional_array($array1, $array2)
    {
        $result = array();
        foreach ($array1 as $key => $value) {
            if (!is_array($array2) || !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }
            if (is_array($value)) {
                $recursiveArrayDiff = compare_multi_dimensional_array($value, $array2[$key]);
                if (count($recursiveArrayDiff)) {
                    $result[$key] = $recursiveArrayDiff;
                }
                continue;
            }
            if ($value != $array2[$key]) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
if (!function_exists('generate_loan_reference')) {
    function generate_loan_reference($setting_key = '', $id = '')
    {
        $prefix = '';
        if ($setting_key) {
            if ($setting = Setting::where('setting_key', $setting_key)->first()) {
                $prefix = $setting->setting_value;
            }
        }
        if (strlen($id) < 2) {
            $sequence_number = '00' . $id;
        } elseif (strlen($id) < 3) {
            $sequence_number = '0' . $id;
        } else {
            $sequence_number = $id;
        }
        $random_number = uniqid();
        $reference_format = Setting::where('setting_key', 'core.reference_format')->first()->setting_value;
        if ($reference_format == 'Sequence Number') {
            return $prefix . $sequence_number;
        } elseif ($reference_format == 'Random Number') {
            return $prefix . $random_number;
        } elseif ($reference_format == 'YEAR/Sequence Number (SL/2014/001)') {
            return $prefix . date("Y") . '/' . $sequence_number;
        } elseif ($reference_format == 'YEAR/MONTH/Sequence Number (SL/2014/08/001)') {
            return $prefix . date("Y") . '/' . date("m") . '/' . $sequence_number;
        } else {
            return $id;
        }
    }
}