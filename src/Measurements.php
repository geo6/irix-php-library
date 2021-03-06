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
 * IRIX PHP Library - Measurements Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Measurements
{
    public $valid_at;
    public $sample = null;
    public $dose_rate = null;

    private $_xml = null;

    public function __construct()
    {
        $this->valid_at = gmdate('Y-m-d\TH:i:s\Z');
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $measurements = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'mon:Measurements');
        $this->_xml->appendChild($measurements);
        $measurements->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:mon', \IRIX\Report::_NAMESPACE.'/Measurements');
        $measurements->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:loc', \IRIX\Report::_NAMESPACE.'/Locations');
        $measurements->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $measurements->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        $measurements->setAttribute('ValidAt', $this->valid_at);

        if (!is_null($this->sample)) {
            if (!is_array($this->sample)) {
                $this->sample = [$this->sample];
            }
            foreach ($this->sample as $s) {
                $measurements->appendChild($this->_xml->importNode($s->getXMLElement(), true));
            }
        }

        if (!is_null($this->dose_rate)) {
            if (!is_array($this->dose_rate)) {
                $this->dose_rate = [$this->dose_rate];
            }
            foreach ($this->dose_rate as $dr) {
                $measurements->appendChild($this->_xml->importNode($dr->getXMLElement(), true));
            }
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Measurements')->item(0);
    }

    public function validate($update = true)
    {
        if ($update) {
            $this->toXML();
        }

        return $this->_xml->schemaValidate(dirname(__DIR__).'/xsd/'.\IRIX\Report::_VERSION.'/Measurements.xsd');
    }

    public static function read($filename)
    {
        if (file_exists($filename)) {
            $xml = new \DOMDocument();
            $xml->load($filename);

            $measurements = $xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Measurements')->item(0);

            if (!is_null($measurements)) {
                $m = new self();
                $m->_xml = new \DOMDocument('1.0', 'UTF-8');
                $m->_xml->appendChild($m->_xml->importNode($measurements, true));

                if ($m->validate(false)) {
                    $m->valid_at = $measurements->getAttribute('ValidAt');

                    $sample = $measurements->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Sample');
                    if ($sample->length > 0) {
                        $m->sample = [];
                        for ($i = 0; $i < $sample->length; $i++) {
                            $m->sample[] = \IRIX\Measurements\Sample::readXMLElement($sample->item($i));
                        }
                    }

                    $dose_rate = $measurements->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'DoseRate');
                    if ($dose_rate->length > 0) {
                        $m->dose_rate = [];
                        for ($i = 0; $i < $dose_rate->length; $i++) {
                            $m->dose_rate[] = \IRIX\Measurements\DoseRate::readXMLElement($dose_rate->item($i));
                        }
                    }

                    return $m;
                }
            } else {
                return;
            }
        }

        return false;
    }
}
