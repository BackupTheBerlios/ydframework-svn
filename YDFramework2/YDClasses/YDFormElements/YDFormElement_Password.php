<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDFormElement.php' );

	/**
	 *	This is the class that define a password form element.
	 */
	class YDFormElement_Password extends YDFormElement {

		/**
		 *	This is the class constructor for the YDFormElement_Password class.
		 *
		 *	@param $form		The name of the form to which this element is connected.
		 *	@param $name		The name of the form element.
		 *	@param $label		(optional) The label for the form element.
		 *	@param $attributes	(optional) The attributes for the form element.
		 *	@param $options		(optional) The options for the elment.
		 */
		function YDFormElement_Password( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// Set the type
			$this->_type = 'password';

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

			// Get the HTML
			return '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . '>';

		}

	}

?>