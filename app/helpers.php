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

if (!function_exists('convertStringArrayToIntArray')) {
    /**
     * Private utility method to convert array of string (integer in string format) to array of int
     *
     * When parameter from AJAX request has array of integer in the JSON, convert the array of string to array integer.
     *
     * @param array $stringArray array containing integer values in string format
     * @return array array containing integers
     */
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
