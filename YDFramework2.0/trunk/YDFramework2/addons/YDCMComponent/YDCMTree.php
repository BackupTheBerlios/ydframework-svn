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


    class YDCMTree extends YDDatabaseObject {
    
        function YDCMTree() {
        
			// init component as non default
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMTree' );

            // register custom key
            $this->registerKey( 'content_id', true );

			// register fields	
			$this->registerField( 'parent_id' );
			$this->registerField( 'type' );
			$this->registerField( 'state' );
			$this->registerField( 'reference' );
			$this->registerField( 'access' );			
			$this->registerField( 'searcheable' );			
			$this->registerField( 'published_date_start' );
			$this->registerField( 'published_date_end' );			
			$this->registerField( 'candrag' );
			$this->registerField( 'candrop' );
			$this->registerField( 'nleft' );
			$this->registerField( 'nright' );

		}


        /**
         *  This function checks if elements are valid drag&dropable
         *
         *  @param $x  Id of dragable node
         *
         *  @param $y  Id of dropable node

         *  @returns    false if elements are invalid, an associative array with node information
         */
		function getDragDropElements( $x = null, $y = null){
		
			// check if elements are numeric
			if ( !is_numeric( $x ) || !is_numeric( $y ) ) return false;
		
			// reset old values and get current table	
			$this->resetValues();

			// set custom where to get those 2 elements
			$this->where( '((content_id = ' . intval( $x ) . ' AND candrag = 1) OR (content_id = ' . intval( $y ) . ' AND candrop = 1))' );

			// we must get always 2 elements otherwise they were not valid
			if ( $this->find() != 2 ) return false;

			// return associative array
			return $this->getResultsAsAssocArray( 'content_id' );
		}



	}

?>