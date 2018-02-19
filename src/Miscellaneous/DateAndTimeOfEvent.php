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
 * * \IRIX\EventInformation().
 */
class DateAndTimeOfEvent
{
    public $value;
    public $is_estimate = null;

    public function __construct($datetime, $estimated = null)
    {
        $this->value = gmdate('Y-m-d\TH:i:s\Z', strtotime($datetime));
        $this->is_estimate = $estimated;
    }
}
