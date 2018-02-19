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
 * IRIX PHP Library - OrganisationContactInfo Container.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class OrganisationContactInfo
{
    public $name;
    public $organisation_id;
    public $country;

    public $web_address = null;
    public $address = null;
    public $phone_number = null;
    public $fax_number = null;
    public $email_address = null;
    public $description = null;

    private $_xml;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');

        $organisation_contact_info = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'OrganisationContactInfo');
        $this->_xml->appendChild($organisation_contact_info);
        $name = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Name', $this->name);
        $organisation_contact_info->appendChild($name);
        $organisation_id = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'OrganisationID', $this->organisation_id);
        $organisation_contact_info->appendChild($organisation_id);
        $country = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Country', $this->country);
        $organisation_contact_info->appendChild($country);
        if (!is_null($this->web_address)) {
            $web_address = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'WebAddress', $this->web_address);
            $organisation_contact_info->appendChild($web_address);
        }
        if (!is_null($this->address)) {
            if (is_array($this->address)) {
                foreach ($this->address as $a) {
                    $organisation_contact_info->appendChild($this->_xml->importNode($a->getXMLElement(), true));
                }
            } else {
                $organisation_contact_info->appendChild($this->_xml->importNode($this->address->getXMLElement(), true));
            }
        }
        if (!is_null($this->phone_number)) {
            $phone_number = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'PhoneNumber', $this->phone_number);
            $organisation_contact_info->appendChild($phone_number);
        }
        if (!is_null($this->fax_number)) {
            $fax_number = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'FaxNumber', $this->fax_number);
            $organisation_contact_info->appendChild($fax_number);
        }
        if (!is_null($this->email_address)) {
            $email_address = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'EmailAddress', $this->email_address);
            $organisation_contact_info->appendChild($email_address);
        }
        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Description', $this->description);
            $organisation_contact_info->appendChild($description);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'OrganisationContactInfo')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $organisation_contact_info = new self();

        $organisation_contact_info->name = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Name')->item(0)->textContent;
        $organisation_contact_info->organisation_id = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'OrganisationID')->item(0)->textContent;
        $organisation_contact_info->country = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Country')->item(0)->textContent;

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'WebAddress')->item(0);
        if (!is_null($item)) {
            $organisation_contact_info->web_address = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Address')->item(0);
        if (!is_null($item)) {
            $organisation_contact_info->address = \IRIX\Base\Address::readXMLElement($item);
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'PhoneNumber')->item(0);
        if (!is_null($item)) {
            $organisation_contact_info->phone_number = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'FaxNumber')->item(0);
        if (!is_null($item)) {
            $organisation_contact_info->fax_number = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Description')->item(0);
        if (!is_null($item)) {
            $organisation_contact_info->description = $item->textContent;
        }

        return $organisation_contact_info;
    }
}
