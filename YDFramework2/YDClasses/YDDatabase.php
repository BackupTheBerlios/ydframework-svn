<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDBase.php' );
    require_once( 'YDError.php' );
    require_once( 'DB.php' );

    /**
     *  This class implements a database interface. To instantiate this object,
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
     *  - dbase  -> dBase
     *  - fbsql  -> FrontBase
     *  - ibase  -> InterBase
     *  - ifx    -> Informix
     *  - msql   -> Mini SQL
     *  - mssql  -> Microsoft SQL Server
     *  - mysql  -> MySQL (for MySQL <= 4.0)
     *  - mysqli -> MySQL (for MySQL >= 4.1) (since DB 1.6.3)
     *  - oci8   -> Oracle 7/8/9
     *  - odbc   -> ODBC (Open Database Connectivity)
     *  - pgsql  -> PostgreSQL
     *  - sqlite -> SQLite
     *  - sybase -> Sybase
     *
     *  @remarks
     *      Some of the database types require you to have special extensions
     *      loaded in the PHP engine. For database engines such as Oracle,
     *      Interbase and Informix, this is the case. Please refer to the full
     *      PHP documentation to find out which extensions are needed for which
     *      database types. More information can be found on http://www.php.net/
     *
     *  @todo
     *      Add support for paged database results.
     *
     *  @todo
     *      Add option to get the last insert ID.
     */
    class YDDatabase extends YDBase {

        /**
         *  The class constructor for the YDDbConn class. When you instantiate
         *  this class, no connection is made yet. This is done when you use
         *  one of the methods of the class.
         *
         *  @param $url The database url for this connection (see above for the
         *              syntax of the database url).
         */
        function YDDatabase( $url ) {

            // Initialize YDBase
            $this->YDBase();

            // Initialize the database URL
            $this->_url = $url;

        }

        /**
         *  This function will return the full URL to the database.
         *
         *  @returns The full URL to the database.
         */
        function getUrl() {
            return $this->_url;
        }

        /**
         *  The "getConnection" function establishes the connection with the
         *  database and returns the database connection object.
         *
         *  By default, a non persistent database connection is established. Not
         *  all database drivers support persistent connections, so use this
         *  option with care. Refer to the PHP documentation on
         *  http://www.php.net/ to see if your database driver supports
         *  persistent connections or not.
         *
         *  This function returns the connection if the database connection was
         *  succesfully established. If the database connection fails, a YDError
         *  object is returned which can be checked with the YDIsError function.
         *
         *  If the connection was succesfull, it automatically sets the default
         *  fetch mode to DB_FETCHMODE_ASSOC, which returns the database results
         *  as an associative array with the column names as the array keys .
         *
         *  @param $dieOnError This paramater (true by default) will stop the
         *                     execution if it fails to connect to the database.
         *  @param $persistent (optional) Indicates if a persistent database
         *                     connection is made or not. Not all database
         *                     drivers support this option so be careful in
         *                     using this option.
         *
         *  @return Returns a connection object if the database connection was
         *          successfully established. If something went wrong, a YDError
         *          object is returned.
         */
        function getConnection( $dieOnError=true, $persistent=false ) {

            // Make the connection
            $conn = DB::connect( $this->getUrl(), $persistent );

            // Check for errors
            if ( DB::isError( $conn ) ) {

                // Return an error
                if ( $dieOnError == true ) {
                    return new YDFatalError( $conn );
                } else {
                    return new YDError( $conn );
                }

            }

            // Set the correct fetch mode
            $conn->setFetchMode( DB_FETCHMODE_ASSOC );

            // Set the options
            $conn->setOption( 'portability', DB_PORTABILITY_LOWERCASE );

            // Return the connection
            return $conn;

        }

        /**
         *  This function will execute the query and return the number of
         *  affected rows.
         *
         *  @param $query  The SQL query or the statement to execute.
         *  @param $params Array, string or numeric data to be added to the
         *                 prepared statement. Quantity of items passed must
         *                 match quantity of placeholders in the prepared
         *                 statement: meaning 1 placeholder for non-array
         *                 parameters or 1 placeholder per array element.
         *
         *  @returns The number of affected rows by the SQL query.
         */
        function executeQuery( $query, $params=array() ) {

            // Get a connection
            $conn = $this->getConnection();

            // Execute the query
            $result = $conn->query( $sql, $params );

            // Update counter
            $this->_incrementSqlCounter();

            // Check for errors
            if ( DB::isError( $result ) ) {
                return new YDFatalError( $result );
            }

            // Get the results
            $result = $db->affectedRows();

            // Close the database connection
            $conn->disconnect();

            // Return the result
            return $result;

        }

        /**
         *  This function will execute the query and return the result of
         *  the query.
         *
         *  @param $query  The SQL query or the statement to execute.
         *  @param $params Array, string or numeric data to be added to the
         *                 prepared statement. Quantity of items passed must
         *                 match quantity of placeholders in the prepared
         *                 statement: meaning 1 placeholder for non-array
         *                 parameters or 1 placeholder per array element.
         *
         *  @returns The result of the SQL query.
         */
        function executeSelect( $query, $params=array() ) {

            // Get a connection
            $conn = $this->getConnection();

            // Execute the query
            $result = $conn->getAll( $query, $params );

            // Update counter
            $this->_incrementSqlCounter();

            // Check for errors
            if ( DB::isError( $result ) ) {
                return new YDFatalError( $result );
            }

            // Close the database connection
            $conn->disconnect();

            // Return the result
            return $result;

        }

        /**
         *  This function will execute the query and return the result of
         *  the query, but fetches only the the specificed count of rows. It is 
         *  an emulation of the MySQL LIMIT option.
         *
         *  @param $query  The SQL query or the statement to execute.
         *  @param $from   The row to start to fetch.
         *  @param $count  The numbers of rows to fetch.
         *  @param $params Array, string or numeric data to be added to the
         *                 prepared statement. Quantity of items passed must
         *                 match quantity of placeholders in the prepared
         *                 statement: meaning 1 placeholder for non-array
         *                 parameters or 1 placeholder per array element.
         *
         *  @returns The result of the SQL query.
         */
        function executeSelectLimit( $query, $from, $count, $params=array() ) {

            // Get a connection
            $conn = $this->getConnection();

            // Execute the query
            $result = $conn->limitQuery( $query, $from, $count, $params );

            // Update counter
            $this->_incrementSqlCounter();

            // Check for errors
            if ( DB::isError( $result ) ) {
                return new YDFatalError( $result );
            }

            // Close the database connection
            $conn->disconnect();

            // Return the result
            return $result;

        }

        /**
         *  This function will execute the query and return the first row from
         *  the result of the query.
         *
         *  @param $query The SQL query or the statement to execute..
         *  @param $params Array, string or numeric data to be added to the
         *                 prepared statement. Quantity of items passed must
         *                 match quantity of placeholders in the prepared
         *                 statement: meaning 1 placeholder for non-array
         *                 parameters or 1 placeholder per array element.
         *
         *  @returns The first row of the result of the SQL query.
         */
        function executeSelectRow( $query, $params=array() ) {

            // Get a connection
            $conn = $this->getConnection();

            // Execute the query
            $result = $conn->query( $query, $params );

            // Update counter
            $this->_incrementSqlCounter();

            // Check for errors
            if ( DB::isError( $result ) ) {
                return new YDFatalError( $result );
            }

            // Get the result
            $result = $result->fetchRow();

            // Close the database connection
            $conn->disconnect();

            // Return the result
            return $result;

        }

        /**
         *  This function will quote a variable so that it is suitable for
         *  inclusion in an SQL statement. The returned string is dependant on
         *  the database type.
         *
         *  @param $string The string to quote.
         *
         *  @returns The string properly quoted for the selected database.
         */
        function quote( $string ) {

            // Get a connection
            $conn = $this->getConnection();

            // Return the quoted string
            return $conn->quote( $string );

        }

        /**
         *  This function will add 1 to the counter of the number of SQL queries
         *  that were executed.
         *
         *  @internal
         */
        function _incrementSqlCounter() {

            // Update or create new counter
            if ( ! isset( $GLOBALS['YD_DB_SQLQ_CNT'] ) ) {
                $GLOBALS['YD_DB_SQLQ_CNT'] = 1;
            } else {
                $GLOBALS['YD_DB_SQLQ_CNT'] = $GLOBALS['YD_DB_SQLQ_CNT'] + 1;
            }

        }

    }

?>
