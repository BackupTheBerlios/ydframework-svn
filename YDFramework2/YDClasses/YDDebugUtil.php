<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

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
				echo( "\n" . '<!--' . "\n" . '[ YD_DEBUG ]' . "\n\n" );
				echo( implode( ' ', $args ) . "\n" );
				echo( '-->' . "\n" );
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
					$data = '<pre><b>' . $label . '</b><br>' . $data . '</pre>';
				} else {
					$data = '<pre>' . $data . '</pre>';
				}
			} else {
				$data = $label . "\n" . $data;
			}
			return $data;
		}

	}

?>
