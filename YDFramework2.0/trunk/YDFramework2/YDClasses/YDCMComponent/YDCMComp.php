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
    
        function YDCMComp( $type, $id ) {
        
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
			$this->registerField( 'version_default' );
			$this->registerField( 'version_date' );
//			$this->registerField( 'user_id' );

			// add static relations to tree and language
			$this->registerField( 'content_id' );
       		$rel = & $this->registerRelation( 'YDCMTree', false, 'YDCMTree' );
			$rel->setLocalKey( 'content_id' );
			$rel->setForeignKey( 'content_id' );

			$this->registerField( 'language_id' );
       		$rel = & $this->registerRelation( 'YDCMLanguages', false, 'YDCMLanguages' );
			$rel->setLocalKey( 'language_id' );
			$rel->setForeignKey( 'language_id' );

			$this->registerField( 'version_user_id' );
       		$rel = & $this->registerRelation( 'YDCMUsers', false, 'YDCMUsers' );
			$rel->setLocalKey( 'version_user_id' );
			$rel->setForeignKey( 'user_id' );

			// add dynamic relation
       		$rel = & $this->registerRelation( $type, false, $type );
			$rel->setLocalKey( 'component_id' );
			$rel->setForeignKey( 'component_id' );
			$rel->setForeignJoin( 'LEFT' );

			// store component id
			$this->_id = intval( $id );
			
			// store type for future use
			$this->_type = $type;
		}


        /**
         *  This function return an array of parent nodes giving an node id
         *
         *  @returns    An array with all parent nodes
         */
		function getPath(){
		
			// 1st step: GET nleft and nright of this node. yes, it's ugly.. 
			// TODO: change it with a better tree implementation
			$this->resetAll();
			$this->content_id  = $this->_id;
			$this->language_id = YDLocale::get();

			if ( $this->find( 'ydcmtree' ) != 1 ) return array();

			$node = $this->getValues( false, false, false, false );
		
			// 2nd step: GET parents (based on previous nleft and nright)
			$this->resetAll();

			$this->language_id = YDLocale::get();
			$this->where( 'YDCMTree.nleft  <= ' . intval( $node[ 'nleft' ] ) );
			$this->where( 'YDCMTree.nright >= ' . intval( $node[ 'nright' ] ) );
			$this->orderBy( 'YDCMTree.nlevel' );
			$this->find( 'ydcmtree' );

			return $this->getResults( false, false, false,false );
		}


        /**
         *  This function returns the path string to a node
         *
         *  @param $separator    Html separator string
         *  @param $classParents Html class for html links of parents
         *  @param $classCurrent Html class for span ( current element )
         *
         *  @returns    An html string
         */
		function getBreadcrum( $separator, $classParents, $classCurrent ){

			// init array for results
			$res = array();

			// init url object
			$url = new YDUrl( YD_SELF_SCRIPT );

			// cycle elements to get titles
			foreach( $this->getPath() as $elements ){
			
				// set url var ID to this element
				$url->setQueryVar( 'id',        $elements[ 'component_id' ] );
				$url->setQueryVar( 'component', $elements[ 'type' ] );

				// compute class name
				if ( $this->_id != $elements[ 'content_id' ] ) $res[] = '<a class="' . $classParents . '" href="' . $url->getUrl() . '">' . $elements[ 'title' ] . '</a>';
				else                                           $res[] = '<span class="' . $classCurrent . '">' . $elements[ 'title' ] . '</span>';
			}

			// return html string
			return implode( $separator, $res );
		}
		
		
        /**
         *  This function returns an array with all direct children
         *
         *  @returns    An array with all direct children
         */
		function getMenu(){
		
			$this->resetAll();

			$this->language_id = YDLocale::get();
			$this->where( 'YDCMTree.parent_id  = ' . $this->_id );
			$this->find( 'ydcmtree' );
			
			return $this->getResults( false, false, false, false );
		}


        /**
         *  This function will render all menu elements
         *
         *  @returns    An array with all direct children
         */
		function renderMenu(){

			$menu = array();

			$url = new YDUrl( YD_SELF_SCRIPT );

			// render each element
			foreach( $this->getMenu() as $element ){

				$el = new $element[ 'type' ];
				$menu[] = $el->render( $element, &$url );
			}

			return $menu;
		}


        /**
         *  This function return node attributes
         *
         *  @returns    An array with all attributes
         */
		function getNode(){
		
			// delete previous stored values
			$this->resetAll();

			// define our id to find		
			$this->content_id  = $this->_id;
			$this->language_id = YDLocale::get();
			
			$this->limit( 1 );
			
			// find node
			$this->findAll();
			
			// parse html
			if ( isset( $this->html ) )  $this->html  = htmlentities( $this->html );
			if ( isset( $this->xhtml ) ) $this->xhtml = htmlentities( $this->xhtml );

			// return node attributes without relation prefixs
			return $this->getValues( false, false, false, false );
		}


        /**
         *  This function return node infomation
         *
         *  @returns    An array with all information: array( 'description' => .., 'versions' => .., 'versions_total' => .. )
         */
		function getInfo(){
		
			// delete previous stored values
			$this->resetAll();

			// define our id to find		
			$this->content_id  = $this->_id;

			// order versions by version date
			$this->orderBy( 'version_date', 'desc' );
			
			// find node
			$this->find( 'ydcmtree', 'ydcmusers', strtolower( $this->_type ) );

			$versions = $this->getResultsAsAssocArray( array( 'language_id', 'version_date' ) );
			
			// init description and total of versions
			$description    = array();
			$versions_total = 0;

			// cycle versions, count and get first description (they must be all the same)
			foreach( $versions as $v => $arr ){
			
				$versions_total += count( $arr );
			
				if ( empty( $description ) ) $description = array_values( $arr );
			}

			// parse publish dates
			if ( $description[0][ 'YDCMTree_state' ] != 2 ){
				$description[0][ 'YDCMTree_published_date_start_trans' ] = t('not applicable');
				$description[0][ 'YDCMTree_published_date_end_trans' ]   = t('not applicable');
			}

			// parse access
			if ( $description[0][ 'YDCMTree_access' ] == 1 ) $description[0][ 'YDCMTree_access_trans' ] = t('public');
			else                                             $description[0][ 'YDCMTree_access_trans' ] = t('private');

			// parse state
			switch ($description[0][ 'YDCMTree_state' ]){
				case 0 : $description[0][ 'YDCMTree_state_trans' ] = t('not published'); break;
				case 1 : $description[0][ 'YDCMTree_state_trans' ] = t('published');     break;
				case 2 : $description[0][ 'YDCMTree_state_trans' ] = t('schedule');      break;
			}

			// parse searcheable
			if ( $description[0][ 'YDCMTree_searcheable' ] == 1 ) $description[0][ 'YDCMTree_searcheable_trans' ] = t('yes');
			else                                                  $description[0][ 'YDCMTree_searcheable_trans' ] = t('no');

			// return the information array
			return array(	'description'    => $description[0],
							'versions'       => $versions,
							'versions_total' => $versions_total );
		}

}

?>