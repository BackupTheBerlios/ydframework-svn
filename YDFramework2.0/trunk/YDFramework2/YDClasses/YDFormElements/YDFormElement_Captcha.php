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
    include_once( YD_DIR_HOME . '/3rdparty/captcha/php-captcha.inc.php' );
    include_once( YD_DIR_HOME_CLS . '/YDFormElements/YDFormElement_Img.php');

    /**
     *	This is the class that define a captcha form element.
     *
     *  @ingroup YDForm
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

            // refresh button or refresh image. not added by default
            $this->_button = null;
            $this->_refreshimage = false;
            $this->_refreshcaption = 'Get another image';

            // add refresh button
            if ( isset( $options[ 'refreshbutton' ] ) || in_array( 'refreshbutton', $options ) )
                $this->addRefreshButton();

            if ( isset( $options[ 'refreshimage' ] ) || in_array( 'refreshimage', $options ) )
                $this->addRefreshImage();

            if ( isset( $options[ 'refreshcaption' ] ) )
                $this->_refreshcaption = $options[ 'refreshcaption' ];

            // compute image
            $this->img = new YDFormElement_Img( $form, $name . 'captcha', $this->_url, array( 'width' => 200, 'height' => 40 ) );
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
         *	This function sets the button caption and the image caption
         *
         *  @param $caption  The string to display in the button.
         */
        function setRefreshCaption( $caption ) {
            $this->_refreshcaption = $caption;
        }


        /**
         *	This function adds a button to renew the image. Useful when image is not clear.
         *
         *  @returns    The button object.
         */
        function & addRefreshButton() {

			include_once( YD_DIR_HOME_CLS . '/YDFormElements/YDFormElement_Button.php');

			$this->_button = new YDFormElement_Button( $this->_form, $this->_name . '_refreshbutton', $this->_refreshcaption );
			$this->_button->setAttribute( 'style',   "vertical-align: middle" );

			return $this->_button;
		}


        /**
         *	This function makes the image clicable to get a new one.
         */
        function addRefreshImage() {
            $this->_refreshimage = true;
        }


        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            $size = YDConfig::get( 'YD_CAPTCHA_NUMCHARS', 5 );

            // Create the list of attributes
            $attribs = array(
                'type' => 'text', 'name' => $this->_form . '_' . $this->_name, 'value' => $this->_value, 'size' => $size, 'maxlength' => $size 
            );
            $attribs = array_merge( $this->_attributes, $attribs );


            // add image auto-refresh
            if ( $this->_refreshimage == true ){
                $this->img->setAttribute( 'onclick', "document.getElementById('" . $this->img->getAttribute( 'id' ) . "').src = document.getElementById('" . $this->img->getAttribute( 'id' ) . "').src.split('&id=')[0] + '&id=' + Math.random();" );
                $this->img->setAttribute( 'style',   "vertical-align: middle;cursor:pointer;" );
                $this->img->setAttribute( 'title',   $this->_refreshcaption );
            }else{
                $this->img->setAttribute( 'style',   "vertical-align: middle;" );
                $this->img->setAttribute( 'title',   $this->_label );
            }

            // compute text box html
            $txt_HTML = '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . ' />';

            // compute refresh button html
            if ( is_null( $this->_button ) ){
                $button_HTML = '';
            }else{
                $this->_button->setAttribute( 'onclick', "document.getElementById('" . $this->img->getAttribute( 'id' ) . "').src = document.getElementById('" . $this->img->getAttribute( 'id' ) . "').src.split('&id=')[0] + '&id=' + Math.random();" );
                $button_HTML = $this->_button->toHTML();
            }

            if ( $this->_textPosition_left ){
                return $txt_HTML . ' ' . $this->img->toHTML() . ' ' . $button_HTML;
            }else{
                return $this->img->toHTML() . ' ' . $txt_HTML . ' ' . $button_HTML;
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
        function setJS( $result = '', $attribute = null, $options = null ){ 

             // if atribute is not defined we must create the default one
             if ( is_null( $attribute ) ) $attribute = 'value';

             // convert $result
             $result = htmlspecialchars( $result );

             // assign result
             $js = '';

             // add text result, if exists
             if ( is_string( $result ) && $result != '' ){
                 $js .= 'document.getElementById("' . $this->getAttribute( 'id' ) . '").' . $attribute . ' = "' . $result . '";';
             }

             // set a new captcha image
             $js .= 'document.getElementById("' . $this->img->getAttribute( 'id' ) . '").src = "' . $this->_url . '";';

             return $js;
        }

    }

?>