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
	YDInclude( 'YDUrl.php' );
	YDInclude( 'YDResult.php' );

	// include YDCM libs
	YDInclude( 'YDCMComp.php' );

	// add generic translation directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/' );

    class YDCMComponent extends YDDatabaseObject {

        function YDCMComponent( $type, $id ) {

			// init DB object
            $this->YDDatabaseObject();

            $this->_author = 'unknown author';
            $this->_version = 'unknown version';
            $this->_copyright = 'no copyright';
            $this->_description = 'no description';

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( $type );

			// add standard relation
			$this->registerField( 'component_id' );
       		$rel = & $this->registerRelation( 'YDCMComp', false, 'YDCMComp' );
			$rel->setLocalKey( 'component_id' );
			$rel->setForeignKey( 'component_id' );

			// add comp object
			$this->comp = new YDCMComp( $type, $id );
			
			$this->_id = intval( $id );
		}


        /**
         *  This function sets a new id for this component
         *
         *  @param $id           Component id
         */
		function setId( $id ){

			// set id for all operations in this class
			$this->_id       = intval( $id );

			// set id for internal YDComp object
			$this->comp->_id = intval( $id );
		}


        /**
         *  This function renders this element for menu
         *
         *  @returns    The YDDatabaseTree object for YDComponent static methods
         */
		function __getYDDatabaseTree(){
		
			// TODO: position is required and we must change order from 'parent_id' to 'parent_id ASC, position ASC'
			$tree = new YDDatabaseTree( 'default', 'YDCMTree', 'content_id', 'parent_id', 'parent_id' );

			// TODO: set sort by parent & position
			// $tree->setSortField( 'parent_id ASC, position ASC' );

			// add tree fields
			$tree->addField( 'type' );
			$tree->addField( 'state' );
			$tree->addField( 'reference' );
			$tree->addField( 'access' );
			$tree->addField( 'searcheable' );
			$tree->addField( 'published_date_start' );
			$tree->addField( 'published_date_end' );
			$tree->addField( 'candrag' );
			$tree->addField( 'candrop' );
			
			return $tree;		
		}


        /**
         *  This function renders this element for menu
         *
         *  @param $id           Component id
         *  @param $urlObj       Url object
         *
         *  @returns    An html string
         */
		function render( $id, & $url ){
		
			return '';
		}


        /**
         *  This function will render a menu
         *
         *  @returns    An array with all direct children
         */
		function renderMenu(){
		
			return $this->comp->renderMenu();
		}


        /**
         *  This function returns all node attributes
         *
         *  @returns    An array with node attributes
         */
		function getNode(){

			// check if we have already node attributes (some sort of caching)
			if ( !isset( $this->__nodeProperties ) ) 
				$this->__nodeProperties = $this->comp->getNode();
		
			return $this->__nodeProperties;
		}


		function getInfo(){
		
			return $this->comp->getInfo();
		}

        /**
         *  This function returns a node attribute
         *
         *  @param $attribute           Attribute name
         *
         *  @returns    The node attribute
         */
		function get( $attribute ){

			$node = $this->getNode();

			// return attribute if exists 
			if ( isset( $node[ $attribute ] ) ) return $node[ $attribute ];

			return false;
		}


        /**
         *  This function returns all elements (except root)
         *
         *  @returns    all tree elements 
         *  @static
         */
		function getTreeElements( $id = null ){

			// init db tree object
			$tree = YDCMComponent::__getYDDatabaseTree();

			// check if we have a custom id
			if( is_null( $id ) ) $id = $this->_id;

			// TODO: check if return element is a array
			return $tree->getDescendants( $id );
		}


        /**
         *  This function checks if elements are valid drag&dropable
         *
         *  @param $x  Id of dragable node
         *  @param $y  Id of dropable node
         *
         *  @returns    false if elements are invalid, an associative array with node types (eg: array( $x => 'PHCMPage', $y => 'PHCMRootmenu ))'
         *  @static
         */
		function getDragDropElements( $x = null, $y = null ){
		
			// get tree module
			$treeObj = YDCMComponent::module( 'YDCMTree' );

			// check drop validation
			return $treeObj->getDragDropElements( $x, $y );
		}


        /**
         *  This function returns the path to current node
         *
         *  @returns    An array with all parents
         */
		function getPath(){

			return $this->comp->getPath();
		}


        /**
         *  This function returns all direct elements of a menu
         *
         *  @returns    An array with all direct children
         */
		function getMenu(){

			return $this->comp->getMenu();
		}


        /**
         *  This function returns the path string to current node
         *
         *  @param $separator    (Optional) Html separator string
         *  @param $classParents (Optional) Html class for html links of parents
         *  @param $classCurrent (Optional) Url object for element links
         *
         *  @returns    An html string
         */
		function getBreadcrum( $separator = ' &gt; ', $classParents = 'breadParents', $classCurrent = 'breadCurrent' ){

			// get breadcrum from component
			return $this->comp->getBreadcrum( $separator, $classParents, $classCurrent );
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
         *  This function sets a node state
         *
         *  @param $state      The state code
         *
         *  @returns    1 if state changed, 0 otherwise
         */
		function setState( $state, $id = null ){

			// init db tree object
			$tree = YDCMComponent::__getYDDatabaseTree();

			// check if static
			if( is_null( $id ) ) $id = $this->_id;

			return $tree->updateNode( array( 'state' => $state ), $id );
		}


        /**
         *  This function deletes the tree part of a component only
         *
         */
		function deleteNode(){

			// init db tree object
			$tree = YDCMComponent::__getYDDatabaseTree();

			return $tree->deleteNode( $this->_id );
		}


        /**
         *  This function adds a standard node in the tree. Experimental
         *
         *  @param $values  Node values
         */
		function addNode( $values, $parent_id = null ){

			// init db tree object
			$tree = YDCMComponent::__getYDDatabaseTree();

			// use YDDatabasetree method
			return $tree->addNode( $values, $parent_id );
		}


        /**
         *  This function updates a node
         *
         *  @param $values  Node values
         */
		function updateNode( $values, $node_id = null ){

			// init db tree object
			$tree = YDCMComponent::__getYDDatabaseTree();

			// use YDDatabasetree method
			return $tree->updateNode( $values, $node_id );
		}


        /**
         *  This function moves a node
         *
         *  @param $values  Node values
         */
		function moveNode( $x, $y ){

			// init db tree object
			$tree = YDCMComponent::__getYDDatabaseTree();

			// use YDDatabasetree method
			return $tree->moveNode( $x, $y );
		}


    }


?>