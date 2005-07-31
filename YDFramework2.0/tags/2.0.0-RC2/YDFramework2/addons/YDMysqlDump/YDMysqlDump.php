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

    /**
     *  This class defines a mySql backup/restore system object.
     */
    class YDMysqlDump extends YDAddOnModule {

        /**
         *  Class constructor for the YDMysqlDump class.
         *
         *  @param $dbinstance      Database instance
         */
        function YDMysqlDump( $dbinstance ) {

            // Initializes YDBase
            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'Francisco Azevedo';
            $this->_version = '1.0';
            $this->_copyright = '(c) 2005 Francisco Azevedo, francisco@fusemail.com';
            $this->_description = 'This class defines a mysql backup/restore system.';

            // database instance
            $this->dbinstance = $dbinstance;
            
            // predefined filepath for store content (used if we don't want a string)
            $this->filepath = YD_DIR_TEMP . '/backup.sql';
            
        }

        /**
         *  This function returns the drop statement for a table
         *
         *  @param $table       Table name
         *  @param $separator   Separator to use in the end of the statement
         *
         *  @returns            The drop statement for a table
         */
        function _getTableDrop( $table, $separator ) {
        
            // return drop statement
            return "DROP TABLE IF EXISTS `" . $table . "`" . $separator;
        
        }
      
        /**
         *  This function returns an array of tables
         *
         *  @returns  An array with the list of tables
         */
        function _getTables() {
            
            // initialize array of tables
            $tables = array();
            
            // foreach table push name to array of tables
            foreach ( $this->dbinstance->getRecords( "SHOW TABLES" ) as $key => $element ) {
                foreach( $element as $key => $table ) {
                    array_push( $tables, $table );
                }
            }
         
            // return array of tables
            return $tables;
         }

        /**
         *  This function returns the create statement for a table
         *
         *  @param $table       Table name
         *  @param $separator   Separator to use in the end of the statement
         *
         *  @returns            The create statement for a table
         */
        function _getTableStructure( $table, $separator ) {
        
            // get creation statement of a specific table
            $record = $this->dbinstance->getRecord( "SHOW CREATE TABLE `". $table . "`" );
            
            // return only the statement
            return $record['create table'] . $separator;
        
        } 
      
        /**
         *  This function returns all insert statements as a string for each row of a table
         *
         *  @param $table       Table name
         *  @param $separator   Separator to use in the end of each insert statement
         *
         *  @returns            The insert statements for each row of the table
         */
        function _getTableData( $table, $separator ) {
   
            // variable to store all insert information
            $content = '';
            
            // return recordset of data
            $rows = $this->dbinstance->getRecords( "SELECT * FROM `" . $table . "`" );
            
            // cycle rows to create insert statement
            foreach ( $rows as $row ) {
            
                // temporary array to store insert
                $insert = array();
            
                // test type of element
                foreach( $row as $element ) {
                
                    if ( is_numeric( $element ) ) {
                        
                        // if element is numeric just add it
                        array_push( $insert, $element );
                        
                    } else if ( !$element ) {
                        
                        // if element is empty insert string NULL
                        array_push( $insert, "NULL" );
                        
                    } else {
                        
                        // if is a string, escape
                        array_push( $insert, "'". mysql_escape_string($element) ."'" );
                        
                    }
                }
            
                // add string statement
                $content .= "INSERT INTO `" . $table . "` VALUES (" . implode( ",", $insert ) . ")" . $separator;
            }
            
            return $content;
        
        }

        /**
         *  This function defines the filepath for the YDFile
         *
         *  @param $filepath    File path
         */
        function setFilePath( $filepath ) {
            $this->filepath = $filepath;
        }
        
        /**
         *  This function returns the filepath for the YDFile
         *
         *  @returns  The file path
         */
        function getFilePath() {
            return $this->filepath;
        }
      
        /**
         *  This function returns the string or the file with the database dump
         *
         *  @param $file        Returns a YDFile or a string
         *  @param $separator   Separator to use in the end of each insert statement
         *
         *  @returns            A YDFile object or a string with the contents
         */ 
        function backup( $file=false, $separator=";" ) {

            // add two new lines to separator
            $separator .= "\n\n";

            // initialize string to record all queries
            $content = '';
            
            // get tables
            $tables = $this->_getTables();
            
            // cycle tables to retrieve structure and data
            foreach( $tables as $table ) {
            
                // drop table
                $content .= $this->_getTableDrop( $table, $separator );
                
                // get table structure information
                $content .= $this->_getTableStructure( $table, $separator );
                
                // get table data
                $content .= $this->_getTableData( $table, $separator );
            }
            
            // return the content if we don't want to download
            if ( $file == false ) {
                return $content;
            }
            
            // include filesystem functions
            include_once( dirname( __FILE__ ) . '/../../YDClasses/YDFileSystem.php' );

            // create file object
            $file = new YDFSFile( $this->filepath, true );
            
            // put content into file
            $file->setContents( $content );
            
            // return file
            return $file;
        } 
      
        /**
         *  This function restores a mysql structure
         *
         *  @param $content     Sql content to use
         *  @param $separator   Separator to parse queries
         */   
        function restore( $content, $separator=";" ) {
   
            // get array of queries
            $content = explode( $separator, $content );
                         
            // delete last element (is always null)
            array_pop( $content );
            
            // begin transaction
            //$this->dbinstance->executeSql("BEGIN");
            
            // execute each sql element of array
            foreach ( $content as $sql_query ) {
                $this->dbinstance->executeSql( trim( $sql_query ) );
            }
            
            // end transaction
            //$this->dbinstance->executeSql("COMMIT");
            
        }
    

    }

?>