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
    YDInclude( 'YDForm.php' );

    /**
     *	This is the class that define a checkbox form element.
     */
    class YDFormElement_Checkbox extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_Checkbox class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) The options for the elment.
         */
        function YDFormElement_Checkbox( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'checkbox';

            // Fix the value setting
            if ( ! empty( $this->_value ) ) {
                $this->setValue( 1 );
            } else {
                $this->setValue( 0 );
            } 

            // Indicate if filters need to be applied
            $this->_applyFilters = false;

            // Indicate that the label should be appended
            $this->_labelPlace = 'after';

        }
        
        /**
         *      This function returns a boolean indicating if the element value was
         *      modified from it's default value.
         *
         *      @returns        Boolean indicating if the element was modified.
         */
        function isModified() {
            if ( ! is_null( $this->_default ) ) {
                if ( $this->_value && ! empty( $this->_default ) ) {
                    return false;
                }
            } else {
                if ( ! $this->_value ) {
                    return false;
                }
            }
            return true;
        }

        /**
         *	This function sets the value for the element.
         *
         *	@param	$val	(optional) The value for this object.
         */
        function setValue( $val='' ) {
            if ( ! empty( $val ) ) {
                $this->_value = 1;
            } else {
                $this->_value = 0;
            }
        }

        /**
         *	This function sets the raw value for the element.
         *
         *	@param	$val	(optional) The value for this object.
         */
        function setRawValue( $val='' ) {
            $this->setValue( $val );
        } 

        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array(
                'type' => $this->_type, 'name' => $this->_form . '_' . $this->_name, 'id' => $this->_form . '_' . $this->_name
            );
            $attribs = array_merge( $this->_attributes, $attribs );

            // If a value, fill it in and make it checked
            if ( $this->getValue() ) {
                $attribs['checked'] = 'checked';
            }

            // Get the HTML
            return '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . ' />';

        }

    }

?>