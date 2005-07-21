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

    // Includes
    YDInclude( 'YDEncryption.php' );

    // Constants
    YDConfig::set( 'YD_PERSISTENT_STORE_NAME', strtoupper( str_replace( ' ', '_', YD_FW_NAME ) ) . '_PERSISTENT_STORE', false );
    YDConfig::set( 'YD_PERSISTENT_DEFAULT_LIFETIME', 0, false );
    YDConfig::set( 'YD_PERSISTENT_DEFAULT_PASSWORD', null, false );
    YDConfig::set( 'YD_PERSISTENT_SCOPE', '/', false );

    /**
     *  This class is able to save and load persistent data. This data stay active between different requests and allows
     *  you to share data between different requests and different sessions.
     */
    class YDPersistent extends YDBase {

        /**
         *	This function initializes the persistent object store.
         *
         *	@internal
         */
        function _init() {
            if ( ! isset( $_COOKIE[ YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) ] ) ) {
                $_COOKIE[ YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) ] = array();
            }
        }

        /**
         *	This funtion add a new object to the persistent object store.
         *
         *	@param	$name		The name of the object.
         *	@param	$object		The object to store.
         *  @param  $passwd     (optional) If not null, the data will be encrypted with this password.
         *  @param  $expire     (optional) The lifetime of the object in seconds. Defaults to 0 (session only).
         *  @param  $override   (optional) If an existing value should be overridden or not. Default is true.
         */
        function set( $name, $object, $passwd=null, $expire=null, $override=true ) {

            // Initialize the store
            YDPersistent::_init();

            // Don't overwrite existing values
            if ( YDPersistent::exists( $name ) && $override === false ) {
                return;
            }

            // Set the expire time
            if ( is_null( $expire ) ) {
                $expire = YDConfig::get( 'YD_PERSISTENT_DEFAULT_LIFETIME', 0 );
            }

            // Convert the object to a base64 encoded serialized version
            $object_data = base64_encode( serialize( $object ) );

            // Encrypt the data if needed
            if ( is_null( $passwd ) && ! is_null( YDConfig::get( 'YD_PERSISTENT_DEFAULT_PASSWORD', null ) ) ) {
                $passwd = YDConfig::get( 'YD_PERSISTENT_DEFAULT_PASSWORD', null );
            }
            if ( ! is_null( $passwd ) ) {
                $object_data = YDEncryption::encrypt( $passwd, $object_data );
            }

            // Get the scope
            $scope = YDConfig::get( 'YD_PERSISTENT_SCOPE', '/' );

            // Save the object
            $_COOKIE[ YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) ][$name] = $object_data;
            setcookie( YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) . '[' . $name . ']', $object_data, $expire, $scope );

        }

        /**
         *	This function returns a variable from the configuration. If the configuration variable doesn't exist, it
         *	returns a fatal error.
         *
         *	@param	$name       The name of the configuration variable to retrieve.
         *  @param  $default    (optional) If not null, this value will be returned if the configuration setting doesn't
         *                      exist in the configuration.
         *  @param  $passwd     (optional) If not null, the data will be decrypted with this password.
         *
         *	@returns	The value of the configuration variable.
         */
        function get( $name, $default=null, $passwd=null ) {

            // Initialize the store
            YDPersistent::_init();

            // If the $_GET variable exists, return that one
            if ( isset( $_GET[$name] ) === true ) {
                YDPersistent::set( $name, $_GET[$name], $passwd );
            }

            // Check if the key exists
            if ( ! YDPersistent::exists( $name ) ) {

                // Check if we have a default, if not, raise an error
                if ( ! is_null( $default ) ) {
                    return $default;
                } else {
                    trigger_error( 'Persistent variable "' . $name . '" is not defined.', YD_ERROR );
                }

            }

            // Get the value
            $obj = $_COOKIE[ YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) ][ $name ];

            // Decrypt the data if needed
            if ( ! is_null( $passwd ) ) {
                $obj = YDEncryption::decrypt( $passwd, $obj );
            }

            // Now, we need to base64 decode and unserialize
            $obj = @unserialize( base64_decode( $obj ) );

            // Return the object
            return $obj;

        }

        /**
         *  This function removes a persistent variable.
         *
         *	@param	$name		The name of the object.
         */
        function delete( $name ) {

            // Initialize the store
            YDPersistent::_init();

            // Get the scope
            $scope = YDConfig::get( 'YD_PERSISTENT_SCOPE', '/' );

            // Remove it from the cookie variable
            unset( $_COOKIE[ YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) ][ $name ] );
            setcookie( YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) . '[' . $name . ']', "", time()-3600, $scope );

        }

        /**
         *	This function checks if the configuration value is set or not.
         *
         *	@returns	Boolean indicating if the configuration value is set or not.
         */
        function exists( $name ) {

            // Initialize the store
            YDPersistent::_init();

            // Return true or false
            return isset( $_COOKIE[ YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) ][ $name ] );

        }

        /**
         *	This function dumps the contents of the configuration.
         *
         *  @param  $obj    (optional) The object to dump. If not set, it will dump all objects.
         */
        function dump( $obj = null ) {

            // Initialize the store
            YDPersistent::_init();

            // Dump the configuration
            if ( ! is_null( $obj ) ) {
                YDDebugUtil::dump( $_COOKIE[ YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) ][ $obj ] );
            } else {
                YDDebugUtil::dump( $_COOKIE[ YDConfig::get( 'YD_PERSISTENT_STORE_NAME' ) ] );
            }

        }

    }

?>