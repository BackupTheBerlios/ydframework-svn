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
            }

            // Indicate if filters need to be applied
            $this->_applyFilters = false;

            // Indicate that the label should be appended
            $this->_placeLabel = 'before';

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
            foreach ( $this->_items as $item ) {
                $output .= '<nowrap>' . $item->toHtml();
                $output .= '&nbsp;<label for="' . $item->_attributes['id'] . '">' . $item->_label . '</label>';
                $output .= '</nowrap>##ITEMSEP##';
            }
            $output = rtrim( $output, '##ITEMSEP##' );
            return $output;

        }

    }

?>