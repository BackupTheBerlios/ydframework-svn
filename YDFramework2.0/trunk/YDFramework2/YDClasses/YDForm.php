<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	/**
	 *	This class defines an object oriented HTML form.
	 *
	 *	@todo
	 *		We need to change the way default values are assigned so that you can execute the setDefaults function after
	 *		adding the elements. Maybe better to move it to a separate function.
	 */
	class YDForm extends YDBase {

		/**
		 *
		 *	@param $name		The name of the form.
		 *	@param $method		(optional) Method used for submitting the form. Normally, this is either POST or GET.
		 *	@param $action		(optional) Action used for submitting the form. If not specified, it will default to the
		 *						URI of the current request.
		 *	@param $target		(optional) HTML target for the form.
		 *	@param $attributes	(optional) Attributes for the form.
		 */
		function YDForm( $name, $method='post', $action='', $target='_self', $attributes=array() ) {

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
			$this->_htmlRequiredStart =  '';
			$this->_htmlRequiredEnd = ' <font color="red">(required)</font>';
			$this->_htmlErrorStart = '<font color="red">Error: ';
			$this->_htmlErrorEnd = '</font>';
			$this->_requiredNote = '';

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
			$this->registerElement( 'button', 'YDFormElement_Button', 'YDFormElement_Button.php' );
			$this->registerElement( 'checkbox', 'YDFormElement_Checkbox', 'YDFormElement_Checkbox.php' );
			$this->registerElement( 'dateselect', 'YDFormElement_DateSelect', 'YDFormElement_DateSelect.php' );
			$this->registerElement( 'file', 'YDFormElement_File', 'YDFormElement_File.php' );
			$this->registerElement( 'hidden', 'YDFormElement_Hidden', 'YDFormElement_Hidden.php' );
			$this->registerElement( 'image', 'YDFormElement_Image', 'YDFormElement_Image.php' );
			$this->registerElement( 'password', 'YDFormElement_Password', 'YDFormElement_Password.php' );
			$this->registerElement( 'radio', 'YDFormElement_Radio', 'YDFormElement_Radio.php' );
			$this->registerElement( 'reset', 'YDFormElement_Reset', 'YDFormElement_Reset.php' );
			$this->registerElement( 'select', 'YDFormElement_Select', 'YDFormElement_Select.php' );
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
			$this->registerRule( 'uploadedfile', array( 'YDValidateRules', 'uploadedfile' ), 'YDValidateRules.php' );
			$this->registerRule( 'maxfilesize', array( 'YDValidateRules', 'maxfilesize' ), 'YDValidateRules.php' );
			$this->registerRule( 'mimetype', array( 'YDValidateRules', 'mimetype' ), 'YDValidateRules.php' );
			$this->registerRule( 'filename', array( 'YDValidateRules', 'filename' ), 'YDValidateRules.php' );

			// Add the filters
			$this->registerFilter( 'trim', 'trim' );
			$this->registerFilter( 'lower', 'strtolower' );
			$this->registerFilter( 'upper', 'strtoupper' );

		}

		/**
		 *	This function will set the HTML that is added before and after the element label to indicate that the 
		 *	element is required. This only has affect if you use the default toHtml function.
		 *
		 *	@param $start	The HTML that should be added before the label.
		 *	@param $end		The HTML that should be added after the label.
		 */
		function setHtmlRequired( $start, $end ) {
			$this->_htmlRequiredStart = $start;
			$this->_htmlRequiredEnd = $end;
		}

		/**
		 *	This function will set the HTML that is added before and after the element label to indicate that the 
		 *	element has an errir. This only has affect if you use the default toHtml function.
		 *
		 *	@param $start	The HTML that should be added before the label.
		 *	@param $end		The HTML that should be added after the label.
		 */
		function setHtmlError( $start, $end ) {
			$this->_htmlErrorStart = $start;
			$this->_htmlErrorEnd = $end;
		}

		/**
		 *	This function will set the text that will be added at the top of the form to indicate that there are
		 *	required items.
		 *
		 *	@param $text	The text to show.
		 */
		function setRequiredNote( $text ) {
			$this->_requiredNote = $text;
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
				trigger_error( 'Unknown for element type "' . $type . '" for element "' . $name . '".', YD_ERROR );
			}

			// Include the element file
			if ( ! empty( $this->_regElements[ $type ]['file'] ) ) {
				require_once( $this->_regElements[ $type ]['file'] );
			}

			// Check if the class exists
			$class = $this->_regElements[ $type ]['class'];

			// Add extra form attributes
			if ( $type == 'file' ) {
				if ( ! is_array( $this->_attributes ) ) {
					$this->_attributes = array();
				}
				$this->_attributes[ 'enctype' ] = 'multipart/form-data';
			}

			// Create the instance
			$instance = new $class( $this->_name, $name, $label, $attributes, $options );

			// Loop over the form variable
			$elementVars = array();
			foreach ( $this->_formVars as $var=>$value ) {
				if ( $var === $this->_name . '_' . $name ) {
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
		 *	This function will remove the specified form element.
		 *
		 *	@param $name	The name of the form element.
		 */
		function removeElement( $name ) {

			// Check if the element exists
			if ( ! array_key_exists( $name, $this->_elements ) ) {
				trigger_error( 'The specified element "' . $name . '" does not exist.', YD_ERROR );
			}

			// Get the element
			unset( $this->_elements[ $name ] );

		}

		/**
		 *	This function will return a reference to the specified form element.
		 *
		 *	@param $name	The name of the form element.
		 *
		 *	@returns	A reference to the specified form element.
		 */
		function & getElement( $name ) {

			// Check if the element exists
			if ( ! array_key_exists( $name, $this->_elements ) ) {
				trigger_error( 'The specified element "' . $name . '" does not exist.', YD_ERROR );
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
				trigger_error( 'Unknown filter "' . $filter . '" for element "' . $element . '"', YD_ERROR );
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
				trigger_error( 'Unknown rule "' . $rule . '" for element "' . $element . '"', YD_ERROR );
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
			$type = $element->_type;
			$value = $element->_value;
			$applyFilters = $element->_applyFilters;

			// Filters should only be applied if the form is submitted and if the element type supports it.
			if ( $this->isSubmitted() ) {
				if ( $applyFilters == true ) {
					$value = $this->_applyFilter( '__ALL__', $value );
					$value = $this->_applyFilter( $name, $value );
				}
			} else {
				if ( isset( $this->_defaults[ $name ] ) ) {
					$value = $this->_defaults[ $name ];
				}
			}

			// Special treatment for uploads
			if ( $type == 'file' ) {
				if ( array_key_exists( $this->_name . '_' . $name, $_FILES ) ) { 
					return $_FILES[ $this->_name . '_' . $name ];
				}
			}

			// Unset the element
			unset( $element );

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
		 *	This function will check if the specified button was clicked or not.
		 *
		 *	@param $button	The name of the button.
		 *
		 *	@returns	Boolean indicating if the button was clicked or not.
		 */
		function isClicked( $button ) {
		
			// Get the element.
			$element = $this->getElement( $button );

			// Check if it's a button
			if ( $element->isButton() === true ) {
				
				// Check the post variables
				if ( array_key_exists( $this->_name . '_' . $element->_name, $this->_formVars ) ) {
					return true;
				}

			}

			// Return false in all other cases
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
							trigger_error( 'Unknown rule: ' . $rule['rule'], YD_ERROR );
						}

						// Get the rule details
						$ruleDetails = $this->_regRules[ $rule['rule'] ];

						// Check if the callback is valid
						if ( ! is_callable( $ruleDetails['callback'] ) ) {
							trigger_error(
								'The callback specified for the rule "' . $rule['rule'] . '" is not valid', YD_ERROR
							);
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
					trigger_error( 'The callback specified for the form "' . $rule . '" is not valid', YD_ERROR );
				}

				// Get the form values
				$values = $this->getValues();

				// Execute the rule
				$result = call_user_func( $rule, $values );

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
				'action'	=> $this->_action, 'target'	=> $this->_target
			);
			$attribs = array_merge( $this->_attributes, $attribs );
			$form['attribs'] = $this->_convertToHtmlAttrib( $attribs );
			$form['tag'] = '<form' . $form['attribs'] . '>';
			$form['requirednote'] = $this->_requiredNote;

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

				// Add the HTML labels
				if ( $form[ $name ]['isButton'] === false && $form[ $name ]['type'] != 'hidden' ) {
					$form[ $name ]['label_html'] = '';
					if ( $form[ $name ]['required'] ) {
						$form[ $name ]['label_html'] .= $this->_htmlRequiredStart;
					}
					if ( ! empty( $form[ $name ]['label'] ) ) {
						$form[ $name ]['label_html'] .= $form[ $name ]['label'];
					}
					if ( $form[ $name ]['required'] ) {
						$form[ $name ]['label_html'] .= $this->_htmlRequiredEnd;
					}
					if ( ! empty( $form[ $name ]['error'] ) ) {
						$form[ $name ]['error_html'] = $this->_htmlErrorStart . $form[ $name ]['error'] . $this->_htmlErrorEnd;
					}
				}

			}

			// If debugging, show contents
			if ( YD_DEBUG ) {
				YDDebugUtil::debug( YDDebugUtil::r_dump( $form ) );
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
			unset( $form['requirednote'] );

			// Add the required note if there are required items
			if ( ! empty( $this->_requiredNote ) ) {
				$reqCount = 0;
				foreach ( $form as $name=>$element ) {
					if ( $element['required'] ) { $reqCount++; };
				}
				if ( $reqCount > 0 ) {
					$html .= '<p>' . $this->_requiredNote . '</p>';
				}
			}

			// Add the elements
			foreach ( $form as $name=>$element ) {
				if ( $element['isButton'] === false ) {
					if ( $element['type'] != 'hidden' ) {
						$html .= '<p>';
						if ( $element['placeLabel'] == 'after' ) {
							$html .= $element['html'] . $element['label_html'];
							if ( ! empty( $element['error'] ) ) {
								$html .= '<br>' . $element['error_html'];
							}
						} else {
							$html .= $element['label_html'];
							if ( ! empty( $element['label'] ) ) {
								$html .= '<br>';
							}
							if ( ! empty( $element['error'] ) ) {
								$html .= $element['error_html'] . '<br>';
							}
							$html .= $element['html'];
						}
						$html .= '</p>';
					} else {
						$html .= $element['html'];
					}
				}
			}

			// Add the buttons
			$buttons = array();
			foreach ( $form as $name=>$element ) {
				if ( $element['isButton'] === true ) {
					array_push( $buttons, $element['html'] );
				}
			}
			if ( sizeof( $buttons ) > 0 ) {
				$html .= '<p>' . implode( '&nbsp;', $buttons ) . '</p>';
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
							trigger_error(
								'The callback specified for the filter "' . $name . '" is not valid', YD_ERROR
							);
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
			$this->_isButton = false;

			// Indicate where the label should be
			$this->_labelPlace = 'before';

			// Indicate if filters need to be applied
			$this->_applyFilters = true;

		}

		/**
		 *	Indicates if the element is a button or not.
		 *
		 *	@returns	Boolean indicating if the element is a button or not.
		 */
		function isButton() {
			if ( $this->_isButton === true ) {
				return true;
			} else {
				return false;
			}
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
				'label'	=> '<label for="' . $this->_form . '_' . $this->_name . '">' . $this->_label . '</label>',
				'options' => $this->_options,
				'placeLabel' => $this->_labelPlace,
				'html'	=> $this->toHtml(),
				'isButton' => $this->isButton(),
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