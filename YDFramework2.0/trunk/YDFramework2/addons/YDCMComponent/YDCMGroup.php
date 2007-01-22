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

    /**
     *  @ingroup YDCMComponent
     */
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
         *  This method deletes a user (and all children) or just the children
         *
         *  @param $group_id       Userobject id
         *  @param $mode           (Optional) 0: delete group_id and ALL children
         *                                    1: delete ALL children of group_id only
         *
         *  @returns    YDResult object. OK      - user deleted
		 *                               WARNING - no deletes where made
         */
		function deleteGroup( $group_id, $mode = 0 ){
		
			$obj = new YDCMUserobject();
			
			// delete user and get result
			if ( $obj->deleteNode( $group_id, $mode ) ) return YDResult::ok( t('ydcmgroup mess delete ok') );
			else                                        return YDResult::fatal( t('ydcmgroup mess delete empty') );
		}


        /**
         *  This method adds form elements for group editing
		 *
		 * @param $group_id     Group id to edit
		 * @param $permObj    The permission object.
         */
		function & addFormEdit( $group_id, $permObj = null ){

			// get editing id to be used when saving
			$this->editing_ID = $group_id;

		 	return $this->_addFormDetails( $group_id, true, $permObj );
		}


        /**
         *  This method adds form elements for addind a new group
		 *
		 * @param $user_id    User ID
		 * @param $permObj    The permission object.
         */
		function & addFormNew( $user_id, $permObj = null ){

			// get editing id to be used when saving
			$this->editing_ID = $user_id;

		 	return $this->_addFormDetails( $user_id, false, $permObj );
		}
		 
		
        /**
         *  Helper method for user management
		 *
		 * @param $id           If you will EDIT some group this is the group id to edit
         *                      If you will ADD a new group this is the parent id. Use NULL to add it to the root tree
         *
		 * @param $edit         TRUE: We are editing a group, FALSE: we are creating a group
		 * @param $permObj    The permission object.
         */
		function & _addFormDetails( $id, $edit, $permObj ){

			YDInclude( 'YDForm.php' );

			// init form
			$this->_form = new YDForm( 'YDCMGroup' );

			// add elements
			$this->_form->addElement( 'text',     'name',        t( 'ydcmgroup label name' ) );
			$this->_form->addElement( 'textarea', 'description', t( 'ydcmgroup label description' ), array( 'rows' => 4, 'cols' => 40 ) );

			// add rules
			$this->_form->addFormRule( array( & $this, '_checkgroup' ), array( $edit, $id ) );


			// if we adding a group, just add a submit button
			if ( $edit == false ){
				$this->_form->addElement( 'submit', '_cmdSubmit', t( 'ydcmgroup label new' ) );

			// otherwise, if we are editing a group, we must add defaults and a submit button too
			}else{

				$defaults = $this->getGroup( $id );
				$this->_form->setDefaults( $defaults );

				// add submit button
				$this->_form->addElement( 'submit', '_cmdSubmit', t( 'ydcmgroup label save' ) );
			}
			
			// check if we are using the permission engine. if not, just return the form
			if ( is_null( $permObj ) ) return $this->_form;

			// include permission engine
			YDInclude( 'YDCMPermission.php' );

			// set YDPermission object form name the same name as group form
			$permObj->setFormName( $this->_form->getName() );

			// get form with all checkboxgroups
			if ( $edit ) $perms_form = & $permObj->addFormEdit( $id );
			else         $perms_form = & $permObj->addFormNew( $id );

			// get all checkboxgroups html code
			$perms_code = '';
			foreach( $perms_form->getElements() as $name => $obj )
				if ( $obj->getType() == 'checkboxgroup' ) $perms_code .= $obj->toFullHTML( '<br>', '', '<br><br>' );

			// create a span and insert all checkboxgroups inside
			$el = &	$this->_form->addElement( 'span', 'permissions', t( 'permissions' ) );
			$el->setValue( $perms_code );

			// get editing YDPermission object to be used when saving
			$this->editing_PERMOBJ = & $permObj;

			return $this->_form;
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
			
			return array( '__ALL__' => t( 'ydcmgroup mess group exists' ) );
		}


        /**
         *  This method updates group attributes
         *
         *  @param $formvalues   (Optional) Custom array with attributes
         *
         *  @returns    YDResult object. OK      - form updated
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to update
         */
		function saveFormEdit( $formvalues = null ){

			return $this->_saveFormDetails( $this->editing_ID, true, $formvalues );
		}


        /**
         *  This method adds a new group
         *
         *  @param $formvalues   (Optional) Custom array with attributes
         *
         *  @returns    YDResult object. OK      - form added
		 *                               WARNING - there are form errors
         *                               FATAL   - was not possible to add
         */
		function saveFormNew( $formvalues = null ){

			return $this->_saveFormDetails( $this->editing_ID, false, $formvalues );
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
				$userobject['state']     = 1;

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

				// update and sum lines afected to userobject
				$res += $this->update();

				// if we are using the permission system, update and sum lines afected in permission table
				if ( isset( $this->editing_PERMOBJ ) ) $res += $this->editing_PERMOBJ->saveFormEdit( $id, $formvalues );
				
				// check update from node update and from group update
				if ( $res > 0 ) return YDResult::ok( t( 'ydcmgroup mess details updated' ), $res );
				else            return YDResult::warning( t( 'ydcmgroup mess details not updated' ), $res );

			}else{

				// create userobject node
				$userobject = array();
				$userobject['type']      = 'YDCMGroup';
				$userobject['reference'] = $values[ 'name' ];
				$userobject['state']     = 1;

				// check default parent id
				if ( is_null( $id ) ) $id = 1;

				// TODO: check if group is valid (and, eg, is not a user node)

				// update userobject and get new id
				$uobj   = new YDCMUserobject();
				$nodeID = $uobj->addNode( $userobject, intval( $id ) );

				// init result count
				$res = $nodeID;

				// if we are using the permission system, add permissions and sum lines afected in permission table
				if ( isset( $this->editing_PERMOBJ ) ) $res += $this->editing_PERMOBJ->saveFormNew( $id, $nodeID, $formvalues );

				// create user row
				$group = array();
				$group[ 'group_id' ]    = intval( $nodeID );
				$group[ 'name' ]        = $values[ 'name' ];
				$group[ 'description' ] = $values[ 'description' ];

				// reset object
				$this->resetAll();
				$this->setValues( $group );

				// insert values
				if ( $this->insert() ) return YDResult::ok( t( 'ydcmgroup mess created' ), $res );
				else                   return YDResult::fatal( t( 'ydcmgroup mess impossible to create' ), $res );
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