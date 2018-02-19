<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\Requests\Response;

class RequestReference
{
    public $requesting_organisation = null;
    public $date_and_time_of_request = null;
    public $request_uuid = null;
    public $type_of_request = null;
    public $request_subject = null;
    public $request_text = null;

    public $metadata = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $request_reference = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestReference');
        $this->_xml->appendChild($request_reference);

        if (!is_null($this->requesting_organisation)) {
            $requesting_organisation = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestingOrganisation', $this->requesting_organisation);
            $request_reference->appendChild($requesting_organisation);
        }
        if (!is_null($this->date_and_time_of_request)) {
            $date_and_time_of_request = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'DateAndTimeOfRequest', $this->date_and_time_of_request);
            $request_reference->appendChild($date_and_time_of_request);
        }
        if (!is_null($this->request_uuid)) {
            $request_uuid = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestUUID', $this->request_uuid);
            $request_reference->appendChild($request_uuid);
        }
        if (!is_null($this->type_of_request)) {
            $type_of_request = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'TypeOfRequest', $this->type_of_request);
            $request_reference->appendChild($type_of_request);
        }
        if (!is_null($this->request_subject)) {
            $request_subject = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestSubject', $this->request_subject);
            $request_reference->appendChild($request_subject);
        }
        if (!is_null($this->request_text)) {
            $request_text = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestText', $this->request_text);
            $request_reference->appendChild($request_text);
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestReference')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $request_reference = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestingOrganisation')->item(0);
        if (!is_null($item)) {
            $request_reference->requesting_organisation = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'DateAndTimeOfRequest')->item(0);
        if (!is_null($item)) {
            $request_reference->date_and_time_of_request = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestUUID')->item(0);
        if (!is_null($item)) {
            $request_reference->request_uuid = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'TypeOfRequest')->item(0);
        if (!is_null($item)) {
            $request_reference->type_of_request = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestSubject')->item(0);
        if (!is_null($item)) {
            $request_reference->request_subject = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestText')->item(0);
        if (!is_null($item)) {
            $request_reference->request_text = $item->textContent;
        }

        return $request_reference;
    }
}
