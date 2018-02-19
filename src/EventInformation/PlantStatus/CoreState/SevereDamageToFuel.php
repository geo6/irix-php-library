<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\EventInformation\PlantStatus\CoreState;

/**
 * IRIX PHP Library - EventInformation Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class SevereDamageToFuel
{
    public $occurence;

    public $time_of_occurence = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $severe_damage_to_fuel = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'SevereDamageToFuel');
        $this->_xml->appendChild($severe_damage_to_fuel);

        $occurence = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Occurence', $this->occurence);
        $severe_damage_to_fuel->appendChild($occurence);
        if (!is_null($this->time_of_occurence)) {
            $time_of_occurence = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TimeOfOccurence', $this->time_of_occurence);
            $severe_damage_to_fuel->appendChild($time_of_occurence);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'SevereDamageToFuel')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $severe_damage_to_fuel = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Occurence')->item(0);
        if (!is_null($item)) {
            $severe_damage_to_fuel->occurence = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TimeOfOccurence')->item(0);
        if (!is_null($item)) {
            $severe_damage_to_fuel->time_of_occurence = $item->textContent;
        }

        return $severe_damage_to_fuel;
    }
}
