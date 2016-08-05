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
 * Used by :
 * * \IRIX\Measurements\DoseRate\Measurement()
 * * \IRIX\Measurements\Sample\Measurement()
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
 * Used by :
 * * \IRIX\EventInformation()
 */
class DateAndTimeOfEvent {
  public $value;
  public $is_estimate = NULL;

  /**
   *
   */
  public function __construct($datetime, $estimated = NULL) {
    $this->value = gmdate('Y-m-d\TH:i:s\Z', strtotime($datetime));
    $this->is_estimate = $estimated;
  }
}

/**
 * Used by :
 * * \IRIX\Locations\Location()
 */
class GeographicCoordinates {
  public $latitude;
  public $longitude;
  public $height = NULL;

  /**
   *
   */
  public function __construct($lat, $lng, $height = NULL) {
    $this->latitude = $lat;
    $this->longitude = $lng;
    $this->height = $height;
  }
}

/**
 * Used by :
 * * \IRIX\GeographicCoordinates()
 */
class Height {
  public $above;
  public $value;
}

/**
 * Used by :
 * * \IRIX\Measurements\DoseRate\Measurement()
 * * \IRIX\Measurements\Sample()
 */
class LocationOffset {
  public $distance;
  public $direction;
}

/**
 * Used by :
 * * \IRIX\Measurements\Sample\Measurement()
 * * \IRIX\Measurements\DoseRate()
 */
class MeasuringPeriod {
  public $start_time;
  public $end_time;
}

/**
 * Used by :
 * * \IRIX\Measurements\Sample()
 */
class SamplingDepth {
  public $min_depth;
  public $max_depth;
}

/**
 * Used by :
 * * \IRIX\Measurements\Sample()
 */
class SamplingPeriod {
  public $start_time;
  public $end_time;
}

/**
 * Used by :
 * * \IRIX\Measurements\DoseRate\Measurement()
 * * \IRIX\Measurements\Sample\Measurement()
 */
class Uncertainty {
  public $value;
  public $unit;
  public $type = NULL;
  public $constraint = NULL;
}

/**
 * Used by :
 * * \IRIX\Measurements\DoseRate\Measurement()
 * * \IRIX\Measurements\Sample\Measurement()
 */
class Value {
  public $value;
  public $unit;
  public $constraint = NULL;
}
