<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );
	require_once( 'YDForm.php' );

	/**
	 *	This is the base class for all form elements.
	 */
	class YDFormElement extends YDBase {

		/**
		 *	This is the class constructor for the YDFormElement class.
		 *
		 *	@param $form		The name of the form to which this element is connected.
		 *	@param $name		The name of the form element.
		 *	@param $label		(optional) The label for the form element.
		 *	@param $attributes	(optional) The attributes for the form element.
		 *	@param $options		(optional) The options for the elment.
		 */
		function YDFormElement( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// Initialize the parent
			$this->YDBase();

			// Initialize the variables
			$this->_form = $form;
			$this->_name = $name;
			$this->_label = $label;
			$this->_attributes = $attributes;
			$this->_options = $options;
			$this->_type = '';
			$this->_value = '';

			// Indicate where the label should be
			$this->_labelPlace = 'before';

			// Indicate if filters need to be applied
			$this->_applyFilters = true;

		}

		/**
		 *	This function will return the element as an array.
		 *
		 *	@returns	The form element as an array.
		 */
		function toArray() {
			if ( $this->_labelPlace != 'after' ) { $this->_labelPlace != 'before'; }
			return array(
				'name'	=> $this->_name,
				'value'	=> $this->_value,
				'type'	=> $this->_type,
				'label'	=> $this->_label,
				'options' => $this->_options,
				'placeLabel' => $this->_labelPlace,
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