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

    /**
     *        This is the class that define a button form element.
     */
    class YDFormElement_Img extends YDFormElement {

        /**
         *        This is the class constructor for the YDFormElement_Button class.
         *
         *        @param $form                The name of the form to which this element is connected.
         *        @param $name                The name of the form element.
         *        @param $label                (optional) The label for the form element.
         *        @param $attributes        (optional) The attributes for the form element.
         *        @param $options                (optional) The options for the elment.
         */
        function YDFormElement_Img( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'img';

            // Set the value correctly
            $this->setValue( $label );
            $this->_label = '';
            $this->_placeLabel = 'none';

            // Indicate if filters need to be applied
            $this->_applyFilters = false;

            // Indicate we are a button type
            $this->_isButton = false;

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
         *        This function will return the element as HTML.
         *
         *        @returns        The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array(
                'id' => $this->_form . '_' . $this->_name, 'src' => $this->_value, 'alt' => $this->_name
            );
            $attribs = array_merge( $this->_attributes, $attribs );

            // Get the HTML
            return '<img' . YDForm::_convertToHtmlAttrib( $attribs ) . ' />';

        }


        /**
         *	This function returns the default javascript event of this element
         */
        function getJSEvent(){ 

            return 'onclick';
        }


        /**
         *	This function gets an element value using javascript
         *
         *	@param $options		(optional) The options for the elment.
         */
        function getJS( $options = null ){ 

            // return js code
            return 'return document.getElementById("' . $this->getAttribute('id') . '").value;';
        }


        /**
         *	This function sets an element value using javascript
         *
         *	@param $result		The result value
         *	@param $attribute	(optional) Element attribute
         *	@param $options		(optional) The options for the elment.
         */
        function setJS( $result, $attribute = null, $options = null ){ 

            // if atribute event is not defined we must create a default one
            if ( is_null( $attribute ) ){

                // the default atribute of an image is its source address
                $attribute = 'src';

                // if we want to display a new image, we must create a new src value otherwise the browser won't replace
                $result .= '?' . microtime();
            }

            // assign result image
            return 'document.getElementById("' . $this->getAttribute( 'id' ) . '").' . $attribute . ' = "' . $result . '";';
        }

    }

?>