<?php
/**
 * Date: 08/08/2018
 * Time: 16:20
 * @author Artur Bartczak <artur.bartczak@code4.pl>
 */

namespace Proexe\BookingApp\Utilities;


class DistanceCalculator
{
    /**
     * @CONST earthRadius
     */
    CONST earthRadius = 6371000;

    /**
     * @param array $from
     * @param array $to
     * @param string $unit - m, km
     *
     * @return mixed
     */
    public function calculate($from, $to, $unit = 'm')
    {
        list($latitudeFrom, $longitudeFrom) = $from;
        list($latitudeTo, $longitudeTo) = $to;

        $unit = strtolower($unit);

        /**
         * Convert from degrees to radians
         */
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        /**
         * Calculate distance [Vincenty formula](https://en.wikipedia.org/wiki/Great-circle_distance#Formulas).
         */
        $lonDelta = $lonTo - $lonFrom;

        $pointA = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);

        $pointB = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($pointA), $pointB);

        /**
         * Return result.
         */
        if ($unit === 'km') {
            $result = floor(($angle * DistanceCalculator::earthRadius) / 1000);
        } else {
            $result = $angle * DistanceCalculator::earthRadius;
        }

        return $result . $unit;
    }

    /**
     * @param array $from
     * @param array $offices
     *
     * @return array
     */
    public function findClosestOffice($from, $offices)
    {
        $data = [];
        $officeName = [];
        foreach ($offices as $item) {
            $data[$item['id']] = (int)substr_replace(
                $this->calculate($from, [$item['lat'], $item['lng']], 'km'),
                "",
                -2
            );
            $officeName[$item['id']] = $item['name'];
        }
        asort($data);
        $id = key($data);

        return $officeName[$id];
    }

    /**
     * DELIMITER $$
     * DROP FUNCTION IF EXISTS vincenty$$
     * CREATE FUNCTION vincenty(
     * lat1 FLOAT,
     * lon1 FLOAT,
     * lat2 FLOAT,
     * lon2 FLOAT
     * ) RETURNS FLOAT
     * NO SQL
     * DETERMINISTIC
     * COMMENT 'Returns the distance in degrees on the
     * Earth between two known points
     * of latitude and longitude
     * using the Vincenty formula
     * from http://en.wikipedia.org/wiki/Great-circle_distance'
     * BEGIN
     * RETURN ATAN2(
     * SQRT(
     * POW(COS(RADIANS(lat2))*SIN(RADIANS(lon2-lon1)),2) +
     * POW(COS(RADIANS(lat1))*SIN(RADIANS(lat2)) -
     * (SIN(RADIANS(lat1))*COS(RADIANS(lat2)) *
     * COS(RADIANS(lon2-lon1))) ,2)
     * ),
     * SIN(RADIANS(lat1))*SIN(RADIANS(lat2)) +
     * COS(RADIANS(lat1))*COS(RADIANS(lat2))*COS(RADIANS(lon2-lon1))
     * ) * 6371000;
     * END$$
     * DELIMITER ;
     *
     * SELECT FLOOR(vincenty(14.12232322, 8.12232322, off.lat, off.lng) / 1000) as distance,
     * off.name FROM offices as off
     * ORDER BY distance
     * LIMIT 1;
     *
     */
}