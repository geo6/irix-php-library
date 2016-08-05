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
    $this->valid_at = gmdate('Y-m-d\TH:i:s\Z');
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
          $e->date_and_time_of_event = new DateAndTimeOfEvent($date_and_time_of_event->textContent);
          $ie = $date_and_time_of_event->getAttribute('IsEstimate'); if (!empty($ie)) $e->date_and_time_of_event->is_estimate = $ie;

          $location = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location')->item(0);
          $ref = $location->getAttribute('ref');
          if (is_null($location->firstChild) && !empty($ref)) {
            $e->location = $ref;
          } else {
            $e->location = \IRIX\Locations\Location::readXMLElement($location);
          }

          $object_involved = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ObjectInvolved')->item(0);
          if (!is_null($object_involved)) { $e->object_involved = \IRIX\EventInformation\ObjectInvolved::readXMLElement($object_involved); }

          $emergency_classification = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EmergencyClassification')->item(0);
          if (!is_null($emergency_classification)) { $e->emergency_classification = \IRIX\EventInformation\EmergencyClassification::readXMLElement($emergency_classification); }

          $plant_status = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'PlantStatus')->item(0);
          if (!is_null($plant_status)) { $e->plant_status = \IRIX\EventInformation\PlantStatus::readXMLElement($plant_status); }

          $ines_classification = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'INESClassification')->item(0);
          if (!is_null($ines_classification)) { $e->ines_classification = \IRIX\EventInformation\INESClassification::readXMLElement($ines_classification); }

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

  private $_xml;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');

    $object_involved = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ObjectInvolved'); $this->_xml->appendChild($object_involved);
    if (!is_null($this->type_of_object_or_activity)) { $type_of_object_or_activity = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfObjectOrActivity', $this->type_of_object_or_activity); $object_involved->appendChild($type_of_object_or_activity); }
    if (!is_null($this->type_of_object_or_activity_description)) { $type_of_object_or_activity_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfObjectOrActivityDescription', $this->type_of_object_or_activity_description); $object_involved->appendChild($type_of_object_or_activity_description); }
    $name = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Name', $this->name); $object_involved->appendChild($name);

    if (!is_null($this->location)) {
      if (is_string($this->location)) {
        $location = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location'); $object_involved->appendChild($location);
        $location->setAttribute('ref', $this->location);
      } else {
        $object_involved->appendChild($this->_xml->importNode($this->location->getXMLElement(), TRUE));
      }
    }

    if (!is_null($this->source_characteristics)) { $object_involved->appendChild($this->_xml->importNode($this->source_characteristics->getXMLElement(), TRUE)); }
    if (!is_null($this->reactor_characteristics)) { $object_involved->appendChild($this->_xml->importNode($this->reactor_characteristics->getXMLElement(), TRUE)); }

    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Description', $this->description); $object_involved->appendChild($description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ObjectInvolved')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $object_involved = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfObjectOrActivity')->item(0); if (!is_null($item)) $object_involved->type_of_object_or_activity = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfObjectOrActivityDescription' )->item(0); if (!is_null($item)) $object_involved->type_of_object_or_activity_description = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Name'                              )->item(0); if (!is_null($item)) $object_involved->name = $item->textContent;

    $location = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location')->item(0);
    if (!is_null($location)) {
      $ref = $location->getAttribute('ref');
      if (is_null($location->firstChild) && !empty($ref)) {
        $object_involved->location = $ref;
      } else {
        $object_involved->location = \IRIX\Locations\Location::readXMLElement($location);
      }
    }

    $source_characteristics = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'SourceCharacteristics')->item(0);
    if (!is_null($source_characteristics)) { $object_involved->source_characteristics = \IRIX\EventInformation\ObjectInvolved\SourceCharacteristics::readXMLElement($source_characteristics); }

    $reactor_characteristics = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ReactorCharacteristics')->item(0);
    if (!is_null($reactor_characteristics)) { $object_involved->reactor_characteristics = \IRIX\EventInformation\ObjectInvolved\ReactorCharacteristics::readXMLElement($reactor_characteristics); }

    return $object_involved;
  }
}

namespace IRIX\EventInformation\ObjectInvolved;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation\ObjectInvolved
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class SourceCharacteristics {
  public $source = array();

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $source_characteristics = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'SourceCharacteristics'); $this->_xml->appendChild($source_characteristics);

    foreach ($this->source as $s) $source_characteristics->appendChild($this->_xml->importNode($s->getXMLElement(), TRUE));

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'SourceCharacteristics')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $source_characteristics = new self();

    $s = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Source');
    for ($i = 0; $i < $s->length; $i++) $source_characteristics->source[] = \IRIX\EventInformation\ObjectInvolved\SourceCharacteristics\Source::readXMLElement($s->item($i));

    return $source_characteristics;
  }
}

namespace IRIX\EventInformation\ObjectInvolved\SourceCharacteristics;

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

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $source = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Source'); $this->_xml->appendChild($identifications);

    if (!is_null($this->sealed)) { $sealed = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Sealed', $this->sealed); $source->appendChild($sealed); }
    if (!is_null($this->shielded)) { $shielded = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Shielded', $this->shielded); $source->appendChild($shielded); }

    $nuclides = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Nuclides', $this->sealed); $source->appendChild($nuclides);
    foreach ($this->nuclides as $n) $nuclides->appendChild($this->_xml->importNode($n->getXMLElement(), TRUE));

    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Description', $this->description); $source->appendChild($description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Source')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $source = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Sealed')->item(0); if (!is_null($item)) $source->sealed = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Shielded')->item(0); if (!is_null($item)) $source->shielded = $item->textContent;

    $n = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Nuclides');
    for ($i = 0; $i < $n->length; $i++) $source->nuclides[] = \IRIX\EventInformation\ObjectInvolved\SourceCharacteristics\Source\Nuclide::readXMLElement($n->item($i));

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Description')->item(0); if (!is_null($item)) $source->description = $item->textContent;

    return $source;
  }
}

namespace IRIX\EventInformation\ObjectInvolved\SourceCharacteristics\Source;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation\ObjectInvolved\SourceCharacteristics\Source
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

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $nuclide = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Nuclide'); $this->_xml->appendChild($nuclide);

    if (!is_null($this->nuclide)) { $nuclide_ = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Nuclide', $this->nuclide); $nuclide->appendChild($nuclide_); }
    if (!is_null($this->nuclide_list)) { $nuclide_list = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'NuclideList', $this->nuclide_list); $nuclide->appendChild($nuclide_list); }
    if (!is_null($this->nuclide_combination)) { $nuclide_combination = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'NuclideCombination', $this->nuclide_combination); $nuclide->appendChild($nuclide_combination); }
    if (!is_null($this->nuclide_description)) { $nuclide_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'NuclideDescription', $this->nuclide_description); $nuclide->appendChild($nuclide_description); }

    if (!is_null($this->activity)) { $activity = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Activity', $this->activity); $nuclide->appendChild($activity); }
    if (!is_null($this->reference_date_and_time)) { $reference_date_and_time = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ReferenceDateAndTime', $this->reference_date_and_time); $nuclide->appendChild($reference_date_and_time); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Nuclide')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $nuclide = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Nuclide')->item(0); if (!is_null($item)) $nuclide->nuclide = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'NuclideList')->item(0); if (!is_null($item)) $nuclide->nuclide_list = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'NuclideCombination')->item(0); if (!is_null($item)) $nuclide->nuclide_combination = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'NuclideDescription')->item(0); if (!is_null($item)) $nuclide->nuclide_description = $item->textContent;

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Activity')->item(0); if (!is_null($item)) $nuclide->activity = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ReferenceDateAndTime')->item(0); if (!is_null($item)) $nuclide->reference_date_and_time = $item->textContent;

    return $nuclide;
  }
}

namespace IRIX\EventInformation\ObjectInvolved;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation\ObjectInvolved
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class ReactorCharacteristics {
  public $type_of_reactor = NULL;
  public $type_of_reactor_description = NULL;
  public $thermal_power = NULL;
  public $electrical_power = NULL;
  public $description = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $reactor_characteristics = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ReactorCharacteristics'); $this->_xml->appendChild($reactor_characteristics);

    if (!is_null($this->type_of_reactor)) { $type_of_reactor = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfReactor', $this->type_of_reactor); $reactor_characteristics->appendChild($type_of_reactor); }
    if (!is_null($this->type_of_reactor_description)) { $type_of_reactor_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfReactorDescription', $this->type_of_reactor_description); $reactor_characteristics->appendChild($type_of_reactor_description); }
    if (!is_null($this->thermal_power)) { $thermal_power = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ThermalPower', $this->thermal_power); $reactor_characteristics->appendChild($thermal_power); }
    if (!is_null($this->electrical_power)) { $electrical_power = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ElectricalPower', $this->electrical_power); $reactor_characteristics->appendChild($electrical_power); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Description', $this->description); $reactor_characteristics->appendChild($description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ReactorCharacteristics')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $reactor_characteristics = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfReactor')->item(0); if (!is_null($item)) $reactor_characteristics->type_of_reactor = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TypeOfReactorDescription')->item(0); if (!is_null($item)) $reactor_characteristics->type_of_reactor_description = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ThermalPower')->item(0); if (!is_null($item)) $reactor_characteristics->thermal_power = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'ElectricalPower')->item(0); if (!is_null($item)) $reactor_characteristics->electrical_power = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Description')->item(0); if (!is_null($item)) $reactor_characteristics->description = $item->textContent;

    return $reactor_characteristics;
  }
}

namespace IRIX\EventInformation;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class EmergencyClassification {
  /* One of the following required */
  public $emergency_class = NULL;
  public $emergency_class_description = NULL;

  public $date_and_time_of_declaration = NULL;
  public $basis_for_declaration = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $emergency_classification = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EmergencyClassification'); $this->_xml->appendChild($emergency_classification);

    if (!is_null($this->emergency_class)) { $emergency_class = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EmergencyClass', $this->emergency_class); $emergency_classification->appendChild($emergency_class); }
    if (!is_null($this->emergency_class_description)) { $emergency_class_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EmergencyClassDescription', $this->emergency_class_description); $emergency_classification->appendChild($emergency_class_description); }
    if (!is_null($this->date_and_time_of_declaration)) { $date_and_time_of_declaration = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'DateAndTimeOfDeclaration', $this->date_and_time_of_declaration); $emergency_classification->appendChild($date_and_time_of_declaration); }
    if (!is_null($this->basis_for_declaration)) { $basis_for_declaration = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'BasisForDeclaration', $this->basis_for_declaration); $emergency_classification->appendChild($basis_for_declaration); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EmergencyClassification')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $emergency_classification = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EmergencyClass')->item(0); if (!is_null($item)) $emergency_classification->emergency_class = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EmergencyClassDescription')->item(0); if (!is_null($item)) $emergency_classification->emergency_class_description = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'DateAndTimeOfDeclaration')->item(0); if (!is_null($item)) $emergency_classification->date_and_time_of_declaration = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'BasisForDeclaration')->item(0); if (!is_null($item)) $emergency_classification->basis_for_declaration = $item->textContent;

    return $reactor_characteristics;
  }
}

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class PlantStatus {
  public $core_state = NULL;
  public $spent_fuel_state = NULL;
  public $trend_in_conditions = NULL;
  public $description = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $plant_status = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'PlantStatus'); $this->_xml->appendChild($plant_status);

    if (!is_null($this->core_state)) { $plant_status->appendChild($this->_xml->importNode($this->core_state->getXMLElement(), TRUE)); }
    if (!is_null($this->spent_fuel_state)) { $spent_fuel_state = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'SpentFuelState', $this->spent_fuel_state); $plant_status->appendChild($spent_fuel_state); }
    if (!is_null($this->trend_in_conditions)) { $trend_in_conditions = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TrendInConditions', $this->trend_in_conditions); $plant_status->appendChild($trend_in_conditions); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Description', $this->description); $plant_status->appendChild($description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'EmergencyClassification')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $plant_status = new self();

    $core_state = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'CoreState')->item(0);
    if (!is_null($core_state)) { $plant_status->core_state = \IRIX\EventInformation\PlantStatus\CoreState::readXMLElement($core_state); }

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'SpentFuelState')->item(0); if (!is_null($item)) $plant_status->spent_fuel_state = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TrendInConditions')->item(0); if (!is_null($item)) $plant_status->trend_in_conditions = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Description')->item(0); if (!is_null($item)) $plant_status->description = $item->textContent;

    return $plant_status;
  }
}

namespace IRIX\EventInformation\PlantStatus;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation\PlantStatus
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class CoreState {
  public $criticality = NULL;
  public $severe_damage_to_fuel = NULL;
  public $description = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $core_state = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'CoreState'); $this->_xml->appendChild($core_state);

    if (!is_null($this->criticality)) { $core_state->appendChild($this->_xml->importNode($this->criticality->getXMLElement(), TRUE)); }
    if (!is_null($this->severe_damage_to_fuel)) { $core_state->appendChild($this->_xml->importNode($this->severe_damage_to_fuel->getXMLElement(), TRUE)); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Description', $this->description); $core_state->appendChild($description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'CoreState')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $core_state = new self();

    $criticality = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Criticality')->item(0);
    if (!is_null($criticality)) { $core_state->criticality = \IRIX\EventInformation\PlantStatus\CoreState\Criticality::readXMLElement($criticality); }

    $severe_damage_to_fuel = $event_information->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'SevereDamageToFuel')->item(0);
    if (!is_null($severe_damage_to_fuel)) { $core_state->severe_damage_to_fuel = \IRIX\EventInformation\PlantStatus\CoreState\SevereDamageToFuel::readXMLElement($severe_damage_to_fuel); }

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Description')->item(0); if (!is_null($item)) $core_state->description = $item->textContent;

    return $core_state;
  }
}

namespace IRIX\EventInformation\PlantStatus\CoreState;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation\PlantStatus\CoreState
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Criticality {
  public $status;

  public $stopped_at = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $criticality = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Criticality'); $this->_xml->appendChild($criticality);

    $status = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Status', $this->status); $criticality->appendChild($status);
    if (!is_null($this->stopped_at)) { $stopped_at = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'StoppedAt', $this->stopped_at); $criticality->appendChild($stopped_at); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Criticality')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $criticality = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Status')->item(0); if (!is_null($item)) $criticality->status = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'StoppedAt')->item(0); if (!is_null($item)) $criticality->stopped_at = $item->textContent;

    return $criticality;
  }
}

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation\PlantStatus\CoreState
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class SevereDamageToFuel {
  public $occurence;

  public $time_of_occurence = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $severe_damage_to_fuel = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'SevereDamageToFuel'); $this->_xml->appendChild($severe_damage_to_fuel);

    $occurence = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Occurence', $this->occurence); $severe_damage_to_fuel->appendChild($occurence);
    if (!is_null($this->time_of_occurence)) { $time_of_occurence = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TimeOfOccurence', $this->time_of_occurence); $severe_damage_to_fuel->appendChild($time_of_occurence); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'SevereDamageToFuel')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $severe_damage_to_fuel = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'Occurence')->item(0); if (!is_null($item)) $severe_damage_to_fuel->occurence = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'TimeOfOccurence')->item(0); if (!is_null($item)) $severe_damage_to_fuel->time_of_occurence = $item->textContent;

    return $severe_damage_to_fuel;
  }
}

namespace IRIX\EventInformation;

/**
 * IRIX PHP Library - EventInformation Section
 * @package IRIX\Report\EventInformation
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class INESClassification {
  public $ines_level;
  public $status_of_classification;

  public $date_and_time_of_classification = NULL;
  public $basis_for_classification = NULL;

  private $_xml;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');

    $ines_classification = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'INESClassification'); $this->_xml->appendChild($ines_classification);
    $ines_level = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'INESLevel', $this->name); $ines_classification->appendChild($ines_level);
    $status_of_classification = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'StatusOfClassification', $this->name); $ines_classification->appendChild($status_of_classification);
    if (!is_null($this->date_and_time_of_classification)) { $date_and_time_of_classification = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'DateAndTimeOfClassification', $this->date_and_time_of_classification); $ines_classification->appendChild($date_and_time_of_classification); }
    if (!is_null($this->basis_for_classification)) { $basis_for_classification = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'BasisForClassification', $this->basis_for_classification); $ines_classification->appendChild($basis_for_classification); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'INESClassification')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $ines_classification = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'INESLevel')->item(0); if (!is_null($item)) $ines_classification->ines_level = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'StatusOfClassification')->item(0); if (!is_null($item)) $ines_classification->status_of_classification = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'DateAndTimeOfClassification')->item(0); if (!is_null($item)) $ines_classification->date_and_time_of_classification = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/EventInformation', 'BasisForClassification')->item(0); if (!is_null($item)) $ines_classification->basis_for_classification = $item->textContent;

    return $object_involved;
  }
}
