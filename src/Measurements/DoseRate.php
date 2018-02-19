<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\Measurements;

/**
 * IRIX PHP Library - Measurements Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class DoseRate
{
    public $dose_rate_type;
    public $measuring_period;
    public $measurements = [];

    public $apparatus_type = null;
    public $apparatus_type_description = null;
    public $description = null;

    private $_xml = null;

    public function __construct()
    {
        $this->measuring_period = new \IRIX\Miscellaneous\MeasuringPeriod();
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $dose_rate = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'mon:DoseRate');
        $this->_xml->appendChild($dose_rate);
        $dose_rate->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:mon', \IRIX\Report::_NAMESPACE.'/Measurements');
        $dose_rate->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:loc', \IRIX\Report::_NAMESPACE.'/Locations');
        $dose_rate->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $dose_rate->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        $dose_rate_type = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'DoseRateType', $this->dose_rate_type);
        $dose_rate->appendChild($dose_rate_type);

        $measuring_period = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'MeasuringPeriod');
        $dose_rate->appendChild($measuring_period);
        $start_time = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'StartTime', $this->measuring_period->start_time);
        $measuring_period->appendChild($start_time);
        $end_time = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'EndTime', $this->measuring_period->end_time);
        $measuring_period->appendChild($end_time);

        if (!is_null($this->apparatus_type)) {
            $apparatus_type = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ApparatusType', $this->apparatus_type);
            $dose_rate->appendChild($apparatus_type);
        }
        if (!is_null($this->apparatus_type_description)) {
            $apparatus_type_description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ApparatusTypeDescription', $this->apparatus_type_description);
            $dose_rate->appendChild($apparatus_type_description);
        }
        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Description', $this->description);
            $dose_rate->appendChild($description);
        }

        $measurements = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Measurements');
        $dose_rate->appendChild($measurements);
        foreach ($this->measurements as $m) {
            $measurements->appendChild($this->_xml->importNode($m->getXMLElement(), true));
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'DoseRate')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $dose_rate = new self();

        $dose_rate->dose_rate_type = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'DoseRateType')->item(0)->textContent;

        $measuring_period = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'MeasuringPeriod')->item(0);
        $dose_rate->measuring_period->start_time = $measuring_period->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'StartTime')->item(0)->textContent;
        $dose_rate->measuring_period->end_time = $measuring_period->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'EndTime')->item(0)->textContent;

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ApparatusType')->item(0);
        if (!is_null($item)) {
            $dose_rate->apparatus_type = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'ApparatusTypeDescription')->item(0);
        if (!is_null($item)) {
            $dose_rate->apparatus_type_description = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Description')->item(0);
        if (!is_null($item)) {
            $dose_rate->description = $item->textContent;
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Measurements')->item(0);
        $measurements = $item->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Measurements', 'Measurement');
        if ($measurements->length > 0) {
            $dose_rate->measurements = [];
            for ($i = 0; $i < $measurements->length; $i++) {
                $dose_rate->measurements[] = \IRIX\Measurements\DoseRate\Measurement::readXMLElement($measurements->item($i));
            }
        }

        return $dose_rate;
    }
}
