<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\EventInformation\ObjectInvolved\SourceCharacteristics;

/**
 * IRIX PHP Library - EventInformation Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Source
{
    public $nuclides = [];

    public $sealed = null;
    public $shielded = null;
    public $description = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $source = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Source');
        $this->_xml->appendChild($identifications);

        if (!is_null($this->sealed)) {
            $sealed = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Sealed', $this->sealed);
            $source->appendChild($sealed);
        }
        if (!is_null($this->shielded)) {
            $shielded = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Shielded', $this->shielded);
            $source->appendChild($shielded);
        }

        $nuclides = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Nuclides', $this->sealed);
        $source->appendChild($nuclides);
        foreach ($this->nuclides as $n) {
            $nuclides->appendChild($this->_xml->importNode($n->getXMLElement(), true));
        }

        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Description', $this->description);
            $source->appendChild($description);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Source')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $source = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Sealed')->item(0);
        if (!is_null($item)) {
            $source->sealed = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Shielded')->item(0);
        if (!is_null($item)) {
            $source->shielded = $item->textContent;
        }

        $n = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Nuclides');
        for ($i = 0; $i < $n->length; $i++) {
            $source->nuclides[] = \IRIX\EventInformation\ObjectInvolved\SourceCharacteristics\Source\Nuclide::readXMLElement($n->item($i));
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Description')->item(0);
        if (!is_null($item)) {
            $source->description = $item->textContent;
        }

        return $source;
    }
}
