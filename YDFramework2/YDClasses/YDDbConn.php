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
     *  This class implements a database connection. To instantiate this object,
     *  you need to pass it a database URL which has the following syntax
     *  (the syntax is the same as the PEAR::DB syntax):
     *
     *  The format of the supplied URL is in its fullest form:
     *  - phptype(dbsyntax)://username:password\@protocol+hostspec/database
     *    
     *  Most variations are allowed:
     *  - phptype://username:password\@protocol+hostspec:110//usr/db_file.db
     *  - phptype://username:password\@hostspec/database_name
     *  - phptype://username:password\@hostspec
     *  - phptype://username\@hostspec
     *  - phptype://hostspec/database
     *  - phptype://hostspec
     *  - phptype(dbsyntax)
     *  - phptype
     *   
     *  The currently supported database backends are: mysql  -> MySQL
     *  - pgsql  -> PostgreSQL
     *  - ibase  -> InterBase
     *  - msql   -> Mini SQL
     *  - mssql  -> Microsoft SQL Server
     *  - oci8   -> Oracle 7/8/8i
     *  - odbc   -> ODBC (Open Database Connectivity)
     *  - sybase -> SyBase
     *  - ifx    -> Informix
     *  - fbsql  -> FrontBase
     *
     *  @remarks
     *      Some of the database types require you to have special extensions
     *      loaded in the PHP engine. For database engines such as Oracle,
     *      Interbase and Informix, this is the case. Please refer to the full
     *      PHP documentation to find out which extensions are needed for which
     *      database types. More information can be found on http://www.php.net/
     */
    class YDDbConn {

        /**
         *  The class constructor for the YDDbConn class. When you instantiate
         *  this class, no connection is made yet. This has to be done manually
         *  using the "connect" function.
         *
         *  @param $url The database url for this connection (see above for the
         *              syntax of the database url).
         */
        function YDDbConn( $url ) {
    
            // Initialize the database URL
            $this->url = $url;

            // Start with no connection
            $this->conn = null;

        }

        /**
         *  The "connect" function establishes the connection with the database.
         *  
         *  By default, a non persistent database connection is established. Not
         *  all database drivers support persistent connections, so use this
         *  option with care. Refer to the PHP documentation on
         *  http://www.php.net/ to see if your database driver supports
         *  persistent connections or not.
         *
         *  This function returns true if the database connection was
         *  succesfully established. If the database connection fails, a YDError
         *  object is returned which can be checked with the YDIsError function.
         *
         *  If the connection was succesfull, it automatically sets the default
         *  fetch mode to DB_FETCHMODE_ASSOC, which returns the database results
         *  as an associative array with the column names as the array keys .
         *
         *  @param $persistent Indicates if a persistent database connection is
         *                     made or not. Not all database drivers support
         *                     this option so be careful in using this option.
         *
         *  @return Returns true if the database connection was successfully
         *          established. If something went wrong, a YDError object is
         *          returned.
         */
        function connect( $persistent=false ) {

            // Include the DB library
            require_once( YD_DIR_3RDP_PEAR . '/DB.php' );

            // Make the connection
            $this->conn = DB::connect( $this->url, $persistent );

            // Check for errors
            if ( DB::isError( $this->conn ) ) {
                
                // Get the error message
                $err = $this->conn->getMessage();

                // Reset the connection object
                $this->conn = null;

                // Return an error
                return new YDError( $err );

            }

            // Set the correct fetch mode
            $this->conn->setFetchMode( DB_FETCHMODE_ASSOC );

            // Keep counter
            if ( ! isset( $GLOBALS['YD_DB_CONN_CNT'] ) ) {
                $GLOBALS['YD_DB_CONN_CNT'] = 1;
            } else {
                $GLOBALS['YD_DB_CONN_CNT'] = $GLOBALS['YD_DB_CONN_CNT'] + 1;
            }

            // Return true
            return true;

        }

    }

?>
