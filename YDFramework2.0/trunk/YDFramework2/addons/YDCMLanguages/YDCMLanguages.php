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

	YDInclude( 'YDCMComponent.php' );

	// add local translation directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/' );


    class YDCMLanguages extends YDCMComponent {

        function YDCMLanguages() {
        
			// init component as a non standard component
            $this->YDCMComponent( 'YDCMLanguages', false );

            $this->_author = 'Francisco Azevedo';
            $this->_version = '0.1';
            $this->_copyright = '(c) Copyright 2006 Francisco Azevedo';
            $this->_description = 'This is a CM component for language management';

            // define custom key
            $this->registerKey( 'language_id', true );

			// Fields
			$this->registerField( 'name' );
			$this->registerField( 'active' );
			$this->registerField( 'visitors_default' );
			$this->registerField( 'admin_default' );

			// we don't have relations from here (just TO here, and we don't need to define that)
		}


        /**
         *  This function returns all active languages
         *
         *  @returns    Array of languages, eg: array( 'en' => 'English', 'pt' => ... )
         */
		function active(){
		
			// reset values
			$this->resetValues();

			// we want only results where active is 1
			$this->active = 1;

			// find all languages
			$this->find();

			// return results as array
			return $this->getResultsAsAssocArray( 'language_id', 'name' );
		}


        /**
         *  This function returns the default language for content creation
         *
         *  @returns    Language code, eg: 'en'. or false
         */
		function adminDefault(){
		
			// reset values
			$this->resetValues();

			// we want the result where admin default is 1
			$this->admin_default = 1;
			$this->limit( 1 );
			
			// find language
			if ( $this->find() == 1 ) return $this->language_id;
			
			return false;
		}


    }
?>