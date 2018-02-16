<?php
/**
 * IRIX PHP Library
 * PHP Version 5.3+.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 * @copyright 2016 Jonathan Beliën
 * @note The IRIX (International Radiological Information Exchange) format is developed and maintained by IAEA (International Atomic Energy Agency) <https://www.iaea.org/>
 */

namespace IRIX\Annexes;

/**
 * IRIX PHP Library - Annexes Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class FileEnclosure
{
    public $title;

    private $mime_type;
    private $file_size;
    private $enclosed_object;

    public $information_category = null;
    public $information_category_description = null;
    public $author_organisation = null;
    public $confidentiality = null;
    public $valid_at = null;
    public $language = null;
    public $description = null;
    public $file_name = null;
    public $file_date_and_time = null;
    public $file_hash = null;

    private $_xml = null;

    public function setFile($file, $filename = null)
    {
        if (file_exists($file)) {
            $this->file_size = filesize($file);
            $this->enclosed_object = chunk_split(base64_encode(file_get_contents($file)));
            $finfo = finfo_open();
            $this->mime_type = finfo_file($finfo, $file, FILEINFO_MIME_TYPE);

            $this->file_name = (!is_null($filename) ? $filename : basename($file));
            $this->file_date_and_time = gmdate('Y-m-d\TH:i:s\Z', filemtime($file));
            $this->file_hash = sha1_file($file);
        }

        return false;
    }

    public function getFileSize()
    {
        return $this->file_size;
    }

    public function getMimeType()
    {
        return $this->mime_type;
    }

    public function getFile()
    {
        $dir = sys_get_temp_dir().'/irix';
        if (!file_exists($dir) || !is_dir($dir)) {
            mkdir($dir);
        }
        $fname = (!is_null($this->file_name) ? $dir.'/'.$this->file_name : tempnam($dir, 'irix'));
        file_put_contents($fname, base64_decode($this->enclosed_object));

        return $fname;
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $file_enclosure = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileEnclosure');
        $this->_xml->appendChild($file_enclosure);
        $file_enclosure->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $file_enclosure->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        $title = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Title', $this->title);
        $file_enclosure->appendChild($title);

        if (!is_null($this->information_category)) {
            if (!is_array($this->information_category)) {
                $this->information_category = [$this->information_category];
            }
            foreach ($this->information_category as $ic) {
                $_ic = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'InformationCategory', $ic);
                $file_enclosure->appendChild($_ic);
            }
        }
        if (!is_null($this->information_category_description)) {
            if (!is_array($this->information_category_description)) {
                $this->information_category_description = [$this->information_category_description];
            }
            foreach ($this->information_category_description as $icd) {
                $_icd = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'InformationCategoryDescription', $icd);
                $file_enclosure->appendChild($_icd);
            }
        }
        if (!is_null($this->author_organisation)) {
            $author_organisation = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'AuthorOrganisation', $this->author_organisation);
            $file_enclosure->appendChild($author_organisation);
        }
        if (!is_null($this->confidentiality)) {
            $confidentiality = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Confidentiality', $this->confidentiality);
            $file_enclosure->appendChild($confidentiality);
        }
        if (!is_null($this->valid_at)) {
            $valid_at = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'ValidAt', $this->valid_at);
            $file_enclosure->appendChild($valid_at);
        }
        if (!is_null($this->language)) {
            $language = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Language', $this->language);
            $file_enclosure->appendChild($language);
        }
        if (!is_null($this->description)) {
            $description = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Description', $this->description);
            $file_enclosure->appendChild($description);
        }
        if (!is_null($this->file_name)) {
            $file_name = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileName', $this->file_name);
            $file_enclosure->appendChild($file_name);
        }
        if (!is_null($this->file_date_and_time)) {
            $file_date_and_time = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileDateAndTime', $this->file_date_and_time);
            $file_enclosure->appendChild($file_date_and_time);
        }

        $mime_type = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'MimeType', $this->mime_type);
        $file_enclosure->appendChild($mime_type);
        $file_size = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileSize', $this->file_size);
        $file_enclosure->appendChild($file_size);

        if (!is_null($this->file_hash)) {
            $file_hash = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileHash', $this->file_hash);
            $file_enclosure->appendChild($file_hash);
            $file_hash->setAttribute('Algorithm', 'SHA-1');
        }

        $enclosed_object = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'EnclosedObject', $this->enclosed_object);
        $file_enclosure->appendChild($enclosed_object);

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileEnclosure')->item(0);
    }

    public static function readXMLElement($domelement)
    {
        $file_enclosure = new self();

        $file_enclosure->title = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Title')->item(0)->textContent;
        $file_enclosure->mime_type = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'MimeType')->item(0)->textContent;
        $file_enclosure->file_size = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileSize')->item(0)->textContent;
        $file_enclosure->enclosed_object = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'EnclosedObject')->item(0)->textContent;

        $information_category = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'InformationCategory');
        if ($information_category->length > 0) {
            $file_enclosure->information_category = [];
            for ($i = 0; $i < $information_category->length; $i++) {
                $file_enclosure->information_category[] = $information_category->item($i)->textContent;
            }
        }

        $information_category_description = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'InformationCategoryDescription');
        if ($information_category_description->length > 0) {
            $file_enclosure->information_category_description = [];
            for ($i = 0; $i < $information_category_description->length; $i++) {
                $file_enclosure->information_category_description[] = $information_category_description->item($i)->textContent;
            }
        }

        $author_organisation = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'AuthorOrganisation')->item(0);
        print_r($author_organisation);
        if (!is_null($author_organisation)) {
            $file_enclosure->author_organisation = $author_organisation->textContent;
        }
        $confidentiality = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Confidentiality')->item(0);
        if (!is_null($confidentiality)) {
            $file_enclosure->confidentiality = $confidentiality->textContent;
        }
        $valid_at = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'ValidAt')->item(0);
        if (!is_null($valid_at)) {
            $file_enclosure->valid_at = $valid_at->textContent;
        }
        $language = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Language')->item(0);
        if (!is_null($language)) {
            $file_enclosure->language = $language->textContent;
        }
        $description = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Description')->item(0);
        if (!is_null($description)) {
            $file_enclosure->description = $description->textContent;
        }
        $file_name = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileName')->item(0);
        if (!is_null($file_name)) {
            $file_enclosure->file_name = $file_name->textContent;
        }
        $file_date_and_time = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileDateAndTime')->item(0);
        if (!is_null($file_date_and_time)) {
            $file_enclosure->file_date_and_time = $file_date_and_time->textContent;
        }
        $file_hash = $domelement->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileHash')->item(0);
        if (!is_null($file_hash)) {
            $file_enclosure->file_hash = $file_hash->textContent;
        }

        return $file_enclosure;
    }
}
