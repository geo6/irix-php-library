<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\Miscellaneous;

/**
 * Used by :
 * * \IRIX\Locations\Location().
 */
class GeographicCoordinates
{
    public $latitude;
    public $longitude;
    public $height = null;

    public function __construct($lat, $lng, $height = null)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->height = $height;
    }
}
