<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDFormElement.php' );

	/**
	 *	@todo
	 *		We need a way to turn on a checkbox by default.
	 */
	class YDFormElement_Checkbox extends YDFormElement {

		function YDFormElement_Checkbox( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// Set the type
			$this->_type = 'checkbox';

			// Fix the value setting
			if ( ! empty( $this->_value ) ) {
				$this->_value = 'on';
			}

			// If options is a string, it overrides the value
			if ( is_string( $options ) ) {
				$this->_value = 'on';
			}

			// Indicate if filters need to be applied
			$this->_applyFilters = false;

			// Indicate that the label should be appended
			$this->_labelPlace = 'after';

		}

		function toHtml() {

			// Create the list of attributes
			$attribs = array(  'type' => $this->_type, 'name' => $this->_form . '_' . $this->_name );
			$attribs = array_merge( $this->_attributes, $attribs );

			// If a value, fill it in and make it checked
			if ( ! empty( $this->_value ) ) {
				$attribs['checked'] = '';
			}

			// Get the HTML
			return '<input' . YDForm2::_convertToHtmlAttrib( $attribs ) . '>';

		}

	}

?>