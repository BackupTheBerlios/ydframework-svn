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

	// set components path
	YDConfig::set( 'YD_DBOBJECT_PATH', YD_DIR_HOME_ADD . '/YDCMComponent', false );

	// add translations directory
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/' );
	YDLocale::addDirectory( dirname( __FILE__ ) . '/languages/YDCMGroup/' );

	// add YDF libs needed by this class
	YDInclude( 'YDDatabaseObject.php' );
	YDInclude( 'YDValidateRules.php' );
	YDInclude( 'YDResult.php' );

	// add YDCM libs
	YDInclude( 'YDCMUserobject.php' );


	// user class
    class YDCMGroup extends YDDatabaseObject {
    
        function YDCMGroup() {
        
			// init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( 'YDCMGroup' );

			// register fields
			$this->registerField( 'group_id' );
			$this->registerField( 'name' );
			$this->registerField( 'description' );			

			// init relation with userobject
            $rel = & $this->registerRelation( 'ydcmuserobject', false, 'ydcmuserobject' );
			$rel->setLocalKey( 'group_id' );
            $rel->setForeignKey( 'userobject_id' );
		}


        /**
         *  This method checks if a group ID is valid ( will check if group_id is on its 2 tables )
         *
         *  @param $group_id        Group id
         *
         *  @returns    TRUE if valid, FALSE otherwise
         */
		function validID( $group_id ){
		
			// reset values
			$this->resetAll();

			// find all rows this user id
			$this->where( 'group_id = ' . intval( $group_id ) );

			// join all tables when finding (to check if group_id really belongs to all 3 tables)
			return ( $this->findAll() == 1 );
		}


        /**
         *  This method deletes a group
         *
         *  @param $group_id       Group id
         *
         *  @returns    delete()
         */
		function deleteUser( $group_id ){
		
			// reset stuff
			$this->resetAll();
			
			$this->set( 'group_id', intval( $group_id ) );
			
			return $this->delete();
		}


        /**
         *  This method adds form elements for group editing
		 *
		 * @param $group_id     Group id to edit
		 *
		 * @param $editable     (Optional) Boolean flag that defines if elements are editable
         */
		function addFormEdit( $group_id, $editable = true ){

		 	return $this->_addFormDetails( $group_id, true, $editable );
		}


        /**
         *  This method adds form elements for addind a new group
		 *
		 * @param $parent_group_id    (Optional) Parent id
         */
		function addFormNew( $parent_group_id = null ){

		 	return $this->_addFormDetails( $parent_group_id, false, true );
		}
		 
		
        /**
         *  Helper method for user management
		 *
		 * @param $id           If you will EDIT some group this is the group id to edit
         *                      If you will ADD a new group this is the parent id. Use NULL to add it to the root tree
         *
		 * @param $edit         TRUE: We are editing a group, FALSE: we are creating a group
		 *
		 * @param $editable     When editing a group, this boolean flag defines if form elements are editable (default true)
         */
		function _addFormDetails( $id, $edit, $editable ){

			YDInclude( 'YDForm.php' );

			// init form
			$this->_form = new YDForm( 'YDCMGroup' );

			// add name and drecription
			if ( $editable ){
				$this->_form->addElement( 'text',     'name',        t( 'group name' ) );
				$this->_form->addElement( 'textarea', 'description', t( 'group description' ), array( 'rows' => 4, 'cols' => 40 ) );
				$this->_form->addElement( 'select',   'state',       t( 'group state' ), array(), array( 0 => t( 'Blocked' ), 1 => t( 'Active' ) ) );

				$this->_form->addFormRule( array( & $this, '_checkgroup' ), array( $edit, $id ) );

			}else{
				$this->_form->addElement( 'span', 'name',        t( 'group name' ) );
				$this->_form->addElement( 'span', 'description', t( 'group description' ) );
			}

			// if we are editing a group, set form defaults
			if ( $edit ){

				// add submit button
				$this->_form->addElement( 'submit', '_cmdSubmit', t( 'save' ) );

				// set form defaults based on user attributes
				$this->_form->setDefaults( $this->getGroup( $id ) );

			}else{

				// add submit button
				$this->_form->addElement( 'submit', '_cmdSubmit', t( 'add' ) );
			}

		}


        /**
         *  Internal method to check if a group name already exists 
         */
		function _checkgroup( $values, $options ){

			$this->resetAll();
			
			$this->set( 'name', $values[ 'name' ] );

			// check if we are editing. if yes, we should check if the name is already used only by another group than current
			if ( $options[0] == true )
				$this->where( 'group_id != ' . intval( $options[1] ) );
			
			if ( $this->find() == 0 ) return true;
			
			return array( '__ALL__' => t( 'group exists' ) );
		}


        /**
         *  This method updates group attributes
         *
         *  @param $id           Group id to save attributes
         *  @param $formvalues   (Optional) Custom array with attributes
         *
         *  @returns    YDResult object. OK      - form updated
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to update
         */
		function saveFormEdit( $id, $formvalues = null ){

			return $this->_saveFormDetails( $id, true, $formvalues );
		}


        /**
         *  This method adds a new group
         *
         *  @param $parent_id    (Optional) Parent id of this new group
         *  @param $formvalues   (Optional) Custom array with attributes
         *
         *  @returns    YDResult object. OK      - form added
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to add
         */
		function saveFormNew( $parent_id = null, $formvalues = null ){

			return $this->_saveFormDetails( $parent_id, false, $formvalues );
		}


        /**
         *  This method adds/saves a group
         *
         *  @param $id           If we are editing, $id is the group id. If we are adding, $id is the parent_id
         *  @param $edit         Boolean flag that defines if we are editing $id or adding to $id
         *  @param $formvalues   (Optional) Custom array with attributes
         *
         *  @returns    YDResult
         */
		function _saveFormDetails( $id, $edit, $formvalues = null ){

			// check form validation
			if ( !$this->_form->validate( $formvalues ) )
				return YDResult::warning( t( 'form errors' ), $this->_form->getErrors() );

			// get form values EXCLUDING spans
			$values = $this->_form->getValues();

			// check if we are editing or adding an element
			if ( $edit ){

				// create userobject node
				$userobject = array();
				$userobject['type']  = 'YDCMGroup';
				$userobject['reference'] = $values[ 'name' ];
//				$userobject['state']     = $values[ 'state' ];

				// update userobject
				$uobj = new YDCMUserobject();
				$res  = $uobj->updateNode( $userobject, $id );

				// create user row
				$group = array();
				$group['name']        = $values[ 'name' ];
				$group['description'] = $values[ 'description' ];

				// update user
				$this->resetValues();
				$this->setValues( $group );
				$this->where( 'group_id = '. $id ); 

				$res = $this->update();
				
				// check update result and return
				if ( $res > 0 ) return YDResult::ok( t( 'group details updated' ), $res );
				else            return YDResult::warning( t( 'group not updated' ), $res );

			}else{

				// create userobject node
				$userobject = array();
				$userobject['type']      = 'YDCMGroup';
				$userobject['reference'] = $values[ 'name' ];
				$userobject['state']     = isset( $values[ 'state' ] ) ? $values[ 'state' ] : 0;

				// check default parent id
				if ( is_null( $id ) ) $id = 1;

				// TODO: check if group is valid (and, eg,  is not a user node)

				// update userobject and get new id
				$uobj = new YDCMUserobject();
				$res  = $uobj->addNode( $userobject, intval( $id ) );

				// create user row
				$group = array();
				$group[ 'group_id' ]    = $res;
				$group[ 'name' ]        = $values[ 'name' ];
				$group[ 'description' ] = $values[ 'description' ];

				// reset object
				$this->resetAll();
				$this->setValues( $group );

				// insert values
				if ( $this->insert() ) return YDResult::ok( t( 'group added' ), $res );
				else                   return YDResult::fatal( t( 'group not added' ), $res );
			}

		}


        /**
         *  This returns the form
         *
         *  @param $defaults  (Optional) default values array to apply in form
         *
         *  @returns    YDForm object
         */
		function & getForm( $defaults = false ){

			// check if we have form defaults and apply them
			if ( is_array( $defaults ) ) $this->_form->setDefaults( $defaults );

			return $this->_form;
		}


        /**
         *  This method returns user attributes
         *
         *  @param      $group_id    Group id or group name
         *
         *  @returns    FALSE if group not valid, ARRAY with details otherwise
         */
		function getGroup( $group_id ){

			$this->resetAll();

			// set group id to search for
			if ( is_numeric( $group_id ) ) $this->set( 'group_id',  intval( $group_id ) );
			else                           $this->set( 'name',      strval( $group_id ) );

			// get all attributes
			if ( $this->findAll() != 1 ) return false;

			return $this->getValues();
		}


        /**
         *  This method returns a specific group attribute
         *
         *  @param      $group_id    Group id to use
         *  @param      $attribute   Attribute name string
         *
         *  @returns    FALSE if group not valid, attribute string otherwise
         */
		function getGroupAttribute( $group_id, $attribute ){

			$this->resetAll();

			// set user id
			$this->set( 'group_id', intval( $group_id ) );

			// get all attributes
			if ( $this->find() != 1 ) return false;

			return $this->get( $attribute );
		}


    }
?>