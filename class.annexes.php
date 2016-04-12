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
          if (!is_null($annotation)) { $a->annotation = array(); for ($i = 0; $i < $annotation->length; $i++) $a->annotation[] = \IRIX\Annexes\Annotation::readXMLElement($annotation->item($i)); }

          return $a;
        }
      }
    }

    return FALSE;
  }
}

/* ************************************************************************
 *
 */
namespace IRIX\Annexes;

class Annotation {
  public $text;
  public $title = NULL;

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

    $title = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Title');
    if (!is_null($annotation)) { $annotation->title = $title->item(0)->textContent; }

    $annotation->text = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Text')->item(0)->textContent;

    return $annotation;
  }
}

class FileEnclosure {
  public $title;
  public $mime_type;
  public $file_size;
  public $enclosed_object;

  public $information_category = NULL;
  public $information_category_description = NULL;
  public $author_organisation = NULL;
  public $confidentiality = NULL;
  public $valid_at = NULL;
  public $language = NULL;
  public $description = NULL;
  public $file_name = NULL;
  public $file_date_and_time = NULL;
  public $filehash = NULL;
}

class Signature {
}