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
    include_once( YD_DIR_HOME_CLS . '/YDFormElements/YDFormElement_Checkbox.php');

    /**
     *	This is the class that define a checkbox form element.
     */
    class YDFormElement_CheckboxGroup extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_Checkbox class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) The options for the elment.
         */
        function YDFormElement_CheckboxGroup( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'checkboxgroup';

            // Add the subitems
            $this->_items = array();
            foreach ( $options as $key=>$val ) {
                $item = new YDFormElement_Checkbox( $form, $name . '[' . $key . ']', $val, $attributes, $options );
                $this->_items[ $key ] = $item;

                // delete attribute. otherwise we would get a 'sep' attribute in html
                $item->delAttribute( 'sep' );
            }

            // Indicate if filters need to be applied
            $this->_applyFilters = false;

            // Indicate that the label should be appended
            $this->_placeLabel = 'before';

            // set default separator and default position
            $this->_separator = '<br />';
            $this->_position  = 'right';

            // parse separator and position from array
            if ( isset ( $attributes['sep'] ) ) {

                // find horizontal and left tags
                if ( is_int( strpos( $attributes['sep'], 'h' ) ) ) $this->_separator = '&nbsp;&nbsp;&nbsp;';
                if ( is_int( strpos( $attributes['sep'], 'l' ) ) ) $this->_position  = 'left';
            }

			// we can even have a custom separator
            if ( isset ( $attributes['separator'] ) )
				$this->_separator = $attributes['separator'];

			$this->_addSelectAll = false;
			$this->_addSelectAll_chk_attributes = array();
			$this->_addSelectAll_label_attributes = array();
        }


        /**
         *      This function returns a boolean indicating if the element value was
         *      modified from it's default value.
         *
         *      @returns        Boolean indicating if the element was modified.
         */
        function isModified() {
            foreach ( $this->_items as $item ) {
                if ( $itm->isModified() === true ) {
                    return true;
                }
            }
            return false;
        }

        /**
         *	This function sets the value for the element.
         *
         *	@param	$val	(optional) The value for this object.
         */
        function setValue( $val=array() ) {
            foreach ( $val as $k=>$v ) {
                $this->_items[$k]->setValue( $v );
            }
        }


        /**
         *	This function sets the attribute for the element.
         *
         *	@param	$attribute	Attribute to change
         *	@param	$value   	Attribute value
         */
        function setAttribute( $attribute, $value ) {
            foreach ( $this->_items as $k=>$v ) {
                $this->_items[$k]->setAttribute( $attribute, $value );
            }
        }


        /**
         *	This function sets checkboxgroup with 'select all' button
         *
         *	@param	$onBottom			(Optional) Boolean that defines if button should be added on bottom (TRUE) or on top (FALSE)
         *	@param	$chk_attributes		(Optional) Attributes to pass to the select all checkbox
         */
        function addSelectAll( $onBottom = true, $chk_attributes = array() ) {

			$this->_addSelectAll = true;
			$this->_addSelectAll_onBottom = $onBottom;
			$this->_addSelectAll_chk_attributes = $chk_attributes;
        }


        /**
         *	This function disables checkboxes
         *
         *	@param	$options	Value, Array of values to disable
         */
        function disable( $options ) {

            // if null disable all elements
            if ( is_null ( $options ) ){
                foreach ( $this->_items as $k => $item )
                    if ( isset( $this->_items[$k] ) ) $this->_items[$k]->disable();
                return;
            }

            // check if options is array
            if ( ! is_array( $options ) ) $options = array( $options );

            // disable specific elements
            foreach( $options as $k )
                if ( isset( $this->_items[$k] ) ) $this->_items[$k]->disable();
        }


        /**
         *      This function returns the value of the element.
         *
         *      @returns        Value of this object.
         */
        function getValue() {
            $values = array();
            foreach ( $this->_items as $key=>$item ) {
                $values[ $key ] = $item->getValue();
            }
            return $values;
        }

        /**
         *	This function sets the raw value for the element.
         *
         *	@param	$val	(optional) The value for this object.
         */
        function setRawValue( $val='' ) {
            foreach ( $val as $k=>$v ) {
                $this->_items[$k]->setRawValue( $v );
            }
        } 

        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Output the HTML
            $output = '';

            foreach ( $this->_items as $item )
                if ( $this->_position == 'right' ) $output .= $item->toHtml() . '&nbsp;<label for="' . $item->_attributes['id'] . '">' . $item->_label . '</label>' . $this->_separator;
                else                               $output .= '<label for="' . $item->_attributes['id'] . '">' . $item->_label . '</label>&nbsp;' . $item->toHtml() . $this->_separator;

			// check if we have more than one element and a 'select all' button is defined
			if ( count( $this->_items ) > 1 && $this->_addSelectAll ){ 

				// compute button code
				$selall = new YDFormElement_Checkbox( $this->_form, $this->getAttribute( 'id' ) . 'sall' );
				$selall->setAttribute( 'onclick', 'for (var i=0;i<document.forms[\''. $this->_form . '\'].elements.length;i++) if (document.forms[\''. $this->_form . '\'].elements[i].type==\'checkbox\' && !document.forms[\''. $this->_form . '\'].elements[i].disabled && /' . $this->_name . '\[[a-zA-Z0-9]+\]/.test( document.forms[\''. $this->_form . '\'].elements[i].name ) ) document.forms[\''. $this->_form . '\'].elements[i].checked = this.checked;' );

				// if all checkboxes are selected, the 'select all' will be selected too
				$selall->setValue(1);
				foreach( $this->getValue() as $elem => $value )
					if ( $value != 1 ){ $selall->setValue(0); break; }

				// check default translation
				if( !isset( $GLOBALS['t']['select all'] ) ) $GLOBALS['t']['select all'] = 'select all';

				// compute button label
				if ( $this->_position == 'right' ) $selall_html = '<span ' . YDForm::_convertToHtmlAttrib( $this->_addSelectAll_chk_attributes ) . '><label>' . $selall->toHTML() . ' ' . t( 'select all' ) . '</label></span>';
				else                               $selall_html = '<span ' . YDForm::_convertToHtmlAttrib( $this->_addSelectAll_chk_attributes ) . '><label>' . t( 'select all' ) . ' ' . $selall->toHTML() . '</label></span>';

				// add button code to html output
				if ( $this->_addSelectAll_onBottom ) $output = $output . $selall_html;
				else                                 $output = $selall_html . $this->_separator . $output;
			}

            return $output;
        }

    }

?>