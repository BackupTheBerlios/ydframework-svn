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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

	YDInclude( 'YDCMComponent.php' );


    class YDCMLink extends YDCMComponent {
    
        function YDCMLink() {
        
			// init component
            $this->YDCMComponent( 'YDCMLink' );

            $this->_author = 'Francisco Azevedo';
            $this->_version = '0.1';
            $this->_copyright = '(c) Copyright 2006 Francisco Azevedo';
            $this->_description = 'This is a link component';

			// register custom fields
			$this->registerField( 'url' );
			$this->registerField( 'num_visits' );

			// we don't have custom relations
		}


        /**
         *  This function will render this element (to be used in a menu)
         *
         *  @returns    Html link
         */
		function render( $component, & $url ){

			$url->setQueryVar( 'component', $component[ 'type' ] );
			$url->setQueryVar( 'id',        $component[ 'content_id' ] );

			// get url properties from DB
			$this->getNode( $component[ 'content_id' ] );

			return '<a href="' . $this->url . '">' . $component[ 'title' ] . '</a>';
		}


    }
?>