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
 * IRIX PHP Library - Annexes Section
 * @package IRIX\Report
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Annexes {
  public $annotation = NULL;
  public $file_enclosure = NULL;
  public $signature = NULL;

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $annexes = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'annex:Annexes'); $this->_xml->appendChild($annexes);
    $annexes->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:annex', 'http://www.iaea.org/2012/IRIX/Format/Annexes');
    $annexes->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $annexes->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    if (!is_null($this->annotation)) {
      if (is_array($this->annotation)) {
        foreach ($this->annotation as $a) { $annexes->appendChild($this->_xml->importNode($a->getXMLElement(), TRUE)); }
      } else {
        $annexes->appendChild($this->_xml->importNode($this->annotation->getXMLElement(), TRUE));
      }
    }

    if (!is_null($this->file_enclosure)) {
      if (is_array($this->file_enclosure)) {
        foreach ($this->file_enclosure as $fe) { $annexes->appendChild($this->_xml->importNode($fe->getXMLElement(), TRUE)); }
      } else {
        $annexes->appendChild($this->_xml->importNode($this->file_enclosure->getXMLElement(), TRUE));
      }
    }

    if (!is_null($this->signature)) {
      if (is_array($this->signature)) {
        foreach ($this->signature as $s) { $annexes->appendChild($this->_xml->importNode($s->getXMLElement(), TRUE)); }
      } else {
        $annexes->appendChild($this->_xml->importNode($this->signature->getXMLElement(), TRUE));
      }
    }

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Annexes')->item(0);
  }

  /**
   *
   */
  public function validate($update = TRUE) {
    if ($update) $this->toXML();
    return $this->_xml->schemaValidate(__DIR__.'/xsd/'.\IRIX\Report::VERSION.'/Annexes.xsd');
  }

  /**
   *
   */
  public static function read($filename) {
    if (file_exists($filename)) {
      $xml = new \DOMDocument(); $xml->load($filename);

      $annexes = $xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Annexes')->item(0);

      if (!is_null($annexes)) {
        $a = new self();
        $a->_xml = new \DOMDocument('1.0', 'UTF-8');
        $a->_xml->appendChild($a->_xml->importNode($annexes, TRUE));

        if ($a->validate(FALSE)) {
          $annotation = $annexes->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Annotation');
          if ($annotation->length > 0) { $a->annotation = array(); for ($i = 0; $i < $annotation->length; $i++) $a->annotation[] = \IRIX\Annexes\Annotation::readXMLElement($annotation->item($i)); }

          $file_enclosure = $annexes->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileEnclosure');
          if ($file_enclosure->length > 0) { $a->file_enclosure = array(); for ($i = 0; $i < $file_enclosure->length; $i++) $a->file_enclosure[] = \IRIX\Annexes\FileEnclosure::readXMLElement($file_enclosure->item($i)); }

          return $a;
        }
      } else {
        return NULL;
      }
    }

    return FALSE;
  }
}

namespace IRIX\Annexes;

require('class.annexes.annotation.php');
require('class.annexes.fileenclosure.php');

class Signature {
}