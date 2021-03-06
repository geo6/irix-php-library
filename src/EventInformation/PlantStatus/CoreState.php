<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\EventInformation\PlantStatus;

/**
 * IRIX PHP Library - EventInformation Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class CoreState
{
    public $criticality = null;
    public $severe_damage_to_fuel = null;
    public $description = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $core_state = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'CoreState');
        $this->_xml->appendChild($core_state);

        if (!is_null($this->criticality)) {
            $core_state->appendChild($this->_xml->importNode($this->criticality->getXMLElement(), true));
        }
        if (!is_null($this->severe_damage_to_fuel)) {
            $core_state->appendChild($this->_xml->importNode($this->severe_damage_to_fuel->getXMLElement(), true));
        }
        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Description', $this->description);
            $core_state->appendChild($description);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'CoreState')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $core_state = new self();

        $criticality = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Criticality')->item(0);
        if (!is_null($criticality)) {
            $core_state->criticality = \IRIX\EventInformation\PlantStatus\CoreState\Criticality::readXMLElement($criticality);
        }

        $severe_damage_to_fuel = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'SevereDamageToFuel')->item(0);
        if (!is_null($severe_damage_to_fuel)) {
            $core_state->severe_damage_to_fuel = \IRIX\EventInformation\PlantStatus\CoreState\SevereDamageToFuel::readXMLElement($severe_damage_to_fuel);
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Description')->item(0);
        if (!is_null($item)) {
            $core_state->description = $item->textContent;
        }

        return $core_state;
    }
}
