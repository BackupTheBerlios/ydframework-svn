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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    /**
     *	This function will include a file from the filesystem. It works similar to the require_once function but it
     *	knows about the include path for the Yellow Duck Framework.
     *
     *	@param $file	File to be included.
     */
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

    /**
     *	This function will add a marker to the global timer.
     *
     *	@param $name	The name of the marker.
     */
    function YDGlobalTimerMarker( $name ) {
        if ( ! isset( $GLOBALS['timer'] ) ) {
            YDInclude( 'YDUtil.php' );
            $GLOBALS['timer'] = new YDTimer();
        }
        $GLOBALS['timer']->addMarker( $name );
    }

    /**
     *	This function will fix magic quotes.
     *
     *	@param $value	The value to fix.
     *
     *	@returns	The fixed value.
     */
    function YDRemoveMagicQuotes( & $value ) {
        if ( get_magic_quotes_gpc() == 1 ) {
            if ( is_array( $value ) ) {
                $result = array();
                foreach ( $value as $key=>$val) {
                    if ( is_array( $val ) ) {
                        $result[ $key ] = YDRemoveMagicQuotes( $val );
                    } else {
                        $result[ $key ] = stripslashes( $val );
                    }
                }
                return $result;
            } else {
                return stripslashes( $value );
            }
        }
        return $value;
    }

?>
