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

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDForm.php');
    include_once( YD_DIR_HOME_CLS . '/YDTemplate.php');

    /**
     *        This is the class that defines a simple ajax grid element
     */
    class YDFormElement_Grid extends YDFormElement {

        /**
         *        This is the class constructor for the YDFormElement_Grid class.
         *
         *        @param $form                The name of the form to which this element is connected.
         *        @param $name                The name of the form element.
         *        @param $label                (optional) The label for the form element.
         *        @param $attributes        (optional) The attributes for the form element.
         *        @param $options                (optional) The options for the elment.
         */
        function YDFormElement_Grid( $form = '', $name = 'gr', $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'grid';

            // Set the value correctly
            $this->_placeLabel = 'none';

            // Indicate if filters need to be applied
            $this->_applyFilters = false;

            // Indicate we are a button type
            $this->_isButton = false;

			// column names
			$this->columns = array();
			
			// default values
			$this->defaults = array();
			
			$this->form = new YDForm( 'gridform' . $name );
			$this->form->addElement( 'hidden', 'gr_column',    '', array('id' => 'gr_column') );
			$this->form->addElement( 'hidden', 'gr_direction', '', array('id' => 'gr_direction') );
			$this->form->addElement( 'hidden', 'gr_page',      '', array('id' => 'gr_page') );
        }


        /**
         *      This function returns a boolean indicating if the element value was
         *      modified from it's default value.
         *
         *      @returns        Boolean indicating if the element was modified.
         */
        function isModified() {
            return false;
        }


        /**
         *      This function adds a ajax event to handle header calls
         *
         *      @param $ajax    YDAjax object to assign grid events
         *      @param $method  Method name where to send events
         *
         */
		function setAjax( &$ajax, $method ){
		
			$ajax->addForm( $this->form );
			$ajax->addEvent( 'ticketReload()', $method, $this->form->getName() );
		}


        /**
         *      This function returns the grid form object
         */
		function getForm(){

			// return form object
			return $this->form;
		}


        /**
         *      This function defines all columns
         *
         *      @param $columns    Column names
         *
         */
		function setColumns( $columns ){

			$this->columns = $columns;
		}


        /**
         *      This function defines all columns
         *
         *      @param $defaults    Default details for grid columns
         *
         */
		function setDefaults( $defaults ){

			$this->defaults = $defaults;
		}


        /**
         *        This function will return the element as HTML.
         *
         *        @returns        The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array(
                'id' => $this->_form . '_' . $this->_name
            );
            $attribs = array_merge( $this->_attributes, $attribs );

			// set form defaults
			if ( isset ( $this->defaults ) ) $this->form->setDefaults( $this->defaults );

			// get record template and assign recordset
			$template_record = new YDTemplate();
			$template_record->assign( 'recordset', $this->_value );
			$template_record->assign( 'columns',   $this->columns );
			$template_record->assign( 'defaults',  $this->defaults );
			$template_record->assignForm( 'form',  $this->form );

            // Get the HTML
			return $template_record->fetch( dirname( __FILE__ ) . '/YDFormElement_Grid.tpl' );
        }

    }

?>