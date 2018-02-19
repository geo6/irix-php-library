<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX;

/**
 * IRIX PHP Library - Report.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Report
{
    const _VERSION = '1.0';
    const _PRETTY = true;
    const _NAMESPACE = 'http://www.iaea.org/2012/IRIX/Format';

    public $identification;

    public $event_information = null;
    public $measurements = null;
    public $locations = null;
    public $requests = null;
    public $annexes = null;

    public $release = null;
    public $meteorology = null;
    public $consequences = null;
    public $response_actions = null;
    public $medical_information = null;
    public $media_information = null;

    private $_xml = null;

    public function __construct()
    {
        $this->identification = new \IRIX\Identification();
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = self::_PRETTY;

        $report = $this->_xml->createElementNS(self::_NAMESPACE, 'irix:Report');
        $report->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', self::_NAMESPACE.'/Base');
        $report->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:id', self::_NAMESPACE.'/Identification');
        if (!is_null($this->locations)) {
            $report->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:loc', self::_NAMESPACE.'/Locations');
        }
        $report->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');
        $report->setAttribute('version', self::_VERSION);
        $this->_xml->appendChild($report);

        $report->appendChild($this->_xml->importNode($this->identification->getXMLElement(), true));
        if (!is_null($this->event_information)) {
            $report->appendChild($this->_xml->importNode($this->event_information->getXMLElement(), true));
        }
        if (!is_null($this->measurements)) {
            $report->appendChild($this->_xml->importNode($this->measurements->getXMLElement(), true));
        }
        if (!is_null($this->locations)) {
            $report->appendChild($this->_xml->importNode($this->locations->getXMLElement(), true));
        }
        if (!is_null($this->requests)) {
            $report->appendChild($this->_xml->importNode($this->requests->getXMLElement(), true));
        }
        if (!is_null($this->annexes)) {
            $report->appendChild($this->_xml->importNode($this->annexes->getXMLElement(), true));
        }

        return $this->_xml->saveXML();
    }

    public function validate($update = true)
    {
        if ($update) {
            $this->toXML();
        }

        return $this->_xml->schemaValidate(dirname(__DIR__).'/xsd/'.self::_VERSION.'/IRIX.xsd');
    }

    public function write($filename)
    {
        if ($this->validate()) {
            return $this->_xml->save($filename);
        }

        return false;
    }

    public static function read($filename)
    {
        if (file_exists($filename)) {
            $r = new self();

            $r->_xml = new \DOMDocument();
            $r->_xml->load($filename);

            if ($r->validate(false)) {
                $r->identification = \IRIX\Identification::read($filename);
                $r->event_information = \IRIX\EventInformation::read($filename);
                $r->measurements = \IRIX\Measurements::read($filename);
                $r->locations = \IRIX\Locations::read($filename);
                $r->requests = \IRIX\Requests::read($filename);
                $r->annexes = \IRIX\Annexes::read($filename);

                return $r;
            }
        }

        return false;
    }

    public static function UUIDv4()
    {
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
