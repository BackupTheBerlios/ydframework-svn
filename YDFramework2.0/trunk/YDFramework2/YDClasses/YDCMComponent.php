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

    YDInclude( 'YDDatabaseObject.php' );
    YDInclude( 'YDCMTree.php' );

    class YDCMComponent extends YDDatabaseObject {

        function YDCMComponent( $name, $standardComponent = true ) {

			// init DB object
            $this->YDDatabaseObject();

            $this->_author = 'unknown author';
            $this->_version = 'unknown version';
            $this->_copyright = 'no copyright';
            $this->_description = 'no description';

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( $name );

			// set standard component
			$this->standardComponent = $standardComponent;

			// if this is a standard component has primary key 'component_id' and 2 standard relations
			if ( $standardComponent ){

				// register standard Key
				$this->registerKey( 'component_id', true );

				// register tree field and relation
				$this->registerField( 'content_id' );
	            $relTree = & $this->registerRelation( 'YDCMTree', false, 'YDCMTree' );
				$relTree->setLocalKey( 'content_id' );
            	$relTree->setForeignKey( 'content_id' );

				// register language field and relation
				$this->registerField( 'language_id' );
         		$relLanguage = & $this->registerRelation( 'YDCMLanguages', false, 'YDCMLanguages' );
				$relLanguage->setLocalKey( 'language_id' );
				$relLanguage->setForeignKey( 'language_id' );

				// a standard component has tree operations
				$this->tree = new YDCMTree();
			}

		}


        /**
         *  This function returns the component author
         *
         *  @returns    component author
         */
		function getAuthor(){
			return $this->_author;
		}


        /**
         *  This function returns the component version
         *
         *  @returns    component version
         */
		function getVersion(){
			return $this->_version;
		}


        /**
         *  This function returns the component copyright
         *
         *  @returns    component copyright
         */
		function getCopyright(){
			return $this->_copyright;
		}


        /**
         *  This function returns the component description
         *
         *  @returns    component description
         */
		function getDescription(){
			return $this->_description;
		}


		// this function will fix the current YDDatabaseObject::registerRelation
		// TODO: delete it when YDDatabaseObject::registerRelation support seach inside YDF addons directory
		function registerRelation( $a, $b, $c ){
		
			YDInclude( $a . '.php' );
			YDInclude( $c . '.php' );			
			
			return parent::registerRelation( $a, $b, $c );
		}
		

        /**
         *  This function changes component state
         *
         *  @param $id  The node id
         *
         *  @returns    true if state changed, false otherwise
         *
         */
		function toogleState( $id ){
		
			return $this->tree->toogleState( $id );
		}


        /**
         *  This function adds a standard node in the tree. Experimental
         *
         *  @param $values  Node values
         */
		function add( $values ){

			// reset values
			$this->reset();
			
			// set values
			$this->setValues( $values );

			// TODO: check if language_id (another admin could delete or deactivate it)
			// TODO: check if content_id exist (not required because it should be locked anyway)

			// insert values
			return $this->insert();
		}

    }
?>