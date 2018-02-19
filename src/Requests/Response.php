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

class Response
{
    public $response_uuid;
    public $request_reference = [];
    public $response_text;

    public $metadata = null;

    private $_xml = null;

    public function __construct($text = null)
    {
        $this->response_uuid = \IRIX\Report::UUIDv4();
        if (!is_null($text)) {
            $this->response_text = $text;
        }
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $response = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Response');
        $this->_xml->appendChild($response);

        $response_uuid = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'ResponseUUID', $this->response_uuid);
        $response->appendChild($response_uuid);
        foreach ($this->request_reference as $r) {
            $response->appendChild($this->_xml->importNode($r->getXMLElement(), true));
        }
        $response_text = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'ResponseText', $this->response_text);
        $response->appendChild($response_text);

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Response')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $response = new self();

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'ResponseUUID')->item(0);
        if (!is_null($item)) {
            $response->response_uuid = $item->textContent;
        }

        $r = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestReference');
        for ($i = 0; $i < $r->length; $i++) {
            $response->request_reference[] = \IRIX\Requests\Response\RequestReference::readXMLElement($r->item($i));
        }

        $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'ResponseText')->item(0);
        if (!is_null($item)) {
            $response->response_text = $item->textContent;
        }

        return $response;
    }
}
