<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\Locations;

class Location
{
    public $id;

    public $name = null;
    public $geographic_coordinates = null;
    public $municipality = null;
    public $administrative_unit = null;
    public $country = null;
    public $accuracy_type = null;
    public $description = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $location = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Location');
        $this->_xml->appendChild($location);
        $location->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:loc', \IRIX\Report::_NAMESPACE.'/Locations');
        $location->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $location->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        if (!empty($this->id)) {
            $location->setAttribute('id', $this->id);
        }

        if (!is_null($this->name)) {
            $name = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Name', $this->name);
            $location->appendChild($name);
        }
        if (!is_null($this->geographic_coordinates)) {
            $geographic_coordinates = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'GeographicCoordinates');
            $location->appendChild($geographic_coordinates);
            $latitude = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Latitude', $this->geographic_coordinates->latitude);
            $geographic_coordinates->appendChild($latitude);
            $longitude = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Longitude', $this->geographic_coordinates->longitude);
            $geographic_coordinates->appendChild($longitude);
            if (!is_null($this->geographic_coordinates->height)) {
                if (!is_array($this->geographic_coordinates->height)) {
                    $this->geographic_coordinates->height = [$this->geographic_coordinates->height];
                }
                foreach ($this->geographic_coordinates->height as $h) {
                    $_h = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Height', $h->value);
                    $_h->setAttribute('Above', $h->above);
                    $_h->setAttribute('Unit', 'm');
                    $geographic_coordinates->appendChild($_h);
                }
            }
        }
        if (!is_null($this->municipality)) {
            $municipality = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Municipality', $this->municipality);
            $location->appendChild($municipality);
        }
        if (!is_null($this->administrative_unit)) {
            $administrative_unit = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'AdministrativeUnit', $this->administrative_unit);
            $location->appendChild($administrative_unit);
        }
        if (!is_null($this->country)) {
            $country = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Country', $this->country);
            $location->appendChild($country);
        }
        if (!is_null($this->accuracy_type)) {
            $accuracy_type = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'AccuracyType', $this->accuracy_type);
            $location->appendChild($accuracy_type);
        }
        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Description', $this->description);
            $location->appendChild($description);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Location')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $location = new self();

        $location->id = $domelement->getAttribute('id');

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Name')->item(0);
        if (!is_null($item)) {
            $location->name = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Municipality')->item(0);
        if (!is_null($item)) {
            $location->municipality = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'AdministrativeUnit')->item(0);
        if (!is_null($item)) {
            $location->administrative_unit = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Country')->item(0);
        if (!is_null($item)) {
            $location->country = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'AccuracyType')->item(0);
        if (!is_null($item)) {
            $location->accuracy_type = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Description')->item(0);
        if (!is_null($item)) {
            $location->description = $item->textContent;
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'GeographicCoordinates')->item(0);
        if (!is_null($item)) {
            $latitude = $item->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Latitude')->item(0)->textContent;
            $longitude = $item->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Longitude')->item(0)->textContent;
            $location->geographic_coordinates = new \IRIX\Miscellaneous\GeographicCoordinates($latitude, $longitude);

            $height = $item->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Locations', 'Height')->item(0);
            if (!is_null($height)) {
                $location->geographic_coordinates->height = new \IRIX\Height();
                $location->geographic_coordinates->height->value = $height->textContent;
                $location->geographic_coordinates->height->above = $height->getAttribute('Above');
            }
        }

        return $location;
    }
}
