<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */
namespace IRIX;

/**
 * \IRIX\Measurements\Sample\Measurement()
 */
class Background {
  public $value;
  public $uncertainty = NULL;
  public $timebase = NULL;
  public $method = NULL;

  /**
   *
   */
  public function __construct() {
    $this->value = new \IRIX\Value();
  }
}

/**
 * \IRIX\Locations\Location()
 */
class GeographicCoordinates {
  public $latitude;
  public $longitude;
  public $height = NULL;
}

/**
 * \IRIX\GeographicCoordinates()
 */
class Height {
  public $above;
  public $height;
}

/**
 * \IRIX\Measurements\Sample\Measurement()
 */
class LocationOffset {
  public $distance;
  public $direction;
}

/**
 * \IRIX\Measurements\Sample\Measurement()
 */
class MeasuringPeriod {
  public $start_time;
  public $end_time;
}

/**
 * \IRIX\Measurements\Sample()
 */
class SamplingPeriod {
  public $start_time;
  public $end_time;
}

/**
 * \IRIX\Measurements\Sample\Measurement()
 */
class Uncertainty {
  public $uncertainty;
  public $unit;
  public $type = NULL;
  public $constraint = NULL;
}

/**
 * \IRIX\Measurements\Sample\Measurement()
 */
class Value {
  public $value;
  public $unit;
  public $constraint = NULL;
}
