<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

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

	}

?>
