<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
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

?>
