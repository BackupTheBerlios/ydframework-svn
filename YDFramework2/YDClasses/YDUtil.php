<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

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
		 *	This function will return the number of seconds elapsed since the timer was instantiated.
		 *
		 *	@returns	The total elapsed time
		 */
		function getElapsed() {
			$endTime = $this->_getMicroTime();
			return intval( ( $endTime - $this->startTime ) * 1000 );
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
		 */
		function convertToTable( $array, $columns, $fillLastRow=false ) {

			// If the number of columns is 1, return the original array
			if ( $columns == 1 ) { return $array; }

			// Use the array_chunk function to convert to a table
			$newArray = @array_chunk( $array, $columns );
			if ( $newArray == null ) { YDFatalError( 'Failed to split the array in chunks.' ); }

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
		 */
		function convertToNested( $array, $key ) {

			// Start with the a new array
			$new = array();

			// Loop over the original array
			foreach ( $array as $item ) {
				if ( ! array_key_exists( $key, $item ) ) {
					YDFatalError( 'YDArrayUtil::convertToNested: key "' . $key . '" not found' );
				}
				if ( ! isset( $new[ $item[ $key ] ] ) ) { $new[ $item[ $key ] ] = array(); }
				array_push( $new[ $item[ $key ] ], $item );
			}

			// Return the new array
			return $new;

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
		 */
		function debug() {
			$args = func_get_args();
			if ( YD_DEBUG == 1 ) {
				echo( "\n" . '<!-- [ YD_DEBUG ] ' . implode( ' ', $args ) . ' -->' . "\n" );
			}
		}

		/**
		 *	Function to dump the contents of pretty much anything. This is the same as the var_dump function in PHP, but
		 *	has a nicer and more readable output.
		 *
		 *	@param $obj		Object you want to dump.
		 *	@param $label	The label for the dump.
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
				$data = $label . "\n" . $data;
			}
			return $data;
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
		 */
		function isSubClass( $obj, $class ) {
			$class = strtolower( $class );
			if ( function_exists( 'is_a' ) ) {
				return is_a( $obj, $class );
			} else {
				if ( is_object( $obj ) ) {
					if ( get_class( $obj ) == $class ) return true;
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
		 */
		function getAncestors( $classname ) {
			if ( is_object( $classname ) ) {
				$classname = get_class( $classname );
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
		 */
		function serialize( $obj ) {
			$obj = serialize( $obj );
			if ( ! $obj ) { YDFatalError( 'Failed serializing the object' ); }
			return $obj;
		}

		/**
		 *	This function will unserialize an object.
		 *
		 *	@param $obj	Object to unserialize.
		 */
		function unserialize( $obj ) {
			$obj = unserialize( $obj );
			if ( ! $obj ) { YDFatalError( 'Failed unserializing the object' ); }
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
		 */
		function formatFilesize( $bytes, $decimals=1 ) {

			$bytes = intval( $bytes );

			// The different units
			$units = array(
			  '1152921504606846976' => 'EB', '1125899906842624' => 'PB', '1099511627776' => 'TB', '1073741824' => 'GB',
			  '1048576' => 'MB', '1024' => 'KB'
			);

			// Smaller than 1 KB, so return bytes
			if ( $bytes <= 1024 ) { return number_format( $bytes / 1024, $decimals, '.', '' ) . ' KB'; }

			// Loop over the remaining possibilities
			foreach ( $units as $base => $title ) {
				if ( floor( $bytes / $base ) != 0 ) {
					return number_format( $bytes / $base, $decimals, '.', '' ) . ' ' . $title;
				}
			}

		}

		/**
		 *	This function will encode all characters which have an ordinal bigger than 128 to numeric HTML entities,
		 *	which can be safely included in e.g. XML output.
		 *
		 *	@param $string	The original string to encode.
		 *	@param $htmlent	Boolean indicating if the result should be HTML encoded or not.
		 *
		 *	@returns	String with all the characters with an ordinal bigger than 128 converted to numeric entities.
		 */
		function encodeString( $string, $htmlent=false ) {
			$encoded = '';
			for ( $i=0; $i < strlen( $string ); $i++ )  {
				if ( ord( substr( $string, $i, 1 ) ) > 128 ) {
					$encoded .= '&#' . ord( substr( $string, $i, 1 ) ) . ';';
				} elseif ( ord( substr( $string, $i, 1 ) ) == 0 ) {
					$encoded .= ' ';
				} else {
					$encoded .= substr( $string, $i, 1 );
				}
			}
			if ( $htmlent == true ) {
				$encoded = htmlentities( $encoded );
			}
			return $encoded;
		}

		/**
		 *	This function will truncate a string.
		 *
		 *	@param $string		String to truncate.
		 *	@param $length		(optional) The length to truncate to. Default length is 80 characters.
		 *	@param $etc			(optional) The string to append if the item gets trunctated. Default is '...'.
		 *	@param $break_words	(optional) Break in the middle of words or not. Default is false.
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

	}

?>
