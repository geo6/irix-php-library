<?php
namespace IRIX;

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

  public function __construct($organisation_reporting, $report_context) {
    $this->identification = new \IRIX\Identification($organisation_reporting, $report_context);
  }

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

    return $this->_xml;
  }

  public function validate() {
    if (is_null($this->_xml)) $this->toXML();
    return $this->_xml->schemaValidate(__DIR__.'/xsd/'.self::VERSION.'/IRIX.xsd');
  }

  public function write($filename) {
    if ($this->validate()) { $this->_xml->save($filename); }
  }

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