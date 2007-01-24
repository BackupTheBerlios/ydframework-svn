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


	// add YD libs
	YDInclude( 'YDDatabaseObjectTree.php' );

    /**
     *  @ingroup YDCMComponent
     */
    class YDCMTree extends YDDatabaseObjectTree {
    
        function YDCMTree() {

			// init parent object
			$this->YDDatabaseObjectTree( 'YDCMTree', 'default', 'tree_id', 'tree_parent_id', 'tree_lineage', 'tree_level', 'tree_position' );

			// add tree fields
			$this->registerField( 'tree_type' );
			$this->registerField( 'tree_access' );			
			$this->registerField( 'tree_state' );
			$this->registerField( 'tree_searcheable' );			
			$this->registerField( 'tree_published_datestart' );
			$this->registerField( 'tree_published_dateend' );			
			$this->registerField( 'tree_candrag' );
			$this->registerField( 'tree_candrop' );
			$this->registerField( 'tree_inline' );
			$this->registerField( 'tree_url' );
			$this->registerField( 'tree_urltarget' );

			// set relation with names table
       		$rel = & $this->registerRelation( 'ydcmtree_titles', false, 'ydcmtree_titles' );
			$rel->setLocalKey( 'ydcmtree.tree_id' );
			$rel->setForeignKey( 'ydcmtree_titles.tree_id' );

			// init language code to be the current locale code	
			$this->setLanguage( null );
		}


        /**
         *  This reset overides the parent resetAll() because we must redefine the language code deleted by parent reset()
         */
		function resetAll(){
		
			// resets object stuff
			parent::resetAll();

			// apply the language code deleted in previous parent reset
			$this->where( 'ydcmtree_titles.language_id = ' . $this->escapeSQL( $this->_language_id ) );
		}
		

        /**
         *  This method defines the language code to use in all sql queries
         *
         *  @param $language_id  (Optional) Language id code, eg 'en'. By default current locale is used
         */
		function setLanguage( $language_id = null ){
		
			if ( ! is_string( $language_id ) ) $this->_language_id = YDLocale::get();
			else                               $this->_language_id = $language_id;
		}


		function getSubElements( $id, $inline = false ){

			$this->resetAll();

			if ( $inline ) $this->where( $this->getTable() . '.tree_inline = 1' );
			else           $this->where( $this->getTable() . '.tree_inline = 0' );

			return $this->_getChildren( $id, false, false );
		}


        /**
         *  This function checks if elements are valid drag&dropable
         *
         *  @param $x  Id of dragable node
         *
         *  @param $y  Id of dropable node
         *
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


    class YDCMTree_titles extends YDDatabaseObject {
    
        function YDCMTree_titles() {
        
			// init component as non default
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMTree_titles' );

            // register key
            $this->registerKey( 'tree_id', true );
			$this->registerKey( 'language_id', false );

			// register fields	
			$this->registerField( 'title_html' );
			$this->registerField( 'title_reference' );
		}
	}

?>