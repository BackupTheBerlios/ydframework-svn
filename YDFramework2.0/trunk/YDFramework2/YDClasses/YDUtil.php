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
     *	This is a general timer class that starts counting when it's instantiated, and which returns the elapsed time as
     *	soon as the finish method is called.
     *
     *  @ingroup YDFramework
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
         *	@param $name	The name to use for the marker
         */
        function addMarker( $name ) {
            array_push( $this->markers, array( $name, $this->getElapsed() ) );
        }

        /**
         *	Finish the timer.
         */
        function finish() {
            $this->addMarker( '** Finish' );
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
            $report = array();
            $report[] = array( 0, 0, '** Start' );
            $previous = 0;
            foreach ( $this->markers as $marker ) {
                $report[] = array( $marker[1], $marker[1]-$previous, $marker[0] );
                $previous = $marker[1];
            }
            return $report;
        }

    }

    /**
     *  This class houses all the array related utility functions. All the methods are implemented as static methods and
     *	do not require you to create a class instance in order to use them.
     *
     *  @ingroup YDFramework
     */
    class YDArrayUtil extends YDBase {

        /**
         *  This function will convert a single dimension array to a multi dimension array with the indicated number of
         *	colums. If the number of columns is 1, it will return the original array.
         *
         *  If you enable the $fillLastRow option, it will fill the last row with null values to match the number of
         *	columns.
         *
         *  @param $array		    The single dimension array you want to convert.
         *  @param $columns		    The number of columns the table should have.
         *  @param $fillLastRow	    (optional) If true, the last row will be filled with null values so that it matches
         *						    the number of columns.
         *  @param $horizontal  	(optional) If true, rows will be filled first, then it will create an new row
         *
         *  @returns	A multi-dimension array with the contents of the original array converted to a table with the
         *				indicated number of colums.
         *
         *	@static
         */
        function convertToTable( $array, $columns, $fillLastRow=false, $horizontal=true ) {

            // If the number of columns is 1, return the original array
            if ( $columns == 1 ) {
                return $array;
            }

            // Return original array if empty
            if ( sizeof( $array ) == 0 ) {
                return $array;
            }

            // Convert horizontally or vertically
            if ( $horizontal ) {

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

            } else {

                // Get only the values of the array
                $array = array_values( $array );

                // Check how many rows we will have
                $rows = ceil( sizeof( $array ) / $columns );

                // Keep track of the current row
                $currentItem = 0;
                $currentCol  = 0;

                // Start with a new empty array
                $newArray = array();

                // Loop over the rows
                while ( $currentCol < $columns ) {
                    foreach ( range( 0, $rows-1 ) as $row ) {
                        $newArray[$row][$currentCol] = isset( $array[$currentItem] ) ? $array[$currentItem] : null;
                        $currentItem++;
                    }
                    $currentCol++;
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
         *	@param $append	(Optional) Boolean that defines if values are append to array (TRUE) or if are assigned (FALSE).
         *
         *	@returns	A new array which is a nested using the given column name.
         *
         *	@static
         */
        function convertToNested( $array, $key, $append = true ) {

            // Start with the a new array
            $new = array();

            // Loop over the original array
            foreach ( $array as $item ) {
                if ( ! array_key_exists( $key, $item ) ) {
                    trigger_error( 'YDArrayUtil::convertToNested: key "' . $key . '" not found', YD_ERROR );
                }

                // append values or assign them
                if ( $append == true ){
                    if ( ! isset( $new[ $item[ $key ] ] ) ) { $new[ $item[ $key ] ] = array(); }
                    array_push( $new[ $item[ $key ] ], $item );
                }else{
                    $new[ $item[ $key ] ] = $item;
                }
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

        /**
         *  This function will implode a 1-level array in a string.
         *
         *  @param $input      The array to implode.
         *  @param $glue       (optional) The key/value glue. Default: =
         *  @param $separator  (optional) The items separator. Default: ,
         *
         *  @returns       The imploded string.
         *
         *  @static
         */
        function implode( $input, $glue='=', $separator=',' ) {
            $return = '';
            foreach ( $input as $k => $v ) {
                $return .= $separator . $k . $glue . $v;
            }
            return substr( $return, 1 );
        }

        /**
         *  This function will initialize the indicated array if it doesn't exist yet.
         *
         *  @param  $where  Where to instantiate the array in.
         *  @param  $name   The name of the array to create.
         *
         *  @static
         */
        function createIfNeeded( & $where, $name ) {
            if ( ! isset( $where[$name] ) ) {
                $where[$name] = array();
            }
        }


        /**
         *  This function will convert a database result array to XML
         *
         *  @param $array      The input array.
         *
         *  @returns       The array as an XML string.
         *
         *  @static
         */
        function toXml( $array ) {
            $out = '';
            foreach ( $array as $record ) {
                $out .= sprintf( '<row>' );
                foreach ( $record as $key=>$val ) {
                    if ( is_numeric( $val ) ) {
                        $out .= sprintf( '<%s>%s</%s>', $key, $val, $key );
                    } else {
                        $out .= sprintf( '<%s><![CDATA[%s]]></%s>', $key, $val, $key );
                    }
                }
                $out .= sprintf( '</row>' );
            }
            $out = sprintf( '<root>%s</root>', $out );
            return '<?xml version="1.0" encoding="utf-8"?>' . $out;
        }

    }

    /**
     *	This class houses all the debug related utility functions. All the methods are implemented as static methods and
     *	do not require you to create a class instance in order to use them.
     *
     *  @ingroup YDFramework
     */
    class YDDebugUtil extends YDBase {

        /**
         *  Trigger an error
         *
         *  @param  $msg    The error to raise.
         *  @param  $sql    (optional) The SQL statement to log with the error.
         *  @param  $level  (optional) The type of error to be raised. Default is YD_ERROR.
         */
        function error( $msg, $sql=null, $level=YD_ERROR ) {
            echo( '<p><b><font color="red">An error occured</font></b></p>' );
            echo( '<b>Stacktrace:</b> <pre>' . YDDebugUtil::getStackTrace() . '</pre>' );
            if ( ! empty( $sql ) ) {
                echo( '<b>SQL Statement:</b> <pre>' . $this->formatSql( $sql ) . '</pre>' );
            }
            trigger_error( $msg, $level );
        }

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
                echo( YD_CRLF . '<!-- [ YD_DEBUG ] ' . implode( ' ', $args ) . '-->' . YD_CRLF );
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
                $style = ' style="text-align: left; font-size: 10pt;"';
                if ( ! empty( $label ) ) {
                    $data = '<pre' . $style . '><b style="color: navy">' . $label . '</b><br>' . $data . '</pre>';
                } else {
                    $data = '<pre' . $style . '>' . $data . '</pre>';
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
                $err = 'URI: ' . YD_SELF_URI . YD_CRLF . YDDebugUtil::getStackTrace();
                if ( ini_get( 'display_errors' ) == 1 ) {
                    echo( '<pre>' . YD_CRLF . htmlentities( $err ) . '</pre>' );
                }
                error_log( $err, 0 );
            }
        }

        /**
         *	Function to get a formatted stack trace.
         *
         *	@static
         */
        function getStackTrace() {
            $err = '';
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
            return $err;
        }

    }

    /**
     *	This class houses all the object and class related utility functions. All the methods are implemented as static
     *	methods and do not require you to create a class instance in order to use them.
     *
     *  @ingroup YDFramework
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
     *
     *  @ingroup YDFramework
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
            if ( is_string( $timestamp ) && ! empty( $timestamp ) ) {
                $timestamp = strtotime( $timestamp );
            }

            // If array, is a date YDForm element value
            if ( is_array( $timestamp ) ) {

                // check if timestamp exists. otherwise create it
                $hours   = isset( $timestamp[ 'hours' ] ) ?   $timestamp[ 'hours' ] : 0;
                $minutes = isset( $timestamp[ 'minutes' ] ) ? $timestamp[ 'minutes' ] : 0;
                $seconds = isset( $timestamp[ 'seconds' ] ) ? $timestamp[ 'seconds' ] : 0;
                $month   = isset( $timestamp[ 'month' ] ) ?   $timestamp[ 'month' ] : 1;
                $day     = isset( $timestamp[ 'day' ] ) ?     $timestamp[ 'day' ] : 1;
                $year    = isset( $timestamp[ 'year' ] ) ?    $timestamp[ 'year' ] : 1970;
                $timestamp = mktime( $hours, $minutes, $seconds, $month, $day, $year );
            }

            // Check the standard formats
            switch( strtolower( $format ) ){
                case 'date' :           $format = '%d %B %Y'; break;
                case 'datetime' :       $format = '%d %B %Y %H:%M'; break;
                case 'datetimesql' :    $format = '%Y-%m-%d %H:%M:%S'; break;
                case 'datesimple' :     $format = '%d/%m/%Y'; break;
                case 'datetimesimple' : $format = '%d/%m/%Y %H:%M'; break;
                case 'time' :           $format = '%H:%M'; break;
                case 'file' :           $format = '%d-%m-%Y %H:%M'; break;
            }

            // Set the new locale
            if ( ! is_null( $locale ) ) {
                $currentLocale = YDLocale::get();
                YDLocale::set( $locale );
            }

            // Return the formatted date
            $timestamp = strftime( $format, intval( $timestamp ) );

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
            $trans = array_flip( get_html_translation_table( HTML_ENTITIES ) );
            $string = strtr( $string, $trans );
            $trans = get_html_translation_table( HTML_ENTITIES, ENT_NOQUOTES );
            foreach ( $trans as $key => $value ) {
                if ( ord( $key ) == 60 || ord( $key ) == 62 || ord( $key ) == 38 ) {
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
         *   This function will encode all characters in a string to it's ASCII value.
         *
         *   @param $string   The original string to encode.
         *
         *   @returns   The encoded string.
         *
         *   @static
         */
        function encodeToAscii( $string ) {
            $result = '';
            for ( $i=0; $i < strlen( $string ); $i++ ) {
                $result .= "&#" . ord( substr( $string, $i, 1) ) . ';';
            }
            return $result;
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

        /**
         *   This function will compare two network ip addresses
         *
         *   @param $ip1      First ip
         *
         *   @param $ip2      Second ip
         *
         *   @returns    Returns < 0 if ip1 is less than ip2; > 0 if str1 is greater than ip2, and 0 if they are equal.
         *
         *   @static
         */
        function ipcmp( $ip1, $ip2 ){

            // get 4 elements from the network address
            $ip1 = explode(".", $ip1);
            $ip2 = explode(".", $ip2);

            // get an integer that represents the numeric value of the address
            $ip1 = $ip1[0]*256^3 + $ip1[1]*256^2 + $ip1[2]*256 + $ip1[3];
            $ip2 = $ip2[0]*256^3 + $ip2[1]*256^2 + $ip2[2]*256 + $ip2[3];

            // return diference
            return $ip1 - $ip2;

        }

        /**
         *  This function will replace all special characters to normal ASCII characters. This is very useful when you
         *  want to rename uploaded files and strip out the special characters.
         *
         *  @param  $data   The data to strip the special characters from.
         *
         *  @returns    The data with the special characters replaced.
         *
         *  @static
         */
        function stripSpecialCharacters( $data ) {

            // Trim the data
            $data = trim( $data );

            // Decode the HTML entities
            $trans = array_flip( get_html_translation_table( HTML_ENTITIES, ENT_NOQUOTES ) );
            $data = strtr( $data, $trans );

            // The characters to replace
            $chars = array(
                '�' => 'A', '�' => 'A', '�' => 'A', '�' => 'AE', '�' => 'E', '�' => 'E', '�' => 'I', '�' => 'I',
                '�' => 'D', '�' => 'O', '�' => 'O', '�' => 'O', '�' => 'O', '�' => 'U', '�' => 'U', '�' => 'a',
                '�' => 'a', '�' => 'a', '�' => 'ae', '�' => 'e', '�' => 'e', '�' => 'i', '�' => 'i', '�' => 'o',
                '�' => 'o', '�' => 'o', '�' => 'o', '�' => 'o', '�' => 'u', '�' => 'u', '�' => 'A', '�' => 'A',
                '�' => 'A', '�' => 'C', '�' => 'E', '�' => 'E', '�' => 'I', '�' => 'I', '�' => 'N', '�' => 'O',
                '�' => 'O', '�' => 'U', '�' => 'U',  '�' => 'Y', '�' => 'B', '�' => 'a', '�' => 'a', '�' => 'a',
                '�' => 'c', '�' => 'e', '�' => 'e', '�' => 'i', '�' => 'i', '�' => 'n', '�' => 'o', '�' => 'o',
                '�' => 'u', '�' => 'u', '�' => 'y', '�' => 'y', '@' => '_', ' ' => '_', '[' => '_', ']' => '_',
                '(' => '_', ')' => '_', '&' => '_', '+' => '_', '=' => '_'
            );

            // Strip the special characters
            $data = str_replace( array_keys( $chars ), $chars, $data );

            // Return the data
            return $data;

        }


        /**
         *  This function will replace a string to a valid url one based on ascii conversion ( by: http://ascii.cl/htmlcodes.htm )
         *
         *  @param  $s		String to convert.
         *  @param  $utf8 	(Optional) Flag that defines if string should be converted from UTF8 to ISO before conversion.
         *
         *  @returns    A converted string.
         *
         *  @static
         */
        function convertToSafe( $s, $utf8 = false ){

            if( $utf8 ){
                $s = utf8_decode( $s );
            }

            $res = '';
            for( $i = 0; $i < strlen( $s ); $i++ ){

                $ord = ord( $s{$i} );
                if ($ord==32) { $res .= '_';    continue; }
                if ($ord<48)  { $res .= '-';    continue; }
                if ($ord<58)  { $res .= $s{$i}; continue; }
                if ($ord<65)  { $res .= '-';    continue; }
                if ($ord<91)  { $res .= $s{$i}; continue; }
                if ($ord<96)  { $res .= '_';    continue; }
                if ($ord<97)  { $res .= '-';    continue; }
                if ($ord<123) { $res .= $s{$i}; continue; }
                if ($ord<192) { $res .= '-';    continue; }
                if ($ord<199) { $res .= 'A';    continue; }
                if ($ord<200) { $res .= 'C';    continue; }
                if ($ord<204) { $res .= 'E';    continue; }
                if ($ord<208) { $res .= 'I';    continue; }
                if ($ord<209) { $res .= 'D';    continue; }
                if ($ord<210) { $res .= 'N';    continue; }
                if ($ord<217) { $res .= 'O';    continue; }
                if ($ord<224) { $res .= 'U';    continue; }
                if ($ord<231) { $res .= 'a';    continue; }
                if ($ord<232) { $res .= 'c';    continue; }
                if ($ord<236) { $res .= 'e';    continue; }
                if ($ord<240) { $res .= 'i';    continue; }
                if ($ord<241) { $res .= 'o';    continue; }
                if ($ord<242) { $res .= 'n';    continue; }
                if ($ord<249) { $res .= 'o';    continue; }
                if ($ord<253) { $res .= 'u';    continue; }
                if ($ord<256) { $res .= 'y';    continue; }
                $res .= '_';
            }

            return $res;
        }


        /**
         *  This function will check if the specified string starts with the indicated text or not. You can specify if
         *  this needs to happen case-sensitively or not.
         *
         *  @param  $string         The string to check.
         *  @param  $substring      The string with which it needs to start.
         *  @param  $case_sensitive (optional) Whether the comparison needs to be case-sensitive or not. Default is true.
         *
         *  @returns    Boolean indicating if the string starts with the specified text or not.
         */
        function startsWith( $string, $substring, $case_sensitive=true ) {
            if ( ! $case_sensitive ) {
                $string = strtolower( $string );
                $substring = strtolower( $substring );
            }
            return ( substr( $string, 0, strlen( $substring ) ) == $substring );
        }

        /**
         *  This function will check if the specified string ends with the indicated text or not. You can specify if
         *  this needs to happen case-sensitively or not.
         *
         *  @param  $string         The string to check.
         *  @param  $substring      The string with which it needs to ends.
         *  @param  $case_sensitive (optional) Whether the comparison needs to be case-sensitive or not. Default is true.
         *
         *  @returns    Boolean indicating if the string ends with the specified text or not.
         */
        function endsWith( $string, $substring, $case_sensitive=true ) {
            if ( ! $case_sensitive ) {
                $string = strtolower( $string );
                $substring = strtolower( $substring );
            }
            return ( substr( $string, -strlen( $substring ) ) == $substring );
        }

        /**
         *  This function is an fnmatch replacement.
         *
         *  $param $string      The string to test
         *  $param $pattern     The pattern to match
         *
         *  @returns    Boolean indicating if the string matches the pattern or not.
         */
        function match( $string, $pattern ) {
            for ( $op = 0, $npattern = '', $n = 0, $l = strlen( $pattern ); $n < $l; $n++ ) {
            switch ($c = $pattern[$n]) {
                case '\\':
                    $npattern .= '\\' . @$pattern[++$n];
                    break;
                case '.':
                case '+':
                case '^':
                case '$':
                case '(':
                case ')':
                case '{':
                case '}':
                case '=':
                case '!':
                case '<':
                case '>':
                case '|':
                    $npattern .= '\\' . $c;
                    break;
                case '?': case '*':
                    $npattern .= '.' . $c;
                    break;
                case '[':
                case ']':
                default:
                    $npattern .= $c;
                    if ($c == '[') {
                        $op++;
                    } else if ($c == ']') {
                        if ($op == 0) return false;
                            $op--;
                        }
                        break;
                    }
            }
            if ( $op != 0 ) {
                return false;
            } else {
                return preg_match( '/' . $npattern . '/i', $string );
            }
        }

        /**
         *  Show the time that has elapsed since a given time.
         *
         *  @param  $time   The time in seconds
         *
         *  @returns    The elapsed time
         */
        function timesince( $time ) {

            // Convert to integer
            if ( is_string( $time ) && is_numeric( $time ) ) {
                $time = intval( $time );
            }

            // Array of time period chunks
            $chunks = array(
                array( 60 * 60 * 24 * 365 , t('years') ),
                array( 60 * 60 * 24 * 30 , t('months') ),
                array( 60 * 60 * 24 * 7, t('weeks') ),
                array( 60 * 60 * 24 , t('days') ),
                array( 60 * 60 , t('hours') ),
                array( 60 , t('minutes') ),
            );

            // Difference in seconds
            $since = time() - $time;

            // The first chunk
            for ( $i = 0, $j = sizeof( $chunks ); $i < $j; $i++ ) {
                $seconds = $chunks[$i][0];
                $name = $chunks[$i][1];
                if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
                    break;
                }
            }

            // Set output var
            $output = ( $count == 1 ) ? '1 '.$name : $count . ' ' . $name;

            // step two: the second chunk
            if ( $i + 1 < $j ) {
                $seconds2 = $chunks[$i + 1][0];
                $name2 = $chunks[$i + 1][1];
                if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
                    $output .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
                }
            }

            // Return the output
            return $output . ' ' . t('ago');

        }

        /**
         *  Creates a random password string
         *
         *  @param  $length		Password length. By default 6
         *  @param  $lengthmax	(Optional) Password max length. If defined, a password length between $length and $maxlength will be generated.
         *
         *  @returns    String
         */
        function generatePassword( $length = 6, $lengthmax = null ) {

            // check length
            if( ! is_null( $lengthmax ) ){
                $length = rand( $length, $lengthmax );
            }

            $chrs = "abcdefg12345hijklmnpABCDEFGHIJKLMNPqrstuvwxyzQRSTUVWXYZ6789";

            // init generator ( used when php version is smaller than 4.2.0 )
            srand( (double) microtime() * 1000000 );

            $pass = "";

            // add chars
            while( $length-- ){
                $pass .= $chrs[ rand( 0, 58 ) ];
            }

            return $pass;
        }


        /**
         *  This method returns a page navigation array. Eg, YDStringUtil::parseNavigation( 20, 8, 2, '..' ) returns array( 1, 2, 3, '..', 6, 7, 8, 9, 10, '..', 18, 19, 20 );
         *
         *  @param  $totalPages		Total pages
         *  @param  $current		(Optional) Current page. By default: 1
         *  @param  $step			(Optional) Step to check. By default: 2
         *  @param  $separator		(Optional) Separator to use. By default: '..'
         *
         *  @returns    Array
         */
        function parseNavigation( $totalPages, $current = 1, $step = 2, $separator = '..' ) {

            // create init numbers for: (s)start, (m)middle and (e)end
            $s_start = 1;
            $s_end   = ( 1 + $step > $totalPages )        ? $totalPages : 1 + $step;

            $m_start = ( $current - $step < $s_end )      ? $s_end      : $current - $step;
            $m_end   = ( $current + $step > $totalPages ) ? $totalPages : $current + $step;

            $e_start = ( $totalPages - $step < $m_end )   ? $m_end      : $totalPages - $step;
            $e_end   = $totalPages;

            // init array
            $result = array();

            // add start elements
            for( $i = $s_start; $i <= $s_end; $i++ ){
                $result[] = $i;
            }

            // check if we can add the separator between start and middle
            if( $m_start - $s_end == 2 ){
                $result[] = $s_end + 1;
            }elseif( $m_start - $s_end > 2 ){
                $result[] = $separator;
            }

            // create middle elements
            for( $i = $m_start; $i <= $m_end; $i++ ){
                if( $i > $s_end ){
                    $result[] = $i;
                }
            }

            // check if we can add the separator between middle and end
            if( $e_start - $m_end == 2 ){
                $result[] = $m_end + 1;
            }elseif( $e_start - $m_end > 2 ){
                $result[] = $separator;
            }

            // create end elements
            for( $i = $e_start; $i <= $e_end; $i++ ){
                if( $i > $m_end ){
                    $result[] = $i;
                }
            }

            return $result;
        }

    }

    /**
     *	This class uses the HTTP_USER_AGENT varaible to get information about the browser the visitor used to perform
     *	the request. We determine the browser name, the version and the platform it's running on.
     *
     *  @ingroup YDFramework
     */
    class YDBrowserInfo extends YDBase {

        /**
         *	The class constructor analyzes for the YDBrowserInfo class. The constructor takes no arguments and uses the
         *	$_SERVER['HTTP_USER_AGENT'] variable to parse the browser info.
         */
        function YDBrowserInfo() {

            // Initialize YDBase
            $this->YDBase();

            // The matching list for browsers
            $browsers = array(
                'bot'       => array( 'bot', 'Yahoo! Slurp', 'crawler', 'scooter', 'mercator', 'altavista', 'Gulliver', 'spider', 'Ask Jeeves' ),
                'opera'     => array( 'opera' ),
                'ie'        => array( 'msie' ),
                'safari'    => array( 'safari' ),
                'konqueror' => array( 'Konqueror' ),
                'feed'      => array( 'feed', 'rss', 'synd', 'bloglines', 'newsgator' ),
                'mozilla'   => array( 'mozilla', 'firefox' ),
            );

            // The matching list for platforms
            $platforms = array(
                'win'       => array( 'win' ),
                'mac'       => array( 'mac', 'apple' ),
                'linux'     => array( 'linux', 'bsd' ),
                'unix'      => array( 'unix', 'sun', 'risc', 'aix' ),
                'bot'       => array( 'bot', 'Yahoo! Slurp', 'crawler' ),
                'feed'      => array( 'feed', 'rss', 'synd', 'bloglines', 'newsgator' ),
            );

            // Mark everything as unknown
            $this->agent = 'unknown';
            $this->browser = 'unknown';
            $this->platform = 'unknown';
            $this->dotnet = 'unknown';

            // Check if the user agent was specified
            if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {

                // Get the user agent
                $this->agent = $_SERVER['HTTP_USER_AGENT'];

                // Get the browser name
                foreach ( $browsers as $browser => $browserpatterns ) {
                    foreach ( $browserpatterns as $browserpattern ) {
                        if ( stristr( $this->agent, $browserpattern ) ) {
                            $this->browser = $browser;
                            continue;
                        }
                    }
                    if ( $this->browser != 'unknown' ) {
                        break;
                    }
                }

                // Get the browser name
                foreach ( $platforms as $platform => $platformpatterns ) {
                    foreach ( $platformpatterns as $platformpattern ) {
                        if ( stristr( $this->agent, $platformpattern ) ) {
                            $this->platform = $platform;
                            continue;
                        }
                    }
                    if ( $this->platform != 'unknown' ) {
                        break;
                    }
                }

                // Get the .NET runtime version
                preg_match_all( '/.NET CLR ([0-9][.][0-9])/i', $this->agent, $ver );
                $this->dotnet = $ver[1];

            }

        }

        /**
         *  Get the ip of the client computer.
         *
         *  @returns The ip of the client computer.
         *
         *  @static
         */
        function getIP() {
            return $_SERVER['REMOTE_ADDR'];
        }

        /**
         *  Get the agent of the client computer.
         *
         *  @returns The agent of the client computer.
         */
        function getAgent() {
            return $this->agent;
        }

        /**
         *  Get the browser of the client computer.
         *
         *  @returns The browser of the client computer.
         */
        function getBrowser() {
            return $this->browser;
        }

        /**
         *  Get the platform of the client computer.
         *
         *  @returns The platform of the client computer.
         */
        function getPlatform() {
            return $this->platform;
        }

        /**
         *  Get the dotnet of the client computer.
         *
         *  @returns The dotnet of the client computer.
         */
        function getDotnet() {
            return $this->dotnet;
        }

        /**
         *  Get the hostname of the client computer.
         *
         *  @returns The hostname of the client computer in lowercase.
         *
         *  @static
         */
        function getComputerName() {
            return strtolower( gethostbyaddr( YDBrowserInfo::getIP() ) );
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
            return array_map( 'strtolower', array_values( $browserLanguages ) );

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

    /**
     *  This class allows you to generate GUIDs (global unique identifier).
     *
     *  More info: http://www.ietf.org/rfc/rfc4122.txt
     *
     *  @ingroup YDFramework
     */
    class YDGuidUtil extends YDBase {

        /**
         *  Generate a new GUID.
         *
         *  @returns    A new GUID as a string.
         */
        function create() {
            return md5( uniqid( rand(), true ) . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_ADDR'] );
        }

        /**
         *  Generate a new formatted GUID.
         *
         *  @returns    A formatted GUID as a string.
         */
        function createFormatted() {
            return YDGuidUtil::format( YDGuidUtil::create() );
        }

        /**
         *  Formats a GUID.
         *
         *  @param $g    GUID to format.
         *
         *  @returns    A formatted GUID as a string.
         */
         function format( $g ) {
            $g = str_replace( '-', '', $g );
            return sprintf(
                '%s-%s-%s-%s-%s',
                substr( $g, 0, 8 ), substr( $g, 8, 4 ), substr( $g, 12, 4 ), substr( $g, 16, 4 ), substr( $g, 20 )
            );
         }

    }

    /**
     *  This class allows you to perform LDAP releated tasks.
     *
     *  @ingroup YDFramework
     */
    class YDLdapUtil extends YDBase {

        /**
         *  Authenticate against a domain.
         *
         *  @param  $server     The name of the domain controller.
         *  @param  $domain     The name of the domain.
         *  @param  $user       The username.
         *  @param  $password   The password.
         *
         *  @returns    A boolean indicating if the user was authenticated or not.
         */
        function authenticate( $server, $domain, $user, $password ) {

            // Connect to the LDAP server
            $conn = ldap_connect( $server );

            // Setup the options
            ldap_set_option( $conn, LDAP_OPT_PROTOCOL_VERSION, 3 );
            ldap_set_option( $conn, LDAP_OPT_REFERRALS, 0 );

            // Require a username
            if ( empty( $user ) || empty( $pass ) ) {
                return false;
            }

            // Fix the username
            $user = strtolower( trim( $user ) );
            if ( strpos( $user, '\\' ) ) {
                $user = substr( $user, strpos( $user, '\\' ) + 1 );
            }

            // Authenticate
            $result = @ldap_bind( $conn, $user . '@' . $server, $password );

            // Close the connection
            ldap_close( $conn );

            // Return the result
            return ( $result ) ? true : false;

        }

    }

?>
