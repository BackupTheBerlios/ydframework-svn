<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 *	This class defines an object oriented form.
	 *
	 *	@todo
	 *		Implement the rest of the form elements.
	 *
	 *	@todo
	 *		Convert all the examples to this new form object and kick out PEAR.
	 *
	 *	@todo
	 *		All registering functions should use lowercase element names.
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
			$this->_action = empty( $action ) ? $_SERVER['REQUEST_URI'] : $action;
			$this->_target = empty( $target ) ? '_self' : $target;
			$this->_attributes = $attributes;

			// The list of known elements, rules, filters and validators
			$this->_regElements = array();
			$this->_regRules = array();
			$this->_regFilters = array();
			$this->_regValidators = array();

			// The list of elements, rules and filters
			$this->_elements = array();
			$this->_rules = array();
			$this->_formrules = array();
			$this->_filters = array();

			// The list of errors
			$this->_errors = array();
			
			// Some static HTML things
			$this->_htmlRequiredStart = '<font color="red">(required)</font> <b>';
			$this->_htmlRequiredEnd = '</b></font>';
			$this->_htmlErrorStart = '<font color="red">Error: ';
			$this->_htmlErrorEnd = '</font>';

			// The list of default values
			$this->_defaults = array();

			// Check for post or get variables
			if ( strtoupper( $this->_method ) == 'GET' ) {
				$this->_formVars = $_GET;
			} else {
				$this->_formVars = $_POST;
			}

			// Add the standard elements
			$this->registerElement( 'bbtextarea', 'YDFormElement_BBTextArea', 'YDFormElement_BBTextArea.php' );
			$this->registerElement( 'checkbox', 'YDFormElement_Checkbox', 'YDFormElement_Checkbox.php' );
			$this->registerElement( 'hidden', 'YDFormElement_Hidden', 'YDFormElement_Hidden.php' );
			$this->registerElement( 'image', 'YDFormElement_Image', 'YDFormElement_Image.php' );
			$this->registerElement( 'password', 'YDFormElement_Password', 'YDFormElement_Password.php' );
			$this->registerElement( 'radio', 'YDFormElement_Radio', 'YDFormElement_Radio.php' );
			$this->registerElement( 'reset', 'YDFormElement_Reset', 'YDFormElement_Reset.php' );
			$this->registerElement( 'submit', 'YDFormElement_Submit', 'YDFormElement_Submit.php' );
			$this->registerElement( 'text', 'YDFormElement_Text', 'YDFormElement_Text.php' );
			$this->registerElement( 'textarea', 'YDFormElement_TextArea', 'YDFormElement_TextArea.php' );

			// Add the rules
			$this->registerRule( 'required', array( 'YDValidateRules', 'required' ), 'YDValidateRules.php' );
			$this->registerRule( 'maxlength', array( 'YDValidateRules', 'maxlength' ), 'YDValidateRules.php' );
			$this->registerRule( 'minlength', array( 'YDValidateRules', 'minlength' ), 'YDValidateRules.php' );
			$this->registerRule( 'rangelength', array( 'YDValidateRules', 'rangelength' ), 'YDValidateRules.php' );
			$this->registerRule( 'regex', array( 'YDValidateRules', 'regex' ), 'YDValidateRules.php' );
			$this->registerRule( 'email', array( 'YDValidateRules', 'email' ), 'YDValidateRules.php' );
			$this->registerRule( 'lettersonly', array( 'YDValidateRules', 'lettersonly' ), 'YDValidateRules.php' );
			$this->registerRule( 'alphanumeric', array( 'YDValidateRules', 'alphanumeric' ), 'YDValidateRules.php' );
			$this->registerRule( 'numeric', array( 'YDValidateRules', 'numeric' ), 'YDValidateRules.php' );
			$this->registerRule( 'nopunctuation', array( 'YDValidateRules', 'nopunctuation' ), 'YDValidateRules.php' );
			$this->registerRule( 'nonzero', array( 'YDValidateRules', 'nonzero' ), 'YDValidateRules.php' );
			$this->registerRule( 'callback', array( 'YDValidateRules', 'callback' ), 'YDValidateRules.php' );

			// Add the filters
			$this->registerFilter( 'trim', 'trim' );
			$this->registerFilter( 'lower', 'strtolower' );
			$this->registerFilter( 'upper', 'strtoupper' );

		}

		/**
		 *	This function will register a new element type.
		 *
		 *	@param $name	Name of the element.
		 *	@param $class	The class name of the element definition.
		 *	@param $file	(optional) The file containing the class definition for this element.
		 */
		function registerElement( $name, $class, $file='' ) {
			$this->_regElements[ $name] = array( 'class' => $class, 'file' => $file );
		}

		/**
		 *	This function will unregister the element type.
		 *
		 *	@param $name	Name of the element.
		 */
		function unregisterElement( $name ) {
			if ( array_key_exists( $name, $this->_regElements ) ) { unset( $this->_regElements[ $name ] ); }
		}

		/**
		 *	This function will register a new validation rule.
		 *
		 *	@param $name		Name of the validation rule.
		 *	@param $callback	The function name of the rule definition.
		 *	@param $file		(optional) The file containing the class definition for this validation rule.
		 */
		function registerRule( $name, $callback, $file='' ) {
			$this->_regRules[ $name ] = array( 'callback' => $callback, 'file' => $file );
		}

		/**
		 *	This function will unregister the validation rule.
		 *
		 *	@param $name	Name of the validation rule.
		 */
		function unregisterRule( $name ) {
			if ( array_key_exists( $name, $this->_regRules ) ) { unset( $this->_regRules[ $name ] ); }
		}

		/**
		 *	This function will register a new filter.
		 *
		 *	@param $name		Name of the filter.
		 *	@param $callback	The function name of the filter.
		 *	@param $file		(optional) The file containing the definition for this filter.
		 */
		function registerFilter( $name, $callback, $file='') {
			$this->_regFilters[ $name ] = array( 'callback' => $callback, 'file' => $file );
		}

		/**
		 *	This function will unregister the filter.
		 *
		 *	@param $name	Name of the filter.
		 */
		function unregisterFilter( $name ) {
			if ( array_key_exists( $name, $this->_regFilters ) ) { unset( $this->_regFilters[ $name ] ); }
		}

		/**
		 *	This function will set the default values for the form.
		 *
		 *	@param $array	Associative array containing the default values.
		 */
		function setDefaults( $array ) {
			$this->_defaults = $array;
		}

		/**
		 *	This function will add a new element to the form.
		 *
		 *	@param $type		The type of element to add.
		 *	@param $name		The name of the form element.
		 *	@param $label		(optional) The label for the form element.
		 *	@param $attributes	(optional) The attributes for the form element.
		 *	@param $options		(optional) The options for the elment.
		 */
		function & addElement( $type, $name, $label='', $attributes=array(), $options=array() ) {

			// Check if the element type is known
			if ( ! array_key_exists( $type, $this->_regElements ) ) {
				YDFatalError( 'Unknown for element type "' . $type . '" for element "' . $name . '".' );
			}

			// Include the element file
			if ( ! empty( $this->_regElements[ $type ]['file'] ) ) {
				require_once( $this->_regElements[ $type ]['file'] );
			}

			// Check if the class exists
			$class = $this->_regElements[ $type ]['class'];

			// Create the instance
			$instance = new $class( $this->_name, $name, $label, $attributes, $options );

			// Loop over the form variable
			$elementVars = array();
			foreach ( $this->_formVars as $var=>$value ) {
				if ( strpos( $var, $this->_name . '_' . $name ) === 0 ) {
					$elementVars[ str_replace( $this->_name . '_', '', $var ) ] = $value;
				}
			}

			// If there is nothing that matches, use the default
			if ( sizeof( $elementVars ) == 0 )  {
				if ( ! $this->isSubmitted() ) {
					if ( isset( $this->_defaults[ $name ] ) ) {
						$instance->_value = $this->_defaults[ $name ];
					}
				}
			} elseif ( sizeof( $elementVars ) == 1 ) {
				$instance->_value = $elementVars[ $name ];
			} else {
				$instance->_value = $elementVars;
			}

			// Register the element in the class.
			$this->_elements[ $name ] = $instance;

			// Return the reference to the instance
			return $this->_elements[ $name ];

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
				YDFatalError( 'The specified element "' . $name . '" does not exist.' );
			}

			// Get the element
			$element = & $this->_elements[ $name ];

			// Return a reference to the element
			return $element;

		}

		/**
		 *	Add a filter to the form for the specified field.
		 *
		 *	@param	$element	The element to apply the filter on.
		 *	@param	$filter		The name of the filter to apply.
		 */
		function addFilter( $element, $filter ) {

			// Check if it's a known filter
			if ( ! array_key_exists( $filter, $this->_regFilters ) ) {
				YDFatalError( 'Unknown filter "' . $filter . '" for element "' . $element . '"' );
			}

			// Include the filter file
			if ( ! empty( $this->_regFilters[ $filter ]['file'] ) ) {
				require_once( $this->_regFilters[ $filter ]['file'] );
			}

			// Initialize the element
			if ( ! @ is_array( $this->_filters[ $element ] ) ) { $this->_filters[ $element ] = array(); }

			// Add the filter
			array_push( $this->_filters[ $element ], $filter );

		}

		/**
		 *	Add a rule to the form for the specified field.
		 *
		 *	@param	$element	The element to apply the rule on.
		 *	@param	$rule		The name of the rule to apply.
		 *	@param	$error		The error message to show if an error occured.
		 *	@param	$options	(optional) The options to pass to the validator function.
		 */
		function addRule( $element, $rule, $error, $options=null ) {

			// Check if it's a known filter
			if ( ! array_key_exists( $rule, $this->_regRules ) ) {
				YDFatalError( 'Unknown rule "' . $rule . '" for element "' . $element . '"' );
			}

			// Include the rule file
			if ( ! empty( $this->_regRules[ $rule ]['file'] ) ) {
				require_once( $this->_regRules[ $rule ]['file'] );
			}

			// Initialize the element
			if ( ! @ is_array( $this->_rules[ $element ] ) ) { $this->_rules[ $element ] = array(); }

			// Add the filter
			array_push( $this->_rules[ $element ], array( 'rule' => $rule, 'error' => $error, 'options' => $options ) );

		}

		/**
		 *	Add rule that point to a custom function and is not bound to a specific form element. The callback function
		 *	should return an associative array with the names of the fields and the errors. You can use the special name
		 *	__ALL__ to add a form wide error.
		 *
		 *	@param $callback	The callback of the funtion to perform for this form rule.
		 */
		function addFormRule( $callback ) {
			array_push( $this->_formrules, $callback );
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
			$applyFilters = $element->_applyFilters;
			unset( $element );

			// Filters should only be applied if the form is submitted and if the element type supports it.
			if ( $this->isSubmitted() ) {
				if ( $applyFilters == true ) {
					$value = $this->_applyFilter( '__ALL__', $value );
					$value = $this->_applyFilter( $name, $value );
				}
			}

			// Return the value
			return $value;
		
		}

		/**
		 *	This function will return all the values for the form as an associative array.
		 *
		 *	@returns	The values for the form as an associative array.
		 */
		function getValues() {
			$vars = array();
			foreach ( $this->_elements as $name => $element ) {
				$vars[ $name ] = $this->getValue( $name );
			}
			return $vars;
		}

		/**
		 *	This function will check if the form was submitted or not.
		 *
		 *	@todo
		 *		Check how it works with file uploads.
		 *
		 *	@returns	Boolean indicating if the form was submitted or not.
		 */
		function isSubmitted() {

			// Loop over the post variables
			foreach ( $_POST as $key=>$value ) {

				// Remove the form name from the element name
				$key = str_replace( $this->_name . '_', '', $key );

				// Check if the key is a form element
				if ( array_key_exists( $key, $this->_elements ) ) { return true; };

			}

			// Return false
			return false;

		}

		/**
		 *	This function will return true if the form is valid. If not, it will return false.
		 *
		 *	If no rules for the form, the form is considered to be valid.
		 *	If no values for this form where submitted, the form is considered to be valid.
		 *
		 *	It will iterate over all the rules and apply them to each field after having applies the filters.
		 *	Errors will be put on the error stack. In the end, it returns false or true.
		 *
		 *	@returns	Boolean indicating if the form is valid or not.
		 */
		function validate() {

			// Form should be submitted
			if ( $this->isSubmitted() == false ) { return false; }

			// Check if there are any rules, if not, form is valid and return true
			if ( sizeof( $this->_rules ) == 0 && sizeof( $this->_formrules ) == 0 ) { return true; }

			// Apply the element rules
			foreach ( $this->_rules as $element=>$rules ) {

				// Check if the element exists
				if ( array_key_exists( $element, $this->_elements ) ) {

					// Loop over the rules
					foreach ( $rules as $rule ) {

						// Check if the rule exists
						if ( ! array_key_exists( $rule['rule'], $this->_regRules ) ) {
							YDFatalError( 'Unknown rule: ' . $rule['rule'] );
						}

						// Get the rule details
						$ruleDetails = $this->_regRules[ $rule['rule'] ];

						// Check if the callback is valid
						if ( ! is_callable( $ruleDetails['callback'] ) ) {
							YDFatalError( 'The callback specified for the rule "' . $rule['rule'] . '" is not valid' );
						}

						// Check the rule
						// @todo Are we able to handle arrays?
						$result = call_user_func( 
							$ruleDetails['callback'], $this->getValue( $element ), $rule['options'] 
						);

						// If the result is false, add the error
						if ( $result == false ) { $this->_errors[ $element ] = $rule['error']; }

						// Step out of the loop
						break;

					}

				}

			}

			// Check for errors
			if ( sizeof( $this->_errors ) > 0 ) { return false; }

			// Apply the form rules
			foreach ( $this->_formrules as $rule ) {

				// Check if the callback is valid
				if ( ! is_callable( $rule ) ) {
					YDFatalError( 'The callback specified for the form "' . $rule . '" is not valid' );
				}

				// Execute the rule
				$result = call_user_func( $rule );

				// Check the result
				if ( is_array( $result ) ) {
					foreach ( $result as $element => $error ) { $this->_errors[ $element ] = $error; }
					break;
				}
			}

			// Check for errors
			if ( sizeof( $this->_errors ) > 0 ) { return false; }

			// All went fine, return true
			return true;
			
		}

		/**
		 *	This function will return the form as an array.
		 *
		 *	@returns	The form as an array.
		 */
		function toArray() {

			// Start with an empty array
			$form = array();

			// Add the list of attributes
			$attribs = array(
				'name'		=> $this->_name, 'id'		=> $this->_name, 'method' => strtoupper( $this->_method ),
				'action'	=> $this->_action, 'target'	=> $this->_target,
			);
			$attribs = array_merge( $this->_attributes, $attribs );
			$form['attribs'] = $this->_convertToHtmlAttrib( $attribs );
			$form['tag'] = '<form' . $form['attribs'] . '>';

			// Add the errors
			$form['errors'] = $this->_errors;

			// Loop over the list of elements
			foreach ( $this->_elements as $name => $element ) {
				
				// Update the value
				$element->_value = $this->getValue( $name );

				// Add the form element
				$form[ $name ] = $element->toArray();

				// Add errors if any
				if ( array_key_exists( $name, $this->_errors ) ) {
					$form[ $name ]['error'] = $this->_errors[ $name ];
				} else {
					$form[ $name ]['error'] = '';
				}

				// Check if the field is required
				if ( array_key_exists( $name, $this->_rules ) ) {
					$form[ $name ]['required'] = true;
				} else {
					$form[ $name ]['required'] = false;
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
			$html = $form['tag'];

			// Add form errors if any
			if ( isset( $form['errors']['__ALL__'] ) ) {
				$html .= '<p>' . $this->_htmlErrorStart . $form['errors']['__ALL__'] . $this->_htmlErrorEnd . '</p>';
			}

			// Remove some things from the array
			unset( $form['attribs'] );
			unset( $form['tag'] );
			unset( $form['errors'] );

			// Add the elements
			foreach ( $form as $name=>$element ) {
				$html .= '<p>';
				if ( $element['placeLabel'] == 'after' ) {
					$html .= $element['html'];
					if ( $element['required'] ) { $html .= $this->_htmlRequiredStart; }
					if ( ! empty( $element['label'] ) ) { $html .= $element['label'] ; }
					if ( $element['required'] ) { $html .= $this->_htmlRequiredEnd; }
					if ( ! empty( $element['error'] ) ) {
						$html .= '<br>' . $this->_htmlErrorStart . $element['error'] . $this->_htmlErrorEnd;
					}
				} else {
					if ( $element['required'] ) { $html .= $this->_htmlRequiredStart; }
					if ( ! empty( $element['label'] ) ) { $html .= $element['label']; }
					if ( $element['required'] ) { $html .= $this->_htmlRequiredEnd; }
					if ( ! empty( $element['label'] ) ) { $html .= '<br>'; }
					$html .= $element['html'];
					if ( ! empty( $element['error'] ) ) {
						$html .= '<br>' . $this->_htmlErrorStart . $element['error'] . $this->_htmlErrorEnd;
					}
				}
				$html .= '</p>';
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
			$out = '';
			foreach ( $array as $key=>$value ) { $out .= ' ' . strval( $key ) . '="' . strval( $value ) . '"'; }
			return $out;
		}

		/**
		 *	Function that will apply the actual filters to the named element.
		 *
		 *	@param	$name	The name of the field to apply the filter to.
		 *	@param	$value	The value to apply the filter on.
		 */
		function _applyFilter( $name, $value ) {
			if ( array_key_exists( $name, $this->_filters ) ) {
				if ( is_array( $this->_filters[ $name ] ) ) {
					foreach ( $this->_filters[ $name ] as $filter ) {
						if ( ! is_callable( $this->_regFilters[ $filter ]['callback'] ) ) {
							YDFatalError( 'The callback specified for the filter "' . $name . '" is not valid' );
						}
						if ( is_array( $value ) ) {
							foreach ( $value as $key=>$x ) {
								$value[$key] = call_user_func( $this->_regFilters[ $filter ]['callback'], $value[$key] );
							}
						} else {
							$value = call_user_func( $this->_regFilters[ $filter ]['callback'], $value );
						}
					}
				}
			}
			return $value;
		}

	}

?>