<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDFormElement.php' );

	class YDFormElement_Hidden extends YDFormElement {

		function YDFormElement_Hidden( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// Set the type
			$this->_type = 'hidden';

			// If options is a string, it overrides the value
			if ( is_string( $options ) ) {
				$this->_value = $options;
			}

			// Indicate if filters need to be applied
			$this->_applyFilters = false;

		}

		function toHtml() {

			// Create the list of attributes
			$attribs = array( 
				'type' => $this->_type, 'name' => $this->_form . '_' . $this->_name, 'value' => $this->_value 
			);
			$attribs = array_merge( $this->_attributes, $attribs );

			// Get the HTML
			return '<input' . YDForm2::_convertToHtmlAttrib( $attribs ) . '>';

		}

	}

?>