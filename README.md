 # PHP Library to read/write IRIX message

 [![Latest Stable Version](https://styleci.io/repos/121750703/shield?branch=master)](https://styleci.io/repos/121750703)

 > The [IAEA](https://www.iaea.org/) has developed the **International Radiological Information Exchange** (IRIX) as the recommended  standard to  exchange  information  among emergency response organizations at national and international levels during a nuclear or radiological emergency. The standard addresses both the data content and format (XML format) and the system interface specification. Data can include status information about a nuclear installation, information about any radioactive releases to the environment, information on protective actions taken or planned by affected countries, environmental radiation monitoring data. The system interface specification (or web-service specification) enables organizations to interconnect their emergency information systems to automate their information exchange in an emergency. The IRIX standard allows the information to be processed, summarized and presented quickly — for example, on status boards in emergency response centres.
>
> — **Source:** <http://www-ns.iaea.org/downloads/iec/info-brochures/13-27431-irix.pdf>

This PHP library allows you to read and write IRIX XML messages.

    composer require geo6/irix-php-library

## Sections already available

- Annexes
- EventInformation
- Identification
- Locations
- Measurements
- Requests

## Sections not available yet

- Consequences
- MediaInformation
- MedicalInformation
- Meteorology
- Release
- ResponseActions
