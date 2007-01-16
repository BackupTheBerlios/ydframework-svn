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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDForm.php');

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

            // Start with an empty array
            $form = array();

            // Add the list of attributes
            $attribs = array(
                'name'		=> $this->_form->_name, 'id'		=> $this->_form->_name, 'method' => $this->_form->_method,
                'action'	=> $this->_form->_action, 'target'	=> $this->_form->_target
            );
            $attribs = array_merge( $attribs, $this->_form->_attributes );
            if ( $attribs['target'] == '_self' ) {
                unset( $attribs['target'] );
            }

            // Add the rest of the form attributes
            $form['attribs'] = $this->_form->_convertToHtmlAttrib( $attribs );
            $form['tag'] = '<form' . $form['attribs'] . '>';
            $form['requirednote'] = $this->_form->_requiredNote;
            $form['endtag'] = '</form>';
            $form['name']   = $this->_form->_name;
            $form['legend'] = $this->_form->_legend;

            // Add the fieldset and legend tag if any
            if ( ! empty( $this->_form->_legend ) ) {

                // Add it to the start tag
                $form['tag'] .= '<fieldset><legend>' . $this->_form->_legend . '</legend>';

                // Add it to the end tag
                $form['endtag'] = '</fieldset>' . $form['endtag'];

            }

            // Add the errors
            $form['errors'] = $this->_form->_errors;
            $form['errors_unique_messages' ] = array_unique( array_values( $this->_form->_errors ) );

            // Loop over the list of elements
            foreach ( $this->_form->_elements as $name => $element ) {

                // Update the value
                $element->_value = $this->_form->getValue( $name );

                // Add the hidden_html value
                $elementArray = $element->toArray();
                $attribs = array(
                    'type' => 'hidden', 'name' => $elementArray['id'], 'value' => $elementArray['value']
                );
                $elementArray['hidden_html'] = '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . ' />';

                // Add the form element
                $form[ $name ] = $elementArray;

                // Add errors if any
                if ( array_key_exists( $name, $this->_form->_errors ) ) {
                    $form[ $name ]['error'] = $this->_form->_errors[ $name ];
                } else {
                    $form[ $name ]['error'] = '';
                }

                // Check if the field is required
                $required = false;
                if ( array_key_exists( $name, $this->_form->_rules ) ) {
                    foreach ( $this->_form->_rules[ $name ] as $rules ) {
                        if ( strtolower( $rules['rule'] ) == 'required' ) {
                            $required = true;
                            break;
                        }
                    }
                }
                $form[ $name ]['required'] = $required;

                // Add required and error HTML
                if ( $form[ $name ]['isButton'] === false && $form[ $name ]['placeLabel'] != 'none' ) {
                    if ( $form[ $name ]['required'] ) {
                        $form[ $name ]['label_html'] = $this->_form->_htmlRequiredStart . $form[ $name ]['label_html'] . $this->_form->_htmlRequiredEnd;
                    }
                    if ( ! empty( $form[ $name ]['error'] ) ) {
                        $form[ $name ]['error_html'] = $this->_form->_htmlErrorStart . $form[ $name ]['error'] . $this->_form->_htmlErrorEnd;
                    }
                }

            }

            // Add the do parameter if it's a get form
            if ( $this->_form->_method == 'get' ) {
                $form['do'] = array();
                $form['do']['name'] = 'do';
                $form['do']['id'] = 'do';
                $form['do']['value'] = YDRequest::getActionName();
                $form['do']['type'] = 'hidden';
                $form['do']['label'] = '';
                $form['do']['label_html'] = '';
                $form['do']['options'] = array();
                $form['do']['placeLabel'] = 'none';
                $form['do']['html'] = '<input type="hidden" name="do" id="do" value="' . YDRequest::getActionName() . '" />';
                $form['do']['isButton'] = false;
                $form['do']['error'] = '';
                $form['do']['required'] = false;
            }

            // Return the form array
            return $form;

        }

    }

?>