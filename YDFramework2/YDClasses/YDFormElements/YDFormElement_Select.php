<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDFormElement.php' );

	class YDFormElement_Select extends YDFormElement {

		function YDFormElement_Select( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// Set the type
			$this->_type = 'select';

			// Indicate if filters need to be applied
			$this->_applyFilters = false;

		}

		function toHtml() {

			// Create the list of attributes
			$attribs = array( 'name' => $this->_form . '_' . $this->_name );

			// Get the HTML
			$html = '';
			$html .= '<select' . YDForm2::_convertToHtmlAttrib( $attribs ) . '>';
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