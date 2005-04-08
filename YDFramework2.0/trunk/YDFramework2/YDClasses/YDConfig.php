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

    // Constants
    define( 'YD_CONFIG_VAR', 'YDF2_GLOBAL_CONFIG' );

    /**
     *	This is the class that hold the global configuration.
     */
    class YDConfig extends YDBase {

        /**
         *	This function initializes the global configuration.
         *
         *	@internal
         */
        function _init() {
            if ( ! isset( $GLOBALS[ YD_CONFIG_VAR ] ) ) {
                $GLOBALS[ YD_CONFIG_VAR ] = array();
            }
        }

        /**
         *	This funtion add a new configuration variable to the configuration.
         *
         *	@param	$name		The name of the configuration variable.
         *	@param	$value		The value of the configuration variable.
         *	@param	$override	(optional) Override the current value or not. Default is true.
         */
        function set( $name, $value, $override=true ) {

            // Set the new variable
            if ( YDConfig::exists( $name ) ) {
                    if ( $override ) {
                    $GLOBALS[ YD_CONFIG_VAR ][ $name ] = $value;
                }
            } else {
                $GLOBALS[ YD_CONFIG_VAR ][ $name ] = $value;
            }

        }

        /**
         *	This function returns a variable from the configuration. If the configuration variable doesn't exist, it
         *	returns a fatal error.
         *
         *	@param	$name       The name of the configuration variable to retrieve.
         *  @param  $default    (optional) If not null, this value will be returned if the configuration setting doesn't
         *                      exist in the configuration.
         *
         *	@returns	The value of the configuration variable.
         */
        function get( $name, $default=null ) {

            // Check if the key exists
            if ( ! YDConfig::exists( $name ) ) {

                // Check if we have a default, if not, raise an error
                if ( ! is_null( $default ) ) {
                    return $default;
                } else {
                    trigger_error( 'Configuration variable "' . $name . '" is not defined.', YD_ERROR );
                }

            }

            // Return the value
            return $GLOBALS[ YD_CONFIG_VAR ][ $name ];

        }

        /**
         *	This function checks if the configuration value is set or not.
         *
         *	@returns	Boolean indicating if the configuration value is set or not.
         */
        function exists( $name ) {

            // Initialize the global configuration if needed
            YDConfig::_init();

            // Return true or false
            return isset( $GLOBALS[ YD_CONFIG_VAR ][ $name ] );

        }

        /**
         *	This function dumps the contents of the configuration.
         */
        function dump() {

            // Initialize the global configuration if needed
            YDConfig::_init();

            // Dump the configuration
            YDDebugUtil::dump( $GLOBALS[ YD_CONFIG_VAR ], 'YDConfig contents' );

        }

    }

?>
