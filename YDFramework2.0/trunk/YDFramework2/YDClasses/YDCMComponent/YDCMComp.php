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

	require_once( dirname( __FILE__ ) . '/YDCMTree.php' );
	require_once( dirname( __FILE__ ) . '/YDCMLanguages.php' );

    class YDCMComp extends YDDatabaseObject {
    
        function YDCMComp( $name ) {
        
			// init component as non default
            $this->YDDatabaseObject();
			
			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMComp' );

            // register custom key
            $this->registerKey( 'component_id', true );

			// register fields	
			$this->registerField( 'title' );
			$this->registerField( 'rendered' );
			
			// add static relations to tree and language
			$this->registerField( 'content_id' );
       		$rel = & $this->registerRelation( 'YDCMTree', false, 'YDCMTree' );
			$rel->setLocalKey( 'content_id' );
			$rel->setForeignKey( 'content_id' );

			$this->registerField( 'language_id' );
       		$rel = & $this->registerRelation( 'YDCMLanguages', false, 'YDCMLanguages' );
			$rel->setLocalKey( 'language_id' );
			$rel->setForeignKey( 'language_id' );

			// add dynamic relation
       		$rel = & $this->registerRelation( $name, false, $name );
			$rel->setLocalKey( 'component_id' );
			$rel->setForeignKey( 'component_id' );
			$rel->setForeignJoin( 'LEFT' );
			
//			$this->name = $name;
		}


        /**
         *  This function return an array of parent nodes giving an node id
         *
         *  @param $id           Node id
         *  @param $language_id  Language id
         *
         *  @returns    An array with all parent nodes
         */
		function getPath( $id, $language_id ){
		
			// 1st step: GET nleft and nright of this node. yes, it's ugly.. 
			// TODO: change it with a better tree implementation
			$this->resetValues();
			$this->content_id = intval( $id );
			$this->language_id = $language_id;

			if ( $this->find( 'ydcmtree' ) != 1 ) return array();

			$node = $this->getValues( false, false, false, false );
		
			// 2nd step: GET parents (based on previous nleft and nright)
			$this->resetAll();

			$this->language_id = $language_id;
			$this->where( 'YDCMTree.nleft  <= ' . intval( $node[ 'nleft' ] ) );
			$this->where( 'YDCMTree.nright >= ' . intval( $node[ 'nright' ] ) );
			$this->orderBy( 'YDCMTree.nlevel' );
			$this->find( 'ydcmtree' );

			return $this->getResults( false, false, false,false );
		}


        /**
         *  This function returns the path string to a node
         *
         *  @param $id           Node id
         *  @param $language_id  Language id
         *  @param $separator    Html separator string
         *  @param $url          Url object for element links
         *  @param $classParents Html class for html links of parents
         *  @param $classCurrent Url object for element links
         *
         *  @returns    An html string
         */
		function getBreadcrum( $id, $language_id, $separator, $url, $classParents, $classCurrent ){

			// init array for results
			$res = array();

			// cycle elements to get titles
			foreach( $this->getPath( $id, $language_id ) as $elements ){
			
				// set url var ID to this element
				$url->setQueryVar( 'id',        $elements[ 'component_id' ] );
				$url->setQueryVar( 'component', $elements[ 'type' ] );

				// compute class name
				if ( $id != $elements[ 'content_id' ] ) $res[] = '<a class="' . $classParents . '" href="' . $url->getUrl() . '">' . $elements[ 'title' ] . '</a>';
				else                                    $res[] = '<span class="' . $classCurrent . '">' . $elements[ 'title' ] . '</span>';
			}

			// return html string
			return implode( $separator, $res );
		}
		
		
        /**
         *  This function returns an array with all direct children
         *
         *  @param $id           Node id
         *  @param $language_id  Language id
         *
         *  @returns    An array with all direct children
         */
		function getMenu( $id, $language_id ){
		
			$this->resetValues();

			$this->language_id = $language_id;
			$this->where( 'YDCMTree.parent_id  = ' . intval( $id ) );
			$this->find( 'ydcmtree' );
			
			return $this->getResults( false, false, false, false );
		}


        /**
         *  This function will render all menu elements
         *
         *  @param $id           Menu id
         *  @param $language_id  Language id
         *  @param $urlObj       YDUrl object
         *
         *  @returns    An array with all direct children
         */
		function renderMenu( $id, $language_id, $urlObj ){

			$menu = array();

			// render each element
			foreach( $this->getMenu( $id, $language_id ) as $element ){

				$el = new $element[ 'type' ];
				$menu[] = $el->render( $element, &$urlObj );
			}

			return $menu;
		}


        /**
         *  This function return node attributes
         *
         *  @param $id           Menu id
         *  @param $language_id  Language id
         *
         *  @returns    An array with all attributes
         */
		function getNode( $id, $language_id ){
		
			// delete previous stored values
			$this->resetValues();

			// define our id to find		
			$this->content_id = intval( $id );
			$this->language_id = $language_id;
			
			$this->limit( 1 );
			
			// find node
			$this->findAll();
			
			// parse html
			if ( isset( $this->html ) )  $this->html  = htmlentities( $this->html );
			if ( isset( $this->xhtml ) ) $this->xhtml = htmlentities( $this->xhtml );

			// return node attributes without relation prefixs
			return $this->getValues( false, false, false, false );
		}

}

?>