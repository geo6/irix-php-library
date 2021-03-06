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
 * IRIX PHP Library - PersonContactInfo Container.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class PersonContactInfo
{
    public $name;

    public $user_id = null;
    public $position = null;
    public $organisation_id = null;
    public $phone_number = null;
    public $fax_number = null;
    public $email_address = null;
    public $description = null;

    private $_xml;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');

        $person_contact_info = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'PersonContactInfo');
        $this->_xml->appendChild($person_contact_info);
        $name = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Name', $this->name);
        $person_contact_info->appendChild($name);
        if (!is_null($this->user_id)) {
            $user_id = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'UserID', $this->user_id);
            $person_contact_info->appendChild($user_id);
        }
        if (!is_null($this->position)) {
            $position = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Position', $this->position);
            $person_contact_info->appendChild($position);
        }
        if (!is_null($this->organisation_id)) {
            $organisation_id = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'OrganisationID', $this->organisation_id);
            $person_contact_info->appendChild($organisation_id);
        }
        if (!is_null($this->phone_number)) {
            $phone_number = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'PhoneNumber', $this->phone_number);
            $person_contact_info->appendChild($phone_number);
        }
        if (!is_null($this->fax_number)) {
            $fax_number = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'FaxNumber', $this->fax_number);
            $person_contact_info->appendChild($fax_number);
        }
        if (!is_null($this->email_address)) {
            $email_address = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'EmailAddress', $this->email_address);
            $person_contact_info->appendChild($email_address);
        }
        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Base', 'Description', $this->description);
            $person_contact_info->appendChild($description);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'PersonContactInfo')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $person_contact_info = new self();

        $person_contact_info->name = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Name')->item(0)->textContent;

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'UserID')->item(0);
        if (!is_null($item)) {
            $person_contact_info->user_id = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Position')->item(0);
        if (!is_null($item)) {
            $person_contact_info->position = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'OrganisationID')->item(0);
        if (!is_null($item)) {
            $person_contact_info->organisation_id = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'PhoneNumber')->item(0);
        if (!is_null($item)) {
            $person_contact_info->phone_number = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'FaxNumber')->item(0);
        if (!is_null($item)) {
            $person_contact_info->fax_number = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'EmailAddress')->item(0);
        if (!is_null($item)) {
            $person_contact_info->email_address = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Base', 'Description')->item(0);
        if (!is_null($item)) {
            $person_contact_info->description = $item->textContent;
        }

        return $person_contact_info;
    }
}
