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

	// include YDF libs
    YDInclude( 'YDDatabaseObject.php' );
	YDInclude( 'YDDatabaseTree.php' );

	require_once( dirname( __FILE__ ) . '/YDCMComponent/YDCMComp.php' );

	// add generic translation directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/YDCMComponent/languages/' );


    class YDCMComponent extends YDDatabaseObject {

        function YDCMComponent( $name, $standardComponent = true ) {

			// init DB object
            $this->YDDatabaseObject();

            $this->_author = 'unknown author';
            $this->_version = 'unknown version';
            $this->_copyright = 'no copyright';
            $this->_description = 'no description';

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( $name );

			// add standard relation
			$this->registerField( 'component_id' );
       		$rel = & $this->registerRelation( 'YDCMComp', false, 'YDCMComp' );
			$rel->setLocalKey( 'component_id' );
			$rel->setForeignKey( 'component_id' );

			// TODO: position is required and we must change order from 'parent_id' to 'parent_id ASC, position ASC'
			$this->tree = new YDDatabaseTree( 'default', 'YDCMTree', 'content_id', 'parent_id', 'parent_id' );

			// TODO: set sort by parent & position
			// $this->tree->setSortField( 'parent_id ASC, position ASC' );

			// add tree fields
			$this->tree->addField( 'type' );
			$this->tree->addField( 'state' );
			$this->tree->addField( 'reference' );
			$this->tree->addField( 'access' );
			$this->tree->addField( 'searcheable' );
			$this->tree->addField( 'published_date_start' );
			$this->tree->addField( 'published_date_end' );
			$this->tree->addField( 'candrag' );
			$this->tree->addField( 'candrop' );

			// add comp object
			if ( $standardComponent )
				$this->comp = new YDCMComp( $name );
		}


        /**
         *  This function renders this element for menu
         *
         *  @param $id           Component id
         *  @param $urlObj       Url object
         *
         *  @returns    An html string
         */

		function render( $id, $url ){
		
			return '';
		}


        /**
         *  This function will render a menu
         *
         *  @param $id           Menu id
         *  @param $language_id  Language id
         *  @param $urlObj       Url object
         *
         *  @returns    An array with all direct children
         */
		function renderMenu( $id, $language_id, $urlObj ){
		
			return $this->comp->renderMenu( $id, $language_id, $urlObj );
		}


        /**
         *  This function returns node attributes
         *
         *  @param $id           Menu id
         *  @param $language_id  Language id
         *
         *  @returns    An array with node attributes
         */
		function getNode( $id, $language = 'all' ){
		
			return $this->comp->getNode( $id, $language );
		}


        /**
         *  This static function returns an YDCMComponent module
         *
         *  @returns    an module object
         */
		function module( $name ){

			// include lib
			// TODO: check if name is a valid lib name
			require_once( dirname( __FILE__ ) . '/YDCMComponent/' . $name . '.php' );

			// return class
			return new $name();
		}


        /**
         *  This function returns all elements (except root)
         *
         *  @returns    all tree elements 
         */
		function getTreeElements(){

			return $this->tree->getDescendants( 1 );
		}


        /**
         *  This function checks if elements are valid drag&dropable
         *
         *  @param $x  Id of dragable node
         *  @param $y  Id of dropable node
         *
         *  @returns    false if elements are invalid, an associative array with node types (eg: array( $x => 'PHCMPage', $y => 'PHCMRootmenu ))'
         */
		function getDragDropElements( $x = null, $y = null ){
		
			// get object tree
			$treeObj = YDCMComponent::module( 'YDCMTree' );

			// check drop validation
			return $treeObj->getDragDropElements( $x, $y );
		}


        /**
         *  This function returns the path to current node
         *
         *  @param $id           (Optional) Node id
         *  @param $language_id  (Optional) Language id
         *
         *  @returns    An array with all parents
         */
		function getPath( $id = 0, $language_id = 'all' ){

			return $this->comp->getPath( $id, $language_id );
		}


        /**
         *  This function returns all direct elements of a menu
         *
         *  @param $id           (Optional) Menu id
         *  @param $language_id  (Optional) Language id
         *
         *  @returns    An array with all direct children
         */
		function getMenu( $id = 0, $language_id = 'all' ){

			return $this->comp->getMenu( $id, $language_id );
		}


        /**
         *  This function returns the path string to current node
         *
         *  @param $id           (Optional) Node id
         *  @param $language_id  (Optional) Language id
         *  @param $separator    (Optional) Html separator string
         *  @param $url          (Optional) Url object for element links
         *  @param $classParents (Optional) Html class for html links of parents
         *  @param $classCurrent (Optional) Url object for element links
         *
         *  @returns    An html string
         */
		function getBreadcrum( $id = 0, $language_id = 'all', $separator = ' &gt; ', $url = null, $classParents = 'breadParents', $classCurrent = 'breadCurrent' ){

			// if url object is not set we use the current url
			if( is_null( $url ) ) $url = new YDUrl( YD_SELF_SCRIPT );

			// get breadcrum from component
			return $this->comp->getBreadcrum( $id, $language_id, $separator, $url, $classParents, $classCurrent );
		}


        /**
         *  This function returns the component author
         *
         *  @returns    component author
         */
		function getAuthor(){

			return $this->_author;
		}


        /**
         *  This function returns the component version
         *
         *  @returns    component version
         */
		function getVersion(){

			return $this->_version;
		}


        /**
         *  This function returns the component copyright
         *
         *  @returns    component copyright
         */
		function getCopyright(){

			return $this->_copyright;
		}


        /**
         *  This function returns the component description
         *
         *  @returns    component description
         */
		function getDescription(){

			return $this->_description;
		}


        /**
         *  This function changes component state
         *
         *  @param $id  The node id
         *
         *  @returns    true if state changed, false otherwise
         *
         */
		function toogleState( $id ){

			// get node values
			$old_node = $this->tree->getNode( $id );

			// change state attribute
			if ( $old_node[ 'state' ] == 0 ) return $this->setState( $id, 1 );
			else                             return $this->setState( $id, 0 );
		}


        /**
         *  This function sets a node state
         *
         *  @param $id         The node id
         *  @param $state      The state code
         *
         *  @returns    1 if state changed, 0 otherwise
         */
		function setState( $id, $state ){
		
			return $this->tree->updateNode( array( 'state' => $state ), $id );
		}


        /**
         *  This function deletes the tree part of a component only
         *
         *  @param $id  The node id
         *
         */
		function deleteNode( $id ){
		
			return $this->tree->deleteNode( intval( $id ) );
		}


        /**
         *  This function adds a standard node in the tree. Experimental
         *
         *  @param $values  Node values
         */
		function addNode( $values, $parent_id = null ){

			// user YDDatabasetree method
			return $this->tree->addNode( $values, $parent_id );
		}


        /**
         *  This function updates a node
         *
         *  @param $values  Node values
         */
		function updateNode( $values, $node_id = null ){

			// user YDDatabasetree method
			return $this->tree->updateNode( $values, $node_id );
		}


        /**
         *  This function moves a node
         *
         *  @param $values  Node values
         */
		function moveNode( $x, $y ){

			// use YDDatabasetree method
			return $this->tree->moveNode( $x, $y );
		}


    }

?>