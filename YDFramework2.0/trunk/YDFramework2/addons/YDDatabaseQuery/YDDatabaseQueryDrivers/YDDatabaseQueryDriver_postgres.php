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
    YDInclude( 'YDDatabaseQuery.php' );

    /**
     *  This class defines a YDSqlQueryDriver_postgres object.
     */
    class YDDatabaseQueryDriver_postgres extends YDDatabaseQueryDriver {

        /**
         *  The class constructor can be used to set the action and optional options.
         */
        function YDDatabaseQueryDriver_postgres( & $db ) {
            $this->YDDatabaseQueryDriver( $db );
            $this->reserved = '"';
        }
        
        /**
         *  This function will check if the server supports this database type.
         *
         *  @returns    Boolean indicating if the database type is supported by the server.
         */
        function isSupported() {
            return extension_loaded( 'pgsql' );
        }

        /**
         *  This function will escape a string so that it's safe to include it 
         *  in an SQL statement.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string.
         */
        function string( $string ) {
            $this->db->connect();
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    return pg_escape_string( $string );
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }
        
    }



?>