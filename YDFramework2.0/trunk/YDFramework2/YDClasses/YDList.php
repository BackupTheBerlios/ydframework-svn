<?php

    /*

        Yellow Duck Framework version 2.1
        (c) Copyright 2002-2007 Pieter Claerhout

        This library is free software; you can redistribute it and/or
        modify it under the terms of the GNU Lesser General Public
        License as published by the Free Software Foundation; either
        version 2.1 of the License, or (at your option) any later version.

        This library is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
        Lesser General Public License for more details.

        You should have received a copy of the GNU Lesser General Public
        License along with this library; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

    */

    /**
     *  @addtogroup YDFramework Core
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }


    /**
     *  This class houses all array lists. All the methods are implemented as static methods and
     *	do not require you to create a class instance in order to use them.
     *
     *  @ingroup YDFramework
     */
    class YDList extends YDBase {

        /**
         *  This function will return a array with gmts.
         *
         *  @param $format      Format to return:     NULL  returns complete array.
         *                                        'simple'  returns: array( -11 => '(GMT -11:00)', -10 => ...
         *                                          'full'  returns: array( -11 => '(GMT -11:00) Nome, Midway Island, Samoa', -10 => ...
         *                                          'keys'  returns keys only
         *
         *  @returns	Array. If format is 
         *  @static
         */
        function gmts( $format = null ){
            $g = array(
                '-11'   => array( '(GMT -11:00)', 'Nome, Midway Island, Samoa' ),
                '-10'   => array( '(GMT -10:00)', 'Hawaii' ),
                 '-9'   => array( '(GMT  -9:00)', 'Alaska' ),
                 '-8'   => array( '(GMT  -8:00)', 'Pacific Time' ),
                 '-7'   => array( '(GMT  -7:00)', 'Mountain Time' ),
                 '-6'   => array( '(GMT  -6:00)', 'Central Time, Mexico City' ),
                 '-5'   => array( '(GMT  -5:00)', 'Eastern Time, Bogota, Lima, Quito' ),
                 '-4'   => array( '(GMT  -4:00)', 'Atlantic Time, Caracas, La Paz' ),
                 '-3.5' => array( '(GMT  -3:30)', 'Newfoundland' ),
                 '-3'   => array( '(GMT  -3:00)', 'Brazil, Buenos Aires, Georgetown, Falkland Is.' ),
                 '-2'   => array( '(GMT  -2:00)', 'Mid-Atlantic, Ascention Is., St Helena' ),
                 '-1'   => array( '(GMT  -1:00)', 'Azores, Cape Verde Islands' ),
                  '0'   => array( '(GMT   0:00)', 'Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia' ),
                  '1'   => array( '(GMT  +1:00)', 'Berlin, Brussels, Copenhagen, Madrid, Paris, Rome' ),
                  '2'   => array( '(GMT  +2:00)', 'Kaliningrad, South Africa, Warsaw' ),
                  '3'   => array( '(GMT  +3:00)', 'Baghdad, Riyadh, Moscow, Nairobi' ),
                  '2.5' => array( '(GMT  +3:30)', 'Tehran' ),
                  '4'   => array( '(GMT  +4:00)', 'Abu Dhabi, Baku, Muscat, Tbilisi' ),
                  '4.5' => array( '(GMT  +4:30)', 'Kabul' ),
                  '5'   => array( '(GMT  +5:00)', 'Islamabad, Karachi, Tashkent' ),
                  '5.5' => array( '(GMT  +5:30)', 'Bombay, Calcutta, Madras, New Delhi' ),
                  '6'   => array( '(GMT  +6:00)', 'Almaty, Colombo, Dhaka' ),
                  '7'   => array( '(GMT  +7:00)', 'Bangkok, Hanoi, Jakarta' ),
                  '8'   => array( '(GMT  +8:00)', 'Beijing, Hong Kong, Perth, Singapore, Taipei' ),
                  '9'   => array( '(GMT  +9:00)', 'Osaka, Sapporo, Seoul, Tokyo, Yakutsk' ),
                  '9.5' => array( '(GMT  +9:30)', 'Adelaide, Darwin' ),
                 '10'   => array( '(GMT +10:00)', 'Melbourne, Papua New Guinea, Sydney, Vladivostok' ),
                 '11'   => array( '(GMT +11:00)', 'Magadan, New Caledonia, Solomon Islands' ),
                 '12'   => array( '(GMT +12:00)', 'Auckland, Wellington, Fiji, Marshall Island' )
            );
            if ( is_null( $format ) ) {
                return $g;
            }
            if ( $format == 'keys' ) {
                return array_keys( $g );
            }
            foreach( $g as $t => $arr ){
                if ( $format == 'simple' ) $g[ $t ] = $arr[ 0 ];
                else                       $g[ $t ] = $arr[ 0 ] . ' ' . $arr[ 1 ];
            }
            return $g;
        }


        /**
         *  This function will return a array of states.
         *
         *  @param $country     Country to search.
         *  @param $format      Format to return:     NULL  returns complete array.
         *                                          'keys'  returns keys only.
         *
         *  @returns	Array.
         *  @static
         */
        function states( $country = null, $format = null ){
            $s = array( 'PT' => array(  'AV' => 'Aveiro',
                                        'BE' => 'Beja',
                                        'BR' => 'Braga',
                                        'BG' => 'Bragan&ccedil;a',
                                        'CB' => 'Castelo Branco',
                                        'CO' => 'Coimbra',
                                        'EV' => '&Eacute;vora',
                                        'FA' => 'Faro',
                                        'GU' => 'Guarda',
                                        'LE' => 'Leiria',
                                        'LI' => 'Lisboa',
                                        'PL' => 'Portalegre',
                                        'PO' => 'Porto',
                                        'SA' => 'Santar&eacute;m',
                                        'SE' => 'Set&uacute;bal',
                                        'VC' => 'Viana do Castelo',
                                        'VI' => 'Viseu',
                                        'VR' => 'Vila Real' ) );

            // check if country exists
            if ( is_null( $country ) ){
                return $s;
            }

            // check if country code exists
            if ( ! isset( $s[ $country ] ) ){
                return array();
            }

            if ( $format == 'keys' ) {
                return array_keys( $s[ $country] );
            }

            return $s[ $country ];
        }


        /**
         *  This function will return a array with countries.
         *
         *  @param $format      Format to return.   NULL  returns complete array
         *                                        'keys'  returns keys only
         *
         *  @returns	Array. If format is 
         * 
         *  @static
         */
        function countries( $format = null ){
            $c = array(
                  "AF" => "Afghanistan",
                  "AL" => "Albania",
                  "DZ" => "Algeria",
                  "AS" => "American Samoa",
                  "AD" => "Andorra",
                  "AO" => "Angola",
                  "AI" => "Anguilla",
                  "AQ" => "Antarctica",
                  "AG" => "Antigua and Barbuda",
                  "AR" => "Argentina",
                  "AM" => "Armenia",
                  "AW" => "Aruba",
                  "AU" => "Australia",
                  "AT" => "Austria",
                  "AZ" => "Azerbaijan",
                  "BS" => "Bahamas",
                  "BH" => "Bahrain",
                  "BD" => "Bangladesh",
                  "BB" => "Barbados",
                  "BY" => "Belarus",
                  "BE" => "Belgium",
                  "BZ" => "Belize",
                  "BJ" => "Benin",
                  "BM" => "Bermuda",
                  "BT" => "Bhutan",
                  "BO" => "Bolivia",
                  "BA" => "Bosnia and Herzegovina",
                  "BW" => "Botswana",
                  "BV" => "Bouvet Island",
                  "BR" => "Brazil",
                  "IO" => "British Indian Ocean Territory",
                  "VG" => "British Virgin Islands",
                  "BN" => "Brunei Darussalam",
                  "BG" => "Bulgaria",
                  "BF" => "Burkina Faso",
                  "BI" => "Burundi",
                  "KH" => "Cambodia",
                  "CM" => "Cameroon",
                  "CA" => "Canada",
                  "CV" => "Cape Verde",
                  "KY" => "Cayman Islands",
                  "CF" => "Central African Republic",
                  "TD" => "Chad",
                  "CL" => "Chile",
                  "CN" => "China",
                  "CX" => "Christmas Island",
                  "CC" => "Cocos Islands",
                  "CO" => "Colombia",
                  "KM" => "Comoros",
                  "CD" => "Congo",
                  "CG" => "Congo",
                  "CK" => "Cook Islands",
                  "CR" => "Costa Rica",
                  "CI" => "Cote D'Ivoire",
                  "CU" => "Cuba",
                  "CY" => "Cyprus",
                  "CZ" => "Czech Republic",
                  "DK" => "Denmark",
                  "DJ" => "Djibouti",
                  "DM" => "Dominica",
                  "DO" => "Dominican Republic",
                  "EC" => "Ecuador",
                  "EG" => "Egypt",
                  "SV" => "El Salvador",
                  "GQ" => "Equatorial Guinea",
                  "ER" => "Eritrea",
                  "EE" => "Estonia",
                  "ET" => "Ethiopia",
                  "FO" => "Faeroe Islands",
                  "FK" => "Falkland Islands (Malvinas)",
                  "FJ" => "Fiji",
                  "FI" => "Finland",
                  "FR" => "France",
                  "GF" => "French Guiana",
                  "PF" => "French Polynesia",
                  "TF" => "French Southern Territories",
                  "GA" => "Gabon",
                  "GM" => "Gambia",
                  "GE" => "Georgia",
                  "DE" => "Germany",
                  "GH" => "Ghana",
                  "GI" => "Gibraltar",
                  "GR" => "Greece",
                  "GL" => "Greenland",
                  "GD" => "Grenada",
                  "GP" => "Guadaloupe",
                  "GU" => "Guam",
                  "GT" => "Guatemala",
                  "GN" => "Guinea",
                  "GW" => "Guinea-Bissau",
                  "GY" => "Guyana",
                  "HT" => "Haiti",
                  "HM" => "Heard and McDonald Islands",
                  "VA" => "Holy See (Vatican City State)",
                  "HN" => "Honduras",
                  "HK" => "Hong Kong",
                  "HR" => "Hrvatska (Croatia)",
                  "HU" => "Hungary",
                  "IS" => "Iceland",
                  "IN" => "India",
                  "ID" => "Indonesia",
                  "IR" => "Iran",
                  "IQ" => "Iraq",
                  "IE" => "Ireland",
                  "IL" => "Israel",
                  "IT" => "Italy",
                  "JM" => "Jamaica",
                  "JP" => "Japan",
                  "JO" => "Jordan",
                  "KZ" => "Kazakhstan",
                  "KE" => "Kenya",
                  "KI" => "Kiribati",
                  "KP" => "Korea",
                  "KR" => "Korea",
                  "KW" => "Kuwait",
                  "KG" => "Kyrgyz Republic",
                  "LA" => "Lao People's Democratic Republic",
                  "LV" => "Latvia",
                  "LB" => "Lebanon",
                  "LS" => "Lesotho",
                  "LR" => "Liberia",
                  "LY" => "Libyan Arab Jamahiriya",
                  "LI" => "Liechtenstein",
                  "LT" => "Lithuania",
                  "LU" => "Luxembourg",
                  "MO" => "Macao",
                  "MK" => "Macedonia",
                  "MG" => "Madagascar",
                  "MW" => "Malawi",
                  "MY" => "Malaysia",
                  "MV" => "Maldives",
                  "ML" => "Mali",
                  "MT" => "Malta",
                  "MH" => "Marshall Islands",
                  "MQ" => "Martinique",
                  "MR" => "Mauritania",
                  "MU" => "Mauritius",
                  "YT" => "Mayotte",
                  "MX" => "Mexico",
                  "FM" => "Micronesia",
                  "MD" => "Moldova",
                  "MC" => "Monaco",
                  "MN" => "Mongolia",
                  "MS" => "Montserrat",
                  "MA" => "Morocco",
                  "MZ" => "Mozambique",
                  "MM" => "Myanmar",
                  "NA" => "Namibia",
                  "NR" => "Nauru",
                  "NP" => "Nepal",
                  "AN" => "Netherlands Antilles",
                  "NL" => "Netherlands",
                  "NC" => "New Caledonia",
                  "NZ" => "New Zealand",
                  "NI" => "Nicaragua",
                  "NE" => "Niger",
                  "NG" => "Nigeria",
                  "NU" => "Niue",
                  "NF" => "Norfolk Island",
                  "MP" => "Northern Mariana Islands",
                  "NO" => "Norway",
                  "OM" => "Oman",
                  "PK" => "Pakistan",
                  "PW" => "Palau",
                  "PS" => "Palestinian Territory",
                  "PA" => "Panama",
                  "PG" => "Papua New Guinea",
                  "PY" => "Paraguay",
                  "PE" => "Peru",
                  "PH" => "Philippines",
                  "PN" => "Pitcairn Island",
                  "PL" => "Poland",
                  "PT" => "Portugal",
                  "PR" => "Puerto Rico",
                  "QA" => "Qatar",
                  "RE" => "Reunion",
                  "RO" => "Romania",
                  "RU" => "Russian Federation",
                  "RW" => "Rwanda",
                  "SH" => "St. Helena",
                  "KN" => "St. Kitts and Nevis",
                  "LC" => "St. Lucia",
                  "PM" => "St. Pierre and Miquelon",
                  "VC" => "St. Vincent and the Grenadines",
                  "WS" => "Samoa",
                  "SM" => "San Marino",
                  "ST" => "Sao Tome and Principe",
                  "SA" => "Saudi Arabia",
                  "SN" => "Senegal",
                  "CS" => "Serbia and Montenegro",
                  "SC" => "Seychelles",
                  "SL" => "Sierra Leone",
                  "SG" => "Singapore",
                  "SK" => "Slovakia",
                  "SI" => "Slovenia",
                  "SB" => "Solomon Islands",
                  "SO" => "Somalia",
                  "ZA" => "South Africa",
                  "GS" => "South Georgia and the South Sandwich Islands",
                  "ES" => "Spain",
                  "LK" => "Sri Lanka",
                  "SD" => "Sudan",
                  "SR" => "Suriname",
                  "SJ" => "Svalbard & Jan Mayen Islands",
                  "SZ" => "Swaziland",
                  "SE" => "Sweden",
                  "CH" => "Switzerland",
                  "SY" => "Syrian Arab Republic",
                  "TW" => "Taiwan",
                  "TJ" => "Tajikistan",
                  "TZ" => "Tanzania",
                  "TH" => "Thailand",
                  "TL" => "Timor-Leste",
                  "TG" => "Togo",
                  "TK" => "Tokelau",
                  "TO" => "Tonga",
                  "TT" => "Trinidad and Tobago",
                  "TN" => "Tunisia",
                  "TR" => "Turkey",
                  "TM" => "Turkmenistan",
                  "TC" => "Turks and Caicos Islands",
                  "TV" => "Tuvalu",
                  "VI" => "US Virgin Islands",
                  "UG" => "Uganda",
                  "UA" => "Ukraine",
                  "AE" => "United Arab Emirates",
                  "GB" => "United Kingdom of Great Britain & N. Ireland",
                  "UM" => "United States Minor Outlying Islands",
                  "US" => "United States of America",
                  "UY" => "Uruguay",
                  "UZ" => "Uzbekistan",
                  "VU" => "Vanuatu",
                  "VE" => "Venezuela",
                  "VN" => "Viet Nam",
                  "WF" => "Wallis and Futuna Islands",
                  "EH" => "Western Sahara",
                  "YE" => "Yemen",
                  "ZM" => "Zambia, Republic of",
                  "ZW" => "Zimbabwe" );

            if ( $format == 'keys' ) {
                return array_keys( $c );
            }

            return $c;
        }



    }

?>
