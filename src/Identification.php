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
 * IRIX PHP Library - Identification Section.
 *
 * @author Jonathan Beliën <jbe@geo6.be>
 */
class Identification
{
    /** @var string Identifier (domain name) of the organisation issuing this report. */
    public $organisation_reporting;
    public $date_and_time_of_creation;
    public $report_context;
    public $report_uuid;
    public $identifications;

    public $sequence_number = null;
    public $follows = null;
    public $revokes = null;
    public $confidentiality = null;
    public $addressees = null;
    public $reporting_bases = null;
    public $contact_person = null;
    public $additional_information_url = null;
    public $event_identifications = null;

    private $_xml = null;

    public function __construct()
    {
        $this->date_and_time_of_creation = gmdate('Y-m-d\TH:i:s\Z');
        $this->report_uuid = Report::UUIDv4();
        $this->identifications = new Identification\Identifications();
    }

    public function toXML()
    {
        $this->_xml = new \DOMDocument('1.0', 'UTF-8');
        $this->_xml->formatOutput = \IRIX\Report::_PRETTY;

        $identification = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'id:Identification');
        $this->_xml->appendChild($identification);
        $identification->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:base', \IRIX\Report::_NAMESPACE.'/Base');
        $identification->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:html', 'http://www.w3.org/1999/xhtml');

        $organisation_reporting = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'OrganisationReporting', $this->organisation_reporting);
        $identification->appendChild($organisation_reporting);
        $date_and_time_of_creation = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'DateAndTimeOfCreation', $this->date_and_time_of_creation);
        $identification->appendChild($date_and_time_of_creation);
        $report_context = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ReportContext', $this->report_context);
        $identification->appendChild($report_context);

        if (!is_null($this->sequence_number)) {
            $sequence_number = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'SequenceNumber', $this->sequence_number);
            $identification->appendChild($sequence_number);
        }

        $report_uuid = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ReportUUID', $this->report_uuid);
        $identification->appendChild($report_uuid);

        if (!is_null($this->follows)) {
            $follows = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Follows', $this->follows);
            $identification->appendChild($follows);
        }
        if (!is_null($this->revokes)) {
            $revokes = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Revokes', $this->revokes);
            $identification->appendChild($revokes);
        }
        if (!is_null($this->confidentiality)) {
            $confidentiality = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Confidentiality', $this->confidentiality);
            $identification->appendChild($confidentiality);
        }

        if (!is_null($this->addressees)) {
            $addressees = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Addressees');
            foreach ($this->addressees as $a) {
                $addressee = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Addressee', $a);
                $addressees->appendChild($addressee);
            }
            $identification->appendChild($addressees);
        }

        if (!is_null($this->reporting_bases)) {
            $reporting_bases = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ReportingBases');
            foreach ($this->reporting_bases as $rb) {
                $reporting_basis = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ReportingBasis', $rb);
                $reporting_bases->appendChild($reporting_basis);
            }
            $identification->appendChild($reporting_bases);
        }

        if (!is_null($this->contact_person)) {
            $contact_person = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ContactPerson', $this->contact_person);
            $identification->appendChild($contact_person);
        }
        if (!is_null($this->additional_information_url)) {
            $additional_information_url = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'AdditionalInformationURL', $this->additional_information_url);
            $identification->appendChild($additional_information_url);
        }

        if (!is_null($this->event_identifications)) {
            $event_identifications = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'EventIdentifications');
            foreach ($this->event_identifications as $ei) {
                if (is_array($ei)) {
                    $event_identification = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'EventIdentification', $ei[0]);
                    if (isset($ei[1])) {
                        $event_identification->setAttribute('Organisation', $ei[1]);
                    }
                } else {
                    $event_identification = $this->_xml->createElementNS(\IRIX\Report::_NAMESPACE.'/Identification', 'EventIdentification', $ei);
                }
                $event_identifications->appendChild($event_identification);
            }
            $identification->appendChild($event_identifications);
        }

        $identification->appendChild($this->_xml->importNode($this->identifications->getXMLElement(), true));

        return $this->_xml;
    }

    public function getXMLElement()
    {
        $this->toXML();

        return $this->_xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Identification')->item(0);
    }

    public function validate($update = true)
    {
        if ($update) {
            $this->toXML();
        }

        return $this->_xml->schemaValidate(dirname(__DIR__).'/xsd/'.\IRIX\Report::_VERSION.'/ReportIdentification.xsd');
    }

    public static function read($filename)
    {
        if (file_exists($filename)) {
            $xml = new \DOMDocument();
            $xml->load($filename);

            $identification = $xml->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Identification')->item(0);

            if (!is_null($identification)) {
                $i = new self();
                $i->_xml = new \DOMDocument('1.0', 'UTF-8');
                $i->_xml->appendChild($i->_xml->importNode($identification, true));

                if ($i->validate(false)) {
                    $i->organisation_reporting = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'OrganisationReporting')->item(0)->textContent;
                    $i->date_and_time_of_creation = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'DateAndTimeOfCreation')->item(0)->textContent;
                    $i->report_context = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ReportContext')->item(0)->textContent;
                    $i->report_uuid = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ReportUUID')->item(0)->textContent;

                    $item = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'SequenceNumber')->item(0);
                    if (!is_null($item)) {
                        $i->sequence_number = $item->textContent;
                    }
                    $item = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Follows')->item(0);
                    if (!is_null($item)) {
                        $i->follows = $item->textContent;
                    }
                    $item = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Revokes')->item(0);
                    if (!is_null($item)) {
                        $i->revokes = $item->textContent;
                    }
                    $item = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Confidentiality')->item(0);
                    if (!is_null($item)) {
                        $i->confidentiality = $item->textContent;
                    }

                    $item = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Addressees')->item(0);
                    if (!is_null($item)) {
                        $i->addressees = [];
                        foreach ($item->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Addressee') as $a) {
                            $i->addressees[] = $a->textContent;
                        }
                    }
                    $item = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ReportingBases')->item(0);
                    if (!is_null($item)) {
                        $i->reporting_bases = [];
                        foreach ($item->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ReportingBasis') as $rb) {
                            $i->reporting_bases[] = $rb->textContent;
                        }
                    }

                    $item = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'ContactPerson')->item(0);
                    if (!is_null($item)) {
                        $i->contact_person = $item->textContent;
                    }
                    $item = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'AdditionalInformationURL')->item(0);
                    if (!is_null($item)) {
                        $i->additional_information_url = $item->textContent;
                    }

                    $item = $identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'EventIdentifications')->item(0);
                    if (!is_null($item)) {
                        $i->event_identifications = [];
                        foreach ($item->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'EventIdentification') as $ei) {
                            if ($ei->hasAttribute('Organisation')) {
                                $i->event_identifications[] = [$ei->textContent, $ei->getAttribute('Organisation')];
                            } else {
                                $i->event_identifications[] = $ei->textContent;
                            }
                        }
                    } else {
                        $i->event_identifications = null;
                    }

                    $i->identifications = \IRIX\Identification\Identifications::readXMLElement($identification->getElementsByTagNameNS(\IRIX\Report::_NAMESPACE.'/Identification', 'Identifications')->item(0));

                    return $i;
                }
            } else {
                return;
            }
        }

        return false;
    }
}
