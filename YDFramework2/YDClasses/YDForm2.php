<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 *	This class defines an object oriented form.
	 */
	class YDForm2 extends YDBase {

		/**
		 *
		 *	@param $name		The name of the form.
		 *	@param $method		(optional) Method used for submitting the form. Normally, this is either POST or GET.
		 *	@param $action		(optional) Action used for submitting the form. If not specified, it will default to the
		 *						URI of the current request.
		 *	@param $target		(optional) HTML target for the form.
		 *	@param $attributes	(optional) Attributes for the form.
		 */
		function YDForm2( $name, $method='post', $action='', $target='_self', $attributes=null ) {

			// Initialize the parent
			$this->YDBase();

			// Initialize the variables
			$this->_name = $name;
			$this->_method = ( strtoupper( $method ) == 'GET' ) ? 'get' : 'post';
			$this->_action = empty( $action ) ? $_SERVER['PHP_SELF'] : $action;
			$this->_target = empty( $target ) ? '_self' : $target;
			$this->_attributes = $attributes;

			// The list of known elements, rules, filters and validators
			$this->_regElements = array();
			$this->_regRules = array();
			$this->_regFilters = array();
			$this->_regValidators = array();

			// The list of form elements
			$this->_elements = array();
			$this->_rules = array();
			$this->_filters = array();

			// The list of errors
			$this->_errors = array();

			// The list of default values
			$this->_default = array();

			// Add the standard elements
			$this->registerElement( 'text', 'YDFormElement_Text' );
			$this->registerElement( 'submit', 'YDFormElement_Submit' );

		}

		/**
		 *	This function will register a new element type.
		 *
		 *	@param $name	Name of the element.
		 *	@param $class	The class name of the element definition.
		 *	@param $file	The file containing the class definition for this element.
		 */
		function registerElement( $name, $class, $file='' ) {
			if ( empty( $file ) ) {
				$file = $class . '.php';
			}
			$this->_regElements[ $name ] = array( 'class' => $class, 'file' => $file );
		}

		function registerRule() {
		}

		function registerValidator() {
		}

		function setDefaults( $array ) {
			$this->_defaults = $array;
		}

		/**
		 *	This function will add a new element to the form.
		 *
		 *	@param $type	The type of element to add.
		 *	@param $name	The HTML name for the element.
		 *	@param $label	The label for the element.
		 *	@param $attribs	The attributes for the element.
		 */
		function addElement( $type, $name, $label, $attribs=array() ) {

			// Check if the element type is known
			if ( ! array_key_exists( $type, $this->_regElements ) ) {
				YDFatalError( 'Unknown for element type "' . $type . '" for element "' . $name . '".' );
			}

			// Include the element file
			require_once( $this->_regElements[ $type ]['file'] );

			// Check if the class exists
			$class = $this->_regElements[ $type ]['class'];
			if ( ! class_exists( $class ) ) {
				YDFatalError( 'Class definition "' . $class . '" for the element type "' . $type . '" is missing' );
			}

			// Create the instance
			$instance = new $class( $name, $label, $attribs );

			// Register the element in the class.
			$this->_elements[ $name ] = $instance;

		}

		/**
		 *	This function will return a reference to the specified form element.
		 *
		 *	@param $name	The name of the form element.
		 *
		 *	@returns	A reference to the specified form element.
		 */
		function getElement( $name ) {

			// Check if the element exists
			if ( ! array_key_exists( $name, $this->_elements ) ) {
				YDFatalError( 'The specified element, "' . $name . '" does not exist.' );
			}

			// Get the element
			$element = & $this->_elements[ $name ];

			// Return a reference to the element
			return $element;

		}

		function applyFilter() {
		}

		function addRule() {
		}

		function validate() {
		}

		/**
		 *	This function will return the value of the specified form element.
		 *
		 *	@param $name	The name of the form element.
		 *
		 *	@returns	The value to the specified form element.
		 */
		function getValue( $name ) {

			// Get the actual element value
			$element = $this->getElement( $name );
			$value = $element->_value;
			unset( $element );

			// Apply the filters

			// Return the value
			return $value;
		
		}

		/**
		 *	This function will return the form as an array.
		 *
		 *	@returns	The form as an array.
		 */
		function toArray() {

			// Update the values of the different elements with their defaults
			foreach ( $this->_defaults as $key=>$value ) {
				if ( array_key_exists( $key, $this->_elements ) ) {
					$element = & $this->_elements[ $key ];
					$element->_value = $value;
					unset( $element );
				}
			}

			// Check for post or get variables
			if ( $this->_method == 'POST' ) {
				$formVars = $_POST;
			} else {
				$formVars = $_GET;
			}

			// Update the values of the different elements with their real values
			foreach ( $formVars as $key=>$value ) {
				if ( array_key_exists( $key, $this->_elements ) ) {
					$element = & $this->_elements[ $key ];
					$element->_value = $value;
					unset( $element );
				}
			}

			// Start with an empty array
			$form = array();

			// Add the list of attributes
			$attribs = array(
				'name'		=> $this->_name, 'id'		=> $this->_name,
				'action'	=> $this->_action, 'target'	=> $this->_target,
			);
			$attribs = array_merge( $this->_attributes, $attribs );
			$form['attribs'] = $this->_convertToHtmlAttrib( $attribs );
			$form['tag'] = '<form' . $form['attribs'] . '>';

			// Add the errors
			$form['errors'] = $this->_errors;

			// Loop over the list of element
			foreach ( $this->_elements as $name => $element ) {
				
				// Add the form element
				$form[ $name ] = $element->toArray();

				// Add errors if any
				if ( array_key_exists( $name, $this->_errors ) ) {
					$form[ $name ]['error'] = $this->_errors[ $name ];
				} else {
					$form[ $name ]['error'] = '';
				}

			}

			// Return the form array
			return $form;

		}

		/**
		 *	This function will return the form as HTML.
		 *
		 *	@returns	The form as HTML text.
		 */
		function toHtml() {

			// Get the form as an array
			$form = $this->toArray();

			// Start with the form element
			$html .= $form['tag'];

			// Add errors if any

			// Remove some things from the array
			unset( $form['attribs'] );
			unset( $form['tag'] );
			unset( $form['errors'] );

			// Add the elements
			foreach ( $form as $name=>$element ) {
				echo( $name );
				echo( $element );
				$html .= '<p>';
				if ( ! empty( $element['label'] ) ) {
					$html .= $element['label'] . '<br>';
				}
				$html .= $element['html'] . '</p>';
			}

			// Close the form tag
			$html .= '</form>';

			// Return the HTML
			return $html;

		}

		/**
		 *	This function will output the form as HTML.
		 */
		function display() {
			echo( $this->toHtml() );
		}

		/**
		 *	This function will convert an associative array to it's HMTL equivalent using keys as attribute names and
		 *	the values as attribute values.
		 *
		 *	@param $array	An associative array.
		 *
		 *	@returns	The associative array as HTML.
		 *
		 *	@internal
		 */
		function _convertToHtmlAttrib( $array ) {
			if ( ! is_array( $array ) ) { return ''; }
			if ( sizeof( $array ) == 0 ) { return ''; }
			foreach ( $array as $key=>$value ) { $out .= ' ' . $key . '="' . $value . '"'; }
			return $out;
		}

	}

?>