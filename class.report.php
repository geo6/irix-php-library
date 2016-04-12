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
 * IRIX PHP Library - Report
 * @package IRIX
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Report {
  const VERSION = '1.0';
  const DEBUG = TRUE;

  public $identification = NULL;
  public $measurements = NULL;
  public $locations = NULL;
  public $annexes = NULL;

  private $event_information = NULL;
  private $release = NULL;
  private $meteorology = NULL;
  private $consequences = NULL;
  private $response_actions = NULL;
  private $medical_information = NULL;
  private $media_information = NULL;
  private $requests = NULL;

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
    if (!is_null($this->locations)) $report->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:loc', 'http://www.iaea.org/2012/IRIX/Format/Locations');
    $report->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:html', 'http://www.w3.org/1999/xhtml');
    $report->setAttribute('version', self::VERSION);
    $this->_xml->appendChild($report);

    $report->appendChild($this->_xml->importNode($this->identification->getXMLElement(), TRUE));
    if (!is_null($this->measurements)) $report->appendChild($this->_xml->importNode($this->measurements->getXMLElement(), TRUE));
    if (!is_null($this->locations)) $report->appendChild($this->_xml->importNode($this->locations->getXMLElement(), TRUE));
    if (!is_null($this->annexes)) $report->appendChild($this->_xml->importNode($this->annexes->getXMLElement(), TRUE));

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
        $r->measurements = \IRIX\Measurements::read($filename);
        $r->locations = \IRIX\Locations::read($filename);
        $r->annexes = \IRIX\Annexes::read($filename);

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