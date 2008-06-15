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
     *	This is the class that define a select button form element.
     *
     *  @ingroup YDForm
     */
    class YDFormElement_Select extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_Select class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) Associative array with the values to show in the select box.
         */
        function YDFormElement_Select( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'select';

            // Indicate if filters need to be applied
            $this->_applyFilters = false;
        }


        /**
         *      This function returns the value of the element.
         *
         *      @returns        String value of this object.
         */
        function getValueAsString() {

            $values = array();

            $value  = $this->getValue();

            // parse location
            if( is_array( $value ) ){
                foreach( $value as $key => $value ){
                    $values[] = $value;
                }
            }

            return implode( ';', $values );
        }


        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // when using a multiple select, we should check if name ends with '[]'
            if( isset( $this->_attributes[ 'multiple' ] ) && strpos( $this->_name, '[]' ) === false ){
                $this->_name .= '[]';
            }

            // Create the list of attributes
            $attribs = array( 'name' => $this->_form . '_' . $this->_name );
            $attribs = array_merge( $this->_attributes, $attribs );

            // check if value is array ( used on multiple select ) or a simple value
            if ( ! is_array( $this->_value ) ) $this->_value = explode( ';', $this->_value );

            // Get the HTML
            $html = '';
            $html .= '<select' . YDForm::_convertToHtmlAttrib( $attribs ) . '>';
            foreach ( $this->_options as $val=>$label ) {
                if ( in_array( strval( $val ), $this->_value ) ) {
                    $html .= '<option value="' . $val . '" selected="selected">' . $label . '</option>';
                } else {
                    $html .= '<option value="' . $val . '">' . $label . '</option>';
                }
            }
            $html .= '</select>';
            return $html;

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

            // if we want all values and not only the select one
            if (in_array( 'all', $options )){

                $js  = "\n\t" . 'var __ydtmparr = new Array();';
                $js .= "\n\t" . 'var __ydtmpsel = document.getElementById("' . $this->getAttribute('id') . '");';
                $js .= "\n\t" . 'for (i = 0; i < __ydtmpsel.length; i++){';
                $js .= "\n\t" . '    __ydtmparr[ __ydtmpsel.options[i].value ] = __ydtmpsel.options[i].text;';
                $js .= "\n\t" . '}';
                $js .= "\n\t" . 'return __ydtmparr;' . "\n";

                return $js;
            }

            // return just the value 
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
            if ( is_null( $attribute ) ) $attribute = 'value';

            // create select variable
            $js = 'var __ydfselect = document.getElementById("' . $this->getAttribute('id') . '");';

            // if we want to define the selected option
            if ( !is_array( $result ) )
                return $js . 'for (counter = 0; counter < __ydfselect.length; counter++){
                                 if (__ydfselect[counter].value == "' . addslashes( $result ) . '"){
                                     __ydfselect.selectedIndex = counter;
                                 }
                              }';

            // if we want to replace all select options
            $js .= '__ydfselect.options.length = 0;';
            foreach( $result as $key => $value )
                $js .= '    __ydfselect.options[ __ydfselect.options.length  ] = new Option("' . addslashes( $value ) . '","' . addslashes( $key ) . '"); ';

            return $js;
        }


    }

?>