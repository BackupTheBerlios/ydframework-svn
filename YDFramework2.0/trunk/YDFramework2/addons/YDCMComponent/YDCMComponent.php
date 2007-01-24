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

    // include YDF libs
    require_once( YD_DIR_HOME_ADD . '/YDDatabaseObject/YDDatabaseObject.php' );


    class YDCMComponent extends YDDatabaseObject{

    /**
     *  @ingroup YDCMComponent
     */
        function YDCMComponent( $table ) {

            // init DB object
            $this->YDDatabaseObject();

			// register database as default
            $this->registerDatabase();

			// register table for this component
            $this->registerTable( $table );

            $this->_author = 'unknown author';
            $this->_version = 'unknown version';
            $this->_copyright = 'no copyright';
            $this->_description = 'no description';

			// init language code to be the current locale code	
			$this->setLanguage( null );

			// virtual relation with tree titles
       		$rel = & $this->registerRelation( 'ydcmtree_titles', false, 'ydcmtree_titles' );
			$rel->setLocalKey( $table . '.tree_id' );
			$rel->setForeignKey( 'ydcmtree_titles.tree_id' );
			$rel->setForeignConditions( 'ydcmtree_titles.language_id = ' . $this->escapeSQL( $this->_language_id ) . ' AND ydcmtree_titles.language_id = ' . $table . '.language_id' );

			// virtual relation with ydcmtree
       		$rel = & $this->registerRelation( 'ydcmtree', false, 'ydcmtree' );
			$rel->setLocalKey( $table . '.tree_id' );
			$rel->setForeignKey( 'ydcmtree.tree_id' );
			$rel->setForeignConditions( 'ydcmtree.tree_state = 1' );

			// register key
			$this->registerKey( 'tree_id', false );

			// init form elements
            $this->_form_elements = array();

			// init form
            $this->_form = null;
		}


        /**
         *  This function registers a field.
         *
         *  @param $name  The field name.
         *  @param $null  (optional) The field can be null? Default: false.
         *
         *  @returns      A reference to the field object.
         */
        function & registerField( $name, $null = false, $formelement_type = null, $formelement_caption = '', $formelement_attribs = array(), $formelement_options = array() ) {

			// add form element
			if ( ! is_null( $formelement_type ) )
				$this->_form_elements[ $name ] = array( $formelement_type, $formelement_caption, $formelement_attribs, $formelement_options );

			return parent::registerField( $name, $null );
        }

        
        /**
         *  This function registers a key.
         *
         *  @param $name  The field name.
         *  @param $auto  (optional) The key is a auto-increment field? Default: false.
         *
         *  @returns      A reference to the field object.
         */
        function & registerKey( $name, $auto = false, $formelement_type = null, $formelement_caption = '', $formelement_attribs = array(), $formelement_options = array() ) {

			if ( ! is_null( $formelement_type ) )
				$this->_form_elements[ $name ] = array( $formelement_type, $formelement_caption, $formelement_attribs, $formelement_options );

			return parent::registerKey( $name, $auto );
        }


        /**
         *  This method returns the current form. If your form must have custom login, overwride this method with your getForm
         *
         *  @returns 	A YDForm object
         */
		function & getForm( $add_tree_elements = true, $add_title_elements = true ){
		
			// return form if already computed
			if ( ! is_null( $this->_form ) ) return $this->_form;		

			// compute form object
			$this->_form = new YDForm( $this->getTable() );

			// add title form elements
			if ( $add_title_elements ){
		        $this->_form->addElement( 'text',           'title',                t('ydcmuser label title'),     array('size' => 70, 'maxlength' => 70) );
			}

			// add node form elements
			if ( $add_tree_elements ){
		        $this->_form->addElement( 'text',           'reference',            t('ydcmuser label reference'), array('size' => 25, 'maxlength' => 35) );
	    	    $this->_form->addElement( 'select',         'language',             t('ydcmuser label language') );
		        $this->_form->addElement( 'select',         'access',               t('ydcmuser label access'),         array(), array( 0 => _('private'), 1 => _('public') ) );
		        $this->_form->addElement( 'select',         'state',                t('ydcmuser label state'),          array(), array( 1 => _('yes'), 0 => _('no'), 2 => _('schedule') ) );
		        $this->_form->addElement( 'datetimeselect', 'published_date_start', t('ydcmuser label published_date_start' ) );
		        $this->_form->addElement( 'datetimeselect', 'published_date_end',   t('ydcmuser label published_date_end' ) );
			}

			// add custom form elements
			foreach( $this->_form_elements as $name => $info )
				$this->_form->addElement( $info[0], $name, $info[1], $info[2], $info[3] );

			// return object
			return $this->_form;
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


        /**
         *  This method returns an element searching by its reference
         *
         *  @param $reference  Reference string
         */
		function getElementByReference( $reference ){

			$this->resetAll();
			$this->load( 'ydcmtree_titles' );
			$this->ydcmtree_titles->set( 'title_reference', $reference );
			$this->findAll();

			return $this->getValues( false, false, false, false );
		}


		function getElementById( $id ){

			$this->resetAll();
			$this->set( 'tree_id', $id );
			$this->findAll();

			return $this->getValues( false, false, false, false );
		}

		

        /**
         *  This method returns all elements of this table
         *
         *  @returns 	An array with all table elements
         */
		function getElements( $content_id = null, $prefix = false ){

			$this->resetAll();
	
			if ( is_numeric( $content_id ) ){
				$this->set( 'tree_id', intval( $content_id ) );
			}

			$this->findAll();

			return $this->getResults( false, false, false, $prefix );
		}





    }


?>
