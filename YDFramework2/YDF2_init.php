<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

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

	// Global framework constants
	@define( 'YD_FW_NAME', 'Yellow Duck Framework' );
	@define( 'YD_FW_VERSION', '2.0.0' );
	@define( 'YD_FW_NAMEVERS', YD_FW_NAME . ' ' . YD_FW_VERSION );
	@define( 'YD_FW_HOMEPAGE', 'http://www.yellowduck.be/ydf2/' );

	// Directory paths
	@define( 'YD_DIR_HOME', dirname( __FILE__ ) );
	@define( 'YD_DIR_CLSS', YD_DIR_HOME . '/YDClasses' );
	@define( 'YD_DIR_3RDP', YD_DIR_HOME . '/3rdparty' );
	if ( ! defined( 'YD_DIR_TEMP' ) ) { define( 'YD_DIR_TEMP', YD_DIR_HOME . '/temp' ); }

	// Action paths
	@define( 'YD_ACTION_PARAM', 'do' );
	@define( 'YD_ACTION_DEFAULT', 'actionDefault' );

	// File and URL constants
	@define( 'YD_SELF_SCRIPT', $_SERVER['SCRIPT_NAME'] );
	if ( ! isset( $_SERVER['SCRIPT_FILENAME'] ) ) { $_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED']; }
	@define( 'YD_SELF_FILE', $_SERVER['SCRIPT_FILENAME'] );
	@define( 'YD_SELF_DIR', dirname( YD_SELF_FILE ) );
	@define( 'YD_SELF_URI', $_SERVER['REQUEST_URI'] );

	// Extensions and prefixes
	@define( 'YD_TPL_EXT', '.tpl' );
	@define( 'YD_SCR_EXT', '.php' );
	@define( 'YD_TMP_PRE', 'YDF_' );

	// Class executor
	if ( ! defined( 'YD_EXECUTOR' ) ) { define( 'YD_EXECUTOR', 'YDExecutor' ); }

	// HTTP constants
	if ( ! defined( 'YD_HTTP_USES_GZIP' ) ) { define( 'YD_HTTP_USES_GZIP', 1 ); }
	if ( ! defined( 'YD_HTTP_CACHE_TIMEOUT' ) ) { define( 'YD_HTTP_CACHE_TIMEOUT', 3600 ); }
	if ( ! defined( 'YD_HTTP_CACHE_USEHEAD' ) ) { define( 'YD_HTTP_CACHE_USEHEAD', 1 ); }

	// Debug constants
	if ( ! defined( 'YD_DEBUG' ) ) {
		if ( @ $_GET['YD_DEBUG'] == 1 ) {
			define( 'YD_DEBUG', 1 );
		} else {
			define( 'YD_DEBUG', 0 );
		}
	}

	// Get the path delimiter
	if ( strtoupper( PHP_OS ) == 'WINNT' || strtoupper( PHP_OS ) == 'WINDOWS' ) {
		@define( 'YD_PATHDELIM', ';' );
	} else {
		@define( 'YD_PATHDELIM', ':' );
	}

	// Start the session
	@session_start();

	/**
	 *	This function will print a stack trace.
	 */
	if ( ! function_exists( 'YDStackTrace' ) ) {
		function YDStackTrace() {
			if ( YD_DEBUG == 1 ) {
				echo( "\r\n" . '<pre>' . "\r\n" );
				echo( 'Debug backtrace:' . "\r\n" );
				foreach( debug_backtrace() as $t ) {
					echo( '    @ ' ); 
					if ( isset( $t['file'] ) ) {
						echo( basename( $t['file'] ) . ':' . $t['line'] ); 
					} else {
						echo( '<PHP inner-code>' ); 
					} 
					echo( ' -- ' ); 
					if ( isset( $t['class'] ) ) { echo( $t['class'] . $t['type'] ); }
					echo( $t['function'] );
					if ( isset( $t['args'] ) && sizeof( $t['args'] ) > 0 ) {
						echo( '(...)' );
					} else {
						echo( '()' );
					}
					echo( "\r\n" ); 
				} 
				echo( '</pre>' );
			}
		}
	}

	/**
	 *	This function defines a fatal error.
	 *
	 *	@param $error Error message.
	 */
	if ( ! function_exists( 'YDFatalError' ) ) {
		function YDFatalError( $error ) { YDStackTrace(); trigger_error( $error, E_USER_ERROR ); }
	}

	/**
	 *	This function defines a warning.
	 *
	 *	@param $error Error message.
	 */
	if ( ! function_exists( 'YDWarning' ) ) {
		function YDWarning( $error ) { trigger_error( $error, E_USER_WARNING ); }
	}

	/**
	 *	This function defines a notice.
	 *
	 *	@param $error Error message.
	 */
	if ( ! function_exists( 'YDNotice' ) ) {
		function YDNotice( $error ) { trigger_error( $error, E_USER_NOTICE ); }
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

	// Include the basis of Yellow Duck framework
	require_once( 'YDBase.php' );

	// Check if we have the right PHP version
	if ( version_compare( phpversion(), '4.2.0' ) == -1 ) {
		YDFatalError( 'PHP version 4.2.0 or greater is required.' );
	}

	// Start the timer
	require_once( 'YDTimer.php' );
	$timer = new YDTimer();

?>
