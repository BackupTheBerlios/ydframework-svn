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
    include_once( dirname( __FILE__ ) . '/../YDForm.php');

    /**
     *        This is the class that define a submit button form element.
     */
    class YDFormElement_Submit extends YDFormElement {

        /**
         *        This is the class constructor for the YDFormElement_Submit class.
         *
         *        @param $form                The name of the form to which this element is connected.
         *        @param $name                The name of the form element.
         *        @param $label                (optional) The label for the form element.
         *        @param $attributes        (optional) The attributes for the form element.
         *        @param $options                (optional) The options for the elment.
         */
        function YDFormElement_Submit( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'submit';

            // Set the value correctly
            $this->setValue( $label );
            $this->_label = '';
            $this->_placeLabel = 'none';

            // Indicate if filters need to be applied
            $this->_applyFilters = false;

            // Indicate we are a button type
            $this->_isButton = true;

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
                'type' => $this->_type, 'name' => $this->_form . '_' . $this->_name, 'value' => $this->_value
            );
            $attribs = array_merge( $this->_attributes, $attribs );

            // Get the HTML
            return '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . ' />';

        }

    }

?>