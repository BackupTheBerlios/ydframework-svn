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

	// set admin template directory (without template name directory)
	YDConfig::set( 'YDCMTEMPLATES_ADMIN_PATH', YD_SELF_DIR, false );

	// set admin template name
	YDConfig::set( 'YDCMTEMPLATES_ADMIN_TEMPLATE', '', false );

	// set visitors template directory (without template name directory)
	YDConfig::set( 'YDCMTEMPLATES_VISITORS_PATH', YD_SELF_DIR, false );

	// set visitors template name
	YDConfig::set( 'YDCMTEMPLATES_VISITORS_TEMPLATE', '', false );

	// set template pack ( a pH technology )
	YDConfig::set( 'YDCMTEMPLATES_PACK', false, false );


	// this is a very special component because don't have any DB table (that's way do not extends YDCMComponent)
    class YDCMTemplates {

        function YDCMTemplates() {
        
			$this->admin_path        = YDConfig::get( 'YDCMTEMPLATES_ADMIN_PATH' );
			$this->admin_template    = YDConfig::get( 'YDCMTEMPLATES_ADMIN_TEMPLATE' );
			$this->visitors_path     = YDConfig::get( 'YDCMTEMPLATES_VISITORS_PATH' );
			$this->visitors_template = YDConfig::get( 'YDCMTEMPLATES_VISITORS_TEMPLATE' );
			$this->template_pack     = YDConfig::get( 'YDCMTEMPLATES_PACK' );
		}


        /**
         *  This function returns all possible templates for administration side
         *
         *  @returns    Associative array of templates, eg: array( 'Default' => 'Default', 'orange' => ... )
         */
		function admin_templates(){
		
			return $this->__getFiles( $this->admin_path );
		}


        /**
         *  This function return the template name of administration side
         *
         *  @returns    Template name
         */
		function admin_template(){
		
			return $this->admin_template;
		}


        /**
         *  This function return the full template path of administration side
         *
         *  @param $subdir  (Optional) template subdirectory
         *
         *  @returns    full path
         */
		function admin_complete( $subdir = '' ){
		
			return $this->admin_path . '/' . $this->admin_template . '/' . $subdir;
		}


        /**
         *  This function return the template pack
         *
         *  @returns    full path
         */
		function template_pack(){
		
			return $this->template_pack;
		}


        /**
         *  This function returns all possible templates for visitors side
         *
         *  @returns    Associative array of templates, eg: array( 'Default' => 'Default', 'orange' => ... )
         */
		function visitors_templates(){
		
			return $this->__getFiles( $this->visitors_path );
		}


        /**
         *  This function return the template name of administration side
         *
         *  @returns    Template name
         */
		function visitors_template(){
		
			return $this->visitors_template;
		}


        /**
         *  This function return the full template path of visitors side
         *
         *  @param $subdir  (Optional) template subdirectory
         *
         *  @returns    full path
         */
		function visitors_complete( $subdir = '' ){
		
			return $this->visitors_path . '/' . $this->visitors_template . '/' . $subdir;
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