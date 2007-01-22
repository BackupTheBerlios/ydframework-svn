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
     *	This is the class that define a textarea form element that has support for a toolbar which can insert BBCode
     *	style tags to the content.
     *
     *  @ingroup YDForm
     */
    class YDFormElement_BBTextArea extends YDFormElement {

        /**
         *	This is the class constructor for the YDFormElement_BBTextArea class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) The options for the elment.
         */
        function YDFormElement_BBTextArea( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'bbtextarea';

            // The list of buttons
            $this->_buttons = array();

            // Add some default buttons
            $this->addModifier( 'b', 'bold' );
            $this->addModifier( 'i', 'italic' );
            $this->addModifier( 'u', 'underline' );
            $this->addSimplePopup( 'url', 'url', 'Enter the url:', 'http://' );

        }

        /**
         *	This function will add a modifier.
         *
         *	@param $name	The name of the modifier.
         *	@param $label	The label of the modifier.
         *	@param $text	(optional) The initial text for the modifier.
         */
        function addModifier( $name, $label, $text='' ) {
            $attrib = array();
            $attrib['name'] = $name;
            $attrib['type'] = 'modifier';
            $attrib['label'] = $label;
            $attrib['text'] = $text;
            array_push( $this->_buttons, $attrib );
        }

        /**
         *	This function will add a simple popup window.
         *
         *	@param $name		The name of the modifier.
         *	@param $label		The label of the simple popup.
         *	@param $question	The question for in the popup window.
         *	@param $default		(optional) The default text for in the popup window.
         *	@param $text		(optional) The initial text for the modifier.
         */
        function addSimplePopup( $name, $label, $question, $default='', $text='' ) {
            $attrib = array();
            $attrib['name'] = $name;
            $attrib['type'] = 'simplepopup';
            $attrib['label'] = $label;
            $attrib['question'] = $question;
            $attrib['default'] = $default;
            $attrib['text'] = $text;
            array_push( $this->_buttons, $attrib );
        }

        /**
         *	This function will add a popup window. This is a popup window that
         *	will point to a URL. This window can do whatever it wants and can
         *	change values in the main window.
         *
         *	@param $url		The url for the popup window.
         *	@param $label	The label of the popup window.
         *	@param $name	(optional) The JavaScript name of the popup window.
         *	@param $params	(optional) The JavaScript parameters for the popup window.
         */
        function addPopupWindow( $url, $label, $name='', $params='' ) {
            $attrib = array();
            $attrib['url'] = $url;
            $attrib['type'] = 'popupwindow';
            $attrib['label'] = $label;
            $attrib['name'] = $name;
            if ( empty( $params ) ) {
                $attrib['params'] = 'width=500,height=320,location=0,menubar=0,resizable=1,scrollbars=1,status=1,titlebar=1';
            } else {
                $attrib['params'] = $params;
            }
            array_push( $this->_buttons, $attrib );
        }

        /**
         *	This function will reset the toolbar and remove all the buttons.
         */
        function clearButtons() {
            $this->_buttons = array();
        }

        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Create the list of attributes
            $attribs = array(
                'name' => $this->_form . '_' . $this->_name,
                'class' => 'bbtextarea',
            );
            //if ( ! isset( $attribs['width'] ) ) { $attribs['width'] = '640'; }
            $attribs = array_merge( $this->_attributes, $attribs );
            
            // Get the HTML
            $out = '';
            if ( sizeof( $this->_buttons ) > 0 ) {
                if ( ! defined( 'YD_BBTA_MAINSCRIPT' ) ) {
                    $out .= '<script src="' . YD_SELF_SCRIPT . '?do=JsBBTextArea" type="text/javascript"></script>';
                    define( 'YD_BBTA_MAINSCRIPT', 1 );
                }
                $out .= '<script type="text/javascript">';
                $out .= '	function doButton( element, name ) {';
                foreach ( $this->_buttons as $button ) {
                    if ( $button['type'] == 'modifier' )  {
                        $out .= 'if ( name == \'' . addslashes( $button['name'] ) . '\' ) {';
                        $out .= '	AddText ( element, \'[' . addslashes( $button['name'] ) . ']\',\'' . addslashes( $button['text'] ) . '\',\'[/' . addslashes( $button['name'] ) . ']\');';
                        $out .= '}';
                    }
                    if ( $button['type'] == 'simplepopup' )  {
                        $out .= 'if ( name == \'' . addslashes( $button['name'] ) . '\' ) {';
                        $out .= '	data = prompt( "' . addslashes( $button['question'] ) . '", "' . addslashes( $button['default'] ) . '" );';
                        $out .= '	if ( data == null ) return;';
                        $out .= '	AddText( element, \'[' . addslashes( $button['name'] ) . '=\' + data + \']\',\'' . addslashes( $button['text'] ) . '\',\'[/' . addslashes( $button['name'] ) . ']\');';
                        $out .= '}';
                    }
                }
                $out .= '	}';
                $out .= '</script>';
                if ( isset( $attribs['width'] ) ) {
                    $out .= '<table border="0" cellpadding="0" cellspacing="0" width="' . $attribs['width'] . '">';
                } else {
                    $out .= '<table border="0" cellpadding="0" cellspacing="0">';
                }
                $out .= '<tr>';
                $out .= '<td class="bbtoolbar">';
                foreach ( $this->_buttons as $button ) {
                    if ( $button['type'] == 'modifier' )  {
                        $out .= '<a href="#" onclick="void( doButton( \'' . addslashes( $attribs['id'] ) . '\', \'' . addslashes( $button['name'] ) . '\') ); return false;">[ ' . $button['label'] . ' ]</a> ';
                    }
                    if ( $button['type'] == 'simplepopup' )  {
                        $out .= '<a href="#" onclick="void( doButton( \'' . addslashes( $attribs['id'] ) . '\', \'' . addslashes( $button['name'] ) . '\') ); return false;">[ ' . $button['label'] . ' ]</a> ';
                    }
                    if ( $button['type'] == 'popupwindow' )  {
                        $out .= '<a href="#" onclick="void( openWin( \'' . addslashes( $button['url'] ) . '\', \'' . addslashes( $button['name'] ) . '\', \'' . addslashes( $button['params'] ) . '\' ) ); return false;">[ ' . $button['label'] . ' ]</a> ';
                    }
                }
                $out .= '</td>';
                $out .= '</tr>';
                $out .= '<tr>';
                $out .= '<td><textarea' . YDForm::_convertToHtmlAttrib( $attribs ) . '>' . $this->_value . '</textarea></td>';
                $out .= '</tr>';
                $out .= '</table>';
            } else {
                $out .= '<textarea' . YDForm::_convertToHtmlAttrib( $attribs ) . '>' . $this->_value . '</textarea>';
            }
            return $out;

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