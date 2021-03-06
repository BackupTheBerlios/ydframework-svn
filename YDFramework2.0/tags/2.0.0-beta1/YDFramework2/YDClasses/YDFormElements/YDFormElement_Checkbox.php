<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDForm.php' );

	/**
	 *	This is the class that define a checkbox form element.
	 */
	class YDFormElement_Checkbox extends YDFormElement {

		/**
		 *	This is the class constructor for the YDFormElement_Checkbox class.
		 *
		 *	@param $form		The name of the form to which this element is connected.
		 *	@param $name		The name of the form element.
		 *	@param $label		(optional) The label for the form element.
		 *	@param $attributes	(optional) The attributes for the form element.
		 *	@param $options		(optional) The options for the elment.
		 */
		function YDFormElement_Checkbox( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDFormElement( $form, $name, $label, $attributes, $options );

			// Set the type
			$this->_type = 'checkbox';

			// Fix the value setting
			if ( ! empty( $this->_value ) ) {
				$this->_value = 'on';
			}

			// Indicate if filters need to be applied
			$this->_applyFilters = false;

			// Indicate that the label should be appended
			$this->_labelPlace = 'after';

		}

		/**
		 *	This function will return the element as HTML.
		 *
		 *	@returns	The form element as HTML text.
		 */
		function toHtml() {

			// Create the list of attributes
			$attribs = array( 
				'type' => $this->_type, 'name' => $this->_form . '_' . $this->_name, 'id' => $this->_form . '_' . $this->_name
			);
			$attribs = array_merge( $this->_attributes, $attribs );

			// If a value, fill it in and make it checked
			if ( ! empty( $this->_value ) ) {
				$attribs['checked'] = '';
			}

			// Get the HTML
			return '<input' . YDForm::_convertToHtmlAttrib( $attribs ) . '>';

		}

	}

?>