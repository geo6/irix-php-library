<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\EventInformation\ObjectInvolved\SourceCharacteristics\Source;

/**
 * IRIX PHP Library - EventInformation Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Nuclide
{
    /* One of the following required */
    public $nuclide = null;
    public $nuclide_list = null;
    public $nuclide_combination = null;
    public $nuclide_description = null;

    public $activity = null;
    public $reference_date_and_time = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $nuclide = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Nuclide');
        $this->_xml->appendChild($nuclide);

        if (!is_null($this->nuclide)) {
            $nuclide_ = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Nuclide', $this->nuclide);
            $nuclide->appendChild($nuclide_);
        }
        if (!is_null($this->nuclide_list)) {
            $nuclide_list = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'NuclideList', $this->nuclide_list);
            $nuclide->appendChild($nuclide_list);
        }
        if (!is_null($this->nuclide_combination)) {
            $nuclide_combination = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'NuclideCombination', $this->nuclide_combination);
            $nuclide->appendChild($nuclide_combination);
        }
        if (!is_null($this->nuclide_description)) {
            $nuclide_description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'NuclideDescription', $this->nuclide_description);
            $nuclide->appendChild($nuclide_description);
        }

        if (!is_null($this->activity)) {
            $activity = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Activity', $this->activity);
            $nuclide->appendChild($activity);
        }
        if (!is_null($this->reference_date_and_time)) {
            $reference_date_and_time = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ReferenceDateAndTime', $this->reference_date_and_time);
            $nuclide->appendChild($reference_date_and_time);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Nuclide')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $nuclide = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Nuclide')->item(0);
        if (!is_null($item)) {
            $nuclide->nuclide = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'NuclideList')->item(0);
        if (!is_null($item)) {
            $nuclide->nuclide_list = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'NuclideCombination')->item(0);
        if (!is_null($item)) {
            $nuclide->nuclide_combination = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'NuclideDescription')->item(0);
        if (!is_null($item)) {
            $nuclide->nuclide_description = $item->textContent;
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Activity')->item(0);
        if (!is_null($item)) {
            $nuclide->activity = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ReferenceDateAndTime')->item(0);
        if (!is_null($item)) {
            $nuclide->reference_date_and_time = $item->textContent;
        }

        return $nuclide;
    }
}
