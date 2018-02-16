 # PHP Library to read/write IRIX message

 [![Latest Stable Version](https://styleci.io/repos/121750703/shield?branch=master)](https://styleci.io/repos/121750703)
 [![Latest Stable Version](https://poser.pugx.org/geo6/irix-php-library/v/stable)](https://packagist.org/packages/geo6/irix-php-library)
 [![Total Downloads](https://poser.pugx.org/geo6/irix-php-library/downloads)](https://packagist.org/packages/geo6/irix-php-library)
 [![Monthly Downloads](https://poser.pugx.org/geo6/irix-php-library/d/monthly.png)](https://packagist.org/packages/geo6/irix-php-library)
 [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)

 > The [IAEA](https://www.iaea.org/) has developed the **International Radiological Information Exchange** (IRIX) as the recommended  standard to  exchange  information  among emergency response organizations at national and international levels during a nuclear or radiological emergency. The standard addresses both the data content and format (XML format) and the system interface specification. Data can include status information about a nuclear installation, information about any radioactive releases to the environment, information on protective actions taken or planned by affected countries, environmental radiation monitoring data. The system interface specification (or web-service specification) enables organizations to interconnect their emergency information systems to automate their information exchange in an emergency. The IRIX standard allows the information to be processed, summarized and presented quickly — for example, on status boards in emergency response centres.
>
> — **Source:** <http://www-ns.iaea.org/downloads/iec/info-brochures/13-27431-irix.pdf>

This PHP library allows you to read and write IRIX XML messages.

    composer require geo6/irix-php-library

Documentation: <https://geo6.github.io/irix-php-library/>

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
