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
	@define( 'YD_FW_REVISION', '$LastChangedRevision$' );
	@define( 'YD_FW_NAME', 'Yellow Duck Framework' );
	@define( 'YD_FW_VERSION', '2.0 (build ' . trim( substr( YD_FW_REVISION, 22, -2 ) ) . ')' );
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

	/**
	 *	This function will print a stack trace.
	 */
	if ( ! function_exists( 'YDStackTrace' ) ) {
		function YDStackTrace() {
			if ( YD_DEBUG == 1 ) {
				$err = '';
				$err .= 'URI: ' . YD_SELF_URI . "\n";
				$err .= 'Debug backtrace:' . "\n";
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
					$err .= "\n"; 
				}
				if ( ini_get( 'display_errors' ) == 1 ) {
					echo( '<pre>' . "\n" . htmlentities( $err ) . '</pre>' );
				}
				error_log( $err, 0 );
			}
		}
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
	
		// Function to include a file from the known include path
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
				'Failed to include the file: ' . $file . ' The file was not found in the include path.',
				YD_ERROR
			);
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
	 *
	 *	@todo
	 *		Add toArray function that gives the class properties as an array (but only string, numeric, array types, not
	 *		objects as this might screw up thing. Mainly for template purposes.
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
