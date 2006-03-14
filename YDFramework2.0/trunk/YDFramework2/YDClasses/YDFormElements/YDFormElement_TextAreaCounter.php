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
    include_once( YD_DIR_HOME_CLS . '/YDForm.php' );
    include_once( 'YDFormElement_Span.php' );

    /**
     *	This is the class that define a text area form element.
     */
    class YDFormElement_TextAreaCounter extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_TextArea class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) The options for the elment.
         */
        function YDFormElement_TextAreaCounter( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Create initial options if necessary
            if ( ! isset( $options['maxlength'] ) ) {
                $options['maxlength'] = 225;
            }
            if ( ! isset( $options['before'] ) ) {
                $options['before'] = ' (';
            }
            if ( ! isset( $options['after'] ) ) {
                $options['after'] = ')';
            }
            if ( ! isset( $options['attributes'] ) ) {
                $options['attributes'] = array();
            }
            if ( ! isset( $options['options'] ) ) {
                $options['options'] = array();
            }
        
            // Add onkeydown and onkeyup attributes
            $attributes['onkeydown'] = 'textCounter( \'' . $form . '_' . $name . '\', ' . $options['maxlength'] . ' )';
            $attributes['onkeyup'] = $attributes['onkeydown'];
            
            // Span counter
            $span = new YDFormElement_Span( $form, $name . '_counter', '', $options['attributes'], $options['options'] );
            $span->setValue( $options['maxlength'] );
            
            // Add counter span to the label
            $label .= $options['before'] . $span->toHtml() . $options['after'];
            
            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );
            
            // Set the type
            $this->_type = 'textareacounter';

        }

        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {
            
            $out = '';
        
            if ( ! defined( 'YD_CTTA_MAINSCRIPT' ) ) {
                $out .= '<script language="JavaScript">';
                $out .= 'function textCounter( field, maxlimit ) {';
                $out .= "  var f = document.getElementById( field );";
                $out .= "  var c = document.getElementById( field + '_counter' );";
                $out .= '  if ( f.value.length > maxlimit )';
                $out .= '    f.value = f.value.substring( 0, maxlimit );';
                $out .= '  else ';
                $out .= '    c.innerHTML = maxlimit - f.value.length;';
                $out .= '}';
                $out .= '</script>';
                define( 'YD_CTTA_MAINSCRIPT', 1 );
            }

            // Create the list of attributes
            $attribs = array( 'name' => $this->_form . '_' . $this->_name );
            $attribs = array_merge( $this->_attributes, $attribs );

            // Get the HTML
            $out .= '<textarea' . YDForm::_convertToHtmlAttrib( $attribs ) . '>' . $this->_value . '</textarea>';
            
            return $out;

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

             // if atribute is not defined we must create the default one
             if ( is_null( $attribute ) ) $attribute = 'value';

             // convert $result
             $result = htmlspecialchars( $result );

             // assign result
             return 'document.getElementById("' . $this->getAttribute( 'id' ) . '").' . $attribute . ' = "' . $result . '";';
        }



    }

?>