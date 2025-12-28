<?php

if (!function_exists('shouldRunCronJob')) {
    function shouldRunCronJob(string $cronExpression, $now = null): bool
    {
        if (empty($cronExpression)) {
            return false;
        }

        if ($now === null) {
            $now = now();
        }

        $parts = array_filter(explode(' ', trim($cronExpression)), function($part) {
            return $part !== '';
        });
        
        if (count($parts) !== 5) {
            return false;
        }

        [$minute, $hour, $day, $month, $dayOfWeek] = $parts;

        $currentMinute = (int) $now->format('i');
        $currentHour = (int) $now->format('G');
        $currentDay = (int) $now->format('j');
        $currentMonth = (int) $now->format('n');
        $currentDayOfWeek = (int) $now->format('w');

        $matchesMinute = $minute === '*' || (int) $minute === $currentMinute;
        $matchesHour = $hour === '*' || (int) $hour === $currentHour;
        $matchesDay = $day === '*' || (int) $day === $currentDay;
        $matchesMonth = $month === '*' || (int) $month === $currentMonth;
        $matchesDayOfWeek = $dayOfWeek === '*' || (int) $dayOfWeek === $currentDayOfWeek;

        return $matchesMinute && $matchesHour && $matchesDay && $matchesMonth && $matchesDayOfWeek;
    }
}

