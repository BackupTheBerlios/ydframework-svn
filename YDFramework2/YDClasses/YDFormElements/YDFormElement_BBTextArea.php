<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDFormElement.php' );

	class YDFormElement_BBTextArea extends YDFormElement {

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

		function toHtml() {

			// Create the list of attributes
			$attribs = array(
				'name' => $this->_form . '_' . $this->_name,
				'id' => $this->_form . '_' . $this->_name,
				'class' => 'bbtextarea',
			);
			if ( ! isset( $attribs['width'] ) ) { $attribs['width'] = '640'; }
			$attribs = array_merge( $this->_attributes, $attribs );

			// Get the HTML
			$out = '';
			if ( sizeof( $this->_buttons ) > 0 ) {
				if ( ! defined( 'YD_BBTA_MAINSCRIPT' ) ) {
					$out .= '<script language="JavaScript">';
					$out .= '	function AddText( element, startTag, defaultText, endTag ) {';
					$out .= '		objElement = document.getElementById( element );';
					$out .= '		if ( objElement.createTextRange ) {';
					$out .= '			var text;';
					$out .= '			objElement.focus( objElement.caretPos);';
					$out .= '			objElement.caretPos = document.selection.createRange().duplicate();';
					$out .= '			if ( objElement.caretPos.text.length > 0 ) {' . "\n";
					$out .= '				objElement.caretPos.text = startTag + objElement.caretPos.text + endTag;';
					$out .= '			} else {';
					$out .= '				objElement.caretPos.text = startTag + defaultText + endTag;';
					$out .= '			}';
					$out .= '		} else {';
					$out .= '			objElement.value += startTag + defaultText + endTag;';
					$out .= '		}';
					$out .= '	}';
					$out .= '	function openWin( url, name, opts ) {';
					$out .= '		window.open( url, name, opts );';
					$out .= '	}';
					$out .= '</script>';
					define( 'YD_BBTA_MAINSCRIPT', 1 );
				}
				$out .= '<script language="JavaScript">';
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
				$out .= '<table border="0" cellpadding="0" cellspacing="0" width="' . $attribs['width'] . '">';
				$out .= '<tr>';
				$out .= '<td class="bbtoolbar">';
				foreach ( $this->_buttons as $button ) {
					if ( $button['type'] == 'modifier' )  {
						$out .= '<a href="javascript:void( doButton( \'' . addslashes( $attribs['name'] ) . '\', \'' . addslashes( $button['name'] ) . '\') );">[ ' . $button['label'] . ' ]</a> ';
					}
					if ( $button['type'] == 'simplepopup' )  {
						$out .= '<a href="javascript:void( doButton( \'' . addslashes( $attribs['name'] ) . '\', \'' . addslashes( $button['name'] ) . '\') );">[ ' . $button['label'] . ' ]</a> ';
					}
					if ( $button['type'] == 'popupwindow' )  {
						$out .= '<a href="javascript:void( openWin( \'' . addslashes( $button['url'] ) . '\', \'' . addslashes( $button['name'] ) . '\', \'' . addslashes( $button['params'] ) . '\' ) );">[ ' . $button['label'] . ' ]</a> ';
					}
				}
				$out .= '</td>';
				$out .= '</tr>';
				$out .= '<tr>';
				$out .= '<td><textarea' . YDForm2::_convertToHtmlAttrib( $attribs ) . '>' . $this->_value . '</textarea></td>';
				$out .= '</tr>';
				$out .= '</table>';
			} else {
				$out .= '<textarea' . YDForm2::_convertToHtmlAttrib( $attribs ) . '>' . $this->_name . '</textarea>';
			}
			return $out;

		}

	}

?>