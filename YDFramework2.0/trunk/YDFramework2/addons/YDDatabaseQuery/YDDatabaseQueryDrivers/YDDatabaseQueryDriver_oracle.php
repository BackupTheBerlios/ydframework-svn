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
     *  @addtogroup YDDatabaseQuery Addons - DatabaseQuery
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME_ADD . '/YDDatabaseQuery/YDDatabaseQuery.php' );

    /**
     *  This class defines a YDSqlQueryDriver_oracle object.
     *
     *  @ingroup YDDatabaseQuery
     */
    class YDDatabaseQueryDriver_oracle extends YDDatabaseQueryDriver {

        /**
         *  The class constructor can be used to set the action and optional options.
         */
        function YDDatabaseQueryDriver_oracle( $db=null ) {
            
            $this->YDDatabaseQueryDriver( $db );
            $this->reserved = '"';
            
        }
        
        /**
         *  This function will check if the server supports this database type.
         *
         *  @returns    Boolean indicating if the database type is supported by the server.
         */
        function isSupported() {
            return extension_loaded( 'oci8' );
        }

        /**
         *  This function will escape a string so that it's safe to include it 
         *  in an SQL statement and will surround it with the quotes appropriate 
         *  for the database backend.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string surrounded by quotes.
         */
        function escapeSql( $string ) {
            if ( is_null( $string ) || strtolower( $string ) == 'null' ) {
                return 'null';
            }
            if ( strtolower( substr( $string, 0, 7 ) ) == 'to_date' ) {
                return $string;
            }
            return $this->quote . $this->escape( $string ) . $this->quote;
        }
        
        /**
         *  This functions builds and returns the select query defined by the object.
         *
         *  @returns  The query.
         */
        function getSelectQuery() {
            
            if ( $this->limit < 0 && $this->offset < 0 ) {
                return 'SELECT ' . $this->getOptions() . $this->getSelect()
                                 . $this->getFrom() . $this->getWhere() . $this->getGroup()
                                 . $this->getHaving() . $this->getOrder();
            }
            
            $offset = $this->offset;
            $limit  = $this->limit;
            
            // if no offset but there's a limit return everything before 1+OFFSET+LIMIT 
            // if offset is defined test limit
            if ( $offset < 0 ) {
                $end = 1 + $offset + $limit;
                $sql_append = 'rn <= ' . $end . ';';
            } else {

               // offset is the line
               $offset++;
          
               // if no limit return everything bigget than OFFSET
               // if limit is defined return everything between OFFSET and OFFSET+LIMIT
               if ( $limit < 0 ) {
                   $sql_append = 'rn => ' . $offset . ';';
               } else {
                   $end = $offset + $limit;
                   $sql_append = 'rn between ' . $offset . ' and ' . $end . ';';
               }
            }

            $sql = 'SELECT ' . $this->getOptions() . ' /*+FIRST_ROWS*/ rownum as rn, '
                             . $this->getSelect() . $this->getFrom() . $this->getWhere()
                             . $this->getGroup() . $this->getHaving() . $this->getOrder();
          
            // Return the changed SQL statement 
            return 'SELECT * FROM (' . $sql . ') WHERE ' . $sql_append;
            
        }
        
    }



?>