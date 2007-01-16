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

    class YDCMStatistics_visits extends YDDatabaseObject {
    
        function YDCMStatistics_visits() {
        
			// init database object
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMStatistics_visits' );

            // register custom key
            $this->registerKey( 'visit_id', true );

            // register custom fields
			$this->registerField( 'date' );
			$this->registerField( 'browser' );
			$this->registerField( 'platform' );
			$this->registerField( 'url' );
			$this->registerField( 'hits' );
		}


    }
?>