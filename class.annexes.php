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

    $title = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Title')->item(0); if (!is_null($annotation)) { $annotation->title = $title->textContent; }

    $annotation->text = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Text')->item(0)->textContent;

    return $annotation;
  }
}

class FileEnclosure {
  public $title;

  private $mime_type;
  private $file_size;
  private $enclosed_object;

  public $information_category = NULL;
  public $information_category_description = NULL;
  public $author_organisation = NULL;
  public $confidentiality = NULL;
  public $valid_at = NULL;
  public $language = NULL;
  public $description = NULL;
  public $file_name = NULL;
  public $file_date_and_time = NULL;
  public $file_hash = NULL;

  /**
   *
   */
  public function setFile($filename) {
    if (file_exists($filename)) {
      $this->file_size = filesize($filename);
      $this->enclosed_object = chunk_split(base64_encode(file_get_contents($filename)));
      $finfo = finfo_open(); $this->mime_type = finfo_file($finfo, $filename, FILEINFO_MIME_TYPE);

      $this->file_name = basename($filename);
      $this->file_date_and_time = gmdate('Y-m-d\TH:i:s\Z', filemtime($filename));
      $this->file_hash = sha1_file($filename);
    }
    return FALSE;
  }

  /**
   *
   */
  public function getFileSize() {
    return $this->file_size;
  }
  /**
   *
   */
  public function getMimeType() {
    return $this->mime_type;
  }
  /**
   *
   */
  public function getFile() {
    $dir = sys_get_temp_dir().'/irix'; if (!file_exists($dir) || !is_dir($dir)) mkdir($dir);
    $fname = $dir.'/'.$this->file_name;
    file_put_contents($fname, base64_decode($this->enclosed_object));
    return $fname;
  }

  /**
   *
   */
  public function toXML() {
    $this->_xml = new \DOMDocument('1.0', 'UTF-8');
    $this->_xml->formatOutput = \IRIX\Report::DEBUG;

    $file_enclosure = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileEnclosure'); $this->_xml->appendChild($file_enclosure);
    $file_enclosure->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', 'http://www.iaea.org/2012/IRIX/Format/Base');
    $file_enclosure->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

    $title = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Title', $this->title); $file_enclosure->appendChild($title);

    if (!is_null($this->information_category)) { if(!is_array($this->information_category)) $this->information_category = array( $this->information_category ); foreach ($this->information_category as $ic) { $_ic = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'InformationCategory', $ic); $file_enclosure->appendChild($_ic); } }
    if (!is_null($this->information_category_description)) { if(!is_array($this->information_category_description)) $this->information_category_description = array( $this->information_category_description ); foreach ($this->information_category_description as $icd) { $_icd = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'InformationCategoryDescription', $icd); $file_enclosure->appendChild($_icd); } }
    if (!is_null($this->author_organisation)) { $author_organisation = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'AuthorOrganisation', $this->author_organisation); $file_enclosure->appendChild($author_organisation); }
    if (!is_null($this->confidentiality)) { $confidentiality = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Confidentiality', $this->confidentiality); $file_enclosure->appendChild($confidentiality); }
    if (!is_null($this->valid_at)) { $valid_at = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'ValidAt', $this->valid_at); $file_enclosure->appendChild($valid_at); }
    if (!is_null($this->language)) { $language = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Language', $this->language); $file_enclosure->appendChild($language); }
    if (!is_null($this->description)) { $description = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Description', $this->description); $file_enclosure->appendChild($description); }
    if (!is_null($this->file_name)) { $file_name = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileName', $this->file_name); $file_enclosure->appendChild($file_name); }
    if (!is_null($this->file_date_and_time)) { $file_date_and_time = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileDateAndTime', $this->file_date_and_time); $file_enclosure->appendChild($file_date_and_time); }

    $mime_type = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'MimeType', $this->mime_type); $file_enclosure->appendChild($mime_type);
    $file_size = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileSize', $this->file_size); $file_enclosure->appendChild($file_size);

    if (!is_null($this->file_hash)) {
      $file_hash = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileHash', $this->file_hash); $file_enclosure->appendChild($file_hash);
      $file_hash->setAttribute('Algorithm', 'SHA-1');
    }

    $enclosed_object = $this->_xml->createElementNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'EnclosedObject', $this->enclosed_object); $file_enclosure->appendChild($enclosed_object);

    return $this->_xml;
  }

  /**
   *
   */
  public function getXMLElement() {
    $this->toXML(); return $this->_xml->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileEnclosure')->item(0);
  }

  /**
   *
   */
  public static function readXMLElement($domelement) {
    $file_enclosure = new self();

    $file_enclosure->title = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Title')->item(0)->textContent;
    $file_enclosure->mime_type = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'MimeType')->item(0)->textContent;
    $file_enclosure->file_size = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileSize')->item(0)->textContent;
    $file_enclosure->enclosed_object = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'EnclosedObject')->item(0)->textContent;

    $information_category = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'InformationCategory');
    if ($information_category->length > 0) { $file_enclosure->information_category = array(); for ($i = 0; $i < $information_category->length; $i++) $file_enclosure->information_category[] = $information_category->item($i)->textContent; }

    $information_category_description = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'InformationCategoryDescription');
    if ($information_category_description->length > 0) { $file_enclosure->information_category_description = array(); for ($i = 0; $i < $information_category_description->length; $i++) $file_enclosure->information_category_description[] = $information_category_description->item($i)->textContent; }

    $author_organisation = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'AuthorOrganisation')->item(0); Print_r($author_organisation); if (!is_null($author_organisation)) { $file_enclosure->author_organisation = $author_organisation->textContent; }
    $confidentiality = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Confidentiality')->item(0); if (!is_null($confidentiality)) { $file_enclosure->confidentiality = $confidentiality->textContent; }
    $valid_at = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'ValidAt')->item(0); if (!is_null($valid_at)) { $file_enclosure->valid_at = $valid_at->textContent; }
    $language = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Language')->item(0); if (!is_null($language)) { $file_enclosure->language = $language->textContent; }
    $description = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'Description')->item(0); if (!is_null($description)) { $file_enclosure->description = $description->textContent; }
    $file_name = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileName')->item(0); if (!is_null($file_name)) { $file_enclosure->file_name = $file_name->textContent; }
    $file_date_and_time = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileDateAndTime')->item(0); if (!is_null($file_date_and_time)) { $file_enclosure->file_date_and_time = $file_date_and_time->textContent; }
    $file_hash = $domelement->getElementsByTagNameNS('http://www.iaea.org/2012/IRIX/Format/Annexes', 'FileHash')->item(0); if (!is_null($file_hash)) { $file_enclosure->file_hash = $file_hash->textContent; }

    return $file_enclosure;
  }
}

class Signature {
}