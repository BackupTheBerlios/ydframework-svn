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

	// Start the session
	@session_start();

	// Get the path delimiter and newline
	if ( strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' ) {
		@define( 'YD_CRLF', "\r\n" );
		@define( 'YD_PATHDELIM', ';' );
		@define( 'YD_DIRDELIM', '\\' );
	} elseif ( strtoupper( PHP_OS ) == 'DARWIN' ) {
		@define( 'YD_CRLF', "\r" );
		@define( 'YD_PATHDELIM', ':' );
		@define( 'YD_DIRDELIM', '/' );
	} else {
		@define( 'YD_CRLF', "\n" );
		@define( 'YD_PATHDELIM', ':' );
		@define( 'YD_DIRDELIM', '/' );
	}

	// Include the version file
	@include( dirname( __FILE__ ) . YD_DIRDELIM . 'YDF2_version.php' );

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
	@define( 'YD_DIR_CLSS', YD_DIR_HOME . YD_DIRDELIM . 'YDClasses' );
	@define( 'YD_DIR_3RDP', YD_DIR_HOME . YD_DIRDELIM . '3rdparty' );
	if ( ! defined( 'YD_DIR_TEMP' ) ) {
		define( 'YD_DIR_TEMP', YD_DIR_HOME . YD_DIRDELIM . 'temp' );
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

	// Template constants
	define( 'YD_TPL_CACHEEXT', '.phc' );
	define( 'YD_TPL_CACHEPRE', YD_TMP_PRE . 'T_' );

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

	// Update the include path
	$includePath = YD_SELF_DIR;
	if ( is_dir( YD_SELF_DIR . '/includes' ) ) {
		$includePath .= YD_PATHDELIM . YD_SELF_DIR . '/includes';
	}
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS;
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS . '/YDFormElements';
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS . '/YDFormRenderers';
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS . '/YDDatabaseDrivers';
	$includePath .= YD_PATHDELIM . YD_DIR_3RDP;
	$includePath .= YD_PATHDELIM . dirname( __FILE__ );
	if ( ini_get( 'include_path' ) != '' ) {
		$includePath .= YD_PATHDELIM . ini_get( 'include_path' );
	}
	$GLOBALS['YD_INCLUDE_PATH'] = explode( YD_PATHDELIM, $includePath );
	@ini_set( 'include_path', $includePath );

	// Include the standard functions
	require_once( 'YDBase.php' );
	require_once( 'YDConfig.php' );
	require_once( 'YDF2_functions.php' );

	// Fix the PHP variables affected by magic_quotes_gpc (which is evil if you ask me ;-)
	if ( ! defined( 'YD_FIXED_MAGIC_QUOTES' ) ) {
		if ( get_magic_quotes_gpc() == 1 ) {
			$_GET = @YDRemoveMagicQuotes( $_GET );
			$_POST = @YDRemoveMagicQuotes( $_POST );
			$_COOKIE = @YDRemoveMagicQuotes( $_COOKIE );
			$_REQUEST = @YDRemoveMagicQuotes( $_REQUEST );
		}
		define( 'YD_FIXED_MAGIC_QUOTES', true );
	}

	// Check if we have the right PHP version
	if ( version_compare( phpversion(), '4.2.0' ) == -1 ) {
		trigger_error( 'PHP version 4.2.0 or greater is required.', YD_ERROR );
	}

	// Class executor
	YDConfig::set( 'YD_EXECUTOR', 'YDExecutor' );

	// Set the debugging level
	switch ( @ $_GET['YD_DEBUG'] ) {
		case 2:
			YDConfig::set( 'YD_DEBUG', 2, false );
			break;
		case 1:
			YDConfig::set( 'YD_DEBUG', 1, false );
			break;
		default:
			YDConfig::set( 'YD_DEBUG', 0, false );
			break;
	}

	// Include the base classes
	YDInclude( 'YDUtil.php' );

	// Start the global timer
	$timer = new YDTimer();

?>
