<?php

    /*

        Yellow Duck Framework version 2.0
        Copyright (C) (c) copyright 2004 Pieter Claerhout

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
     *	This is the class that is able to render a form object to HTML.
     */
    class YDFormRenderer_array extends YDFormRenderer {

        /**
         *	This is the class constructor for the YDFormRenderer_array class.
         *
         *	@param $form		The form that needs to be rendered.
         */
        function YDFormRenderer_array( $form ) {

            // Initialize the parent
            $this->YDFormRenderer( $form );

        }

        /**
         *	This function will render the form.
         *
         *	@returns	The rendered form.
         */
        function render() {

            // Get the form as an array
            //$form = $this->_form->toArray();

            // Start with an empty array
            $form = array();

            // Add the list of attributes
            $attribs = array(
                'name'		=> $this->_form->_name, 'id'		=> $this->_form->_name, 'method' => strtoupper( $this->_form->_method ),
                'action'	=> $this->_form->_action, 'target'	=> $this->_form->_target
            );
            $attribs = array_merge( $attribs, $this->_form->_attributes );
            $form['attribs'] = $this->_form->_convertToHtmlAttrib( $attribs );
            $form['tag'] = '<form' . $form['attribs'] . '>';
            $form['requirednote'] = $this->_form->_requiredNote;
            $form['endtag'] = '</form>';

            // Add the errors
            $form['errors'] = $this->_form->_errors;
            $form['errors_unique_messages' ] = array_unique( array_values( $this->_form->_errors ) );

            // Loop over the list of elements
            foreach ( $this->_form->_elements as $name => $element ) {

                // Update the value
                $element->_value = $this->_form->getValue( $name );

                // Add the form element
                $form[ $name ] = $element->toArray();

                // Add errors if any
                if ( array_key_exists( $name, $this->_form->_errors ) ) {
                    $form[ $name ]['error'] = $this->_form->_errors[ $name ];
                } else {
                    $form[ $name ]['error'] = '';
                }

                // Check if the field is required
                if ( array_key_exists( $name, $this->_form->_rules ) ) {
                    $form[ $name ]['required'] = true;
                } else {
                    $form[ $name ]['required'] = false;
                }

                // Add the HTML labels
                if ( $form[ $name ]['isButton'] === false && $form[ $name ]['type'] != 'hidden' ) {
                    $form[ $name ]['label_html'] = '';
                    if ( ! empty( $form[ $name ]['label'] ) ) {
                        $form[ $name ]['label_html'] .= $form[ $name ]['label'];
                    }
                    $obj = $this->_form->getElement( $name );
                    if ( $form[ $name ]['required'] && ( ! isset( $obj->_options['mandatory'] ) OR $obj->_options['mandatory'] == true ) ) {
                        $form[ $name ]['label_html'] = $this->_form->_htmlRequiredStart . $form[ $name ]['label_html'] . $this->_form->_htmlRequiredEnd;
                    }
                    if ( ! empty( $form[ $name ]['error'] ) ) {
                        $form[ $name ]['error_html'] = $this->_form->_htmlErrorStart . $form[ $name ]['error'] . $this->_form->_htmlErrorEnd;
                    }
                }

            }

            // Add the do parameter if it's a get form
            if ( $this->_form->_method == 'get' ) {
                $form[YD_ACTION_PARAM] = array();
                $form[YD_ACTION_PARAM]['name'] = YD_ACTION_PARAM;
                $form[YD_ACTION_PARAM]['value'] = YDRequest::getActionName();
                $form[YD_ACTION_PARAM]['type'] = 'hidden';
                $form[YD_ACTION_PARAM]['label'] = '';
                $form[YD_ACTION_PARAM]['options'] = array();
                $form[YD_ACTION_PARAM]['placeLabel'] = 'before';
                $form[YD_ACTION_PARAM]['html'] = '<input type="hidden" name="' . YD_ACTION_PARAM . '" value="' . YDRequest::getActionName() . '">';
                $form[YD_ACTION_PARAM]['isButton'] = false;
                $form[YD_ACTION_PARAM]['error'] = '';
                $form[YD_ACTION_PARAM]['required'] = false;
            }

            // Return the form array
            return $form;

        }

    }

?>