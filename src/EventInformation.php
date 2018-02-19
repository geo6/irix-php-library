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
 * IRIX PHP Library - EventInformation Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class EventInformation
{
    public $valid_at;
    public $type_of_event;
    public $date_and_time_of_event;
    public $location;

    public $type_of_event_description = null;
    public $object_involved = null;
    public $emergency_classification = null;
    public $plant_status = null;
    public $ines_classification = null;
    public $event_description = null;

    private $_xml = null;

    public function __construct()
    {
        $this->valid_at = gmdate('Y-m-d\TH:i:s\Z');
        $this->location = new \IRIX\Locations\Location();
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $event_information = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ev:EventInformation');
        $this->_xml->appendChild($event_information);
        $event_information->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ev', \IRIX\Report::_NAMESPACE.'/EventInformation');
        $event_information->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:loc', \IRIX\Report::_NAMESPACE.'/Locations');
        $event_information->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $event_information->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        $event_information->setAttribute('ValidAt', $this->valid_at);

        $type_of_event = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfEvent', $this->type_of_event);
        $event_information->appendChild($type_of_event);

        if (!is_null($this->type_of_event_description)) {
            $type_of_event_description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfEventDescription', $this->type_of_event_description);
            $event_information->appendChild($type_of_event_description);
        }

        $date_and_time_of_event = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'DateAndTimeOfEvent', $this->date_and_time_of_event->value);
        $event_information->appendChild($date_and_time_of_event);
        if (!is_null($this->date_and_time_of_event->is_estimate)) {
            $date_and_time_of_event->setAttribute('IsEstimate', $this->date_and_time_of_event->is_estimate);
        }

        if (is_string($this->location)) {
            $location = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Location');
            $event_information->appendChild($location);
            $location->setAttribute('ref', $this->location);
        } else {
            $event_information->appendChild($this->_xml->importNode($this->location->getXMLElement(), true));
        }

        if (!is_null($this->object_involved)) {
            $event_information->appendChild($this->_xml->importNode($this->object_involved->getXMLElement(), true));
        }
        if (!is_null($this->emergency_classification)) {
            $event_information->appendChild($this->_xml->importNode($this->emergency_classification->getXMLElement(), true));
        }
        if (!is_null($this->plant_status)) {
            $event_information->appendChild($this->_xml->importNode($this->plant_status->getXMLElement(), true));
        }
        if (!is_null($this->ines_classification)) {
            $event_information->appendChild($this->_xml->importNode($this->ines_classification->getXMLElement(), true));
        }

        if (!is_null($this->event_description)) {
            $event_description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EventDescription', $this->event_description);
            $event_information->appendChild($event_description);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EventInformation')->item(0);
    }

    public function validate($update = true)
    {
        if ($update) {
            $this->toXML();
        }

        return $this->_xml->schemaValidate(dirname(__DIR__).'/xsd/'.\IRIX\Report::_VERSION.'/EventInformation.xsd');
    }

    public static function read($filename)
    {
        if (file_exists($filename)) {
            $xml = new \DOMDocument();
            $xml->load($filename);

            $event_information = $xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EventInformation')->item(0);

            if (!is_null($event_information)) {
                $e = new self();
                $e->_xml = new \DOMDocument('1.0', 'UTF-8');
                $e->_xml->appendChild($e->_xml->importNode($event_information, true));

                if ($e->validate(false)) {
                    $e->valid_at = $event_information->getAttribute('ValidAt');

                    $e->type_of_event = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfEvent')->item(0)->textContent;

                    $item = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'TypeOfEventDescription')->item(0);
                    if (!is_null($item)) {
                        $e->type_of_event_description = $item->textContent;
                    }

                    $date_and_time_of_event = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'DateAndTimeOfEvent')->item(0);
                    $e->date_and_time_of_event = new \IRIX\Miscellaneous\DateAndTimeOfEvent($date_and_time_of_event->textContent);
                    $ie = $date_and_time_of_event->getAttribute('IsEstimate');
                    if (!empty($ie)) {
                        $e->date_and_time_of_event->is_estimate = $ie;
                    }

                    $location = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Location')->item(0);
                    $ref = $location->getAttribute('ref');
                    if (is_null($location->firstChild) && !empty($ref)) {
                        $e->location = $ref;
                    } else {
                        $e->location = \IRIX\Locations\Location::readXMLElement($location);
                    }

                    $object_involved = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'ObjectInvolved')->item(0);
                    if (!is_null($object_involved)) {
                        $e->object_involved = \IRIX\EventInformation\ObjectInvolved::readXMLElement($object_involved);
                    }

                    $emergency_classification = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EmergencyClassification')->item(0);
                    if (!is_null($emergency_classification)) {
                        $e->emergency_classification = \IRIX\EventInformation\EmergencyClassification::readXMLElement($emergency_classification);
                    }

                    $plant_status = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'PlantStatus')->item(0);
                    if (!is_null($plant_status)) {
                        $e->plant_status = \IRIX\EventInformation\PlantStatus::readXMLElement($plant_status);
                    }

                    $ines_classification = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'INESClassification')->item(0);
                    if (!is_null($ines_classification)) {
                        $e->ines_classification = \IRIX\EventInformation\INESClassification::readXMLElement($ines_classification);
                    }

                    $item = $event_information->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/EventInformation', 'EventDescription')->item(0);
                    if (!is_null($item)) {
                        $e->event_description = $item->textContent;
                    }

                    return $e;
                }
            } else {
                return;
            }
        }

        return false;
    }
}
