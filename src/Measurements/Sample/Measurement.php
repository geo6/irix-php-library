<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\Measurements\Sample;

class Measurement
{
    public $value;

    /* One of the following required */
    public $nuclide = null;
    public $nuclide_list = null;
    public $nuclide_combination = null;
    public $nuclide_description = null;

    public $uncertainty = null;
    public $timebase = null;
    public $background = null;
    public $measuring_period = null;
    public $reference_date_and_time = null;
    public $apparatus_type = null;
    public $apparatus_type_description = null;
    public $validated = null;
    public $laboratory = null;
    public $description = null;

    private $_xml = null;

    public function __construct()
    {
        $this->value = new \IRIX\Miscellaneous\Value();
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $measurement = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Measurement');
        $this->_xml->appendChild($measurement);
        $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        if (!is_null($this->nuclide)) {
            $nuclide = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Nuclide', $this->nuclide);
            $measurement->appendChild($nuclide);
        }
        if (!is_null($this->nuclide_list)) {
            $nuclide_list = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'NuclideList', implode(' ', $this->nuclide_list));
            $measurement->appendChild($nuclide_list);
        }
        if (!is_null($this->nuclide_combination)) {
            $nuclide_combination = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'NuclideCombination', $this->nuclide_combination);
            $measurement->appendChild($nuclide_combination);
        }
        if (!is_null($this->nuclide_description)) {
            $nuclide_description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'NuclideDescription', $this->nuclide_description);
            $measurement->appendChild($nuclide_description);
        }

        $value = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Value', $this->value->value);
        $measurement->appendChild($value);
        $value->setAttribute('Unit', $this->value->unit);
        if (!is_null($this->value->constraint)) {
            $value->setAttribute('Constraint', $this->value->constraint);
        }

        if (!is_null($this->uncertainty)) {
            $uncertainty = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Uncertainty', $this->uncertainty->uncertainty);
            $measurement->appendChild($uncertainty);
            if (!is_null($this->uncertainty->type)) {
                $uncertainty->setAttribute('Type', $this->uncertainty->type);
            }
            if (!is_null($this->uncertainty->constraint)) {
                $uncertainty->setAttribute('Constraint', $this->uncertainty->constraint);
            }
            $uncertainty->setAttribute('Unit', $this->uncertainty->unit);
        }

        if (!is_null($this->timebase)) {
            $timebase = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Timebase', $this->timebase);
            $measurement->appendChild($timebase);
        }

        if (!is_null($this->background)) {
            $background = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Background');
            $measurement->appendChild($background);

            $value = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Value');
            $background->appendChild($value);
            if (!is_null($this->value->constraint)) {
                $value->setAttribute('Constraint', $this->value->constraint);
            }
            $value->setAttribute('Unit', $this->value->unit);

            if (!is_null($this->background->uncertainty)) {
                $uncertainty = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Uncertainty', $this->background->uncertainty->uncertainty);
                $background->appendChild($uncertainty);
                if (!is_null($this->background->uncertainty->type)) {
                    $uncertainty->setAttribute('Type', $this->background->uncertainty->type);
                }
                if (!is_null($this->background->uncertainty->constraint)) {
                    $uncertainty->setAttribute('Constraint', $this->background->uncertainty->constraint);
                }
                $uncertainty->setAttribute('Unit', $this->background->uncertainty->unit);
            }

            if (!is_null($this->background->timebase)) {
                $timebase = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Timebase', $this->background->timebase);
                $background->appendChild($timebase);
            }
            if (!is_null($this->background->method)) {
                $method = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Method', $this->background->method);
                $background->appendChild($method);
            }
        }

        if (!is_null($this->measuring_period)) {
            $measuring_period = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'MeasuringPeriod');
            $measurement->appendChild($measuring_period);
            $start_time = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'StartTime', $this->sampling_period->start_time);
            $measuring_period->appendChild($start_time);
            $end_time = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'EndTime', $this->sampling_period->end_time);
            $measuring_period->appendChild($end_time);
        }

        if (!is_null($this->reference_date_and_time)) {
            $reference_date_and_time = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ReferenceDateAndTime', $this->reference_date_and_time);
            $measurement->appendChild($reference_date_and_time);
        }
        if (!is_null($this->apparatus_type)) {
            $apparatus_type = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ApparatusType', $this->apparatus_type);
            $measurement->appendChild($apparatus_type);
        }
        if (!is_null($this->apparatus_type_description)) {
            $apparatus_type_description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ApparatusTypeDescription', $this->apparatus_type_description);
            $measurement->appendChild($apparatus_type_description);
        }
        if (!is_null($this->validated)) {
            $validated = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Validated', $this->validated);
            $measurement->appendChild($validated);
        }
        if (!is_null($this->laboratory)) {
            $laboratory = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Laboratory', $this->laboratory);
            $measurement->appendChild($laboratory);
        }
        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Description', $this->description);
            $measurement->appendChild($description);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Measurement')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $measurement = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Nuclide')->item(0);
        if (!is_null($item)) {
            $measurement->nuclide = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'NuclideList')->item(0);
        if (!is_null($item)) {
            $measurement->nuclide_list = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'NuclideCombination')->item(0);
        if (!is_null($item)) {
            $measurement->nuclide_combination = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'NuclideDescription')->item(0);
        if (!is_null($item)) {
            $measurement->nuclide_description = $item->textContent;
        }

        $value = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Value')->item(0);
        $measurement->value->value = $value->textContent;
        $measurement->value->unit = $value->getAttribute('Unit');
        $c = $value->getAttribute('Constraint');
        if (!empty($c)) {
            $measurement->value->constraint = $c;
        }

        $uncertainty = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Uncertainty')->item(0);
        if (!is_null($uncertainty)) {
            $measurement->uncertainty = new \IRIX\Miscellaneous\Uncertainty();
            $measurement->uncertainty->uncertainty = $uncertainty->textContent;
            $measurement->uncertainty->unit = $uncertainty->getAttribute('Unit');
            $t = $uncertainty->getAttribute('Type');
            if (!empty($t)) {
                $measurement->uncertainty->type = $t;
            }
            $c = $uncertainty->getAttribute('Constraint');
            if (!empty($c)) {
                $measurement->uncertainty->constraint = $c;
            }
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Timebase')->item(0);
        if (!is_null($item)) {
            $measurement->timebase = $item->textContent;
        }

        $background = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Background')->item(0);
        if (!is_null($background)) {
            $measurement->background = new \IRIX\Miscellaneous\Background();

            $value = $background->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Value')->item(0);
            $measurement->background->value->value = $value->textContent;
            $measurement->background->value->unit = $value->getAttribute('Unit');
            $c = $value->getAttribute('Constraint');
            if (!empty($c)) {
                $measurement->background->value->constraint = $c;
            }

            $uncertainty = $background->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Uncertainty')->item(0);
            if (!is_null($uncertainty)) {
                $measurement->background->uncertainty = new \IRIX\Miscellaneous\Uncertainty();
                $measurement->background->uncertainty->uncertainty = $uncertainty->textContent;
                $measurement->background->uncertainty->unit = $uncertainty->getAttribute('Unit');
                $t = $uncertainty->getAttribute('Type');
                if (!empty($t)) {
                    $measurement->background->uncertainty->type = $t;
                }
                $c = $uncertainty->getAttribute('Constraint');
                if (!empty($c)) {
                    $measurement->background->uncertainty->constraint = $c;
                }
            }

            $item = $background->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Timebase')->item(0);
            if (!is_null($item)) {
                $measurement->background->timebase = $item->textContent;
            }
            $item = $background->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Method')->item(0);
            if (!is_null($item)) {
                $measurement->background->method = $item->textContent;
            }
        }

        $measuring_period = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'MeasuringPeriod')->item(0);
        if (!is_null($measuring_period)) {
            $measurement->measuring_period = new \IRIX\Miscellaneous\MeasuringPeriod();
            $measurement->measuring_period->start_time = $measuring_period->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'StartTime')->item(0)->textContent;
            $measurement->measuring_period->end_time = $measuring_period->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'EndTime')->item(0)->textContent;
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ReferenceDateAndTime')->item(0);
        if (!is_null($item)) {
            $measurement->reference_date_and_time = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ApparatusType')->item(0);
        if (!is_null($item)) {
            $measurement->apparatus_type = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ApparatusTypeDescription')->item(0);
        if (!is_null($item)) {
            $measurement->apparatus_type_description = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Validated')->item(0);
        if (!is_null($item)) {
            $measurement->validated = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Laboratory')->item(0);
        if (!is_null($item)) {
            $measurement->laboratory = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Description')->item(0);
        if (!is_null($item)) {
            $measurement->description = $item->textContent;
        }

        return $measurement;
    }
}
