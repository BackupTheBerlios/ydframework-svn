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
     *	This is the class that define a text form element.
     */
    class YDFormElement_Autocompleter extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_Autocompleter class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		The label for the form element.
         *	@param $attributes	The attributes for the form element.
         *	@param $options		The options for the elment.
         */
        function YDFormElement_Autocompleter( $form, $name, $label, $attributes, $options ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'autocompleter';

        }

        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array(
                'type' => 'text', 'name' => $this->_form . '_' . $this->_name, 'value' => $this->_value
            );
            $attribs = array_merge( $this->_attributes, $attribs );

            // trick to make this text box with the same width as autocompleter.
            // create a style array and a default width for text and div
            $style = array();
            $style['width'] = '143px';

            // if user has defined a custom style we must parse it to check if width was defined
            if (isset($attribs['style']))
                foreach (explode(";", $attribs['style'] ) as $att)
                    if (trim( $att ) != ''){
                        list($name, $value) =  split(":", $att);
                        $style[ strtolower($name) ] = trim( $value );
                    }

            // compute style attribute
            $attribs['style'] = '';
            foreach ($style as $name => $value)
                $attribs['style'] .= $name .':'. $value .';';

            // if is a autocompleter we must add an extra div. TODO: automagically apply width to text element and div
            return '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . ' /><div style="display:none; z-index:999;'. $attribs['style'] .'" id="' .  $attribs['name'] . '_div"><ul></ul></div>';

        }


        /**
         *	This function will return the ajax call of this element
         *
         *	@returns	YDAjax call array
         */
		function getAjaxCall(){
		
			return array( $this->_options[0], $this->_options[1] );
		}


        /**
         *	This function will return the ajax call arguments of this element
         *
         *	@returns	YDAjax call arguments array
         */
		function getAjaxArguments(){
			
			if (!isset( $this->_options[2] ) || ($this->_options[2] == '') || is_null( $this->_options[2] ) ) return $this->_name;
			
			return $this->_options[2];
		}


        /**
         *	This function will return the ajax call effects objects of this element
         *
         *	@returns	YDAjax call effects array
         */
		function getAjaxEffects(){

			if (isset( $this->_options[3] ) ) return $this->_options[3];
			
			return null;
		}


    }

?>