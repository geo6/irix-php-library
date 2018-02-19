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
 * IRIX PHP Library - Requests Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Requests
{
    public $request = [];
    public $response = [];

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $requests = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'req:Requests');
        $this->_xml->appendChild($requests);
        $requests->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:req', \IRIX\Report::_NAMESPACE.'/Requests');
        $requests->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $requests->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        if (!is_null($this->request)) {
            if (!is_array($this->request)) {
                $this->request = [$this->request];
            }
            foreach ($this->request as $r) {
                $requests->appendChild($this->_xml->importNode($r->getXMLElement(), true));
            }
        }
        if (!is_null($this->response)) {
            if (!is_array($this->response)) {
                $this->response = [$this->response];
            }
            foreach ($this->response as $r) {
                $requests->appendChild($this->_xml->importNode($r->getXMLElement(), true));
            }
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Requests')->item(0);
    }

    public function validate($update = true)
    {
        if ($update) {
            $this->toXML();
        }

        return $this->_xml->schemaValidate(dirname(__DIR__).'/xsd/'.\IRIX\Report::_VERSION.'/Requests.xsd');
    }

    public static function read($filename)
    {
        if (file_exists($filename)) {
            $xml = new \DOMDocument();
            $xml->load($filename);

            $requests = $xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Requests')->item(0);

            if (!is_null($requests)) {
                $r = new self();
                $r->_xml = new \DOMDocument('1.0', 'UTF-8');
                $r->_xml->appendChild($r->_xml->importNode($requests, true));

                if ($r->validate(false)) {
                    $request = $requests->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Request');
                    if ($request->length > 0) {
                        $r->request = [];
                        for ($i = 0; $i < $request->length; $i++) {
                            $r->request[] = \IRIX\Requests\Request::readXMLElement($request->item($i));
                        }
                    }

                    $response = $requests->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Response');
                    if ($response->length > 0) {
                        $r->response = [];
                        for ($i = 0; $i < $response->length; $i++) {
                            $r->response[] = \IRIX\Requests\Response::readXMLElement($response->item($i));
                        }
                    }

                    return $r;
                }
            } else {
                return;
            }
        }

        return false;
    }
}
