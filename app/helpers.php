<?php

if (!function_exists('ISO8601ToSeconds')) {
    function ISO8601ToSeconds($ISO8601)
    {
        preg_match('/\d{1,2}[H]/', $ISO8601, $hours);
        preg_match('/\d{1,2}[M]/', $ISO8601, $minutes);
        preg_match('/\d{1,2}[S]/', $ISO8601, $seconds);

        $duration = [
            'hours' => $hours ? $hours[0] : 0,
            'minutes' => $minutes ? $minutes[0] : 0,
            'seconds' => $seconds ? $seconds[0] : 0,
        ];

        $hours = intval(substr($duration['hours'], 0, -1));
        $minutes = intval(substr($duration['minutes'], 0, -1));
        $seconds = intval(substr($duration['seconds'], 0, -1));

        $toltalSeconds = ($hours * 60 * 60) + ($minutes * 60) + $seconds;

        return $toltalSeconds;
    }
}

/**
 * Private utility method to convert array of string (integer in string format) to array of int
 *
 * When parameter from AJAX request has array of integer in the JSON, convert the array of string to array integer.
 *
 * @param array $stringArray array containing integer values in string format
 * @return array array containing integers
 */
if (!function_exists('convertStringArrayToIntArray')) {
    function convertStringArrayToIntArray($stringArray)
    {
        $intArray = null;
        if (!empty($stringArray)) {
            $intArray = [];
            foreach ($stringArray as $str) {
                $int = intval($str);
                $intArray[] = $int;
            }
        }
        return $intArray;
    }
}

/**
 * Private utility method to format time from seconds to 00:00:00 format
 *
 * @param integer $seconds
 * @return string time in string format of 00:00:00
 */
if (!function_exists('formatTime')) {
    function formatTime($seconds)
    {
        $hours = floor($seconds / (60 * 60));
        $rest = floor($seconds % (60 * 60));
        $minutes = floor($rest / 60);
        $rest = floor($rest % 60);
        $seconds = floor($rest);
        $millis = floor($rest);
        $time = doubleDigits($hours) . ":" . doubleDigits($minutes) . ":" . doubleDigits($seconds);
        return $time;
    }
}

/**
 * Private utility function to convert number used in time to be double digit (00)
 *
 * @param number $value
 * @return string With 0 at start if single digit number
 */
if (!function_exists('doubleDigits')) {
    function doubleDigits($value)
    {
        $value = (string) $value;
        if ($value <= 9) {
            $value = "0" . $value;
        }
        return $value;
    }
}
