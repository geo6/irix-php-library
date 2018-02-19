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
class ObjectInvolved
{
    /* One of the following required */
    public $type_of_object_or_activity = null;
    public $type_of_object_or_activity_description = null;

    public $name;

    public $location = null;
    public $source_characteristics = null;
    public $reactor_characteristics = null;
    public $description = null;

    private $_xml;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');

        $object_involved = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ObjectInvolved');
        $this->_xml->appendChild($object_involved);
        if (!is_null($this->type_of_object_or_activity)) {
            $type_of_object_or_activity = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfObjectOrActivity', $this->type_of_object_or_activity);
            $object_involved->appendChild($type_of_object_or_activity);
        }
        if (!is_null($this->type_of_object_or_activity_description)) {
            $type_of_object_or_activity_description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfObjectOrActivityDescription', $this->type_of_object_or_activity_description);
            $object_involved->appendChild($type_of_object_or_activity_description);
        }
        $name = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Name', $this->name);
        $object_involved->appendChild($name);

        if (!is_null($this->location)) {
            if (is_string($this->location)) {
                $location = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Location');
                $object_involved->appendChild($location);
                $location->setAttribute('ref', $this->location);
            } else {
                $object_involved->appendChild($this->_xml->importNode($this->location->getXMLElement(), true));
            }
        }

        if (!is_null($this->source_characteristics)) {
            $object_involved->appendChild($this->_xml->importNode($this->source_characteristics->getXMLElement(), true));
        }
        if (!is_null($this->reactor_characteristics)) {
            $object_involved->appendChild($this->_xml->importNode($this->reactor_characteristics->getXMLElement(), true));
        }

        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Description', $this->description);
            $object_involved->appendChild($description);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ObjectInvolved')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $object_involved = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfObjectOrActivity')->item(0);
        if (!is_null($item)) {
            $object_involved->type_of_object_or_activity = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfObjectOrActivityDescription')->item(0);
        if (!is_null($item)) {
            $object_involved->type_of_object_or_activity_description = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Name')->item(0);
        if (!is_null($item)) {
            $object_involved->name = $item->textContent;
        }

        $location = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Location')->item(0);
        if (!is_null($location)) {
            $ref = $location->getAttribute('ref');
            if (is_null($location->firstChild) && !empty($ref)) {
                $object_involved->location = $ref;
            } else {
                $object_involved->location = \IRIX\Locations\Location::readXMLElement($location);
            }
        }

        $source_characteristics = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'SourceCharacteristics')->item(0);
        if (!is_null($source_characteristics)) {
            $object_involved->source_characteristics = \IRIX\EventInformation\ObjectInvolved\SourceCharacteristics::readXMLElement($source_characteristics);
        }

        $reactor_characteristics = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ReactorCharacteristics')->item(0);
        if (!is_null($reactor_characteristics)) {
            $object_involved->reactor_characteristics = \IRIX\EventInformation\ObjectInvolved\ReactorCharacteristics::readXMLElement($reactor_characteristics);
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'Description')->item(0);
        if (!is_null($item)) {
            $object_involved->description = $item->textContent;
        }

        return $object_involved;
    }
}
