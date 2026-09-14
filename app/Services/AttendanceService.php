<?php

namespace App\Services;

use Carbon\Carbon;

class AttendanceService
{
    /**
     * Calculate Haversine distance in meters between two lat/lng coordinates.
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Map timezone string like 'WIB', 'WITA', 'WIT' to PHP timezone identifier.
     */
    public static function getTimezoneIdentifier(?string $zone): string
    {
        return match (strtoupper($zone ?? 'WIB')) {
            'WITA' => 'Asia/Makassar',
            'WIT' => 'Asia/Jayapura',
            default => 'Asia/Jakarta',
        };
    }

    /**
     * Get current Carbon time based on location timezone.
     */
    public static function getNowInTimezone(?string $zone): Carbon
    {
        return Carbon::now(self::getTimezoneIdentifier($zone));
    }

    /**
     * Determine attendance status (Tepat Waktu or Terlambat)
     */
    public static function checkInStatus(string $checkInTime, string $expectedTime): string
    {
        return $checkInTime > $expectedTime ? 'Terlambat' : 'Tepat Waktu';
    }
}
