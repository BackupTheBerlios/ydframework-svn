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
     *	This is the class that is able to render a form object to HTML.
     */
    class YDFormRenderer_html extends YDFormRenderer {

        /**
         *	This is the class constructor for the YDFormRenderer_html class.
         *
         *	@param $form		The form that needs to be rendered.
         */
        function YDFormRenderer_html( $form ) {

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
            $form = $this->_form->toArray();

            // Start with the form element
            $html = $form['tag'];

            // Add form errors if any
            if ( isset( $form['errors']['__ALL__'] ) ) {
                $html .= '<p>' . $this->_form->_htmlErrorStart . $form['errors']['__ALL__'] . $this->_form->_htmlErrorEnd . '</p>';
            }

            // Remove some things from the array
            unset( $form['attribs'] );
            unset( $form['tag'] );
            unset( $form['errors'] );
            unset( $form['errors_unique_messages'] );
            unset( $form['requirednote'] );

            // Add the required note if there are required items
            if ( ! empty( $this->_form->_requiredNote ) ) {
                $reqCount = 0;
                foreach ( $form as $name=>$element ) {
                    if ( $element['required'] ) { $reqCount++; };
                }
                if ( $reqCount > 0 ) {
                    $html .= '<p>' . $this->_form->_requiredNote . '</p>';
                }
            }

            // Add the elements
            foreach ( $form as $name=>$element ) {
                if ( $element['isButton'] === false ) {
                    if ( $element['type'] != 'hidden' ) {
                        $html .= '<p>';
                        if ( $element['placeLabel'] == 'after' ) {
                            $html .= $element['html'] . $element['label_html'];
                            if ( ! empty( $element['error'] ) ) {
                                $html .= '<br />' . $element['error_html'];
                            }
                        } else {
                            $html .= $element['label_html'];
                            if ( ! empty( $element['label'] ) ) {
                                $html .= '<br />';
                            }
                            if ( ! empty( $element['error'] ) ) {
                                $html .= $element['error_html'] . '<br />';
                            }
                            $html .= $element['html'];
                        }
                        $html .= '</p>';
                    } else {
                        $html .= $element['html'];
                    }
                }
            }

            // Add the buttons
            $buttons = array();
            foreach ( $form as $name=>$element ) {
                if ( $element['isButton'] === true ) {
                    array_push( $buttons, $element['html'] );
                }
            }
            if ( sizeof( $buttons ) > 0 ) {
                $html .= '<p>' . implode( '&nbsp;', $buttons ) . '</p>';
            }

            // Close the form tag
            $html .= '</form>';

            // Return the HTML
            return $html;

        }

    }

?>