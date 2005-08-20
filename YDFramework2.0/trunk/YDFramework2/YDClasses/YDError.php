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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // The different error levels
    @define( 'YD_ERROR_FATAL',   3 );
    @define( 'YD_ERROR_WARNING', 2 );
    @define( 'YD_ERROR_NOTICE',  1 );
    @define( 'YD_ERROR_NONE',    0 );
    
    // The config variable name for storing the errors
    YDConfig::set( 'YD_ERROR_STORE_NAME',
        strtoupper( str_replace( ' ', '_', YD_FW_NAME ) ) . '_ERROR_STORE', false
    );
    
    // The error reporting level
    YDConfig::set( 'YD_ERROR_REPORTING', YD_ERROR_NONE, false );
    
    // The stored errors
    YDConfig::set( YDConfig::get( 'YD_ERROR_STORE_NAME' ), array(), false );

    // Global names for the errors
    YDConfig::set( 'YD_ERROR_LEVELS', array(
            YD_ERROR_NOTICE  => 'Notice',
            YD_ERROR_WARNING => 'Warning',
            YD_ERROR_FATAL   => 'Fatal'
        ), false
    );

    /**
     *  This class defines an YDError object.
     */
    class YDError extends YDBase {

        /**
         *  The class constructor.
         *  Should not be used directly.
         *
         *  @param  $level          The error level.
         *  @param  $message        The error message.
         *  @param  $file           The file where the error was triggered.
         *  @param  $line           The line of the file.
         *  @param  $function       The function where it happened.
         *  @param  $stacktrace     The complete stack trace for this error
         */
        function YDError( $level, $message, $file, $line, $function='', $stacktrace='' ) {
            
            $this->YDBase();
            
            $levels = YDConfig::get( 'YD_ERROR_LEVELS' );
            
            $this->level        = $level;
            $this->name         = $levels[ $level ];
            $this->message      = $message;
            $this->file         = $file;
            $this->line         = $line;
            $this->func         = $function;
            $this->stacktrace   = trim( $stacktrace );
            
        }
        
        /**
         *  This function checks if the $mixed parameter is an error object.
         *
         *  @param  $mixed  The variable to be checked.
         *
         *  @returns     A boolean indicating if $mixed is an error object.
         *
         *  @static
         */
        function isError( $mixed ) {
            if ( is_object( $mixed ) && is_a( $mixed, 'YDError' ) ) {
                return true;
            }
            return false;
        }
        
        /**
         *  This function sets the reporting level.
         *
         *  @param  $level  (Optional) The error level. Should be one of the
         *                  following constants: YD_ERROR_NONE, YD_ERROR_NOTICE,
         *                  YD_ERROR_WARNING or YD_ERROR_FATAL.
         *
         *  @static
         */
        function reporting( $level=YD_ERROR_NONE ) {
            YDConfig::set( 'YD_ERROR_REPORTING', $level, true );
        }

        /**
         *  This function creates a notice error.
         *
         *  @param  $message  The error message.
         *
         *  @returns     An YDError object.
         *
         *  @static
         */
        function notice( $message ) {
            return YDError::_error( YD_ERROR_NOTICE, $message );
        }

        /**
         *  This function creates a warning error.
         *
         *  @param  $message  The error message.
         *
         *  @returns     An YDError object.
         *
         *  @static
         */
        function warning( $message ) {
            return YDError::_error( YD_ERROR_WARNING, $message );
        }

        /**
         *  This function creates a fatal error.
         *
         *  @param  $message  The error message.
         *
         *  @returns     An YDError object.
         *
         *  @static
         */
        function fatal( $message ) {
            return YDError::_error( YD_ERROR_FATAL, $message );
        }

        /**
         *  This function clears all stored errors.
         *
         *  @static
         */
        function clear() {
            YDConfig::set( YDConfig::get( 'YD_ERROR_STORE_NAME' ), array(), true );
        }
        
        /**
         *  This function returns all stored errors.
         *
         *  @returns     All stored errors.
         *
         *  @static
         */
        function get() {
            return YDConfig::get( YDConfig::get( 'YD_ERROR_STORE_NAME' ) );
        }
        
        /**
         *  This function dumps the last stored error.
         *
         *  @param  $html  (optional) Dump an HTML error. Default: true.
         */
        function dump( $html=true ) {
            
            // Get the stored errors
            $errors = YDError::get();
            
            // If no errors, returns empty
            if ( ! sizeof( $errors ) ) {
                return '';
            }
            
            // Get last error
            $last = array_pop( $errors );
            
            // Dump last error
            echo( YDError::r_dump( $html, 1 ) );

            // If fatal error, stops the script execution
            if ( $last->level == YD_ERROR_FATAL ) {
                die();
            }
            
        }
        
        /**
         *  This function returns the dump of the last stored error.
         *
         *  @param  $html  (optional) Dump an HTML error. Default: true.
         *  @param  $back  (optional) The backtrace level.
         *
         *  @returns   The last error dump information.
         */
        function r_dump( $html=true, $back=0 ) {
            
            // Get the stored errors
            $errors = YDError::get();
            
            // If no errors, returns empty
            if ( ! sizeof( $errors ) ) {
                return '';
            }
            
            // Get last error
            $last = array_pop( $errors );
            
            // Get the file and line of the dump
            $stack = debug_backtrace();
            $file = $stack[ $back ]['file'];
            $line = $stack[ $back ]['line'];
            
            // Get the error levels
            $levels = YDConfig::get( 'YD_ERROR_LEVELS' );
            
            // Creates the message
            $msg = '';
            if ( $html ) $msg .= '<br /><b>';
            $msg .= strtoupper( $levels[ $last->level ] ) . ': ';
            if ( $html ) $msg .= '</b>';
            $msg .= $last->message . YD_CRLF;
            
            // File
            if ( $html ) $msg .= '<br />';
            if ( $html ) $msg .= '<b>';
            $msg .= 'File: ';
            if ( $html ) $msg .= '</b>';
            $msg .= $last->file . YD_CRLF;
            
            // File line
            if ( $html ) $msg .= '<br />';
            if ( $html ) $msg .= '<b>';
            $msg .= 'Line: ';
            if ( $html ) $msg .= '</b>';
            $msg .= $last->line . YD_CRLF;
            
            // Dump
            if ( $html ) $msg .= '<br />';
            if ( $html ) $msg .= '<b>';
            $msg .= 'Dump: ';
            if ( $html ) $msg .= '</b>';
            $msg .= $file . YD_CRLF;
            
            // Dump line
            if ( $html ) $msg .= '<br />';
            if ( $html ) $msg .= '<b>';
            $msg .= 'Line: ';
            if ( $html ) $msg .= '</b>';
            $msg .= $line . YD_CRLF;
            if ( $html ) $msg .= '<br />';
            
            // Stack trace
            if ( $html ) $msg .= '<b>';
            $msg .= 'Stack Trace: ';
            if ( $html ) $msg .= '</b>';
            $msg .= nl2br( str_replace( ' ', '&nbsp;', $last->stacktrace ) );
            if ( $html ) $msg .= '<br />';
            $msg .= YD_CRLF;
            
            return $msg;
        
        }

        /**
         *  This function creates an YDError object and store it.
         *
         *  @param  $level    The error level.
         *  @param  $message  The error message.
         *
         *  @returns     An YDError object.
         *  
         *  @internal
         *  @static
         */
        function _error( $level, $message ) {

            // Get the current stack
            $stack = debug_backtrace();
            
            // Get level names
            $levels = YDConfig::get( 'YD_ERROR_LEVELS' );
            
            // Get the complete stack trace
            $stacktrace = strtoupper( $levels[ $level ] ) . ': ' . $message . YD_CRLF;
            foreach( array_slice( $stack, 1 ) as $t ) {
                $stacktrace .= '    @ ';
                if ( isset( $t['file'] ) ) {
                    $stacktrace .= basename( $t['file'] ) . ':' . $t['line'];
                } else {
                    $stacktrace .= basename( YD_SELF_FILE );
                }
                $stacktrace .= ' -- ';
                if ( isset( $t['class'] ) ) {
                    $stacktrace .= $t['class'] . $t['type'];
                }
                $stacktrace .= $t['function'];
                if ( isset( $t['args'] ) && sizeof( $t['args'] ) > 0 ) {
                    $stacktrace .= '(...)';
                } else {
                    $stacktrace .= '()';
                }
                $stacktrace .= YD_CRLF;
            }

            // Create the error object
            $error = new YDError(
                $level, $message, $stack[1]['file'], $stack[1]['line'],
                $stack[2]['class'] . $stack[2]['type'] . $stack[2]['function'],
                $stacktrace
            );
            
            // Store the error object on the global errors array
            YDConfig::set(
                YDConfig::get( 'YD_ERROR_STORE_NAME' ),
                array_merge( YDConfig::get( YDConfig::get( 'YD_ERROR_STORE_NAME' ) ), array( $error ) )
            );
            
            // If reporting for this level is on, dump the error
            if ( $level <= YDConfig::get( 'YD_ERROR_REPORTING' ) ) {
                YDError::dump();
            }
            
            // Return the error object
            return $error;

        }

    }
    
?>