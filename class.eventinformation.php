<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */
namespace IRIX;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class EventInformation {
  public $valid_at;
  public $type_of_event;
  public $date_and_time_of_event;
  public $location;

  public $type_of_event_description = NULL;
  public $object_involved = NULL;
  public $emergency_classification = NULL;
  public $plant_status = NULL;
  public $ines_classification = NULL;
  public $event_description = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function __construct() {
    $this->date_and_time_of_event = new \IRIX\DateAndTimeOfEvent();
    $this->location = new \IRIX\Locations\Location();
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $event_information = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ev:EventInformation'); $this->_xml->appendChild($event_information);
    $event_information->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ev', 'http://www.iaea.org/2012/IRIX/Format/EventInformation');
    $event_information->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:loc', 'http://www.iaea.org/2012/IRIX/Format/Locations');
    $event_information->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $event_information->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    $event_information->setAttribute('ValidAt', $this->valid_at);

    $type_of_event = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfEvent', $this->type_of_event); $event_information->appendChild($type_of_event);

    if (!is_null($this->type_of_event_description)) { $type_of_event_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfEventDescription', $this->type_of_event_description); $event_information->appendChild($type_of_event_description); }

    $date_and_time_of_event = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'DateAndTimeOfEvent', $this->date_and_time_of_event->value); $event_information->appendChild($date_and_time_of_event);
    if (!is_null($this->date_and_time_of_event->is_estimate)) { $date_and_time_of_event->setAttribute('IsEstimate', $this->date_and_time_of_event->is_estimate); }

    if (is_string($this->location)) {
      $location = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location'); $event_information->appendChild($location);
      $location->setAttribute('ref', $this->location);
    } else {
      $event_information->appendChild($this->_xml->importNode($this->location->getXMLElement(), TRUE));
    }

    if (!is_null($this->object_involved)) { $event_information->appendChild($this->_xml->importNode($this->object_involved->getXMLElement(), TRUE)); }
    if (!is_null($this->emergency_classification)) { $event_information->appendChild($this->_xml->importNode($this->emergency_classification->getXMLElement(), TRUE)); }
    if (!is_null($this->plant_status)) { $event_information->appendChild($this->_xml->importNode($this->plant_status->getXMLElement(), TRUE)); }
    if (!is_null($this->ines_classification)) { $event_information->appendChild($this->_xml->importNode($this->ines_classification->getXMLElement(), TRUE)); }

    if (!is_null($this->event_description)) { $event_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EventDescription', $this->event_description); $event_information->appendChild($event_description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EventInformation')->item(0);
  }

  /**
   *
   */
  public function validate($update = TRUE) {
    if ($update) $this->toXML();
    return $this->_xml->schemaValidate(__DIR__.'/xsd/'.\IRIX\Report::VERSION.'/EventInformation.xsd');
  }

  /**
   *
   */
  public static function read($filename) {
    if (file_exists($filename)) {
      $xml = new \DOMDocument(); $xml->load($filename);

      $event_information = $xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EventInformation')->item(0);

      if (!is_null($event_information)) {
        $e = new self();
        $e->_xml = new \DOMDocument('1.0', 'UTF-8');
        $e->_xml->appendChild($e->_xml->importNode($event_information, TRUE));

        if ($e->validate(FALSE)) {
          $e->valid_at = $event_information->getAttribute('ValidAt');

          $e->type_of_event = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfEvent')->item(0)->textContent;

          $item = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfEventDescription')->item(0); if (!is_null($item)) $e->type_of_event_description = $item->textContent;

          $date_and_time_of_event = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'DateAndTimeOfEvent')->item(0);
          $e->date_and_time_of_event->value = $date_and_time_of_event->textContent;
          $ie = $date_and_time_of_event->getAttribute('IsEstimate'); if (!empty($ie)) $e->date_and_time_of_event->is_estimate = $ie;

          $location = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location')->item(0);
          $ref = $location->getAttribute('ref');
          if (is_null($location->firstChild) && !empty($ref)) {
            $e->location = $ref;
          } else {
            $e->location = \IRIX\Locations\Location::readXMLElement($location);
          }

          //$object_involved = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ObjectInvolved')->item(0);
          //if (!is_null($object_involved)) { $e->object_involved = \IRIX\EventInformation\ObjectInvolved::readXMLElement($object_involved); }

          //$emergency_classification = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EmergencyClassification')->item(0);
          //if (!is_null($emergency_classification)) { $e->emergency_classification = \IRIX\EventInformation\EmergencyClassification::readXMLElement($emergency_classification); }

          //$plant_status = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'PlantStatus')->item(0);
          //if (!is_null($plant_status)) { $e->plant_status = \IRIX\EventInformation\PlantStatus::readXMLElement($plant_status); }

          //$ines_classification = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'INESClassification')->item(0);
          //if (!is_null($ines_classification)) { $e->ines_classification = \IRIX\EventInformation\INESClassification::readXMLElement($ines_classification); }

          $item = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EventDescription')->item(0); if (!is_null($item)) $e->event_description = $item->textContent;

          return $e;
        }
      } else {
        return NULL;
      }
    }

    return FALSE;
  }
}

namespace IRIX\EventInformation;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class ObjectInvolved {
  /* One of the following required */
  public $type_of_object_or_activity = NULL;
  public $type_of_object_or_activity_description = NULL;

  public $name;

  public $location = NULL;
  public $source_characteristics = NULL;
  public $reactor_characteristics = NULL;
  public $description = NULL;
}

namespace IRIX\EventInformation\ObjectInvolved;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation\ObjectInvolved
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class SourceCharacteristics {
  public $source = array();
}

namespace IRIX\EventInformation\SourceCharacteristics;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation\ObjectInvolved\SourceCharacteristics
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Source {
  public $nuclides = array();

  public $sealed = NULL;
  public $shielded = NULL;
  public $description = NULL;
}

namespace IRIX\EventInformation\SourceCharacteristics\Source;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation\SourceCharacteristics\Source
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Nuclide {
  /* One of the following required */
  public $nuclide = NULL;
  public $nuclide_list = NULL;
  public $nuclide_combination = NULL;
  public $nuclide_description = NULL;

  public $activity = NULL;
  public $reference_date_and_time = NULL;
}
