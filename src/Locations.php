<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX;

/**
 * IRIX PHP Library - Locations Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Locations
{
    public $locations = [];

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $locations = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'loc:Locations');
        $this->_xml->appendChild($locations);
        $locations->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:loc', \IRIX\Report::_NAMESPACE.'/Locations');
        $locations->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $locations->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        if (!is_null($this->locations)) {
            if (!is_array($this->locations)) {
                $this->locations = [$this->locations];
            }
            foreach ($this->locations as $l) {
                $locations->appendChild($this->_xml->importNode($l->getXMLElement(), true));
            }
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Locations')->item(0);
    }

    public function validate($update = true)
    {
        if ($update) {
            $this->toXML();
        }

        return $this->_xml->schemaValidate(dirname(__DIR__).'/xsd/'.\IRIX\Report::_VERSION.'/Locations.xsd');
    }

    public static function read($filename)
    {
        if (file_exists($filename)) {
            $xml = new \DOMDocument();
            $xml->load($filename);

            $locations = $xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Locations')->item(0);

            if (!is_null($locations)) {
                $l = new self();
                $l->_xml = new \DOMDocument('1.0', 'UTF-8');
                $l->_xml->appendChild($l->_xml->importNode($locations, true));

                if ($l->validate(false)) {
                    $location = $locations->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Location');
                    if ($location->length > 0) {
                        $l->location = [];
                        for ($i = 0; $i < $location->length; $i++) {
                            $l->location[] = \IRIX\Locations\Location::readXMLElement($location->item($i));
                        }
                    }

                    return $l;
                }
            } else {
                return;
            }
        }

        return false;
    }
}
