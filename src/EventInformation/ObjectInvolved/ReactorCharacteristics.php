<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\EventInformation\ObjectInvolved;

/**
 * IRIX PHP Library - EventInformation Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class ReactorCharacteristics
{
    public $type_of_reactor = null;
    public $type_of_reactor_description = null;
    public $thermal_power = null;
    public $electrical_power = null;
    public $description = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $reactor_characteristics = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ReactorCharacteristics');
        $this->_xml->appendChild($reactor_characteristics);

        if (!is_null($this->type_of_reactor)) {
            $type_of_reactor = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfReactor', $this->type_of_reactor);
            $reactor_characteristics->appendChild($type_of_reactor);
        }
        if (!is_null($this->type_of_reactor_description)) {
            $type_of_reactor_description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfReactorDescription', $this->type_of_reactor_description);
            $reactor_characteristics->appendChild($type_of_reactor_description);
        }
        if (!is_null($this->thermal_power)) {
            $thermal_power = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ThermalPower', $this->thermal_power);
            $reactor_characteristics->appendChild($thermal_power);
        }
        if (!is_null($this->electrical_power)) {
            $electrical_power = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ElectricalPower', $this->electrical_power);
            $reactor_characteristics->appendChild($electrical_power);
        }
        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Description', $this->description);
            $reactor_characteristics->appendChild($description);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ReactorCharacteristics')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $reactor_characteristics = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfReactor')->item(0);
        if (!is_null($item)) {
            $reactor_characteristics->type_of_reactor = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfReactorDescription')->item(0);
        if (!is_null($item)) {
            $reactor_characteristics->type_of_reactor_description = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ThermalPower')->item(0);
        if (!is_null($item)) {
            $reactor_characteristics->thermal_power = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ElectricalPower')->item(0);
        if (!is_null($item)) {
            $reactor_characteristics->electrical_power = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Description')->item(0);
        if (!is_null($item)) {
            $reactor_characteristics->description = $item->textContent;
        }

        return $reactor_characteristics;
    }
}
