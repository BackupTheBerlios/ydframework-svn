<?php

	/*
	
		Yellow Duck Framework version 2.0
		Copyright (C) (c) copyright 2004 Pieter Claerhout
	
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

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	YDInclude( 'YDFileSystem.php' );

	// The different log levels
	@define( 'YD_LOG_DEBUG', 4 );
	@define( 'YD_LOG_INFO', 3 );
	@define( 'YD_LOG_WARNING', 2 );
	@define( 'YD_LOG_ERROR', 1 );

	// Define the log level
	if ( ! defined( 'YD_LOG_LEVEL' ) ) {
		define( 'YD_LOG_LEVEL', YD_LOG_INFO );
	}

	// Define the log file
	if ( ! defined( 'YD_LOG_FILE' ) ) {
		define( 'YD_LOG_FILE', YDPath::join( YD_DIR_TEMP, 'YDFramework2_log.xml' ) );
	}

	// Define the log format (TEXT/XML)
	if ( ! defined( 'YD_LOG_FORMAT' ) ) {
		define( 'YD_LOG_FORMAT', 'XML' );
	}

	// Define the format
	if ( ! defined( 'YD_LOG_TEXTFORMAT' ) ) {
		define( 
			'YD_LOG_TEXTFORMAT',
			"%date% | %level% | %uri% | %basefile%:%line% | %function% | %message%"
		);
	}

	// Define if long lines need to be wrapped or not
	if ( ! defined( 'YD_LOG_WRAPLINES' ) ) {
		define( 'YD_LOG_WRAPLINES', false );
	}

	// Define the maximum line width for a log entry
	if ( ! defined( 'YD_LOG_MAX_LINESIZE' ) ) {
		define( 'YD_LOG_MAX_LINESIZE', 100 );
	}

	/**
	 *  This class defines the logging static functions.
	 */
	class YDLog extends YDBase {

		/**
		 *	This adds a debug message to the logfile.
		 *
		 *	@param $text	The message to add to the logfile.
		 *
		 *	@static
		 */
		function debug( $text ) {
			if ( YD_LOG_DEBUG <= YD_LOG_LEVEL ) {
				@YDLog::_log( 'debug', $text );
			}
		}

		/**
		 *	This adds an informational message to the logfile.
		 *
		 *	@param $text	The message to add to the logfile.
		 *
		 *	@static
		 */
		function info( $text ) {
			if ( YD_LOG_INFO <= YD_LOG_LEVEL ) {
				@YDLog::_log( 'info', $text );
			}
		}

		/**
		 *	This adds a warning message to the logfile.
		 *
		 *	@param $text	The message to add to the logfile.
		 *
		 *	@static
		 */
		function warning( $text ) {
			if ( YD_LOG_WARNING <= YD_LOG_LEVEL ) {
				@YDLog::_log( 'warning', $text );
			}
		}

		/**
		 *	This adds an error message to the logfile.
		 *
		 *	@param $text	The message to add to the logfile.
		 *
		 *	@static
		 */
		function error( $text ) {
			if ( YD_LOG_ERROR <= YD_LOG_LEVEL ) {
				@YDLog::_log( 'error', $text );
			}
		}

		/**
		 *	This clears the contents of the logfile.
		 *
		 *	@static
		 */
		function clear() {

			// Clear the contents of the logfle
			$f = fopen( YD_LOG_FILE, 'w' );
			fclose( $f );

		}

		/**
		 *	This function will log the specified text to the logfile, indicating the correct level.
		 *
		 *	@param $level	The level to show in the logfile
		 *	@param $text	The text to show in the logfile
		 *
		 *	@internal
		 */
		function _log( $level, $text ) {

			// Get the maximum linesize
			$maxlinesize = ( is_numeric( YD_LOG_MAX_LINESIZE ) ) ? intval( YD_LOG_MAX_LINESIZE ) : 80;
			$wraplines = ( is_bool( YD_LOG_WRAPLINES ) ) ? YD_LOG_WRAPLINES : false;

			// Split the text up in parts if longer than the maximum linesize
			if ( strlen( $text ) > $maxlinesize && $wraplines ) {

				// The break character we are going to use
				$break = '__YD_LOG_BREAK__';

				// Wrap the text
				$text = YD_CRLF . "\t" . wordwrap( $text, $maxlinesize, YD_CRLF . "\t" );

			}

			// Get the current stack
			$stack = debug_backtrace();

			// Plain text logfile
			if ( strtoupper( YD_LOG_FORMAT ) == 'TEXT' ) {

				// Get the template
				$msg = YD_LOG_TEXTFORMAT;

				// Fill in the variables
				$msg = str_replace( '%date%', strftime( '%Y-%m-%d %H:%M:%S' ), $msg );
				$msg = str_replace( '%level%', strtoupper( $level ), $msg );
				$msg = str_replace( '%file%', $stack[1]['file'], $msg );
				$msg = str_replace( '%basefile%', basename( $stack[1]['file'] ), $msg );
				$msg = str_replace( '%uri%', YD_SELF_URI, $msg );
				$msg = str_replace( '%line%', $stack[1]['line'], $msg );
				$msg = str_replace( '%function%', $stack[2]['class'] . $stack[2]['type'] . $stack[2]['function'], $msg );
				$msg = str_replace( '%message%', $text, $msg );
				$msg = $msg . YD_CRLF;

				// Write to the file
				$f = fopen( YD_LOG_FILE, 'a' );
				fwrite( $f, $msg );
				fclose( $f );

			}

			// XML logfile
			if ( strtoupper( YD_LOG_FORMAT ) == 'XML' ) {

				// Create the log entry
				$msg = '<entry>';
				$msg .= '<date>' . strftime( '%Y-%m-%d %H:%M:%S' ) . '</date>';
				$msg .= '<level>' . htmlentities( strtoupper( $level ) ) . '</level>';
				$msg .= '<file>' . htmlentities( $stack[1]['file'] ) . '</file>';
				$msg .= '<basefile>' . htmlentities( basename( $stack[1]['file'] ) ) . '</basefile>';
				$msg .= '<uri>' . htmlentities( YD_SELF_URI ) . '</uri>';
				$msg .= '<line>' . htmlentities( $stack[1]['line'] ) . '</line>';
				$msg .= '<function>' . htmlentities( $stack[2]['class'] . $stack[2]['type'] . $stack[2]['function'] ) . '</function>';
				$msg .= '<message>' . htmlentities( trim( $text ) ) . '</message>';
				$msg .= '</entry>';
				$msg .= '</log>';

				// Write to the file
				$f = fopen( YD_LOG_FILE, 'a' );
				fclose( $f );
				$f = fopen( YD_LOG_FILE, 'r+' );
				clearstatcache();
				if ( filesize( YD_LOG_FILE ) == 0 ) {
					fwrite( $f, '<?xml version=\'1.0\'?>' . YD_CRLF . '<log creator="' . htmlentities( YD_FW_NAMEVERS ) . '"></log>' );
				}
				fseek( $f, -6, SEEK_END );
				fwrite( $f, $msg );
				fclose( $f );

			}

		}

	}

?>
