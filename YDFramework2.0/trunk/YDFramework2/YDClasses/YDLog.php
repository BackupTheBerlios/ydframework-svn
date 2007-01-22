<?php

    /*

        Yellow Duck Framework version 2.1
        (c) Copyright 2002-2007 Pieter Claerhout

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

    /**
     *  @addtogroup YDFramework Core
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDFileSystem.php');

    // The different log levels
    @define( 'YD_LOG_DEBUG', 4 );
    @define( 'YD_LOG_INFO', 3 );
    @define( 'YD_LOG_WARNING', 2 );
    @define( 'YD_LOG_ERROR', 1 );

    // Configure the default for this class
    YDConfig::set( 'YD_LOG_LEVEL', YD_LOG_INFO, false );
    YDConfig::set( 'YD_LOG_FILE', YDPath::join( YD_DIR_TEMP, 'YDFramework2_log.xml' ), false );
    YDConfig::set( 'YD_LOG_FORMAT', 'XML', false );
    YDConfig::set(
        'YD_LOG_TEXTFORMAT', "%date% | %level% | %uri% | %basefile%:%line% | %function% | %message%", false
    );
    YDConfig::set( 'YD_LOG_WRAPLINES', false, false );
    YDConfig::set( 'YD_LOG_MAX_LINESIZE', 100, false );

    /**
     *  This class defines the logging static functions.
     *
     *  @ingroup YDFramework
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
            if ( YD_LOG_DEBUG <= YDConfig::get( 'YD_LOG_LEVEL' ) ) {
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
            if ( YD_LOG_INFO <= YDConfig::get( 'YD_LOG_LEVEL' ) ) {
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
            if ( YD_LOG_WARNING <= YDConfig::get( 'YD_LOG_LEVEL' ) ) {
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
            if ( YD_LOG_ERROR <= YDConfig::get( 'YD_LOG_LEVEL' ) ) {
                @YDLog::_log( 'error', $text );
            }
        }

        /**
         *	This clears the contents of the logfile.
         *
         *	@static
         */
        function clear() {
            $f = fopen( YDConfig::get( 'YD_LOG_FILE' ), 'w' );
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
            $maxlinesize = ( is_numeric( YDConfig::get( 'YD_LOG_MAX_LINESIZE' ) ) ) ? intval( YDConfig::get( 'YD_LOG_MAX_LINESIZE' ) ) : 80;
            $wraplines = ( is_bool( YDConfig::get( 'YD_LOG_WRAPLINES' ) ) ) ? YDConfig::get( 'YD_LOG_WRAPLINES' ) : false;

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
            if ( strtoupper( YDConfig::get( 'YD_LOG_FORMAT' ) ) == 'TEXT' ) {

                // Get the template
                $msg = YDConfig::get( 'YD_LOG_TEXTFORMAT' );

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
                $f = fopen( YDConfig::get( 'YD_LOG_FILE' ), 'a' );
                fwrite( $f, $msg );
                fclose( $f );

            }

            // XML logfile
            if ( strtoupper( YDConfig::get( 'YD_LOG_FORMAT' ) ) == 'XML' ) {

                // Create the log entry
                $msg = '<entry>';
                $msg .= '<date>' . strftime( '%Y-%m-%d %H:%M:%S' ) . '</date>';
                $msg .= '<level>' . htmlentities( strtoupper( $level ) ) . '</level>';
                $msg .= '<file>' . htmlentities( $stack[1]['file'] ) . '</file>';
                $msg .= '<basefile>' . htmlentities( basename( $stack[1]['file'] ) ) . '</basefile>';
                $msg .= '<uri>' . htmlentities( YD_SELF_URI ) . '</uri>';
                $msg .= '<line>' . htmlentities( $stack[1]['line'] ) . '</line>';
                $msg .= '<function>' . htmlentities( $stack[2]['class'] . $stack[2]['type'] . $stack[2]['function'] ) . '</function>';
                $msg .= '<message>' . htmlentities( $text ) . '</message>';
                $msg .= '</entry>' . YD_CRLF;
                $msg .= '</log>';

                // Write to the file
                $f = fopen( YDConfig::get( 'YD_LOG_FILE' ), 'a' );
                fclose( $f );
                $f = fopen( YDConfig::get( 'YD_LOG_FILE' ), 'r+' );
                clearstatcache();
                if ( filesize( YDConfig::get( 'YD_LOG_FILE' ) ) == 0 ) {
                    fwrite( $f, '<?xml version=\'1.0\'?>' . YD_CRLF . '<log creator="' . htmlentities( YD_FW_NAMEVERS ) . '">' . YD_CRLF . '</log>' );
                }
                fseek( $f, -6, SEEK_END );
                fwrite( $f, $msg );
                fclose( $f );

            }

            // Use another function if not text or XML
            if ( ! in_array( strtoupper( YDConfig::get( 'YD_LOG_FORMAT' ) ), array( 'XML', 'TEXT' ) ) ) {

                // Get the values
                $values = array();
                $values['date']     = time(); //strftime( '%Y-%m-%d %H:%M:%S' );
                $values['level']    = strtoupper( $level );
                $values['file']     = $stack[1]['file'];
                $values['basefile'] = basename( $stack[1]['file'] );
                $values['uri']      =  YD_SELF_URI;
                $values['line']     = $stack[1]['line'];
                $values['function'] = $stack[2]['class'] . $stack[2]['type'] . $stack[2]['function'];
                $values['message']  = $text;

                // Log the values
                call_user_func( 'YDLogWrite_' . strtoupper( YDConfig::get( 'YD_LOG_FORMAT' ) ), $values );

            }

        }

    }

?>
