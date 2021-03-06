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
class Criticality
{
    public $status;

    public $stopped_at = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $criticality = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Criticality');
        $this->_xml->appendChild($criticality);

        $status = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Status', $this->status);
        $criticality->appendChild($status);
        if (!is_null($this->stopped_at)) {
            $stopped_at = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'StoppedAt', $this->stopped_at);
            $criticality->appendChild($stopped_at);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Criticality')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $criticality = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Status')->item(0);
        if (!is_null($item)) {
            $criticality->status = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'StoppedAt')->item(0);
        if (!is_null($item)) {
            $criticality->stopped_at = $item->textContent;
        }

        return $criticality;
    }
}
