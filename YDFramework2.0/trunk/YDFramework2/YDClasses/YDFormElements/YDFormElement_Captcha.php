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
    include_once( YD_DIR_HOME . '/3rdparty/captcha/php-captcha.inc.php' );

    /**
     *	This is the class that define a captcha form element.
     */
    class YDFormElement_Captcha extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_Captcha class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) The options for the elment.
         */
        function YDFormElement_Captcha( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // init parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // set type
            $this->_type = 'captcha';
			
			// create default image url
			$this->_url = YDRequest::getCurrentUrl( true ) . '?do=ShowCaptcha&id=' . md5( microtime() );

			// text box position
			$this->_textPosition_left = false;
			
			// refresh button. not added by default
			$this->_button = null;
        }


        /**
         *	This function set text element position
         *
         *	@param $onleft		Boolean that defines if text is before image (TRUE: on left) or after image (FALSE: on right)
         */
        function setTextPosition( $onleft = true ) {
			$this->_textPosition_left = $onleft;
		}


        /**
         *	This function adds a button to renew the image. Useful when image is not clear.
         */
        function & addRefreshButton( $caption = null ) {

			if ( ! is_string( $caption ) ) $caption = 'Get another image';

			include_once( YD_DIR_HOME_CLS . '/YDFormElements/YDFormElement_Button.php');

			$this->_button = new YDFormElement_Button( $this->_form, $this->_name . '_refreshbutton', $caption );
			$this->_button->setAttribute( 'onclick', "document.getElementById('" . $this->getAttribute( 'id' ) . "_captcha').src = document.getElementById('" . $this->getAttribute( 'id' ) . "_captcha').src.split('&id=')[0] + '&id=' + Math.random();" );
			$this->_button->setAttribute( 'style',   "vertical-align: middle" );

			return $this->_button;
		}


        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array(
                'type' => 'text', 'name' => $this->_form . '_' . $this->_name, 'value' => $this->_value, 'size' => 7
            );
            $attribs = array_merge( $this->_attributes, $attribs );

            // Get the HTML
			$img = '<img id="' . $this->getAttribute( 'id' ) . '_captcha" width="200" height="40" src="' . $this->_url . '" style="vertical-align: middle"/>';
			$txt = '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . ' />';

			if ( is_null( $this->_button ) ) $button = '';
			else                             $button = $this->_button->toHTML();

			if ( $this->_textPosition_left ){
				return $txt . ' ' . $img . ' ' . $button;
			}else{
				return $img . ' ' . $txt . ' ' . $button;
			}
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