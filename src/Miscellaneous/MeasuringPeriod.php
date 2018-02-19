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
 * * \IRIX\Measurements\Sample\Measurement()
 * * \IRIX\Measurements\DoseRate().
 */
class MeasuringPeriod
{
    public $start_time;
    public $end_time;
}
