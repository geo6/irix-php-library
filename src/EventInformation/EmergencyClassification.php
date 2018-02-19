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
class EmergencyClassification
{
    /* One of the following required */
    public $emergency_class = null;
    public $emergency_class_description = null;

    public $date_and_time_of_declaration = null;
    public $basis_for_declaration = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $emergency_classification = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EmergencyClassification');
        $this->_xml->appendChild($emergency_classification);

        if (!is_null($this->emergency_class)) {
            $emergency_class = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EmergencyClass', $this->emergency_class);
            $emergency_classification->appendChild($emergency_class);
        }
        if (!is_null($this->emergency_class_description)) {
            $emergency_class_description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EmergencyClassDescription', $this->emergency_class_description);
            $emergency_classification->appendChild($emergency_class_description);
        }
        if (!is_null($this->date_and_time_of_declaration)) {
            $date_and_time_of_declaration = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'DateAndTimeOfDeclaration', $this->date_and_time_of_declaration);
            $emergency_classification->appendChild($date_and_time_of_declaration);
        }
        if (!is_null($this->basis_for_declaration)) {
            $basis_for_declaration = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'BasisForDeclaration', $this->basis_for_declaration);
            $emergency_classification->appendChild($basis_for_declaration);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EmergencyClassification')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $emergency_classification = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EmergencyClass')->item(0);
        if (!is_null($item)) {
            $emergency_classification->emergency_class = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EmergencyClassDescription')->item(0);
        if (!is_null($item)) {
            $emergency_classification->emergency_class_description = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'DateAndTimeOfDeclaration')->item(0);
        if (!is_null($item)) {
            $emergency_classification->date_and_time_of_declaration = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'BasisForDeclaration')->item(0);
        if (!is_null($item)) {
            $emergency_classification->basis_for_declaration = $item->textContent;
        }

        return $emergency_classification;
    }
}
