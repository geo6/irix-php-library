<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\EventInformation;

/**
 * IRIX PHP Library - EventInformation Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class INESClassification
{
    public $ines_level;
    public $status_of_classification;

    public $date_and_time_of_classification = null;
    public $basis_for_classification = null;

    private $_xml;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');

        $ines_classification = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'INESClassification');
        $this->_xml->appendChild($ines_classification);
        $ines_level = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'INESLevel', $this->ines_level);
        $ines_classification->appendChild($ines_level);
        $status_of_classification = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'StatusOfClassification', $this->status_of_classification);
        $ines_classification->appendChild($status_of_classification);
        if (!is_null($this->date_and_time_of_classification)) {
            $date_and_time_of_classification = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'DateAndTimeOfClassification', $this->date_and_time_of_classification);
            $ines_classification->appendChild($date_and_time_of_classification);
        }
        if (!is_null($this->basis_for_classification)) {
            $basis_for_classification = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'BasisForClassification', $this->basis_for_classification);
            $ines_classification->appendChild($basis_for_classification);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'INESClassification')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $ines_classification = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'INESLevel')->item(0);
        if (!is_null($item)) {
            $ines_classification->ines_level = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'StatusOfClassification')->item(0);
        if (!is_null($item)) {
            $ines_classification->status_of_classification = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'DateAndTimeOfClassification')->item(0);
        if (!is_null($item)) {
            $ines_classification->date_and_time_of_classification = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'BasisForClassification')->item(0);
        if (!is_null($item)) {
            $ines_classification->basis_for_classification = $item->textContent;
        }

        return $ines_classification;
    }
}
