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
 * * \IRIX\Measurements\DoseRate\Measurement()
 * * \IRIX\Measurements\Sample\Measurement().
 */
class Value
{
    public $value;
    public $unit;
    public $constraint = null;

    public function __construct($value = null, $unit = null)
    {
        if (!is_null($value)) {
            $this->value = $value;
        }
        if (!is_null($unit)) {
            $this->unit = $unit;
        }
    }
}
