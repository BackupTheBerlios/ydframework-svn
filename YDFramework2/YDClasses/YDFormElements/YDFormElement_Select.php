<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDFormElement.php' );

	/**
	 *	This is the class that define a select button form element.
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
		 *	This function will return the element as HTML.
		 *
		 *	@returns	The form element as HTML text.
		 */
		function toHtml() {

			// Create the list of attributes
			$attribs = array( 'name' => $this->_form . '_' . $this->_name );
			$attribs = array_merge( $this->_attributes, $attribs );

			// Get the HTML
			$html = '';
			$html .= '<select' . YDForm::_convertToHtmlAttrib( $attribs ) . '>';
			foreach ( $this->_options as $val=>$label ) {
				if ( strval( $this->_value ) == strval( $val ) ) {
					$html .= '<option value="' . $val . '" selected>' . $label . '</option>';
				} else {
					$html .= '<option value="' . $val . '">' . $label . '</option>';
				}
			}
			$html .= '</select>';
			return $html;

		}

	}

?>