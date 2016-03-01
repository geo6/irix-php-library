<?php
namespace IRIX;

class Identification {
  public $organisation_reporting;
  public $date_and_time_of_creation;
  public $report_context;
  public $report_uuid;
  public $identifications;

  private $_xml = NULL;

  public function __construct($organisation_reporting, $report_context) {
    $this->organisation_reporting = $organisation_reporting;
    $this->date_and_time_of_creation = gmdate('Y-m-d\TH:i:s\Z');
    $this->report_context = $report_context;
    $this->report_uuid = Report::UUIDv4();
    $this->identifications = new Identification\Identifications();
  }

  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $identification = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'id:Identification'); $this->_xml->appendChild($identification);
    $identification->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $organisation_reporting = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'OrganisationReporting', $this->organisation_reporting); $identification->appendChild($organisation_reporting);
    $date_and_time_of_creation = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'DateAndTimeOfCreation', $this->date_and_time_of_creation); $identification->appendChild($date_and_time_of_creation);
    $report_context = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportContext', $this->report_context); $identification->appendChild($report_context);
    $report_uuid = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'ReportUUID', $this->report_uuid); $identification->appendChild($report_uuid);
    $identifications = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Identifications'); $identification->appendChild($identifications);

    foreach ($this->identifications->personcontact_info as $p) $identifications->appendChild($this->_xml->importNode($p->getXMLElement(), TRUE));
    foreach ($this->identifications->organisation_contact_info as $o) $identifications->appendChild($this->_xml->importNode($o->getXMLElement(), TRUE));

    return $this->_xml;
  }

  public function getXMLElement() {
    if (is_null($this->_xml)) { $this->toXML(); }
    return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Identification', 'Identification')->item(0);
  }

  public function validate() {
    if (is_null($this->_xml)) $this->toXML();
    return $this->_xml->schemaValidate(__DIR__.'/xsd/'.\IRIX\Report::VERSION.'/ReportIdentification.xsd');
  }
}

/* ************************************************************************
 *
 */
namespace IRIX\Identification;

class Identifications {
  public $personcontact_info = array();
  public $organisation_contact_info = array();
}

class OrganisationContactInfo {
  public $name;
  public $organisation_id;
  public $country;

  private $_xml;

  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');

    $organisation_contact_info = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationContactInfo'); $this->_xml->appendChild($organisation_contact_info);
    $name = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Name', $this->name); $organisation_contact_info->appendChild($name);
    $organisation_id = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationID', $this->organisation_id); $organisation_contact_info->appendChild($organisation_id);
    $country = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Base', 'Country', $this->country); $organisation_contact_info->appendChild($country);

    return $this->_xml;
  }

  public function getXMLElement() {
    if (is_null($this->_xml)) { $this->toXML(); }
    return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Base', 'OrganisationContactInfo')->item(0);
  }
}