<?php

    /*

       Yellow Duck Framework version 2.0
       (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

    */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    /**
     *  This class defines a raw SQL query. There a a number of standard
     *  functions this object has to get the data from this query given a 
     *  database URL to retrieve the data from.
     */
    class YDSqlRaw {

        /**
         *  The class constructor for the YDSqlRaw class. When you instantiate
         *  this class, no connection is made yet. This has to be done manually
         *  using the "connect" function.
         *
         *  @param $sql The SQL query associated with this object.
         */
        function YDSqlRaw( $sql ) {
    
            // Initialize the sql query
            $this->_sql = $sql;

        }
        
        /**
         *  This function will return the raw SQL statement for this query.
         *
         *  @returns The raw sql statement for this query.
         */
        function getSql() {
            return $this->_sql;
        }

        /**
         *  This function will execute the query and return the number of
         *  affected rows.
         *
         *  @param $url The database url for this query.
         *
         *  @returns The number of affected row by the SQL query.
         */
        function executeQuery( $url ) {

            // Instantiate the database object
            $db = new YDDatabase( $url );

            // Execute the query
            return $db->executeQuery( $this->getSql() );

        }

        /**
         *  This function will execute the query and return the result of
         *  the query.
         *
         *  @param $url The database url for this query.
         *
         *  @returns The result of the SQL query.
         */
        function executeSelect( $url ) {

            // Instantiate the database object
            $db = new YDDatabase( $url );

            // Execute the query
            return $db->executeSelect( $this->getSql() );

        }

        /**
         *  This function will execute the query and return the first row from 
         *  the result of the query.
         *
         *  @param $url The database url for this query.
         *
         *  @returns The first row of the result of the SQL query.
         */
        function executeSelectRow( $url ) {

            // Instantiate the database object
            $db = new YDDatabase( $url );

            // Execute the query
            return $db->executeSelectRow( $this->getSql() );

        }

    }

?>
