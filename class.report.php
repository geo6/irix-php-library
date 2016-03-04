<?php
namespace IRIX;

/**
 * IRIX PHP Library - Report
 * @package IRIX
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Report {
  const VERSION = '1.0';
  const DEBUG = TRUE;

  public $identification = NULL;
  private $event_information = NULL;
  private $release = NULL;
  private $meteorology = NULL;
  private $consequences = NULL;
  private $response_actions = NULL;
  private $measurements = NULL;
  private $medical_information = NULL;
  private $media_information = NULL;
  private $locations = NULL;
  private $requests = NULL;
  private $annexes = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function __construct() {
    $this->identification = new \IRIX\Identification();
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = self::DEBUG;

    $report = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format', 'irix:Report');
    $report->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $report->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:id', 'http://www.iaea.org/2012/IRIX/Format/Identification');
    $report->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:html', 'http://www.w3.org/1999/xhtml');
    $report->setAttribute('version', self::VERSION);
    $this->_xml->appendChild($report);

    $report->appendChild($this->_xml->importNode($this->identification->getXMLElement(), TRUE));

    return $this->_xml->saveXML();
  }

  /**
   *
   */
  public function validate($update = TRUE) {
    if ($update) $this->toXML();
    return $this->_xml->schemaValidate(__DIR__.'/xsd/'.self::VERSION.'/IRIX.xsd');
  }

  /**
   *
   */
  public function write($filename) {
    if ($this->validate()) { return $this->_xml->save($filename); }
    return FALSE;
  }

  /**
   *
   */
  public static function read($filename) {
    if (file_exists($filename)) {
      $r = new self();

      $r->_xml = new \DOMDocument();
      $r->_xml->load($filename);

      if ($r->validate(FALSE)) {
        $r->identification = \IRIX\Identification::read($filename);

        return $r;
      }
    }

    return FALSE;
  }

  /**
   *
   */
  public static function UUIDv4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),
      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,
      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,
      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }
}

/* ************************************************************************
 *
 */

namespace IRIX\Base;

/**
 * IRIX PHP Library - PersonContactInfo Container
 * @package IRIX\Base
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class PersonContactInfo {
  public $name;

  public $user_id = NULL;
  public $position = NULL;
  public $organisation_id = NULL;
  public $phone_number = NULL;
  public $fax_number = NULL;
  public $email_address = NULL;
  public $description = NULL;

  private $_xml;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');

    $person_contact_info = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'PersonContactInfo'); $this->_xml->appendChild($person_contact_info);
    $name = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Name', $this->name); $person_contact_info->appendChild($name);
    if (!is_null($this->user_id)) { $user_id = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'UserID', $this->user_id); $person_contact_info->appendChild($user_id); }
    if (!is_null($this->position)) { $position = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Position', $this->position); $person_contact_info->appendChild($position); }
    if (!is_null($this->organisation_id)) { $organisation_id = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationID', $this->organisation_id); $person_contact_info->appendChild($organisation_id); }
    if (!is_null($this->phone_number)) { $phone_number = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'PhoneNumber', $this->phone_number); $person_contact_info->appendChild($phone_number); }
    if (!is_null($this->fax_number)) { $fax_number = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'FaxNumber', $this->fax_number); $person_contact_info->appendChild($fax_number); }
    if (!is_null($this->email_address)) { $email_address = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'EmailAddress', $this->email_address); $person_contact_info->appendChild($email_address); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Description', $this->description); $person_contact_info->appendChild($description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'PersonContactInfo')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $person_contact_info = new self();

    $person_contact_info->name = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Name')->item(0)->textContent;

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'UserID'        )->item(0); if (!is_null($item)) $person_contact_info->user_id = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Position'      )->item(0); if (!is_null($item)) $person_contact_info->position = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationID')->item(0); if (!is_null($item)) $person_contact_info->organisation_id = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'PhoneNumber'   )->item(0); if (!is_null($item)) $person_contact_info->phone_number = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'FaxNumber'     )->item(0); if (!is_null($item)) $person_contact_info->fax_number = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'EmailAddress'  )->item(0); if (!is_null($item)) $person_contact_info->email_address = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Description'   )->item(0); if (!is_null($item)) $person_contact_info->description = $item->textContent;

    return $person_contact_info;
  }
}

/**
 * IRIX PHP Library - OrganisationContactInfo Container
 * @package IRIX\Base
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class OrganisationContactInfo {
  public $name;
  public $organisation_id;
  public $country;

  public $web_address = NULL;
  public $address = NULL;
  public $phone_number = NULL;
  public $fax_number = NULL;
  public $email_address = NULL;
  public $description = NULL;

  private $_xml;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');

    $organisation_contact_info = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationContactInfo'); $this->_xml->appendChild($organisation_contact_info);
    $name = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Name', $this->name); $organisation_contact_info->appendChild($name);
    $organisation_id = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationID', $this->organisation_id); $organisation_contact_info->appendChild($organisation_id);
    $country = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Country', $this->country); $organisation_contact_info->appendChild($country);
    if (!is_null($this->web_address)) { $web_address = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'WebAddress', $this->web_address); $organisation_contact_info->appendChild($web_address); }
    if (!is_null($this->address)) {
      if (is_array($this->address)) {
        foreach ($this->address as $a) $organisation_contact_info->appendChild($this->_xml->importNode($a->getXMLElement(), TRUE));
      } else {
        $organisation_contact_info->appendChild($this->_xml->importNode($this->address->getXMLElement(), TRUE));
      }
    }
    if (!is_null($this->phone_number)) { $phone_number = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'PhoneNumber', $this->phone_number); $organisation_contact_info->appendChild($phone_number); }
    if (!is_null($this->fax_number)) { $fax_number = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'FaxNumber', $this->fax_number); $organisation_contact_info->appendChild($fax_number); }
    if (!is_null($this->email_address)) { $email_address = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'EmailAddress', $this->email_address); $organisation_contact_info->appendChild($email_address); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Description', $this->description); $organisation_contact_info->appendChild($description); }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationContactInfo')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $organisation_contact_info = new self();

    $organisation_contact_info->name            = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Name'          )->item(0)->textContent;
    $organisation_contact_info->organisation_id = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationID')->item(0)->textContent;
    $organisation_contact_info->country         = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Country'       )->item(0)->textContent;

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'WebAddress' )->item(0); if (!is_null($item)) $organisation_contact_info->web_address  = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Address'    )->item(0); if (!is_null($item)) $organisation_contact_info->address      = \IRIX\Base\Address::readXMLElement($item);
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'PhoneNumber')->item(0); if (!is_null($item)) $organisation_contact_info->phone_number = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'FaxNumber'  )->item(0); if (!is_null($item)) $organisation_contact_info->fax_number   = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Description')->item(0); if (!is_null($item)) $organisation_contact_info->web_address  = $item->textContent;

    return $organisation_contact_info;
  }
}

/**
 * IRIX PHP Library - Address Container
 * @package IRIX\Base
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Address {
  public $type;

  public $postal_code;
  public $municipality;
  public $country;

  public $postbox = NULL;
  public $street = NULL;

  private $_xml;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');

    $address = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Address'); $this->_xml->appendChild($address);
    $address->setAttribute('Type', $this->type);
    if (!is_null($this->postbox)) { $postbox = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Postbox', $this->postbox); $address->appendChild($postbox); }
    if (!is_null($this->street)) { $street = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Street', $this->street); $address->appendChild($street); }
    $postal_code = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'PostalCode', $this->postal_code); $address->appendChild($postal_code);
    $municipality = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Municipality', $this->municipality); $address->appendChild($municipality);
    $country = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Country', $this->country); $address->appendChild($country);

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Address')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $address = new self();

    $address->type = $domelement->getAttribute('Type');

    $address->postal_code  = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'PostalCode'  )->item(0)->textContent;
    $address->municipality = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Municipality')->item(0)->textContent;
    $address->country      = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Country'     )->item(0)->textContent;

    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Postbox')->item(0); if (!is_null($item)) $address->postbox = $item->textContent;
    $item = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Street' )->item(0); if (!is_null($item)) $street->address  = $item->textContent;

    return $address;
  }
}