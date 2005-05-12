<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    /**
     *	This is a general timer class that starts counting when it's instantiated, and which returns the elapsed time as
     *	soon as the finish method is called.
     */
    class YDTimer extends YDBase {

        /**
         *	This is the class constructor of the YDTimer class.
         */
        function YDTimer() {

            // Initialize YDBase
            $this->YDBase();

            // Get the start time
            $this->startTime = $this->_getMicroTime();

            // Keep a list of intermediate times
            $this->markers = array();

        }

        /**
         *	This function returns the current microtime as a double.
         *
         *	@returns	Double containing the current time.
         *
         *	@internal
         */
        function _getMicroTime() {
            $time = explode ( ' ', microtime() );
            return ( doubleval( $time[0] ) + $time[1] );
        }

        /**
         *	This will add a named marker.
         *
         *	@remark
         *		If there is already a marker with that name, a fatal error will be raised.
         *
         *	@param $name	The name to use for the marker
         */
        function addMarker( $name ) {
            if ( isset( $this->markers[ $name ] ) ) {
                trigger_error( 'Marker with name "' . $name . '" is already set.', YD_ERROR );
            }
            array_push( $this->markers, array( $name => $this->getElapsed() ) );
        }

        /**
         *	Finish the timer.
         */
        function finish() {
            $this->addMarker( '-- Finish' );
        }

        /**
         *	This function will return the number of seconds elapsed since the timer was instantiated.
         *
         *	@returns	The total elapsed time
         */
        function getElapsed() {
            $endTime = $this->_getMicroTime();
            return intval( ( $endTime - $this->startTime ) * 1000 );
        }

        /**
         *	This function returns a report as an array, with each row containing the following info: elapsed time,
         *	difference with previous marker and marker name.
         *
         *	@returns	Array with the elapsed times, differences and marker names.
         */
        function getReport() {
            $report = array( '-- Start' => array( 0, 0, '-- Start' ) );
            $previous = 0;
            foreach ( $this->markers as $marker ) {
                foreach ( $marker as $key=>$val ) {
                    $diff = $val - $previous;
                    $previous = $val;
                    $report[ $key ] = array( $val, $diff, $key );
                }
            }
            foreach ( $report as $key=>$val ) {
                if ( isset( $report[ $key + 1 ] ) ) {
                    $report[$key][1] = $report[$key+1][1];
                }
                if ( $key == sizeof( $report )-1 ) {
                    $report[$key][1] = '-';
                }
            }
            return $report;
        }

    }

    /**
     *  This class houses all the array related utility functions. All the methods are implemented as static methods and
     *	do not require you to create a class instance in order to use them.
     */
    class YDArrayUtil extends YDBase {

        /**
         *  This function will convert a single dimension array to a multi dimension array with the indicated number of
         *	colums. If the number of columns is 1, it will return the original array.
         *
         *  If you enable the $fillLastRow option, it will fill the last row with null values to match the number of
         *	columns.
         *
         *  @param $array		The single dimension array you want to convert.
         *  @param $columns		The number of columns the table should have.
         *  @param $fillLastRow	(optional) If true, the last row will be filled with null values so that it matches the
         *						number of columns.
         *
         *  @returns	A multi-dimension array with the contents of the original array converted to a table with the
         *				indicated number of colums.
         *
         *	@static
         */
        function convertToTable( $array, $columns, $fillLastRow=false ) {

            // If the number of columns is 1, return the original array
            if ( $columns == 1 ) { return $array; }

            // Use the array_chunk function to convert to a table
            $newArray = @array_chunk( $array, $columns );
            if ( $newArray == null ) {
                trigger_error( 'Failed to split the array in chunks.', YD_ERROR );
            }

            // Pad the last row
            if ( $fillLastRow ) {
                $lastRow = $newArray[sizeof( $newArray )-1];
                $numMissing = $columns - sizeof( $lastRow );
                for ( $i = 0; $i < $numMissing; $i++ ) {
                    array_push( $newArray[sizeof( $newArray )-1], null );
                }

            }

            // Return the array
            return $newArray;

        }

        /**
         *	This function will create a new array which is a nested using the given column name.
         *
         *	@param $array	The array to convert.
         *	@param $key		The column to use as the key name.
         *
         *	@returns	A new array which is a nested using the given column name.
         *
         *	@static
         */
        function convertToNested( $array, $key ) {

            // Start with the a new array
            $new = array();

            // Loop over the original array
            foreach ( $array as $item ) {
                if ( ! array_key_exists( $key, $item ) ) {
                    trigger_error( 'YDArrayUtil::convertToNested: key "' . $key . '" not found', YD_ERROR );
                }
                if ( ! isset( $new[ $item[ $key ] ] ) ) { $new[ $item[ $key ] ] = array(); }
                array_push( $new[ $item[ $key ] ], $item );
            }

            // Return the new array
            return $new;

        }

        /**
         *	This function will create a new array which is grouped by a given key and mapped according to a given array.
         *
         *	@param $input	The array to convert.
         *	@param $key		The column to use as the key name.
         *	@param $map		The array or string which indicates how values of the children arrays should be mapped in 
         *                  the parent array.
         *
         *	@returns		The array resulting from the mapping.
         *
         *	@static
         */
        function map( $input, $key, $map ) {

            // Starting with new array
            $output = array();

            // Loop over the original array
            foreach ( $input as $inputFragment ) {
                if ( isset( $inputFragment[ $key ] ) ) {
                    if ( is_array($map)) {
                        foreach ( $map as $mapKey => $mapValue ) {
                            if ( ! array_key_exists( $key, $inputFragment ) ) {
                                trigger_error( 'YDArrayUtil::map: key "' . $key . '" not found', YD_ERROR );
                            }
                            if ( isset( $inputFragment[ $mapKey ] ) && isset( $inputFragment[ $mapValue ] ) ) {
                                $output[ $inputFragment[ $key ] ][ $inputFragment[ $mapKey ] ] = $inputFragment[ $mapValue ];
                            }
                            else {
                                $output[ $inputFragment[ $key ] ][ $inputFragment[ $mapKey ] ] = NULL;
                            }
                        }
                    }
                    else {
                        $output[ $inputFragment[ $key ] ] = $inputFragment[ $map ];
                    }
                }
            }

            // Return the new array
            return $output;
        } 

    }

    /**
     *	This class houses all the debug related utility functions. All the methods are implemented as static methods and
     *	do not require you to create a class instance in order to use them.
     */
    class YDDebugUtil extends YDBase {

        /**
         *	Function to output a debug message. These message are only shown if the constant YD_DEBUG is set to 1. You
         *	can turn on debugging by specifying the YD_DEBUG parameter in the url and assigning it the value 1.
         *
         *	Example url with debugging turned on: http://localhost/index.php?YD_DEBUG=1
         *
         *	This function accepts a variable amount of arguments which are all concatenated using a space in between.
         *	All debug messages will be shown as HTML comments with the prefix "[ YD_DEBUG ]".
         *
         *	@static
         */
        function debug() {
            $args = func_get_args();
            if ( YDConfig::get( 'YD_DEBUG' ) == 1 ) {
                echo( YD_CRLF . '<!-- [ YD_DEBUG ] ' . implode( ' ', $args ) . ' -->' . YD_CRLF );
            }
            if ( YDConfig::get( 'YD_DEBUG' ) == 2 ) {
                echo( '<table border="0" cellspacing="0" cellpadding="4"><tr>' );
                echo( '<td bgcolor="#FFCC00">' );
                echo( '<b>' . YD_FW_NAME . ' Debug Information</b> ' );
                echo( '<pre>' . htmlspecialchars( trim( implode( ' ', $args ) ) ) . '</pre>' );
                echo( '</td></tr></table>' );
            }
        }

        /**
         *	Function to dump the contents of pretty much anything. This is the same as the var_dump function in PHP, but
         *	has a nicer and more readable output.
         *
         *	@param $obj		Object you want to dump.
         *	@param $label	The label for the dump.
         *
         *	@static
         */
        function dump( $obj, $label='' ) {
            echo( YDDebugUtil::r_dump( $obj, true, $label ) );
        }

        /**
         *	Function to return the contents of pretty much anything. This is the same as the var_export function in PHP.
         *
         *	@param $obj		Object you want to dump.
         *	@param $html	(optional) If you want to have everything returned as HTML or text. The default is false,
         *					returning text.
         *	@param $label	The label for the dump.
         *
         *	@returns	Text representation of the object.
         *
         *	@static
         */
        function r_dump( $obj, $html=false, $label='' ) {
            $data = var_export( $obj, true );
            if ( $html == true ) {
                $data = stripslashes( htmlentities( $data ) );
                if ( ! empty( $label ) ) {
                    $data = '<pre><b style="color: navy">' . $label . '</b><br>' . $data . '</pre>';
                } else {
                    $data = '<pre>' . $data . '</pre>';
                }
            } else {
                $data = $label . YD_CRLF . $data;
            }
            return $data;
        }

        /**
         *	This function will print a stack trace.
         *
         *	@static
         */
        function stackTrace() {
            if ( YDConfig::get( 'YD_DEBUG' ) == 1 || YDConfig::get( 'YD_DEBUG' ) == 2 ) {
                $err = '';
                $err .= 'URI: ' . YD_SELF_URI . YD_CRLF;
                $err .= 'Debug backtrace:' . YD_CRLF;
                foreach( debug_backtrace() as $t ) {
                    $err .= '    @ ';
                    if ( isset( $t['file'] ) ) {
                        $err .= basename( $t['file'] ) . ':' . $t['line'];
                    } else {
                        $err .= basename( YD_SELF_FILE );
                    }
                    $err .= ' -- ';
                    if ( isset( $t['class'] ) ) {
                        $err .= $t['class'] . $t['type'];
                    }
                    $err .= $t['function'];
                    if ( isset( $t['args'] ) && sizeof( $t['args'] ) > 0 ) {
                        $err .= '(...)';
                    } else {
                        $err .= '()';
                    }
                    $err .= YD_CRLF;
                }
                if ( ini_get( 'display_errors' ) == 1 ) {
                    echo( '<pre>' . YD_CRLF . htmlentities( $err ) . '</pre>' );
                }
                error_log( $err, 0 );
            }
        }

    }

    /**
     *	This class houses all the object and class related utility functions. All the methods are implemented as static
     *	methods and do not require you to create a class instance in order to use them.
     */
    class YDObjectUtil extends YDBase {

        /**
         *	This function checks if an object instance is of a specific class or is based on a derived class of the
         *	given class. The class name is case insensitive.
         *
         *	@param $obj		The object instance to check.
         *	@param $class	The object type you want to check against.
         *
         *	@returns	Boolean indicating if the object is of the specified class.
         *
         *	@static
         */
        function isSubClass( $obj, $class ) {
            $class = strtolower( $class );
            if ( function_exists( 'is_a' ) ) {
                return is_a( $obj, $class );
            } else {
                if ( is_object( $obj ) ) {
                    if ( strtolower( get_class( $obj ) ) == strtolower( $class ) ) return true;
                    if ( is_subclass_of( $obj, $class ) ) return true;
                }
            }
            return false;
        }

        /**
         *	Function to get all the ancestors of a class. The list will contain the parent class first, and then it's
         *	parent class, etc. You can pass both the name of the class or an object instance to this function
         *
         *	@param $classname	Name of the class or object.
         *
         *	@returns	Array with all the ancestors.
         *
         *	@static
         */
        function getAncestors( $classname ) {
            if ( is_object( $classname ) ) {
                $classname = strtolower( get_class( $classname ) );
            }
            $ancestors = array();
            $father = get_parent_class( $classname );
            if ( $father != '' ) {
                $ancestors = YDObjectUtil::getAncestors( $father );
                $ancestors[] = $father;
            }
            return array_reverse( $ancestors );
        }

        /**
         *	This function will serialize an object.
         *
         *	@param $obj	Object to serialize.
         *
         *	@static
         */
        function serialize( $obj ) {
            $obj = serialize( $obj );
            if ( ! $obj ) {
                trigger_error( 'Failed serializing the object', YD_ERROR );
            }
            return $obj;
        }

        /**
         *	This function will unserialize an object.
         *
         *	@param $obj	Object to unserialize.
         *
         *	@static
         */
        function unserialize( $obj ) {
            $obj = unserialize( $obj );
            if ( ! $obj ) {
                trigger_error( 'Failed unserializing the object', YD_ERROR );
            }
            return $obj;
        }

    }

    /**
     *	This class houses all the string related utility functions. All the methods are implemented as static methods
     *	and do not require you to create a class instance in order to use them.
     */
    class YDStringUtil extends YDBase {

        /**
         *	Function to format a file size to a meaningful value.
         *
         *	@param $bytes		The file size to format.
         *	@param $decimals	(optional) The number of decimals that should be returned.
         *
         *	@returns String containing the formatted file size.
         *
         *	@static
         */
        function formatFilesize( $bytes, $decimals=1 ) {

            // Convert the bytes to a string
            $bytes = strval( $bytes );

            // The different units
            $units = array(
                '1152921504606846976'	=> 'EB',
                '1125899906842624'		=> 'PB',
                '1099511627776'			=> 'TB',
                '1073741824'			=> 'GB',
                '1048576'				=> 'MB',
                '1024'					=> 'KB'
            );

            // If smaller than 1024, return it as bytes
            if ( $bytes <= 1024 ) {
                return $bytes . ' bytes';
            }

            // Check the right format
            foreach ( $units as $base=>$title ) {
                if ( floor( $bytes / $base ) != 0 ) {
                    return number_format( $bytes / $base, $decimals, '.', "'" ) . ' ' . $title;
                }
            }

        }

        /**
         *  This function will format a timestamp using the strftime function.
         *
         *  @param  $timestamp  The timestamp to format. It can also be a date/time form object.
         *  @param  $format     The strftime format to use. You can also use the predefined options date, time and
         *                      datetime.
         *  @param  $locale     (optional) The locale to use to format the date.
         *
         *  @returns    A formatted timestamp
         *
         *  @static
         */
        function formatDate( $timestamp, $format, $locale=null ) {

            // Check if the timestamp is an object and has the getTimeStamp function
            if ( is_object( $timestamp ) && method_exists( $timestamp, 'getTimeStamp' ) ) {
                $timestamp = $timestamp->getTimeStamp();
            }

            // Convert to an integer
            if ( is_numeric( $timestamp ) ) {
                $timestamp = intval( $timestamp );
            }

            // If text, convert to number
            if ( is_string( $timestamp ) ) {
                $timestamp = strtotime( $timestamp );
            }

            // Check the standard formats
            if ( strtolower( $format ) == 'date' ) {
                $format = '%d %B %Y';
            }
            if ( strtolower( $format ) == 'datetime' ) {
                $format = '%d %B %Y %H:%M';
            }
            if ( strtolower( $format ) == 'time' ) {
                $format = '%H:%M';
            }

            // Set the new locale
            if ( ! is_null( $locale ) ) {
                $currentLocale = YDLocale::get();
                YDLocale::set( $locale );
            }

            // Return the formatted date
            $timestamp = strftime( $format, $timestamp );

            // Reset the old locale
            if ( ! is_null( $locale ) ) {
                YDLocale::set( $currentLocale );
            }

            // Return the timestamp
            return $timestamp;

        }

        /**
         *	This function will encode all characters which have an ordinal bigger than 128 to numeric HTML entities,
         *	which can be safely included in e.g. XML output.
         *
         *	@param $string	The original string to encode.
         *	@param $htmlent	Boolean indicating if the result should be HTML encoded or not.
         *
         *	@returns	String with all the characters with an ordinal bigger than 128 converted to numeric entities.
         *
         *	@static
         */
        function encodeString( $string, $htmlent=false ) {
            $trans = get_html_translation_table( HTML_ENTITIES, ENT_NOQUOTES );
            foreach ( $trans as $key => $value ) {
                if ( ord( $key ) == 60 || ord( $key ) == 62 ) {
                    unset( $trans[$key] );
                } else {
                    $trans[$key] = '&#' . ord( $key ) . ';';
                }
            }
            $string = strtr( $string, $trans );
            if ( $htmlent == true ) {
                $string = htmlentities( $string );
            }
            return $string;
        }

        /**
         *	This function will truncate a string.
         *
         *	@param $string		String to truncate.
         *	@param $length		(optional) The length to truncate to. Default length is 80 characters.
         *	@param $etc			(optional) The string to append if the item gets trunctated. Default is '...'.
         *	@param $break_words	(optional) Break in the middle of words or not. Default is false.
         *
         *	@static
         */
        function truncate( $string, $length=80, $etc='...', $break_words=false ) {
            if ( $length == 0 ) { return ''; }
            if ( strlen( $string ) > $length ) {
                $string = html_entity_decode( strip_tags( $string ) );
                $length -= strlen( $etc );
                if ( ! $break_words ) {
                    $string = preg_replace( '/\s+?(\S+)?$/', '', substr( $string, 0, $length+1 ) );
                }
                return htmlentities( substr( $string, 0, $length ) . $etc );
            } else {
                return $string;
            }
        }

        /**
         *	This function normalizes all the newlines to the correct newline character for the current platform.
         *
         *	@param $string		String to normalize the newlines from.
         *
         *	@returns	The original string with normalized newlines.
         *
         *	@static
         */
        function normalizeNewlines( $string ) {

            // First, change all to \n
            $string = str_replace( "\r\n", "\n",    $string );
            $string = str_replace( "\r",   "\n",    $string );

            // Now, change everything to the correct one
            $string = str_replace( "\n",   YD_CRLF, $string );

            // Return the changed string
            return $string;

        }

        /**
         *	This function will remove all newlines and all spaces at the beginning and end of each line.
         *
         *	@param $string		String to remove the whitespace from.
         *
         *	@returns	The original string without the newlines and spaces at the beginning and end of each line.
         *
         *	@static
         */
        function removeWhiteSpace( $string ) {

            // First, normalize the newlines
            $string = YDStringUtil::normalizeNewLines( $string );

            // Now, remove the whitespace
            $string = implode( ' ', array_map( 'trim', explode( YD_CRLF, $string ) ) );

            // Return the changed string
            return $string;

        }

    }

    /**
     *	This class uses the HTTP_USER_AGENT varaible to get information about the browser the visitor used to perform
     *	the request. We determine the browser name, the version and the platform it's running on.
     */
    class YDBrowserInfo extends YDBase {

        /**
         *	The class constructor analyzes for the YDBrowserInfo class. The constructor takes no arguments and uses the
         *	$_SERVER['HTTP_USER_AGENT'] variable to parse the browser info.
         */
        function YDBrowserInfo() {

            // Initialize YDBase
            $this->YDBase();

            // Check if the user agent was specified
            if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {

                // Mark everything as unknown
                $this->agent = 'unknown';
                $this->browser = 'unknown';
                $this->version = 'unknown';
                $this->platform = 'unknown';
                $this->dotnet = 'unknown';

            } else {

                // Get the user agent
                $this->agent = $_SERVER['HTTP_USER_AGENT'];

                // Determine the browser name
                if ( preg_match( '/MSIE ([0-9].[0-9]{1,2})/', $this->agent, $ver ) ) {
                    $this->version = $ver[1];
                    $this->browser = 'ie';
                } elseif ( preg_match( '/Safari\/([0-9]+)/', $this->agent, $ver ) ) {
                    $this->version = '1.0b' . $ver[1];
                    $this->browser = 'safari';
                } elseif ( preg_match( '/Opera ([0-9].[0-9]{1,2})/', $this->agent, $ver ) ) {
                    $this->version = $ver[1];
                    $this->browser = 'opera';
                } elseif ( preg_match( '/Opera\/([0-9].[0-9]{1,2})/', $this->agent, $ver ) ) {
                    $this->version = $ver[1];
                    $this->browser = 'opera';
                } elseif ( preg_match( '/Firefox\/([0-9].[0-9]{1,2})/', $this->agent, $ver ) ) {
                    $this->version = $ver[1];
                    $this->browser = 'firefox';
                } elseif ( preg_match( '/Mozilla\/([0-9].[0-9]{1,2})/', $this->agent, $ver ) ) {
                    $this->version = $ver[1];
                    $this->browser = 'mozilla';
                } else {
                    $this->version = 0;
                    $this->browser = 'other';
                }

                // Determine the platform
                if ( stristr( $this->agent,'Win' ) ) {
                    $this->platform = 'win';
                } elseif ( stristr( $this->agent,'Mac' ) ) {
                    $this->platform = 'mac';
                } elseif ( stristr( $this->agent,'Linux' ) ) {
                    $this->platform = 'linux';
                } elseif ( stristr( $this->agent,'Unix' ) ) {
                    $this->platform = 'unix';
                } else {
                    $this->platform = 'other';
                }

                // Get the .NET runtime version
                preg_match_all( '/.NET CLR ([0-9][.][0-9])/i', $this->agent, $ver );
                $this->dotnet = $ver[1];

            }

        }

        /**
         *	This function returns an array with the languages that are supported by the browser. This is done by using
         *	the HTTP_ACCEPT_LANGUAGE server variable that gets send with the HTTP headers.
         *
         *	@return Array containing the list of supported languages
         */
        function getBrowserLanguages() {

            // We parse the language headers sent by the browser
            if ( ! isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
                return array();
            }
            $browserLanguages = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );

            // Normalize the browser language headers
            for ( $i = 0; $i < sizeof( $browserLanguages ); $i++ ) {
                $browserLanguage = explode( ';', $browserLanguages[$i] );
                $browserLanguages[$i] = substr( $browserLanguage[0], 0, 2 );
            }

            // Remove the duplicates
            $browserLanguages = array_unique( $browserLanguages );

            // Return the browser languages
            return array_values( $browserLanguages );

        }

        /**
         *  This function returns an array with the languages that are supported by the browser and also interprets the
         *  country information that the browser sends over.. This is done by using the HTTP_ACCEPT_LANGUAGE server
         *  variable that gets send with the HTTP headers.
         *
         *  @return Array containing the list of supported languages
         */
        function getBrowserLanguagesAndCountries() {

            // We parse the language headers sent by the browser
            if ( !isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
                return array();
            }
            $browserCountries = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
            $languagesAndCountries = array();

            // Loop over the languages and normalize them
            foreach( $browserCountries as $value ) {
                $lang = explode( ';', $value );
                $lang = explode( '-', $lang[0] );
                $lang[1] = ( isset( $lang[1] ) && $lang[1] != '' ? $lang[1] : $lang[0] );
                $languagesAndCountries[][ $lang[1] ] = $lang[0];
            }

            // Return the normalized list
            return $languagesAndCountries;

        }

        /**
         *	This function will get the most appropriate language for the browser, considering the list of supported
         *	languages by both the browser and the web application.
         *
         *	@param $supported	(optional) An array with the list of supported languages. By default, only english is
         *						supported.
         */
        function getLanguage( $supported=array( 'en' ) ) {

            // Start with the default language
            $language = $supported[0];

            // Get the list of languages supported by the browser
            $browserLanguages = $this->getBrowserLanguages();

            // Now, we look if the browser specified one
            if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
                foreach ( $browserLanguages as $browserLanguage ) {
                    if ( in_array( $browserLanguage, $supported ) ) {
                        $language = $browserLanguage;
                        break;
                    }
                }
            }

            // Return the language
            return $language;

        }

    }

?>
