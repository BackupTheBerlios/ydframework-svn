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
     *  @addtogroup YDLoadconfig Addons - Loadconfig
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }


    /**
     *  This class reads a database table to apply correspondent YDConfig
     *
     *  @ingroup YDLoadconfig
     */
    class YDLoadconfig extends YDAddOnModule {

        /**
         *  Class constructor for the YDLoadconfig class.
         *
         *  @param $dbinstance      Database instance name or object.
         */
        function YDLoadconfig( $dbinstance = 'default' ) {

            // Initializes YDBase
            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'Francisco Azevedo';
            $this->_version = '1.0b';
            $this->_copyright = '(c) 2006 Francisco Azevedo, francisco@fusemail.com';
            $this->_description = 'This class handles db configurations.';

			// init DB object
            $this->db = new YDDatabaseObject();

			// register database as default
            $this->db->registerDatabase( $dbinstance );

            // init table information
			$this->_tables = array();

			// init results
			$this->_results = array();
        }


        /**
         *  This function adds a table and correspondent name and value columns
         *
         *  @param $tablename       Table name
         *  @param $column_name     (Optional) Column name that have variable names. By default: 'name'.
         *  @param $column_value    (Optional) Column name that have variable values. By default: 'value'.
         *
         */
        function addTable( $tablename, $column_name = 'name', $column_value = 'value' ) {

            // if table don't exist add it to tables array
            if ( ! in_array( $tablename, $this->_tables ) ) $this->_tables[ $tablename ] = array( $column_name, $column_value );
        }


        /**
         *  This function loads configuration from db
         */
        function load() {

			// clean possible previous results
			$this->_results = array();

            // cycle all tables to get their column names
			foreach( $this->_tables as $tablename => $columns ){

				// copy default db object
	            $db = $this->db;

				// register table
				$db->registerTable( $tablename );

				// register fields
				$db->registerField( $columns[0] );
				$db->registerField( $columns[1] );

				// find everything in db
				$db->find();

				// add results
				$this->_results = array_merge( $this->_results, $db->getResultsAsAssocArray( $columns[0], $columns[1] ) );
			}
        }


        /**
         *  This function creates all YDF Configs
         */
        function apply() {

			// if we don't have results maybe we should load them
			if ( empty( $this->_results ) ) $this->load();

            // cycle all results to apply YDConfig
			foreach( $this->_results as $name => $value ){
				YDConfig::set( $name, $value );
			}
        }


    }

?>