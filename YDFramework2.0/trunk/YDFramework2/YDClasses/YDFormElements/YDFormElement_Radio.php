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
     *	This is the class that define a radio button form element.
     *
     *  @ingroup YDForm
     */
    class YDFormElement_Radio extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_Radio class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) An associative array with the values and labels for the different radio
         *						buttons.
         */
        function YDFormElement_Radio( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // set default separator
            $this->_separator = '';

            // parse separator
            if ( isset ( $attributes['sep'] ) && $attributes['sep'] == 'v' ) {
                $this->_separator = '<br />';
                unset( $attributes[ 'sep' ] );
            }

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'radio';

            // Indicate if filters need to be applied
            $this->_applyFilters = false;

            // set if we want to use LABEL for each option label or just a just a simple string
            $this->_enableLabels = true;
        }


        /**
         *	This function sets if we want default LABEL element for each option.
         */
        function enableLabels( $flag ){

            $this->_enableLabels = $flag;
        }


        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array(
                'type' => $this->_type, 'name' => $this->_form . '_' . $this->_name, 'value' => $this->_value
            );
            $attribs = array_merge( $this->_attributes, $attribs );

            // Create the HTML
            $out = array();
            if ( sizeof( $this->_options ) > 0 ) {
                foreach ( $this->_options as $key=>$val ) {
                    $attribsElement = $attribs;
                    $attribsElement['value'] = $key;
                    $attribsElement['id'] .= $key;
                    if ( $this->_value == strval( $key ) ) {
                        $attribsElement['checked'] = 'checked';
                    }
                    $label = $this->_enableLabels ? '<label for="' . $attribsElement['id'] . '">' . $val . '</label>' : $val;
                    $out[] = '<input' . YDForm::_convertToHtmlAttrib( $attribsElement ) . ' /> ' . $label . ' ';
                }
            } else {
                $label = $this->_enableLabels ? '<label for="' . $attribs['id'] . '">' . $this->_value . '</label>' : $this->_value;
                $out[] = '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . ' /> ' . $label . ' ';
            }

            // Return the HTML
            return implode( $this->_separator, $out );

        }


        /**
         *	This function returns the default javascript event of this element
         */
        function getJSEvent(){ 

            return 'onchange';
        }


        /**
         *	This function gets an element value using javascript
         *
         *	@param $options		(optional) The options for the elment.
         */
        function getJS( $options = null ){ 

            // add custom js function
            $js  = "\n\t" . 'var __ydftmp = document.getElementById("' . $this->getAttribute('id') . '");';
            $js .= "\n\t" . 'for (counter = 0; counter < __ydftmp.length; counter++)';
            $js .= "\n\t" . '   if (__ydftmp[counter].checked) return __ydftmp[counter].value;';
            $js .= "\n\t" . 'return false;' . "\n";

            // return function code
            return $jscode;
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

            // check and convert $result
            $result = htmlspecialchars( $result );

            // we must cycle all radio instances of this radio element
            return '__ydftmp = document.getElementById("' . $this->getAttribute( 'id' ) . '");
                    for (counter = 0; counter < __ydftmp.length; counter++)
                        if (__ydftmp[counter].value == "' . $result . '") __ydftmp[counter].checked = true;
                        else __ydftmp[counter].checked = false;';
        }

    }

?>