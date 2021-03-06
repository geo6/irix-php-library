<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\Measurements\DoseRate;

class Measurement
{
    public $value;
    public $location;

    public $location_offset = null;
    public $uncertainty = null;
    public $timebase = null;
    public $background = null;
    public $validated = null;
    public $description = null;

    private $_xml = null;

    public function __construct()
    {
        $this->value = new \IRIX\Miscellaneous\Value();
        $this->location = new \IRIX\Locations\Location();
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $measurement = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Measurement');
        $this->_xml->appendChild($measurement);
        $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:loc', \IRIX\Report::_NAMESPACE.'/Locations');
        $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        if (is_string($this->location)) {
            $location = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Location');
            $measurement->appendChild($location);
            $location->setAttribute('ref', $this->location);
        } else {
            $measurement->appendChild($this->_xml->importNode($this->location->getXMLElement(), true));
        }

        if (!is_null($this->location_offset)) {
            $location_offset = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'LocationOffset');
            $measurement->appendChild($location_offset);
            $distance = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Distance', $this->location_offset->distance);
            $distance->setAttribute('Unit', 'm');
            $location_offset->appendChild($distance);
            $direction = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Direction', $this->location_offset->direction);
            $direction->setAttribute('Unit', 'DegreesFromNorth');
            $location_offset->appendChild($direction);
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

        if (!is_null($this->validated)) {
            $validated = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Validated', $this->validated);
            $measurement->appendChild($validated);
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

        $location = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Location')->item(0);
        $ref = $location->getAttribute('ref');
        if (is_null($location->firstChild) && !empty($ref)) {
            $measurement->location = $ref;
        } else {
            $measurement->location = \IRIX\Locations\Location::readXMLElement($location);
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'LocationOffset')->item(0);
        if (!is_null($item)) {
            $measurement->location_offset = new \IRIX\LocationOffset();
            $distance = $item->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Distance')->item(0);
            if (!is_null($distance)) {
                $measurement->location_offset->distance = $distance->textContent;
            }
            $direction = $item->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Direction')->item(0);
            if (!is_null($direction)) {
                $measurement->location_offset->direction = $direction->textContent;
            }
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

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Validated')->item(0);
        if (!is_null($item)) {
            $measurement->validated = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Description')->item(0);
        if (!is_null($item)) {
            $measurement->description = $item->textContent;
        }

        return $measurement;
    }
}
