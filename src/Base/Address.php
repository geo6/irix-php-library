<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\Base;

/**
 * IRIX PHP Library - Address Container.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Address
{
    public $type;

    public $postal_code;
    public $municipality;
    public $country;

    public $postbox = null;
    public $street = null;

    private $_xml;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');

        $address = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Address');
        $this->_xml->appendChild($address);
        $address->setAttribute('Type', $this->type);
        if (!is_null($this->postbox)) {
            $postbox = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Postbox', $this->postbox);
            $address->appendChild($postbox);
        }
        if (!is_null($this->street)) {
            $street = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Street', $this->street);
            $address->appendChild($street);
        }
        $postal_code = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'PostalCode', $this->postal_code);
        $address->appendChild($postal_code);
        $municipality = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Municipality', $this->municipality);
        $address->appendChild($municipality);
        $country = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Country', $this->country);
        $address->appendChild($country);

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Address')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $address = new self();

        $address->type = $domelement->getAttribute('Type');

        $address->postal_code = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'PostalCode')->item(0)->textContent;
        $address->municipality = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Municipality')->item(0)->textContent;
        $address->country = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Country')->item(0)->textContent;

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Postbox')->item(0);
        if (!is_null($item)) {
            $address->postbox = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Street')->item(0);
        if (!is_null($item)) {
            $address->street = $item->textContent;
        }

        return $address;
    }
}
