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
    class YDFormElement_SelectImage extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_SelectImage class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) Associative array with the values to show in the select box.
         */
        function YDFormElement_SelectImage( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// compute image parameters
			$this->_image_parameters = array();

			// check if src exist
			if ( isset( $attributes['src'] ) ){
				$this->_img_src = $attributes['src'] . '/';
				unset( $attributes['src'] );
			}

			// create default extension
			$this->_img_ext = "";

			// check if ext exist
			if ( isset( $attributes['ext'] ) ){
				$this->_img_ext = $attributes['ext'];
				unset( $attributes['ext'] );
			}

			// check if width exist
			if ( isset( $attributes['width'] ) ){
				$this->_image_parameters['width'] = $attributes['width'];
				unset( $attributes['width'] );
			}

			// check if height exist
			if ( isset( $attributes['height'] ) ){
				$this->_image_parameters['height'] = $attributes['height'];
				unset( $attributes['height'] );
			}

			// check if border exist
			if ( isset( $attributes['border'] ) ){
				$this->_image_parameters['border'] = $attributes['border'];
				unset( $attributes['border'] );
			}

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'selectimage';

            // Indicate if filters need to be applied
            $this->_applyFilters = false;

            // If options is just an element we should add create an array with it
            if( !is_array( $options ) ) $this->_options = array( $options );
        }


        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array( 'name' => $this->_form . '_' . $this->_name, 'onchange' => "document.getElementById('" . $this->getAttribute( 'id' ) . "_image').src='" . $this->_img_src . "' + document.getElementById('" . $this->getAttribute( 'id' ) . "').options[this.selectedIndex].value + '" . $this->_img_ext . "';" );
            $attribs = array_merge( $this->_attributes, $attribs );

            // Start html table and insert select in left column
            $html = '<table border="0" cellspacing="0" cellpadding="0"><tr><td>';

			// add select
            $html .= '<select' . YDForm::_convertToHtmlAttrib( $attribs ) . '>';
            foreach ( $this->_options as $val=>$label ) {
                if ( strval( $this->_value ) == strval( $val ) ) {
                    $html .= '<option value="' . $val . '" selected="selected">' . $label . '</option>';
                } else {
                    $html .= '<option value="' . $val . '">' . $label . '</option>';
                }
            }

			// end select and left column
            $html .= '</select></td>';

			// compute image source
			$source = $this->_img_src;

			// compute selected option. If default value was set and that value is valid, use it. Otherwise use 1st option if exist
			if ( ! empty( $this->_value ) && isset( $this->_options[ $this->_value ] ) ){
				$source .= $this->_value;
			}else{
				$values  = array_keys( $this->_options );
				$source .= isset( $values[0] ) ? $values[0] : '';
			}

			// add extension
			$source .= $this->_img_ext;

            // Create the list of attributes for image
            $attribs = array( 'id' => $this->getAttribute( 'id' ) . '_image', 'src' => $source );
            $attribs = array_merge( $this->_image_parameters, $attribs );

			// add image html to right column and close table row
			$html .= '<td>&nbsp;&nbsp;&nbsp;<img' . YDForm::_convertToHtmlAttrib( $attribs ) . '></td></tr></table>';

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