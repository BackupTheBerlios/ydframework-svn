<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );
	require_once( 'YDForm2.php' );

	/**
	 *	This is the base class for all form elements.
	 */
	class YDFormElement extends YDBase {

		/**
		 *	This is the class constructor for the YDFormElement class.
		 *
		 *	@param $name		The name of the form element.
		 *	@param $label		The label for the form element.
		 *	@param $attributes	The attributes for the form element.
		 */
		function YDFormElement( $name=null, $label=null, $attributes=null ) {

			// Initialize the parent
			$this->YDBase();

			// Initialize the variables
			$this->_name = $name;
			$this->_label = $label;
			$this->_attributes = $attributes;
			$this->_type = '';
			$this->_value = '';

		}

		/**
		 *	This function will return the element as an array.
		 *
		 *	@returns	The form element as an array.
		 */
		function toArray() {
			return array(
				'name'	=> $this->_name,
				'value'	=> $this->_value,
				'type'	=> $this->_type,
				'label'	=> $this->_label,
				'html'	=> $this->toHtml(),
			);
		}

		/**
		 *	This function will return the element as HTML.
		 *
		 *	@returns	The form element as HTML text.
		 */
		function toHtml() {
		}

	}

?>