<?php

	/*
	
		Yellow Duck Framework version 2.0
		Copyright (C) (c) copyright 2004 Pieter Claerhout
	
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

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	YDInclude( 'YDForm.php' );

	/**
	 *	This is the class that define a radio button form element.
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

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// Set the type
			$this->_type = 'radio';

			// Indicate if filters need to be applied
			$this->_applyFilters = false;

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
			$out = '';
			if ( sizeof( $this->_options ) > 0 ) {
				foreach ( $this->_options as $key=>$val ) {
					$attribsElement = $attribs;
					$attribsElement['value'] = $key;
					if ( $this->_value == strval( $key ) ) {
						$attribsElement['checked'] = '';
					}
					$out .= '<input' . YDForm::_convertToHtmlAttrib( $attribsElement ) . '>' . $val;

				}
			} else {
				$out .= '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . '>' . $this->_value;
			}

			// Return the HTML
			return $out;

		}

	}

?>