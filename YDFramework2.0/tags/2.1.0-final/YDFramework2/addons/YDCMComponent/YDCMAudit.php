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

    class YDCMAudit extends YDDatabaseObject {
    
        function YDCMAudit() {
        
			// init database object
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMAudit' );

            // register custom key
            $this->registerKey( 'audit_id', true );

            // register custom fields
			$this->registerField( 'user_id' );
			$this->registerField( 'date' );
			$this->registerField( 'object' );
			$this->registerField( 'object_action' );
			$this->registerField( 'object_id' );
			$this->registerField( 'browser' );
			$this->registerField( 'platform' );
			$this->registerField( 'url' );

			// register custom relation
            $rel = & $this->registerRelation( 'YDCMUsers', false, 'YDCMUsers' );
			$rel->setLocalKey( 'user_id' );
            $rel->setForeignKey( 'user_id' );
			$rel->setForeignJoin( 'LEFT' );
		}


        /**
         *  This function counts visits of a object
         *
         *  @param $id       Object id (content_id)
         *  @param $user_id  (Optional) User id (0 means visitors)
         *  @param $period   (Optional) Period for count. Valid values: 'week', 'month', year'
         *
         *  @returns    an array of labels => values
         */
		function getObjectVisits( $id, $user_id = 0, $period = 'week' ){
		
			YDInclude( 'YDDate.php' );
		
			// compute start date
			$date = new YDDate();

			$labels = array();
			
			if ( $period == 'week' )
				for( $i = 0; $i < 7; $i++ ){
					$labels[] = $date->getDayName();
					$date->addDay( -1 );
				}

			if ( $period == 'month' )
				for( $i = 0; $i < 4; $i++ ){
					$labels[] = $date->getMonthName();
					$date->addMonth( -1 );
				}

			if ( $period == 'year' )
				for( $i = 0; $i < 3; $i++ ){
					$labels[] = $date->getYear();
					$date->addYear( -1 );
				}

			
			$labels = array_reverse( $labels );

		
			$this->resetAll();
			
			$this->user_id = intval( $user_id );
			
		
		}
		
		
		

    }
?>