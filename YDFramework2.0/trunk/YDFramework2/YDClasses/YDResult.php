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

    // The different error levels
    @define( 'YD_RESULT_FATAL',   40 );
    @define( 'YD_RESULT_WARNING', 30 );
    @define( 'YD_RESULT_NOTICE',  20 );
    @define( 'YD_RESULT_OK',      10 );
    @define( 'YD_RESULT_NONE',    0 );
    
    // The config variable name for storing the errors
    YDConfig::set( 'YD_RESULT_STORE_NAME',
        strtoupper( str_replace( ' ', '_', YD_FW_NAME ) ) . '_ERROR_STORE', false
    );
    
    // The error reporting level
    YDConfig::set( 'YD_RESULT_REPORTING', YD_RESULT_NONE, false );
    
    // The error catch number
    YDConfig::set( 'YD_RESULT_CATCH', array(), true );
    YDConfig::set( 'YD_RESULT_CATCH_TEMP', array(), true );
    
    // The stored errors
    YDConfig::set( YDConfig::get( 'YD_RESULT_STORE_NAME' ), array(), true );

    // Global names for the errors
    YDConfig::set( 'YD_RESULT_LEVELS', array(
            YD_RESULT_NOTICE  => 'Notice',
            YD_RESULT_WARNING => 'Warning',
            YD_RESULT_FATAL   => 'Fatal',
            YD_RESULT_OK      => 'Ok'
        ), false
    );

    /**
     *  This class defines an YDResult object.
     *
     *  @ingroup YDFramework
     */
    class YDResult extends YDBase {

        /**
         *  The class constructor.
         *  Should not be used directly.
         *
         *  @param  $level          The error level.
         *  @param  $message        The error message.
         *  @param  $file           The file where the error was triggered.
         *  @param  $line           The line of the file.
         *  @param  $function       (optional) The function where it happened.
         *  @param  $stacktrace     (optional) The complete stack trace for this error
         *  @param  $custom         (optional) A custom value
         */
        function YDResult( $level, $message, $file, $line, $function='', $stacktrace='', $custom=null ) {
            
            $this->YDBase();
            
            $levels = YDConfig::get( 'YD_RESULT_LEVELS' );
            
            $this->level        = $level;
            $this->name         = $levels[ $level ];
            $this->message      = $message;
            $this->file         = $file;
            $this->line         = $line;
            $this->func         = $function;
            $this->custom       = $custom;
            $this->run_file     = null;
            $this->run_line     = null;
            $this->md5          = md5( $level . $message . $file . $line . $function . $stacktrace . mt_rand() );
            $this->stacktrace   = $stacktrace;
            $this->ok           = ( $level === YD_RESULT_OK );
            $this->warning      = ( $level === YD_RESULT_WARNING );
            $this->notice       = ( $level === YD_RESULT_NOTICE );
            $this->fatal        = ( $level === YD_RESULT_FATAL );
            $this->value        = $custom;
        }
        

        /**
         *  This function catch the last stored error.
         *
         *  @param  $error  (optional) Variable or array of variables to check if they are errors. If null, all triggered errors.
         *  @param  $level  (optional) All errors bellow this $level will be catched. Default: YD_RESULT_FATAL.
         *
         *  @returns     A YDResult object or false.
         *
         *  @static
         */
        function catchError( $error=null, $level=YD_RESULT_FATAL ) {
            
            $catch = YDConfig::get( 'YD_RESULT_CATCH' );
            $catch_temp = YDConfig::get( 'YD_RESULT_CATCH_TEMP' );
            
            if ( is_null( $error ) ) {
                $c = & $catch;
                $errors = YDResult::getAll();
            } else { 
                $c = & $catch_temp;
                if ( is_array( $error ) ) {
                    $errors = $error;
                } else {
                    $errors = array( $error );
                }
            }
            
            foreach ( $errors as $e ) {
                
                if ( is_object( $e ) && is_a( $e, 'YDResult' ) && $e->level <= $level && ! in_array( $e->md5, $c ) ) {
                    
                    if ( ! is_null( $error ) ) {
                        YDConfig::set( 'YD_RESULT_CATCH_TEMP', array_merge( $catch_temp, array( $e->md5 ) ) );
                    } 
                    YDConfig::set( 'YD_RESULT_CATCH', array_merge( $catch, array( $e->md5 ) ) );
                    
                    $stack = debug_backtrace();
                    $e->run_file = $stack[ 0 ]['file'];
                    $e->run_line = $stack[ 0 ]['line'];
                    
                    return $e;
                }
            }
            if ( ! is_null( $error ) ) {
                YDConfig::set( 'YD_RESULT_CATCH_TEMP', array(), true );
            }
            return false;
        }
        
        /**
         *  This function sets the reporting level.
         *
         *  @param  $level  (Optional) The error level. Should be one of the
         *                  following constants: YD_RESULT_NONE, YD_RESULT_NOTICE,
         *                  YD_RESULT_WARNING or YD_RESULT_FATAL.
         *
         *  @static
         */
        function reporting( $level=YD_RESULT_NONE ) {
            YDConfig::set( 'YD_RESULT_REPORTING', $level, true );
        }

        /**
         *  This method creates a OK type
         *
         *  @param  $message  The message.
         *  @param  $value    (optional) A custom value
         *
         *  @returns     An YDCMResult object.
         *
         *  @static
         */
        function ok( $message, $value = null ) {
            return YDResult::_result( YD_RESULT_OK, $message, $value );
        }


        /**
         *  This function creates a notice error.
         *
         *  @param  $message  The error message.
         *  @param  $custom   (optional) A custom value
         *
         *  @returns     An YDResult object.
         *
         *  @static
         */
        function notice( $message, $custom=null ) {
            return YDResult::_result( YD_RESULT_NOTICE, $message, $custom );
        }

        /**
         *  This function creates a warning error.
         *
         *  @param  $message  The error message.
         *  @param  $custom   (optional) A custom value
         *
         *  @returns     An YDResult object.
         *
         *  @static
         */
        function warning( $message, $custom=null ) {
            return YDResult::_result( YD_RESULT_WARNING, $message, $custom );
        }

        /**
         *  This function creates a fatal error.
         *
         *  @param  $message  The error message.
         *  @param  $custom   (optional) A custom value
         *
         *  @returns     An YDResult object.
         *
         *  @static
         */
        function fatal( $message, $custom=null ) {
            return YDResult::_result( YD_RESULT_FATAL, $message, $custom );
        }

        /**
         *  This function clears all stored errors.
         *
         *  @static
         */
        function clear() {
            YDConfig::set( YDConfig::get( 'YD_RESULT_STORE_NAME' ), array(), true );
            YDConfig::set( 'YD_RESULT_CATCH', array(), true );
            YDConfig::set( 'YD_RESULT_CATCH_TEMP', array(), true );
        }
        
        /**
         *  This function returns all stored errors.
         *
         *  @returns     All stored errors.
         *
         *  @static
         */
        function getAll() {
            return YDConfig::get( YDConfig::get( 'YD_RESULT_STORE_NAME' ) );
        }
        
        /**
         *  This function dumps the error.
         *
         *  @param  $html  (optional) Dump an HTML error. Default: true.
         */
        function dump( $html=true ) {
            echo $this->_dump( $html );
        }
        
        /**
         *  This function returns the dump string of the error.
         *
         *  @param  $html  (optional) Dump an HTML error. Default: true.
         *
         *  @returns  The dump information.
         */
        function r_dump( $html=true ) {
            return $this->_dump( $html );
        }
        
        /**
         *  This function returns the dump information of the error.
         *
         *  @param  $html  (optional) Dump an HTML error. Default: true.
         *  @param  $back  (optional) The backtrace level.
         *
         *  @returns   The dump information.
         *
         *  @internal
         */
        function _dump( $html=true, $back=1 ) {
        
            // Get the file and line of the dump
            $stack = debug_backtrace();
            $file = $stack[ $back ]['file'];
            $line = $stack[ $back ]['line'];
            
            $this->run_file = $this->run_file ? $this->run_file : $file;
            $this->run_line = $this->run_line ? $this->run_line : $line;
            
            // Get the error levels
            $levels = YDConfig::get( 'YD_RESULT_LEVELS' );
            
            // Creates the message
            $msg = '';
            if ( $html ) $msg .= '<br /><b>';
            $msg .= strtoupper( $levels[ $this->level ] ) . ': ';
            if ( $html ) $msg .= '</b>';
            $msg .= $this->message . YD_CRLF;
            
            // File
            if ( $html ) $msg .= '<br />';
            if ( $html ) $msg .= '<b>';
            $msg .= 'File: ';
            if ( $html ) $msg .= '</b>';
            $msg .= $this->file . YD_CRLF;
            
            // File line
            if ( $html ) $msg .= '<br />';
            if ( $html ) $msg .= '<b>';
            $msg .= 'Line: ';
            if ( $html ) $msg .= '</b>';
            $msg .= $this->line . YD_CRLF;
            
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
            if ( $html ) {
                $msg .= nl2br( str_replace( ' ', '&nbsp;', $this->stacktrace ) );
            } else {
                $msg .= $this->stacktrace;
            }
            if ( $html ) $msg .= '<br />';
            $msg .= YD_CRLF;
            
            return $msg;
        
        }
        
        /**
         *  This function creates an YDResult object and store it.
         *
         *  @param  $level    The error level.
         *  @param  $message  The error message.
         *  @param  $custom   (optional)
         *
         *  @returns     An YDResult object.
         *  
         *  @internal
         *  @static
         */
        function _result( $level, $message, $custom=null ) {

            // Get the current stack
            $stack = debug_backtrace();
            
            // Get level names
            $levels = YDConfig::get( 'YD_RESULT_LEVELS' );
            
            // Get the complete stack trace
            $stacktrace = YD_CRLF;
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
            $error = new YDResult(
                $level, $message, $stack[1]['file'], $stack[1]['line'],
                $stack[2]['class'] . $stack[2]['type'] . $stack[2]['function'],
                $stacktrace, $custom
            );
            
            // Store the error object on the global errors array
            YDConfig::set(
                YDConfig::get( 'YD_RESULT_STORE_NAME' ),
                array_merge( YDResult::getAll(), array( $error ) )
            );
            
            // If reporting for this level is on, dump the error
            if ( $level <= YDConfig::get( 'YD_RESULT_REPORTING' ) ) {
                echo $error->_dump( true, 2 );
                if ( $level == YD_RESULT_FATAL ) {
                    die();
                }
            }
            
            // Return the error object
            return $error;

        }

    }
    
?>