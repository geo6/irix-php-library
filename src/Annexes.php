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
 * IRIX PHP Library - Annexes Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Annexes
{
    public $annotation = null;
    public $file_enclosure = null;
    public $signature = null;

    private $_xml = null;

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $annexes = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'annex:Annexes');
        $this->_xml->appendChild($annexes);
        $annexes->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:annex', \IRIX\Report::_NAMESPACE.'/Annexes');
        $annexes->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $annexes->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        if (!is_null($this->annotation)) {
            if (!is_array($this->annotation)) {
                $this->annotation = [$this->annotation];
            }
            foreach ($this->annotation as $a) {
                $annexes->appendChild($this->_xml->importNode($a->getXMLElement(), true));
            }
        }

        if (!is_null($this->file_enclosure)) {
            if (!is_array($this->file_enclosure)) {
                $this->file_enclosure = [$this->file_enclosure];
            }
            foreach ($this->file_enclosure as $fe) {
                $annexes->appendChild($this->_xml->importNode($fe->getXMLElement(), true));
            }
        }

        if (!is_null($this->signature)) {
            if (!is_array($this->signature)) {
                $this->signature = [$this->signature];
            }
            foreach ($this->signature as $s) {
                $annexes->appendChild($this->_xml->importNode($s->getXMLElement(), true));
            }
        }

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Annexes')->item(0);
    }

    public function validate($update = true)
    {
        if ($update) {
            $this->toXML();
        }

        return $this->_xml->schemaValidate(dirname(__DIR__).'/xsd/'.\IRIX\Report::_VERSION.'/Annexes.xsd');
    }

    public static function read($filename)
    {
        if (file_exists($filename)) {
            $xml = new \DOMDocument();
            $xml->load($filename);

            $annexes = $xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Annexes')->item(0);

            if (!is_null($annexes)) {
                $a = new self();
                $a->_xml = new \DOMDocument('1.0', 'UTF-8');
                $a->_xml->appendChild($a->_xml->importNode($annexes, true));

                if ($a->validate(false)) {
                    $annotation = $annexes->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'Annotation');
                    if ($annotation->length > 0) {
                        $a->annotation = [];
                        for ($i = 0; $i < $annotation->length; $i++) {
                            $a->annotation[] = \IRIX\Annexes\Annotation::readXMLElement($annotation->item($i));
                        }
                    }

                    $file_enclosure = $annexes->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Annexes', 'FileEnclosure');
                    if ($file_enclosure->length > 0) {
                        $a->file_enclosure = [];
                        for ($i = 0; $i < $file_enclosure->length; $i++) {
                            $a->file_enclosure[] = \IRIX\Annexes\FileEnclosure::readXMLElement($file_enclosure->item($i));
                        }
                    }

                    return $a;
                }
            } else {
                return;
            }
        }

        return false;
    }
}
