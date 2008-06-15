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
     *  @addtogroup YDDatabase Core - Database
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDDatabase.php');

    /**
     *	This class defines a database driver for PDO.
     *
     *  @ingroup YDDatabase
     */
    class YDDatabaseQueryDriver_pdo extends YDDatabaseQueryDriver {

        /**
         *	This is the class constructor for the YDDatabaseQueryDriver_pdo class.
         *
         *	@param $db		DB object.
         */
        function YDDatabaseQueryDriver_pdo( $db ) {
            $this->YDDatabaseQueryDriver( $db );
            $this->_driver = 'pdo';
        }

        /**
         *	This function will check if the server supports this database type.
         *
         *	@returns	Boolean indicating if the database type is supported by the server.
         */
        function isSupported() {

			// pdo extension not available
			if( ! class_exists( 'PDO' ) )
				return false;

			$drivers = PDO::getAvailableDrivers();

			// pdo_sqlite extension not available
			if( ! in_array( 'sqlite', $drivers ) )
				return false;

			return true;
        }

        /**
         *	This function will return the version of the database server software.
         *
         *	@returns	The version of the database server software.
         */
        function getServerVersion() {

            // Connect
            $result = $this->connect();

            // Handle errors
            if ( ! $result && $this->_failOnError === true ) {
                trigger_error( $GLOBALS['YD_PDO_error'], YD_ERROR );
            }

            // Return the version
			return $this->_conn->getAttribute(PDO::ATTR_SERVER_VERSION);
        }

        /**
         *	Function that makes the actual connection.
         */
        function connect() {

            if ( $this->_conn == null ) {

				try{
					$this->_conn = new PDO( $this->db->_db );

				}catch( PDOException $exception ){

					$this->_conn = null;
					return false;
				}

            }
            return true;
        }

        /**
         *	This function will return a single record.
         *
         *	@param $sql	The SQL statement to use.
         *
         *	@returns	A single record matching the SQL statement.
         */
        function getRecord( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            $type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? PDO::FETCH_NUM : PDO::FETCH_ASSOC;
            return $this->_lowerKeyNames( $result->fetch( $type ) );
        }

        /**
         *	This function will execute the SQL statement and return the records as an associative array. Optionally, you
         *	can limit the number of records that are returned. Optionally, you can also specify which record to start
         *	from.
         *
         *	@param $sql	The SQL statement to use.
         *	@param $limit	(optional) How many records to return
         *	@param $offset	(optional) Where to start from
         *
         *	@returns	The records matching the SQL statement as an associative array.
         */
        function getRecords( $sql, $limit=-1, $offset=-1 ) {

		    $sql = $this->_prepareSqlForLimit( $sql, $limit, $offset );
            $result = & $this->_connectAndExec( $sql );

			$type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? PDO::FETCH_NUM : PDO::FETCH_ASSOC;
    		return $this->_lowerKeyNames( $result->fetchAll( $type ) );
        }

        /**
         *	This function will execute the SQL statement and return the number of affected records.
         *
         *	@param $sql	The SQL statement to use.
         *
         *	@returns	The number of affected rows.
         */
        function executeSql( $sql ) {

			$res = $this->_conn->exec( $sql );

			return $res;
        }

        /**
         *	This function will return the number of rows matched by the SQL query.
         *
         *	@param $sql	The SQL statement to use.
         *
         *	@returns	The number of rows matched by the SQL query.
         */
        function getMatchedRowsNum( $sql ) {
			return $this->_conn->exec( $sql );
        }

        /**
         *	This function will close the database connection.
         */
        function close() {
            if ( $this->_conn != null ) {
                $this->_conn = null;

            }
        }


        /**
         *	This function will connect to the database, execute a query and will return the result handle.
         *
         *	@param $sql	The SQL statement to execute.
         *
         *	@returns	Handle to the result of the query.
         *
         *	@internal
         */
        function & _connectAndExec( $sql ) {

            // Add the table prefix
            $sql = str_replace( ' #_', ' ' . YDConfig::get( 'YD_DB_TABLEPREFIX', '' ), $sql );

            // Update the language placeholders
            $languageIndex = YDConfig::get( 'YD_DB_LANGUAGE_INDEX', null );
            if ( ! is_null( $languageIndex ) ) {
                $sql = str_replace( '_@', '_' . $languageIndex, $sql );
            }

            // Connect
            $result = $this->connect();

            // Record the start time
            $timer = new YDTimer();

            // Perform the query
			$result = $this->_conn->queryExec( $sql );

            // Handle errors
            if ( $result === false && $this->_failOnError === true ) {
                echo( '<b>Stacktrace:</b> <pre>' . YDDebugUtil::getStackTrace() . '</pre>' );
                echo( '<b>SQL Statement:</b> <pre>' . $this->formatSql( $sql ) . '</pre>' );
                $msg = $this->_conn->errorInfo();
				trigger_error( '<b>SQLite error message: </b>' . $msg[2], YD_ERROR );
            }

            // Log the statement
            $this->_logSql( $sql, $timer->getElapsed() );

            // Return the result
            return $result;

        }

        /**
         *	This function will preprare an SQL SELECT statement for the getRecordsLimit and getRecordsPaged functions.
         *
         *	@param $sql	The SQL statement to prepare
         *	@param $limit	(optional) How many records to return
         *	@param $offset	(optional) Where to start from
         *
         *	@internal
         */
        function _prepareSqlForLimit( $sql, $limit=-1, $offset=-1 ) {

            // If no limit and offset, return the original SQL statement
            if ( $limit == -1 && $offset == -1 ) {
                return $sql;
            }

            // Check if there is an offset
            $offset = ( $offset >= 0 ) ? ' OFFSET ' . strval( $offset ) : '';

            // Return the changed SQL statement
            return $sql . ' LIMIT ' . strval( $limit ) . $offset;

        }

    }

?>