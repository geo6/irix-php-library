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
 * IRIX PHP Library - Locations Section
 * @package IRIX\Report
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Locations {
  public $location = array();

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $locations = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'loc:Locations'); $this->_xml->appendChild($locations);
    $locations->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:loc', 'http://www.iaea.org/2012/IRIX/Format/Locations');
    $locations->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $locations->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    if (!is_null($this->location)) {
      if (!is_array($this->location)) $this->location = array( $this->location );
      foreach ($this->location as $l) { $locations->appendChild($this->_xml->importNode($l->getXMLElement(), TRUE)); }
    }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Locations')->item(0);
  }

  /**
   *
   */
  public function validate($update = TRUE) {
    if ($update) $this->toXML();
    return $this->_xml->schemaValidate(__DIR__.'/xsd/'.\IRIX\Report::VERSION.'/Locations.xsd');
  }

  /**
   *
   */
  public static function read($filename) {
    if (file_exists($filename)) {
      $xml = new \DOMDocument(); $xml->load($filename);

      $locations = $xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Locations')->item(0);

      if (!is_null($locations)) {
        $l = new self();
        $l->_xml = new \DOMDocument('1.0', 'UTF-8');
        $l->_xml->appendChild($l->_xml->importNode($locations, TRUE));

        if ($l->validate(FALSE)) {
          $location = $locations->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location');
          if ($location->length > 0) { $l->location = array(); for ($i = 0; $i < $location->length; $i++) $l->location[] = \IRIX\Locations\Location::readXMLElement($location->item($i)); }

          return $l;
        }
      } else {
        return NULL;
      }
    }

    return FALSE;
  }
}

/* ************************************************************************
 *
 */
namespace IRIX\Locations;

class Location {
  public $id;

  public $name = NULL;
  public $geographic_coordinates = NULL;
  public $municipality = NULL;
  public $administrative_unit = NULL;
  public $country = NULL;
  public $accuracy_type = NULL;
  public $description = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $location = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location'); $this->_xml->appendChild($location);
    $location->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:loc', 'http://www.iaea.org/2012/IRIX/Format/Locations');
    $location->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $location->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    if (!empty($this->id)) $location->setAttribute('id', $this->id);

    if (!is_null($this->name)) { $name = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Name', $this->name); $location->appendChild($name); }
    if (!is_null($this->geographic_coordinates)) {
      $geographic_coordinates = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'GeographicCoordinates'); $location->appendChild($geographic_coordinates);
      $latitude = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Latitude', $this->geographic_coordinates->latitude); $geographic_coordinates->appendChild($latitude);
      $longitude = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Longitude', $this->geographic_coordinates->longitude); $geographic_coordinates->appendChild($longitude);
      if (!is_null($this->geographic_coordinates->height)) {
        if (!is_array($this->geographic_coordinates->height)) $this->geographic_coordinates->height = array( $this->geographic_coordinates->height );
        foreach ($this->geographic_coordinates->height as $h) {
          $_h = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Height', $h->height);
          $_h->setAttribute('Above', $h->above);
          $_h->setAttribute('Unit', 'm');
          $geographic_coordinates->appendChild($_h);
        }
      }
    }
    if (!is_null($this->municipality)) { $municipality = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Municipality', $this->municipality); $location->appendChild($municipality); }
    if (!is_null($this->administrative_unit)) { $administrative_unit = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'AdministrativeUnit', $this->administrative_unit); $location->appendChild($administrative_unit); }
    if (!is_null($this->country)) { $country = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Country', $this->country); $location->appendChild($country); }
    if (!is_null($this->accuracy_type)) { $accuracy_type = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'AccuracyType', $this->accuracy_type); $location->appendChild($accuracy_type); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Description', $this->description); $location->appendChild($description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Location')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $location = new self();

    $location->id = $domelement->getAttribute('id');

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Name')->item(0); if (!is_null($item)) $location->name = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Municipality')->item(0); if (!is_null($item)) $location->municipality = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'AdministrativeUnit')->item(0); if (!is_null($item)) $location->administrative_unit = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Country')->item(0); if (!is_null($item)) $location->country = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'AccuracyType')->item(0); if (!is_null($item)) $location->accuracy_type = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Description')->item(0); if (!is_null($item)) $location->description = $item->textContent;

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'GeographicCoordinates')->item(0);
    if (!is_null($item)) {
      $location->geographic_coordinates = new \IRIX\GeographicCoordinates();
      $location->geographic_coordinates->latitude = $item->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Latitude')->item(0)->textContent;
      $location->geographic_coordinates->longitude = $item->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Longitude')->item(0)->textContent;

      $height = $item->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Locations', 'Height')->item(0);
      if (!is_null($height)) {
        $location->geographic_coordinates->height = new \IRIX\Height();
        $location->geographic_coordinates->height->height = $height->textContent;
        $location->geographic_coordinates->height->above = $height->getAttribute('Above');
      }
    }

    return $location;
  }
}