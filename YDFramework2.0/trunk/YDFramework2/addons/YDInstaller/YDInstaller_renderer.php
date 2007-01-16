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
    include_once( YD_DIR_HOME_CLS . '/YDForm.php' );

    /**
     *        This is the class that is able to render a form object to HTML.
     */
    class YDInstaller_renderer extends YDFormRenderer {

        /**
         *        This is the class constructor for the YDFormRenderer_html class.
         *
         *        @param $form                The form that needs to be rendered.
         */
        function YDInstaller_renderer( $form ) {

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
            
            // Get all unique errors
            /*
            if ( sizeof( $form['errors'] ) ) {
                $html .= '<p class="error">';
                foreach ( $form['errors'] as $error ) {
                    $html .= '&middot ' . $error . '<br />' . YD_CRLF;
                }
                $html .= '</p>' . YD_CRLF;
            }*/
            
            // Remove some things from the array
            unset( $form['attribs'] );
            unset( $form['tag'] );
            unset( $form['endtag'] );
            unset( $form['errors'] );
            unset( $form['errors_unique_messages'] );
            unset( $form['requirednote'] );
            unset( $form['name'] );

            // Add the elements
            
            $html .= '<table width="700" cellspacing="0" cellpadding="0" border="0">';
            
            $buttons = array();
            foreach ( $form as $name=>$element ) {
               
                if ( $element['isButton'] === true ) {
                    $buttons[] = $element['html'];
                    continue;
                }
                
                if ( $element['type'] == 'hidden' ) {
                
                    $html .= $element['html'] . YD_CRLF;
                
                } else {
                    
                    $html .= '<tr >' . YD_CRLF;
                    
                    if ( $element['type'] == 'span' || $element['type'] == 'div' ) {
                    
                        $html .= '<td class="' . $element['type'] . '" colspan="2">';
                        $html .= $element['html'];
                        $html .= '</td>' . YD_CRLF;
                        
                    } else if ( $element['type'] == 'textarea' || $element['type'] == 'textareacounter' ) {
                        
                        $html .= '<td class="textarea" colspan="2">';
                        if ( ! empty( $element['label'] ) ) {
                            $html .= $element['label_html'] . '<br />';
                        }
                        $html .= $element['html'];
                        $html .= '</td>' . YD_CRLF;
                        

                    } else {
                    
                        switch ( $element['type'] ) {
                            case 'password':
                                $class = 'text'; break;
                            case 'reset':
                                $class = 'button'; break;
                            case 'date':
                            case 'dateselect':
                            case 'datetimeselect':
                            case 'timeselect':
                                $class = 'select'; break;
                            case 'textarea':
                            case 'textareacounter':
                                $class = 'textarea'; break;
                            default:
                                $class = $element['type'];
                        }
                        
                        if ( $element['placeLabel'] == 'before' ) {
                        
                            $html .= '<td class="left" width="300">';
                            $html .= $element['label_html'];
                            $html .= '</td>' . YD_CRLF;
                            
                            $html .= '<td class="right ' . $class . '" width="400">';
                            $html .= $element['html'];
                            $html .= '</td>' . YD_CRLF;
                            
                        } else {
                            
                            $html .= '<td class="left ' . $class . '" colspan="2">';
                            $html .= $element['html'];
                            
                            if ( $element['placeLabel'] == 'after' ) {
                                $html .= ' ' . $element['label_html'];
                            }
                            
                            $html .= '</td>' . YD_CRLF;
                            
                        } 
                        
                    }
                    
                    $html .= '</tr>' . YD_CRLF;
                }
            }
            
            // Add the buttons
            
            if ( sizeof( $buttons ) > 0 ) {
                
                $html .= '<tr><td colspan="2">&nbsp;</td></tr>' . YD_CRLF;
                
                $html .= '<tr>' . YD_CRLF;
                    
                $html .= '<td width="700" colspan="2">';
                $html .= implode( ' ', $buttons );
                $html .= '</td>' . YD_CRLF;
                
                $html .= '</tr>' . YD_CRLF;
                
            }
            
            $html .= '</table>' . YD_CRLF;
            $html .= '</form>' . YD_CRLF;

            return $html;

        }

    }

?>