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
 * IRIX PHP Library - Requests Section
 * @package IRIX\Report
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Requests {
  public $requests = array();

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

    $requests = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'req:Requests'); $this->_xml->appendChild($requests);
    $requests->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:req', \IRIX\Report::_NAMESPACE.'/Requests');
    $requests->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
    $requests->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    if (!is_null($this->requests)) {
      if (!is_array($this->requests)) $this->requests = array( $this->requests );
      foreach ($this->requests as $r) { $requests->appendChild($this->_xml->importNode($r->getXMLElement(), TRUE)); }
    }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Requests')->item(0);
  }

  /**
   *
   */
  public function validate($update = TRUE) {
    if ($update) $this->toXML();
    return $this->_xml->schemaValidate(__DIR__.'/xsd/'.\IRIX\Report::_VERSION.'/Requests.xsd');
  }

  /**
   *
   */
  public static function read($filename) {
    if (file_exists($filename)) {
      $xml = new \DOMDocument(); $xml->load($filename);

      $requests = $xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Requests')->item(0);

      if (!is_null($requests)) {
        $r = new self();
        $r->_xml = new \DOMDocument('1.0', 'UTF-8');
        $r->_xml->appendChild($r->_xml->importNode($requests, TRUE));

        if ($r->validate(FALSE)) {
          $request = $requests->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Request');
          if ($request->length > 0) { $r->requests = array(); for ($i = 0; $i < $request->length; $i++) $r->requests[] = \IRIX\Requests\Request::readXMLElement($request->item($i)); }

          return $r;
        }
      } else {
        return NULL;
      }
    }

    return FALSE;
  }
}

/* ************************************************************************
 *
 */
namespace IRIX\Requests;

class Request {
  public $request_uuid;
  public $request_subject;
  public $request_text;

  public $type_of_request = NULL;
  public $response = NULL;

  public $metadata = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function __construct($subject = NULL, $text = NULL) {
    $this->request_uuid = \IRIX\Report::UUIDv4();
    if (!is_null($subject) && !is_null($text)) {
      $this->request_subject = $subject;
      $this->request_text = $text;
    }
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

    $request = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Request'); $this->_xml->appendChild($request);
    $request->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:req', \IRIX\Report::_NAMESPACE.'/Requests');
    $request->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
    $request->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    $request_uuid = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestUUID', $this->request_uuid); $request->appendChild($request_uuid);
    if (!is_null($this->type_of_request)) { $type_of_request = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'TypeOfRequest', $this->type_of_request); $request->appendChild($type_of_request); }
    $request_subject = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestSubject', $this->request_subject); $request->appendChild($request_subject);
    $request_text = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestText', $this->request_text); $request->appendChild($request_text);

    if (!is_null($this->response)) $request->appendChild($this->_xml->importNode($this->response->getXMLElement(), TRUE));

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Request')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $request = new self();

    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestUUID')->item(0); if (!is_null($item)) $request->request_uuid = $item->textContent;
    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'TypeOfRequest')->item(0); if (!is_null($item)) $request->type_of_request = $item->textContent;
    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestSubject')->item(0); if (!is_null($item)) $request->request_subject = $item->textContent;
    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestText')->item(0); if (!is_null($item)) $request->request_text = $item->textContent;

    $response = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Response')->item(0);
    if (!is_null($response)) { $request->response = \IRIX\Requests\Request\Response::readXMLElement($response); }

    return $request;
  }
}

/* ************************************************************************
 *
 */
namespace IRIX\Requests\Request;

class Response {
  public $response_uuid;
  public $request_reference = array();
  public $response_text;

  public $metadata = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function __construct($text = NULL) {
    $this->response_uuid = \IRIX\Report::UUIDv4();
    if (!is_null($text)) $this->response_text = $text;
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

    $response = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Response'); $this->_xml->appendChild($response);

    $response_uuid = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'ResponseUUID', $this->response_uuid); $response->appendChild($response_uuid);
    foreach ($this->request_reference as $r) $response->appendChild($this->_xml->importNode($r->getXMLElement(), TRUE));
    $response_text = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'ResponseText', $this->response_text); $response->appendChild($response_text);

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'Response')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $response = new self();

    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'ResponseUUID')->item(0); if (!is_null($item)) $response->response_uuid = $item->textContent;

    $r = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestReference');
    for ($i = 0; $i < $r->length; $i++) $response->request_reference[] = \IRIX\Request\Request\Response\RequestReference::readXMLElement($r->item($i));

    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'ResponseText')->item(0); if (!is_null($item)) $response->response_text = $item->textContent;

    return $response;
  }
}

/* ************************************************************************
 *
 */
namespace IRIX\Requests\Request\Response;

class RequestReference {
  public $requesting_organisation = NULL;
  public $date_and_time_of_request = NULL;
  public $request_uuid = NULL;
  public $type_of_request = NULL;
  public $request_subject = NULL;
  public $request_text = NULL;

  public $metadata = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

    $request_reference = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestReference'); $this->_xml->appendChild($request_reference);

    if (!is_null($requesting_organisation)) $requesting_organisation = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestingOrganisation', $this->requesting_organisation); $request_reference->appendChild($requesting_organisation);
    if (!is_null($date_and_time_of_request)) $date_and_time_of_request = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'DateAndTimeOfRequest', $this->date_and_time_of_request); $request_reference->appendChild($date_and_time_of_request);
    if (!is_null($request_uuid)) $request_uuid = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestUUID', $this->request_uuid); $request_reference->appendChild($request_uuid);
    if (!is_null($type_of_request)) $type_of_request = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'TypeOfRequest', $this->type_of_request); $request_reference->appendChild($type_of_request);
    if (!is_null($request_subject)) $request_subject = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestSubject', $this->request_subject); $request_reference->appendChild($request_subject);
    if (!is_null($request_text)) $request_text = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestText', $this->request_text); $request_reference->appendChild($request_text);

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestReference')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $request_reference = new self();

    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestingOrganisation')->item(0); if (!is_null($item)) $request_reference->requesting_organisation = $item->textContent;
    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'DateAndTimeOfRequest')->item(0); if (!is_null($item)) $request_reference->requesting_organisation = $item->textContent;
    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestUUID')->item(0); if (!is_null($item)) $request_reference->requesting_organisation = $item->textContent;
    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'TypeOfRequest')->item(0); if (!is_null($item)) $request_reference->requesting_organisation = $item->textContent;
    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestSubject')->item(0); if (!is_null($item)) $request_reference->requesting_organisation = $item->textContent;
    $item = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Requests', 'RequestText')->item(0); if (!is_null($item)) $request_reference->requesting_organisation = $item->textContent;

    return $request_reference;
  }
}