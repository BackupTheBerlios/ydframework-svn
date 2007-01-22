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

    /*
     *  @addtogroup YDCMComponent Addons - CMComponent
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    /**
     *  @ingroup YDCMComponent
     */
    class YDCMLocks extends YDDatabaseObject {
    
        function YDCMLocks() {
        
			// init component as non default
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMLocks' );

            // register custom fields
			$this->registerField( 'content_id' );
			$this->registerField( 'user_id' );

			// register custom relations
            $relTree = & $this->registerRelation( 'YDCMTree', false, 'YDCMTree' );
			$relTree->setLocalKey( 'content_id' );
            $relTree->setForeignKey( 'content_id' );

            $relUsers = & $this->registerRelation( 'YDCMUsers', false, 'YDCMUsers' );
			$relUsers->setLocalKey( 'user_id' );
            $relUsers->setForeignKey( 'user_id' );
		}


    }
?>