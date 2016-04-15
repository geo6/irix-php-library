<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */
namespace IRIX\Measurements;

/**
 * IRIX PHP Library - Measurements Section
 * @package IRIX\Report\Measurements
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class DoseRate {
  public $dose_rate_type;
  public $measuring_period;
  public $measurements = array();

  public $apparatus_type = NULL;
  public $apparatus_type_description = NULL;
  public $description = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function __construct() {
    $this->measuring_period = new \IRIX\MeasuringPeriod();
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $dose_rate = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'mon:DoseRate'); $this->_xml->appendChild($dose_rate);
    $dose_rate->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:mon', 'http://www.iaea.org/2012/IRIX/Format/Measurements');
    $dose_rate->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:loc', 'http://www.iaea.org/2012/IRIX/Format/Locations');
    $dose_rate->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $dose_rate->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    $dose_rate_type = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'DoseRateType', $this->dose_rate_type); $dose_rate->appendChild($dose_rate_type);

    $measuring_period = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'MeasuringPeriod'); $dose_rate->appendChild($measuring_period);
    $start_time = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'StartTime', $this->measuring_period->start_time); $measuring_period->appendChild($start_time);
    $end_time = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'EndTime', $this->measuring_period->end_time); $measuring_period->appendChild($end_time);

    if (!is_null($this->apparatus_type)) { $apparatus_type = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ApparatusType', $this->apparatus_type); $dose_rate->appendChild($apparatus_type); }
    if (!is_null($this->apparatus_type_description)) { $apparatus_type_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ApparatusTypeDescription', $this->apparatus_type_description); $dose_rate->appendChild($apparatus_type_description); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Description', $this->description); $dose_rate->appendChild($description); }

    $measurements = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Measurements'); $dose_rate->appendChild($measurements);
    foreach ($this->measurements as $m) $measurements->appendChild($this->_xml->importNode($m->getXMLElement(), TRUE));

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'DoseRate')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $dose_rate = new self();

    $dose_rate->dose_rate_type = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'DoseRateType')->item(0)->textContent;

    $measuring_period = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'MeasuringPeriod')->item(0);
    $dose_rate->measuring_period->start_time = $measuring_period->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'StartTime')->item(0)->textContent;
    $dose_rate->measuring_period->end_time = $measuring_period->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'EndTime')->item(0)->textContent;

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ApparatusType')->item(0); if (!is_null($item)) $dose_rate->apparatus_type = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ApparatusTypeDescription')->item(0); if (!is_null($item)) $dose_rate->apparatus_type_description = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Description')->item(0); if (!is_null($item)) $dose_rate->description = $item->textContent;

    $measurements = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Measurements');
    if ($measurements->length > 0) { $dose_rate->measurements = array(); for ($i = 0; $i < $measurements->length; $i++) $dose_rate->measurements[] = \IRIX\Measurements\DoseRate\Measurement::readXMLElement($measurements->item($i)); }

    return $dose_rate;
  }
}

namespace IRIX\Measurements\DoseRate;

class Measurement {
  public $value;
  public $location;

  public $location_offset = NULL;
  public $uncertainty = NULL;
  public $timebase = NULL;
  public $background = NULL;
  public $validated = NULL;
  public $description = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function __construct() {
    $this->value = new \IRIX\Value();
    $this->location = new \IRIX\Locations\Location();
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $measurement = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Measurement'); $this->_xml->appendChild($measurement);
    $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:loc', 'http://www.iaea.org/2012/IRIX/Format/Locations');
    $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    if (is_string($this->location)) {
      $location = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location'); $measurement->appendChild($location);
      $location->setAttribute('ref', $this->location);
    } else {
      $measurement->appendChild($this->_xml->importNode($this->location->getXMLElement(), TRUE));
    }

    if (!is_null($this->location_offset)) {
      $location_offset = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'LocationOffset'); $measurement->appendChild($location_offset);
      $distance = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Distance', $this->location_offset->distance); $distance->setAttribute('Unit', 'm'); $location_offset->appendChild($distance);
      $direction = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Direction', $this->location_offset->direction); $direction->setAttribute('Unit', 'DegreesFromNorth'); $location_offset->appendChild($direction);
    }

    $value = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Value', $this->value->value); $measurement->appendChild($value);
    $value->setAttribute('Unit', $this->value->unit);
    if (!is_null($this->value->constraint)) $value->setAttribute('Constraint', $this->value->constraint);

    if (!is_null($this->uncertainty)) {
      $uncertainty = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Uncertainty', $this->uncertainty->uncertainty); $measurement->appendChild($uncertainty);
      if (!is_null($this->uncertainty->type)) $uncertainty->setAttribute('Type', $this->uncertainty->type);
      if (!is_null($this->uncertainty->constraint)) $uncertainty->setAttribute('Constraint', $this->uncertainty->constraint);
      $uncertainty->setAttribute('Unit', $this->uncertainty->unit);
    }

    if (!is_null($this->timebase)) { $timebase = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Timebase', $this->timebase); $measurement->appendChild($timebase); }

    if (!is_null($this->background)) {
      $background = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Background'); $measurement->appendChild($background);

      $value = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Value'); $background->appendChild($value);
      if (!is_null($this->value->constraint)) $value->setAttribute('Constraint', $this->value->constraint);
      $value->setAttribute('Unit', $this->value->unit);

      if (!is_null($this->background->uncertainty)) {
        $uncertainty = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Uncertainty', $this->background->uncertainty->uncertainty); $background->appendChild($uncertainty);
        if (!is_null($this->background->uncertainty->type)) $uncertainty->setAttribute('Type', $this->background->uncertainty->type);
        if (!is_null($this->background->uncertainty->constraint)) $uncertainty->setAttribute('Constraint', $this->background->uncertainty->constraint);
        $uncertainty->setAttribute('Unit', $this->background->uncertainty->unit);
      }

      if (!is_null($this->background->timebase)) { $timebase = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Timebase', $this->background->timebase); $background->appendChild($timebase); }
      if (!is_null($this->background->method)) { $method = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Method', $this->background->method); $background->appendChild($method); }
    }

    if (!is_null($this->validated)) { $validated = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Validated', $this->validated); $measurement->appendChild($validated); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Description', $this->description); $measurement->appendChild($description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Measurement')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $measurement = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Nuclide')->item(0); if (!is_null($item)) $measurement->nuclide = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'NuclideList')->item(0); if (!is_null($item)) $measurement->nuclide_list = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'NuclideCombination')->item(0); if (!is_null($item)) $measurement->nuclide_combination = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'NuclideDescription')->item(0); if (!is_null($item)) $measurement->nuclide_description = $item->textContent;

    $location = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location')->item(0);
    $ref = $location->getAttribute('ref');
    if (is_null($location->firstChild) && !empty($ref)) {
      $measurement->location = $ref;
    } else {
      $measurement->location = \IRIX\Locations\Location::readXMLElement($location);
    }

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'LocationOffset')->item(0);
    if (!is_null($item)) {
      $measurement->location_offset = new \IRIX\LocationOffset();
      $distance = $item->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Distance')->item(0); if (!is_null($distance)) $measurement->location_offset->distance = $distance->textContent;
      $direction = $item->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Direction')->item(0); if (!is_null($direction)) $measurement->location_offset->direction = $direction->textContent;
    }

    $value = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Value')->item(0);
    $measurement->value->value = $value->textContent;
    $measurement->value->unit = $value->getAttribute('Unit');
    $c = $value->getAttribute('Constraint'); if (!empty($c)) $measurement->value->constraint = $c;

    $uncertainty = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Uncertainty')->item(0);
    if (!is_null($uncertainty)) {
      $measurement->uncertainty = new \IRIX\Uncertainty();
      $measurement->uncertainty->uncertainty = $uncertainty->textContent;
      $measurement->uncertainty->unit = $uncertainty->getAttribute('Unit');
      $t = $uncertainty->getAttribute('Type'); if (!empty($t)) $measurement->uncertainty->type = $t;
      $c = $uncertainty->getAttribute('Constraint'); if (!empty($c)) $measurement->uncertainty->constraint = $c;
    }

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Timebase')->item(0); if (!is_null($item)) $measurement->timebase = $item->textContent;

    $background = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Background')->item(0);
    if (!is_null($background)) {
      $measurement->background = new \IRIX\Background();

      $value = $background->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Value')->item(0);
      $measurement->background->value->value = $value->textContent;
      $measurement->background->value->unit = $value->getAttribute('Unit');
      $c = $value->getAttribute('Constraint'); if (!empty($c)) $measurement->background->value->constraint = $c;

      $uncertainty = $background->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Uncertainty')->item(0);
      if (!is_null($uncertainty)) {
        $measurement->background->uncertainty = new \IRIX\Uncertainty();
        $measurement->background->uncertainty->uncertainty = $uncertainty->textContent;
        $measurement->background->uncertainty->unit = $uncertainty->getAttribute('Unit');
        $t = $uncertainty->getAttribute('Type'); if (!empty($t)) $measurement->background->uncertainty->type = $t;
        $c = $uncertainty->getAttribute('Constraint'); if (!empty($c)) $measurement->background->uncertainty->constraint = $c;
      }

      $item = $background->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Timebase')->item(0); if (!is_null($item)) $measurement->background->timebase = $item->textContent;
      $item = $background->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Method')->item(0); if (!is_null($item)) $measurement->background->method = $item->textContent;
    }

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Validated')->item(0); if (!is_null($item)) $measurement->validated = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Description')->item(0); if (!is_null($item)) $measurement->description = $item->textContent;

    return $measurement;
  }
}