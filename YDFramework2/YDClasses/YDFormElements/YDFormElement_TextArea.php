<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );
	require_once( 'YDForm2.php' );
	require_once( 'YDFormElement.php' );

	class YDFormElement_TextArea extends YDFormElement {

		function YDFormElement_TextArea( $form, $name, $label, $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// Set the type
			$this->_type = 'textarea';

		}

		function toHtml() {

			// Create the list of attributes
			$attribs = array( 'name' => $this->_form . '_' . $this->_name );
			$attribs = array_merge( $this->_attributes, $attribs );

			// Get the HTML
			return '<textarea' . YDForm2::_convertToHtmlAttrib( $attribs ) . '>' . $this->_value . '</textarea>';

		}

	}

?>