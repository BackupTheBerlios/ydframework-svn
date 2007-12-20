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

    /**
     *  @addtogroup YDForm Core - Form
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDForm.php');


    /**
     *  This config defines the start tag to show before render an element
     *  Default: '<p>'
     */
    YDConfig::set( 'YD_RENDER_HTML_PRETAG', "<p>", false );


    /**
     *  This config defines the end tag to show after render an element
     *  Default: '<p>'
     */
    YDConfig::set( 'YD_RENDER_HTML_POSTAG', "</p>", false );


    /**
     *  This is the class that is able to render a form object to HTML.
     *
     *  @ingroup YDForm
     */
    class YDFormRenderer_html extends YDFormRenderer {

        /**
         *        This is the class constructor for the YDFormRenderer_html class.
         *
         *        @param $form                The form that needs to be rendered.
         */
        function YDFormRenderer_html( $form ) {

            // Initialize the parent
            $this->YDFormRenderer( $form );

        }

        /**
         *        This function will render the form.
         *
         *        @returns        The rendered form.
         */
        function render() {

            // Get the form as an array
            $form = $this->_form->toArray();

            // Start with the form element
            $html = $form['tag'] . YD_CRLF;

            // Add form errors if any
            if ( isset( $form['errors']['__ALL__'] ) ) {
                $html .= '<p>' . $this->_form->_htmlErrorStart . $form['errors']['__ALL__'] . $this->_form->_htmlErrorEnd . '</p>' . YD_CRLF;
            }

            // Store the HTML end
            $html_end = $form['endtag'];

            // Remove some things from the array
            unset( $form['attribs'] );
            unset( $form['tag'] );
            unset( $form['endtag'] );
            unset( $form['errors'] );
            unset( $form['errors_unique_messages'] );
            unset( $form['requirednote'] );
            unset( $form['legend'] );

            // Remove the form name
            if ( ! is_array( $form['name'] ) ) {
                unset( $form['name'] );
            }

            // Add the required note if there are required items
            if ( ! empty( $this->_form->_requiredNote ) ) {
                $reqCount = 0;
                foreach ( $form as $name=>$element ) {
                    if ( $element['required'] ) { $reqCount++; };
                }
                if ( $reqCount > 0 ) {
                    $html .= '<p>' . $this->_form->_requiredNote . '</p>' . YD_CRLF;
                }
            }

            // Add the elements
            foreach ( $form as $name=>$element ) {
                if ( $element['isButton'] === true ) {
                    continue;
                }
                if ( $element['type'] == 'hidden' ) {
                    $html .= $element['html'] . YD_CRLF;
                }
                elseif ( $element['type'] == 'fieldset' ) {

                    // if we had already a fieldset, we must close tag
                    if ( isset( $this->_fsets ) ){
                        $html .= '</fieldset>';
                    }

                    // add fieldset start and set fieldsets in use
                    $html .= '<fieldset>' . $element['html'];
                    $this->_fsets = true;
                } else {

                    // add '<p>' tag
                    $html .= YDConfig::get( 'YD_RENDER_HTML_PRETAG' );

                    if ( $element['placeLabel'] == 'after' ) {
                        $html .= $element['html'] . YD_CRLF . $element['label_html'];
                        if ( ! empty( $element['error'] ) ) {
                            $html .= YD_CRLF . '<br />' . $element['error_html'];
                        }
                    } else if ( $element['placeLabel'] == 'before' ) {
                        $html .= $element['label_html'];
                        if ( ! empty( $element['label'] ) ) {
                            $html .= '<br />';
                        }
                        $html .= YD_CRLF;
                        if ( ! empty( $element['error'] ) ) {
                            $html .= $element['error_html'] . '<br />' . YD_CRLF;
                        }
                        $html .= $element['html'];
                    } else {
                        if ( ! empty( $element['error'] ) ) {
                            $html .= $element['error_html'] . '<br />' . YD_CRLF;
                        }
                        $html .= $element['html'];
                    }

                    // add help
                    $html .= ' ' . $element['help'];

                    // add '</p>' tag
                    $html .= YDConfig::get( 'YD_RENDER_HTML_POSTAG' );

                    $html .= YD_CRLF;
                }
            }

            // Close last fieldset, if exist
            if ( isset( $this->_fsets ) ){
                $html .= '</fieldset>';
            }

            // Add the buttons
            $buttons = array();
            foreach ( $form as $name=>$element ) {
                if ( $element['isButton'] === true ) {
                    array_push( $buttons, $element['html'] );
                }
            }
            if ( sizeof( $buttons ) > 0 ) {
                $html .= '<p>' . implode( '&nbsp;', $buttons ) . '</p>' . YD_CRLF;
            }

            // Close the form tag
            $html .= $html_end;

            // Return the HTML
            return $html;

        }

    }

?>