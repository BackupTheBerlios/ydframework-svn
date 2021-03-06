<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

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
     *	found on http://ydframework.berlios.de/.
     *
     *	(c) Copyright 2002-2005 Pieter Claerhout
     *
     *	Webpage: http://ydframework.berlios.de/
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
    } elseif ( strtoupper( PHP_OS ) == 'DARWIN' ) {
        @define( 'YD_CRLF', "\r" );
        @define( 'YD_PATHDELIM', ':' );
    } else {
        @define( 'YD_CRLF', "\n" );
        @define( 'YD_PATHDELIM', ':' );
    }

    // Global framework constants
    @define( 'YD_FW_REVISION', 'unknown' );
    @define( 'YD_FW_NAME', 'Yellow Duck Framework' );
    @define( 'YD_FW_VERSION', '2.0 (build ' . YD_FW_REVISION . ')' );
    @define( 'YD_FW_NAMEVERS', YD_FW_NAME . ' ' . YD_FW_VERSION );
    @define( 'YD_FW_HOMEPAGE', 'http://ydframework.berlios.de/' );
    @define( 'YD_FW_COPYRIGHT', '(c) 2002-2005 Pieter Claerhout, pieter@yellowduck.be' );

    // Directory paths
    @define( 'YD_DIR_HOME', dirname( __FILE__ ) );
    @define( 'YD_DIR_HOME_CLS', YD_DIR_HOME . '/YDClasses' );
    @define( 'YD_DIR_HOME_ADD', YD_DIR_HOME . '/addons' );
    @define( 'YD_DIR_TEMP', YD_DIR_HOME . '/temp' );

    // File and URL constants
    @define( 'YD_SELF_SCRIPT', $_SERVER['PHP_SELF'] );
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
    $includePath .= YD_PATHDELIM . YD_DIR_HOME_CLS;
    $includePath .= YD_PATHDELIM . YD_DIR_HOME;
    $includePath .= YD_PATHDELIM . YD_DIR_HOME_CLS . '/YDFormElements';
    $includePath .= YD_PATHDELIM . YD_DIR_HOME_CLS . '/YDFormRenderers';
    $includePath .= YD_PATHDELIM . YD_DIR_HOME_CLS . '/YDDatabaseDrivers';
    if ( is_dir( YD_SELF_DIR . '/includes' ) ) {
        $includePath .= YD_PATHDELIM . YD_SELF_DIR . '/includes';
    }
    $includePath .= YD_PATHDELIM . YD_DIR_HOME . '/3rdparty';
    $includePath .= YD_PATHDELIM . YD_DIR_HOME . '/addons';
    if ( $handle = opendir( YD_DIR_HOME . '/addons' ) ) {
        while ( false !== ( $file = readdir( $handle ) ) ) {
           if ( substr( $file, 0, 1 ) != '.' && is_dir( YD_DIR_HOME . '/addons/' . $file ) ) {
                $includePath .= YD_PATHDELIM . YD_DIR_HOME . '/addons/' . $file;
           }
        }
        closedir( $handle );
    }
    if ( ini_get( 'include_path' ) != '' ) {
        $includePath .= YD_PATHDELIM . ini_get( 'include_path' );
    }
    $GLOBALS['YD_INCLUDE_PATH'] = explode( YD_PATHDELIM, $includePath );
    @ini_set( 'include_path', $includePath );

    // Include the standard functions
    include_once( YD_DIR_HOME . '/YDF2_core.php' );

    // Default the locale to English
    YDConfig::set( 'YD_LOCALE', 'en', false );

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
    include_once( YD_DIR_HOME . '/YDClasses/YDUtil.php' );

    // Start the global timer
    $timer = new YDTimer();

?>