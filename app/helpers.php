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
