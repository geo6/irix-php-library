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
 * IRIX PHP Library - Identification Section
 * @package IRIX\Report
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Identification {
  /** @var string Identifier (domain name) of the organisation issuing this report. */
  public $organisation_reporting;
  public $date_and_time_of_creation;
  public $report_context;
  public $report_uuid;
  public $identifications;

  public $sequence_number = NULL;
  public $follows = NULL;
  public $revokes = NULL;
  public $confidentiality = NULL;
  public $addressees = NULL;
  public $reporting_bases = NULL;
  public $contact_person = NULL;
  public $additional_information_url = NULL;
  public $event_identifications = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function __construct() {
    $this->date_and_time_of_creation = gmdate('Y-m-d\TH:i:s\Z');
    $this->report_uuid = Report::UUIDv4();
    $this->identifications = new Identification\Identifications();
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $identification = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'id:Identification'); $this->_xml->appendChild($identification);
    $identification->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $identification->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    $organisation_reporting    = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'OrganisationReporting', $this->organisation_reporting   ); $identification->appendChild($organisation_reporting);
    $date_and_time_of_creation = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'DateAndTimeOfCreation', $this->date_and_time_of_creation); $identification->appendChild($date_and_time_of_creation);
    $report_context            = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportContext'        , $this->report_context           ); $identification->appendChild($report_context);

    if (!is_null($this->sequence_number)) { $sequence_number = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'SequenceNumber', $this->sequence_number); $identification->appendChild($sequence_number); }

    $report_uuid               = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportUUID'           , $this->report_uuid              ); $identification->appendChild($report_uuid);

    if (!is_null($this->follows        )) { $follows         = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Follows'        , $this->follows        ); $identification->appendChild($follows); }
    if (!is_null($this->revokes        )) { $revokes         = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Revokes'        , $this->revokes        ); $identification->appendChild($revokes); }
    if (!is_null($this->confidentiality)) { $confidentiality = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Confidentiality', $this->confidentiality); $identification->appendChild($confidentiality); }

    if (!is_null($this->addressees)) {
      $addressees = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Addressees');
      foreach ($this->addressees as $a) { $addressee = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Addressee', $a); $addressees->appendChild($addressee); }
      $identification->appendChild($addressees);
    }

    if (!is_null($this->reporting_bases)) {
      $reporting_bases = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportingBases');
      foreach ($this->reporting_bases as $rb) { $reporting_basis = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportingBasis', $rb); $reporting_bases->appendChild($reporting_basis); }
      $identification->appendChild($reporting_bases);
    }

    if (!is_null($this->contact_person            )) { $contact_person             = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ContactPerson'           , $this->contact_person            ); $identification->appendChild($contact_person); }
    if (!is_null($this->additional_information_url)) { $additional_information_url = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'AdditionalInformationURL', $this->additional_information_url); $identification->appendChild($additional_information_url); }

    if (!is_null($this->event_identifications)) {
      $event_identifications = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'EventIdentifications');
      foreach ($this->event_identifications as $ei) {
        if (is_array($ei)) {
          $event_identification = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'EventIdentification', $ei[0]);
          if (isset($ei[1])) $event_identification->setAttribute('Organisation', $ei[1]);
        } else {
          $event_identification = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'EventIdentification', $ei);
        }
        $event_identifications->appendChild($event_identification);
      }
      $identification->appendChild($event_identifications);
    }

    $identification->appendChild($this->_xml->importNode($this->identifications->getXMLElement(), TRUE));

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Identification')->item(0);
  }

  /**
   *
   */
  public function validate($update = TRUE) {
    if ($update) $this->toXML();
    return $this->_xml->schemaValidate(__DIR__.'/xsd/'.\IRIX\Report::VERSION.'/ReportIdentification.xsd');
  }

  /**
   *
   */
  public static function read($filename) {
    if (file_exists($filename)) {
      $xml = new \DOMDocument(); $xml->load($filename);

      $identification = $xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Identification')->item(0);

      if (!is_null($identification)) {
        $i = new self();
        $i->_xml = new \DOMDocument('1.0', 'UTF-8');
        $i->_xml->appendChild($i->_xml->importNode($identification, TRUE));

        if ($i->validate(FALSE)) {
          $i->organisation_reporting    = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'OrganisationReporting')->item(0)->textContent;
          $i->date_and_time_of_creation = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'DateAndTimeOfCreation')->item(0)->textContent;
          $i->report_context            = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportContext'        )->item(0)->textContent;
          $i->report_uuid               = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportUUID'           )->item(0)->textContent;

          $i->identifications = \IRIX\Identification\Identifications::readXMLElement($identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Identifications')->item(0));

          $item = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'SequenceNumber'          )->item(0); if (!is_null($item)) $i->sequence_number            = $item->textContent;
          $item = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Follows'                 )->item(0); if (!is_null($item)) $i->follows                    = $item->textContent;
          $item = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Revokes'                 )->item(0); if (!is_null($item)) $i->revokes                    = $item->textContent;
          $item = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Confidentiality'         )->item(0); if (!is_null($item)) $i->confidentiality            = $item->textContent;

          $item = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Addressees'              )->item(0); if (!is_null($item)) { $i->addressees = array(); foreach ($item->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Addressee') as $a) $i->addressees[] = $a->textContent; }
          $item = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportingBases'          )->item(0); if (!is_null($item)) { $i->reporting_bases = array(); foreach ($item->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportingBasis') as $rb) $i->reporting_bases[] = $rb->textContent; }

          $item = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ContactPerson'           )->item(0); if (!is_null($item)) $i->contact_person             = $item->textContent;
          $item = $identification->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'AdditionalInformationURL')->item(0); if (!is_null($item)) $i->additional_information_url = $item->textContent;

          $i->event_identifications = NULL;

          return $i;
        }
      }
    }

    return FALSE;
  }
}

/* ************************************************************************
 *
 */
namespace IRIX\Identification;

class Identifications {
  public $person_contact_info = NULL;
  public $organisation_contact_info = array();

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $identifications = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Identifications'); $this->_xml->appendChild($identifications);
    $identifications->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $identifications->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    if (!is_null($this->person_contact_info)) foreach ($this->person_contact_info as $p) $identifications->appendChild($this->_xml->importNode($p->getXMLElement(), TRUE));
    foreach ($this->organisation_contact_info as $o) $identifications->appendChild($this->_xml->importNode($o->getXMLElement(), TRUE));

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Identifications')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $identifications = new self();

    $pci = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'PersonContactInfo');
    if (!is_null($pci)) { $identifications->person_contact_info = array(); for ($i = 0; $i < $pci->length; $i++) $identifications->person_contact_info[] = \IRIX\Base\PersonContactInfo::readXMLElement($pci->item($i)); }

    $oci = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationContactInfo');
    for ($i = 0; $i < $oci->length; $i++) $identifications->organisation_contact_info[] = \IRIX\Base\OrganisationContactInfo::readXMLElement($oci->item($i));

    return $identifications;
  }
}