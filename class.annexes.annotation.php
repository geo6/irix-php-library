<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */
namespace IRIX\Annexes;

/**
 * IRIX PHP Library - Annexes Section
 * @package IRIX\Report
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Annotation {
  public $text;
  public $title = NULL;

  private $_xml = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $annotation = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Annotation'); $this->_xml->appendChild($annotation);
    $annotation->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $annotation->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    if (!is_null($this->title)) $title = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Title', $this->title); $annotation->appendChild($title);

    $text = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Text', $this->text); $annotation->appendChild($text);

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Annotation')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $annotation = new self();

    $title = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Title')->item(0); if (!is_null($annotation)) { $annotation->title = $title->textContent; }

    $annotation->text = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Text')->item(0)->textContent;

    return $annotation;
  }
}
