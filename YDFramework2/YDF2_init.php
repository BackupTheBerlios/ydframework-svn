<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	/*! \mainpage Yellow Duck Framework
	 *
	 *	The Yellow Duck framework is web application framework created by Pieter Claerhout. More information can be 
	 *	found on http://www.yellowduck.be/.
	 *
	 *	(c) 2002-2004 Pieter Claerhout, pieter@yellowduck.be
	 *
	 *	Webpage: http://www.yellowduck.be/
	 *
	 *	Author: Pieter Claerhout, pieter@yellowduck.be
	 */

	// Set the error reporting correctly.
	error_reporting( E_ALL ^ E_NOTICE );

	// Start the session
	session_start();

	// Global framework constants
	define( 'YD_FW_NAME', 'Yellow Duck Framework' );
	define( 'YD_FW_VERSION', '2.0.0' );
	define( 'YD_FW_NAMEVERS', YD_FW_NAME . ' ' . YD_FW_VERSION );
	define( 'YD_FW_HOMEPAGE', 'http://www.yellowduck.be/ydf2/' );

	// Directory paths
	define( 'YD_DIR_HOME', dirname( __FILE__ ) );
	define( 'YD_DIR_CLSS', YD_DIR_HOME . '/YDClasses' );
	define( 'YD_DIR_3RDP', YD_DIR_HOME . '/3rdparty' );
	if ( ! defined( 'YD_DIR_TEMP' ) ) { define( 'YD_DIR_TEMP', YD_DIR_HOME . '/temp' ); }

	// Action paths
	define( 'YD_ACTION_PARAM', 'do' );
	define( 'YD_ACTION_DEFAULT', 'actionDefault' );

	// File and URL constants
	define( 'YD_SELF_SCRIPT', $_SERVER['SCRIPT_NAME'] );
	if ( ! isset( $_SERVER['SCRIPT_FILENAME'] ) ) { $_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED']; }
	define( 'YD_SELF_FILE', $_SERVER['SCRIPT_FILENAME'] );
	define( 'YD_SELF_DIR', dirname( YD_SELF_FILE ) );
	define( 'YD_SELF_URI', $_SERVER['REQUEST_URI'] );

	// Extensions and prefixes
	define( 'YD_TPL_EXT', '.tpl' );
	define( 'YD_SCR_EXT', '.php' );
	define( 'YD_TMP_PRE', 'YDF_' );

	// Classes and handlers
	if ( ! defined( 'YD_EXECUTOR' ) ) { define( 'YD_EXECUTOR', 'YDExecutor' ); }
	if ( ! defined( 'YD_ERR_HANDLER' ) ) { define( 'YD_ERR_HANDLER', 'YDErrorHandler' ); }

	// HTTP constants
	if ( ! defined( 'YD_HTTP_USES_GZIP' ) ) { define( 'YD_HTTP_USES_GZIP', 1 ); }
	if ( ! defined( 'YD_HTTP_CACHE_TIMEOUT' ) ) { define( 'YD_HTTP_CACHE_TIMEOUT', 3600 ); }
	if ( ! defined( 'YD_HTTP_CACHE_USEHEAD' ) ) { define( 'YD_HTTP_CACHE_USEHEAD', 1 ); }

	// Debug constants
	if ( ! defined( 'YD_DEBUG' ) ) {
		if ( $_GET['YD_DEBUG'] == 1 ) {
			define( 'YD_DEBUG', 1 );
		} else {
			define( 'YD_DEBUG', 0 );
		}
	}

	// Get the path delimiter
	if ( strtoupper( PHP_OS ) == 'WINNT' || strtoupper( PHP_OS ) == 'WINDOWS' ) {
		define( 'YD_PATHDELIM', ';' );
	} else {
		define( 'YD_PATHDELIM', ':' );
	}

	// Update the include path
	$includePath = YD_SELF_DIR;
	if ( is_dir( YD_SELF_DIR . '/includes' ) ) {
		$includePath .= YD_PATHDELIM . YD_SELF_DIR . '/includes';
	}
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS;
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS . '/YDFormElements';
	$includePath .= YD_PATHDELIM . YD_DIR_CLSS . '/YDDatabaseDrivers';
	$includePath .= YD_PATHDELIM . YD_DIR_3RDP;
	$includePath .= YD_PATHDELIM . YD_DIR_3RDP . '/PEAR';
	ini_set( 'include_path', $includePath );

	// Include the basis of Yellow Duck framework
	require_once( 'YDBase.php' );
	require_once( 'YDError.php' );

	// Register the error handler
	set_error_handler( YD_ERR_HANDLER );

	// Check if we have the right PHP version
	if ( version_compare( phpversion(), '4.2.0' ) == -1 ) {
		YDFatalError( 'PHP version 4.2.0 or greater is required.' );
	}

	// Check if running in debugging mode
	if ( YD_DEBUG == 1 ) {
		require_once( 'YDTimer.php' );
		$timer = new YDTimer();
	}

?>
