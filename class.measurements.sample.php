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
 * @package IRIX\Report
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Sample {
  public $sampling_period;
  public $location;
  public $measurements = array();

  /* One of the following required */
  public $sample_type = NULL;
  public $sample_type_description = NULL;

  public $location_offset = NULL;
  public $value_type = NULL;
  public $sampling_depth = NULL;
  public $surface_type_description = NULL;
  public $volume = NULL;
  public $sample_treatment = NULL;
  public $sample_treatment_description = NULL;
  public $description = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function __construct() {
    $this->sampling_period = new \IRIX\SamplingPeriod();
    $this->location = new \IRIX\Locations\Location();
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $sample = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'mon:Sample'); $this->_xml->appendChild($sample);
    $sample->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:mon', 'http://www.iaea.org/2012/IRIX/Format/Measurements');
    $sample->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:loc', 'http://www.iaea.org/2012/IRIX/Format/Locations');
    $sample->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $sample->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    if (!is_null($this->sample_type)) { $sample_type = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SampleType', $this->sample_type); $sample->appendChild($sample_type); }
    if (!is_null($this->sample_type_description)) { $sample_type_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SampleTypeDescription', $this->sample_type_description); $sample->appendChild($sample_type_description); }

    $sampling_period = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SamplingPeriod'); $sample->appendChild($sampling_period);
    $start_time = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'StartTime', $this->sampling_period->start_time); $sampling_period->appendChild($start_time);
    $end_time = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'EndTime', $this->sampling_period->end_time); $sampling_period->appendChild($end_time);

    if (is_string($this->location)) {
      $location = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location'); $sample->appendChild($location);
      $location->setAttribute('ref', $this->location);
    } else {
      $sample->appendChild($this->_xml->importNode($this->location->getXMLElement(), TRUE));
    }

    if (!is_null($this->location_offset)) {
      $location_offset = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'LocationOffset'); $sample->appendChild($location_offset);
      $distance = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Distance', $this->location_offset->distance); $distance->setAttribute('Unit', 'm'); $location_offset->appendChild($distance);
      $direction = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Direction', $this->location_offset->direction); $direction->setAttribute('Unit', 'DegreesFromNorth'); $location_offset->appendChild($direction);
    }

    if (!is_null($this->value_type)) { $value_type = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ValueType', $this->value_type); $sample->appendChild($value_type); }

    if (!is_null($this->sampling_depth)) {
      $sampling_depth = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SamplingDepth'); $sample->appendChild($sampling_depth);
      if (!is_null($this->sampling_depth->max_depth)) { $max_depth = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'MaxDepth', $this->sampling_depth->max_depth); $sampling_depth->appendChild($max_depth); }
      if (!is_null($this->sampling_depth->min_depth)) { $min_depth = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'MinDepth', $this->sampling_depth->min_depth); $sampling_depth->appendChild($min_depth); }
    }

    if (!is_null($this->surface_type_description)) { $surface_type_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SurfaceTypeDescription', $this->surface_type_description); $sample->appendChild($surface_type_description); }
    if (!is_null($this->volume)) { $volume = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Volume', $this->volume); $sample->appendChild($volume); }
    if (!is_null($this->sample_treatment)) { $sample_treatment = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SampleTreatment', $this->sample_treatment); $sample->appendChild($sample_treatment); }
    if (!is_null($this->sample_treatment_description)) { $sample_treatment_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SampleTreatmentDescription', $this->sample_treatment_description); $sample->appendChild($sample_treatment_description); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Description', $this->description); $sample->appendChild($description); }

    $measurements = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Measurements'); $sample->appendChild($measurements);
    foreach ($this->measurements as $m) $measurements->appendChild($this->_xml->importNode($m->getXMLElement(), TRUE));

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Sample')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $sample = new self();

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SampleType')->item(0); if (!is_null($item)) $sample->sample_type = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SampleTypeDescription')->item(0); if (!is_null($item)) $sample->sample_type_description = $item->textContent;

    $sampling_period = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SamplingPeriod')->item(0);
    $sample->sampling_period = new \IRIX\SamplingPeriod();
    $sample->sampling_period->start_time = $sampling_period->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'StartTime')->item(0)->textContent;
    $sample->sampling_period->end_time = $sampling_period->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'EndTime')->item(0)->textContent;

    $location = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location')->item(0);
    $ref = $location->getAttribute('ref');
    if (is_null($location->firstChild) && !empty($ref)) {
      $sample->location = $ref;
    } else {
      $sample->location = \IRIX\Locations\Location::readXMLElement($location);
    }

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ValueType')->item(0); if (!is_null($item)) $sample->value_type = $item->textContent;

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SamplingDepth')->item(0);
    if (!is_null($item)) {
      $sample->sampling_depth = new \IRIX\SamplingDepth();
      $max_depth = $item->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'MaxDepth')->item(0); if (!is_null($max_depth)) $sample->sampling_depth->max_depth = $max_depth->textContent;
      $min_depth = $item->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'MinDepth')->item(0); if (!is_null($min_depth)) $sample->sampling_depth->min_depth = $min_depth->textContent;
    }

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SurfaceTypeDescription')->item(0); if (!is_null($item)) $sample->surface_type_description = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Volume')->item(0); if (!is_null($item)) $sample->volume = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SampleTreatment')->item(0); if (!is_null($item)) $sample->sample_treatment = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'SampleTreatmentDescription')->item(0); if (!is_null($item)) $sample->sample_treatment_description = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Description')->item(0); if (!is_null($item)) $sample->description = $item->textContent;

    $measurements = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Measurements');
    if ($measurements->length > 0) { $sample->measurements = array(); for ($i = 0; $i < $measurements->length; $i++) $sample->measurements[] = \IRIX\Measurements\Sample\Measurement::readXMLElement($measurements->item($i)); }

    return $sample;
  }
}

namespace IRIX\Measurements\Sample;

class Measurement {
  public $value;

  /* One of the following required */
  public $nuclide = NULL;
  public $nuclide_list = NULL;
  public $nuclide_combination = NULL;
  public $nuclide_description = NULL;

  public $uncertainty = NULL;
  public $timebase = NULL;
  public $background = NULL;
  public $measuring_period = NULL;
  public $reference_date_and_time = NULL;
  public $apparatus_type = NULL;
  public $apparatus_type_description = NULL;
  public $validated = NULL;
  public $laboratory = NULL;
  public $description = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function __construct() {
    $this->value = new \IRIX\Value();
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $measurement = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Measurement'); $this->_xml->appendChild($measurement);
    $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $measurement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    if (!is_null($this->nuclide)) { $nuclide = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Nuclide', $this->nuclide); $measurement->appendChild($nuclide); }
    if (!is_null($this->nuclide_list)) { $nuclide_list = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'NuclideList', $this->nuclide_list); $measurement->appendChild($nuclide_list); }
    if (!is_null($this->nuclide_combination)) { $nuclide_combination = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'NuclideCombination', $this->nuclide_combination); $measurement->appendChild($nuclide_combination); }
    if (!is_null($this->nuclide_description)) { $nuclide_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'NuclideDescription', $this->nuclide_description); $measurement->appendChild($nuclide_description); }

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

    if (!is_null($this->measuring_period)) {
      $measuring_period = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'MeasuringPeriod'); $measurement->appendChild($measuring_period);
      $start_time = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'StartTime', $this->sampling_period->start_time); $measuring_period->appendChild($start_time);
      $end_time = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'EndTime', $this->sampling_period->end_time); $measuring_period->appendChild($end_time);
    }

    if (!is_null($this->reference_date_and_time)) { $reference_date_and_time = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ReferenceDateAndTime', $this->reference_date_and_time); $measurement->appendChild($reference_date_and_time); }
    if (!is_null($this->apparatus_type)) { $apparatus_type = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ApparatusType', $this->apparatus_type); $measurement->appendChild($apparatus_type); }
    if (!is_null($this->apparatus_type_description)) { $apparatus_type_description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ApparatusTypeDescription', $this->apparatus_type_description); $measurement->appendChild($apparatus_type_description); }
    if (!is_null($this->validated)) { $validated = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Validated', $this->validated); $measurement->appendChild($validated); }
    if (!is_null($this->laboratory)) { $laboratory = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Laboratory', $this->laboratory); $measurement->appendChild($laboratory); }
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

    $measuring_period = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'MeasuringPeriod')->item(0);
    if (!is_null($measuring_period)) {
      $measurement->measuring_period = new \IRIX\MeasuringPeriod();
      $measurement->measuring_period->start_time = $measuring_period->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'StartTime')->item(0)->textContent;
      $measurement->measuring_period->end_time = $measuring_period->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'EndTime')->item(0)->textContent;
    }

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ReferenceDateAndTime')->item(0); if (!is_null($item)) $measurement->reference_date_and_time = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ApparatusType')->item(0); if (!is_null($item)) $measurement->apparatus_type = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'ApparatusTypeDescription')->item(0); if (!is_null($item)) $measurement->apparatus_type_description = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Validated')->item(0); if (!is_null($item)) $measurement->validated = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Laboratory')->item(0); if (!is_null($item)) $measurement->laboratory = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Measurements', 'Description')->item(0); if (!is_null($item)) $measurement->description = $item->textContent;

    return $measurement;
  }
}