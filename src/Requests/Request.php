<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\Requests;

class Request
{
    public $request_uuid;
    public $request_subject;
    public $request_text;

    public $type_of_request = null;
    public $response = null;

    public $metadata = null;

    private $_xml = null;

    public function __construct($subject = null, $text = null)
    {
        $this->request_uuid = \IRIX\Report::UUIDv4();
        if (!is_null($subject) && !is_null($text)) {
            $this->request_subject = $subject;
            $this->request_text = $text;
        }
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $request = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Request');
        $this->_xml->appendChild($request);
        $request->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:req', \IRIX\Report::_NAMESPACE.'/Requests');
        $request->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $request->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        $request_uuid = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestUUID', $this->request_uuid);
        $request->appendChild($request_uuid);
        if (!is_null($this->type_of_request)) {
            $type_of_request = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'TypeOfRequest', $this->type_of_request);
            $request->appendChild($type_of_request);
        }
        $request_subject = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestSubject', $this->request_subject);
        $request->appendChild($request_subject);
        $request_text = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestText', $this->request_text);
        $request->appendChild($request_text);

        if (!is_null($this->response)) {
            $request->appendChild($this->_xml->importNode($this->response->getXMLElement(), true));
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Request')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $request = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestUUID')->item(0);
        if (!is_null($item)) {
            $request->request_uuid = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'TypeOfRequest')->item(0);
        if (!is_null($item)) {
            $request->type_of_request = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestSubject')->item(0);
        if (!is_null($item)) {
            $request->request_subject = $item->textContent;
        }
        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestText')->item(0);
        if (!is_null($item)) {
            $request->request_text = $item->textContent;
        }

        $response = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Response')->item(0);
        if (!is_null($response)) {
            $request->response = \IRIX\Requests\Request\Response::readXMLElement($response);
        }

        return $request;
    }
}
