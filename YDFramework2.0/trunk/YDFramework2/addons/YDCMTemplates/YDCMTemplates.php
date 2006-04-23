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

	// set admin template directory
	YDConfig::set( 'YDCMTEMPLATES_ADMIN_PATH', YD_SELF_DIR, false );

	// set visitors template directory
	YDConfig::set( 'YDCMTEMPLATES_VISITORS_PATH', YD_SELF_DIR, false );


	// this is a very special component because don't have any DB table (that's way do not extends YDCMComponent)
    class YDCMTemplates {

        function YDCMTemplates() {
        
            $this->_author = 'Francisco Azevedo';
            $this->_version = '0.1';
            $this->_copyright = '(c) Copyright 2006 Francisco Azevedo';
            $this->_description = 'This is a CM component for template management';
		}


        /**
         *  This function returns templates in administration side
         *
         *  @returns    Array of templates, eg: array( 'default' => 'Default', 'orange' => ... )
         */
		function forAdministrators(){
		
			return $this->__getFiles( YDConfig::get( 'YDCMTEMPLATES_ADMIN_PATH' ) );
		}


        /**
         *  This function returns templates in visitors side
         *
         *  @returns    Array of templates, eg: array( 'default' => 'Default', 'orange' => ... )
         */
		function forVisitors(){
		
			return $this->__getFiles( YDConfig::get( 'YDCMTEMPLATES_VISITORS_PATH' ) );
		}


        /**
         *  This private function returns all filenames of a directory
         *
         *  @returns    Array of filenames, eg: array( 'default' => 'Default', 'orange' => ... )
         */
		function __getFiles( $dir ){
		
			// init directories
			$directories = array();

			// init template directory
			$dir = new YDFSDirectory( $dir );
			
			// compute directories array
			foreach( $dir->getContents( '!.*', '', 'YDFSDirectory' ) as $d )
				$directories[ $d ] = $d;

			return $directories;
		}

    }
?>