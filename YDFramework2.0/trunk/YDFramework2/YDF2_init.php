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

	/*! \mainpage Yellow Duck Framework
	 *
	 *	The Yellow Duck Framework is web application framework created by Pieter Claerhout. More information can be 
	 *	found on http://www.yellowduck.be/ydf2/.
	 *
	 *	(c) 2002-2004 Pieter Claerhout, pieter@yellowduck.be
	 *
	 *	Webpage: http://www.yellowduck.be/ydf2/
	 *
	 *	Author: Pieter Claerhout, pieter@yellowduck.be
	 */

	// Set the error reporting correctly.
	error_reporting( E_ALL );

	// Disable magic_quotes_runtime
	set_magic_quotes_runtime( 0 );
	
	// Include the version file
	@include( dirname( __FILE__ ) . '/YDF2_version.php' );

	// Global framework constants
	if ( ! defined( 'YD_FW_REVISION' ) ) {
		@define( 'YD_FW_REVISION', 'unknown' );
	}
	@define( 'YD_FW_NAME', 'Yellow Duck Framework' );
	@define( 'YD_FW_VERSION', '2.0 (build ' . YD_FW_REVISION . ')' );
	@define( 'YD_FW_NAMEVERS', YD_FW_NAME . ' ' . YD_FW_VERSION );
	@define( 'YD_FW_HOMEPAGE', 'http://www.yellowduck.be/ydf2/' );

	// Directory paths
	@define( 'YD_DIR_HOME', dirname( __FILE__ ) );
	@define( 'YD_DIR_CLSS', YD_DIR_HOME . '/YDClasses' );
	@define( 'YD_DIR_3RDP', YD_DIR_HOME . '/3rdparty' );
	if ( ! defined( 'YD_DIR_TEMP' ) ) {
		define( 'YD_DIR_TEMP', YD_DIR_HOME . '/temp' );
	}

	// Action paths
	@define( 'YD_ACTION_PARAM', 'do' );
	@define( 'YD_ACTION_DEFAULT', 'actionDefault' );

	// File and URL constants
	@define( 'YD_SELF_SCRIPT', $_SERVER['SCRIPT_NAME'] );
	if ( ! isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
		$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
	}
	@define( 'YD_SELF_FILE', $_SERVER['SCRIPT_FILENAME'] );
	@define( 'YD_SELF_DIR', dirname( YD_SELF_FILE ) );
	@define( 'YD_SELF_URI', $_SERVER['REQUEST_URI'] );

	// Extensions and prefixes
	@define( 'YD_TPL_EXT', '.tpl' );
	@define( 'YD_SCR_EXT', '.php' );
	@define( 'YD_TMP_PRE', 'YDF_' );

	// Class executor
	if ( ! defined( 'YD_EXECUTOR' ) ) {
		define( 'YD_EXECUTOR', 'YDExecutor' );
	}

	// HTTP constants
	if ( ! defined( 'YD_HTTP_USES_GZIP' ) ) {
		define( 'YD_HTTP_USES_GZIP', 1 );
	}
	if ( ! defined( 'YD_HTTP_CACHE_TIMEOUT' ) ) {
		define( 'YD_HTTP_CACHE_TIMEOUT', 3600 );
	}
	if ( ! defined( 'YD_HTTP_CACHE_USEHEAD' ) ) {
		define( 'YD_HTTP_CACHE_USEHEAD', 1 );
	}

	// Debug constants
	if ( ! defined( 'YD_DEBUG' ) ) {
		if ( @ $_GET['YD_DEBUG'] == 2 ) {
			define( 'YD_DEBUG', 2 );
		} elseif ( @ $_GET['YD_DEBUG'] == 1 ) {
			define( 'YD_DEBUG', 1 );
		} else {
			define( 'YD_DEBUG', 0 );
		}
	}

	// Get the path delimiter and newline
	if ( strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' ) {
		@define( 'YD_CRLF', '\r\n' );
		@define( 'YD_PATHDELIM', ';' );
	} elseif ( strtoupper( PHP_OS ) == 'DARWIN' ) {
		@define( 'YD_CRLF', '\r' );
		@define( 'YD_PATHDELIM', ':' );
	} else {
		@define( 'YD_CRLF', '\n' );
		@define( 'YD_PATHDELIM', ':' );
	}

	// Error constants
	@define( 'YD_ERROR', E_USER_ERROR );
	@define( 'YD_WARNING', E_USER_WARNING );
	@define( 'YD_NOTICE', E_USER_NOTICE );

	// For servers that do not send the request uri
	if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		if ( isset ( $_SERVER['QUERY_STRING'] ) ) {
			$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
		}
	}

	// Keep some global variables
	$GLOBALS['YD_SQL_QUERY'] = array();

	// Start the session
	@session_start();

	// Update the include path
	$includePath = YD_SELF_DIR;
	if ( is_dir( YD_SELF_DIR . '/includes' ) ) {
		$includePath .= YD_PATHDELIM . YD_SELF_DIR . '/includes';
	}
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS;
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS . '/YDFormElements';
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS . '/YDDatabaseDrivers';
	$includePath .= YD_PATHDELIM . YD_DIR_3RDP;
	$includePath .= YD_PATHDELIM . dirname( __FILE__ );
	if ( ini_get( 'include_path' ) != '' ) {
		$includePath .= YD_PATHDELIM . ini_get( 'include_path' );
	}
	$GLOBALS['YD_INCLUDE_PATH'] = explode( YD_PATHDELIM, $includePath );
	@ini_set( 'include_path', $includePath );

	/**
	 *	This function will include a file from the filesystem. It works similar to the require_once function but it
	 *	knows about the include path for the Yellow Duck Framework.
	 *
	 *	@param $file	File to be included.
	 */
	if ( ! function_exists( 'YDInclude' ) ) {
		function YDInclude( $file ) {
			foreach ( $GLOBALS['YD_INCLUDE_PATH'] as $include ) {
				if ( realpath( $include ) != false ) {
					if ( file_exists( realpath( $include ) . '/' . $file ) ) {
						require_once( realpath( $include ) . '/' . $file );
						return;
					}
				}
			}
			if ( is_file( $file ) ) {
				require_once( $file );
				return;
			}
			trigger_error(
				'Failed to include the file: ' . $file . ' The file was not found in the include path.', YD_ERROR
			);
		}
	}

	/**
	 *	This function will add a marker to the global timer.
	 *
	 *	@param $name	The name of the marker.
	 */
	if ( ! function_exists( 'YDGlobalTimerMarker' ) ) {
		function YDGlobalTimerMarker( $name ) {
			$GLOBALS['timer']->addMarker( $name );
		}
	}

	// Fix the PHP variables affected by magic_quotes_gpc (which is evil if you ask me ;-)
	if ( ! defined( 'YD_FIXED_MAGIC_QUOTES' ) ) {
		if ( get_magic_quotes_gpc() == 1 ) {
			$_GET = array_map( 'stripslashes', $_GET );
			$_POST = array_map( 'stripslashes', $_POST );
			$_COOKIE = array_map( 'stripslashes', $_COOKIE );
			$_REQUEST = array_map( 'stripslashes', $_REQUEST );
		}
		define( 'YD_FIXED_MAGIC_QUOTES', true );
	}

	// Check if we have the right PHP version
	if ( version_compare( phpversion(), '4.2.0' ) == -1 ) {
		trigger_error( 'PHP version 4.2.0 or greater is required.', YD_ERROR );
	}

	/**
	 *	This is the base class for all other YD classes.
	 */
	class YDBase {

		/**
		 *	Class constructor for the YDBase class.
		 */
		function YDBase() {
		}

		/**
		 *	Converts the properties of the object to an associative array.
		 *
		 *	@returns	Associative array with the object properties.
		 */
		function toArray() {
			return get_object_vars( $this );
		}

	}

	// Start the timer
	YDInclude( 'YDUtil.php' );
	$timer = new YDTimer();

?>
