<?php

    /*

        Yellow Duck Framework version 2.0
        Copyright (C) (c) copyright 2004 Pieter Claerhout

        This library is free software; you can redistribute it and/or
        modify it under the terms of the GNU Lesser General Public
        License as published by the Free Software Foundation; either
        version 2.1 of the License, or (at your option) any later version.

        This library is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
        Lesser General Public License for more details.

        You should have received a copy of the GNU Lesser General Public
        License along with this library; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

    */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    YDInclude( 'YDRequest.php' );

    /**
     *	This class defines an object oriented HTML form.
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
            $tihs->_regRenderers = array();

            // The list of elements, rules and filters
            $this->_elements = array();
            $this->_rules = array();
            $this->_comparerules = array();
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
            $this->registerElement( 'datetimeselect', 'YDFormElement_DateTimeSelect', 'YDFormElement_DateTimeSelect.php' );
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
            $this->registerElement( 'timeselect', 'YDFormElement_TimeSelect', 'YDFormElement_TimeSelect.php' );

            // Add the rules
            $this->registerRule( 'required', array( 'YDValidateRules', 'required' ), 'YDValidateRules.php' );
            $this->registerRule( 'maxlength', array( 'YDValidateRules', 'maxlength' ), 'YDValidateRules.php' );
            $this->registerRule( 'minlength', array( 'YDValidateRules', 'minlength' ), 'YDValidateRules.php' );
            $this->registerRule( 'rangelength', array( 'YDValidateRules', 'rangelength' ), 'YDValidateRules.php' );
            $this->registerRule( 'regex', array( 'YDValidateRules', 'regex' ), 'YDValidateRules.php' );
            $this->registerRule( 'email', array( 'YDValidateRules', 'email' ), 'YDValidateRules.php' );
            $this->registerRule( 'lettersonly', array( 'YDValidateRules', 'lettersonly' ), 'YDValidateRules.php' );
            $this->registerRule( 'character', array( 'YDValidateRules', 'character' ), 'YDValidateRules.php' );
            $this->registerRule( 'alphanumeric', array( 'YDValidateRules', 'alphanumeric' ), 'YDValidateRules.php' );
            $this->registerRule( 'numeric', array( 'YDValidateRules', 'numeric' ), 'YDValidateRules.php' );
            $this->registerRule( 'digit', array( 'YDValidateRules', 'digit' ), 'YDValidateRules.php' );
            $this->registerRule( 'nopunctuation', array( 'YDValidateRules', 'nopunctuation' ), 'YDValidateRules.php' );
            $this->registerRule( 'nonzero', array( 'YDValidateRules', 'nonzero' ), 'YDValidateRules.php' );
            $this->registerRule( 'exact', array( 'YDValidateRules', 'exact' ), 'YDValidateRules.php' );
            $this->registerRule( 'in_array', array( 'YDValidateRules', 'in_array' ), 'YDValidateRules.php' );
            $this->registerRule( 'not_in_array', array( 'YDValidateRules', 'not_in_array' ), 'YDValidateRules.php' );
            $this->registerRule( 'callback', array( 'YDValidateRules', 'callback' ), 'YDValidateRules.php' );
            $this->registerRule( 'uploadedfile', array( 'YDValidateRules', 'uploadedfile' ), 'YDValidateRules.php' );
            $this->registerRule( 'maxfilesize', array( 'YDValidateRules', 'maxfilesize' ), 'YDValidateRules.php' );
            $this->registerRule( 'mimetype', array( 'YDValidateRules', 'mimetype' ), 'YDValidateRules.php' );
            $this->registerRule( 'filename', array( 'YDValidateRules', 'filename' ), 'YDValidateRules.php' );
            $this->registerRule( 'extension', array( 'YDValidateRules', 'extension' ), 'YDValidateRules.php' );
            $this->registerRule( 'date', array( 'YDValidateRules', 'date' ), 'YDValidateRules.php' );
            $this->registerRule( 'time', array( 'YDValidateRules', 'time' ), 'YDValidateRules.php' );
            $this->registerRule( 'datetime', array( 'YDValidateRules', 'datetime' ), 'YDValidateRules.php' );

            // Add the filters
            $this->registerFilter( 'trim', 'trim' );
            $this->registerFilter( 'lower', 'strtolower' );
            $this->registerFilter( 'upper', 'strtoupper' );

            // Add the renderers
            $this->registerRenderer( 'html', 'YDFormRenderer_html', 'YDFormRenderer_html.php' );

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
            $this->_regElements[ $name ] = array( 'class' => $class, 'file' => $file );
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
         *	This function will register a new form renderer.
         *
         *	@param $name	Name of the render.
         *	@param $class	The class name of the renderer definition.
         *	@param $file	(optional) The file containing the class definition for this renderer.
         */
        function registerRenderer( $name, $class, $file='' ) {
            $this->_regRenderers[ $name ] = array( 'class' => $class, 'file' => $file );
        }

        /**
         *	This function will unregister the renderer.
         *
         *	@param $name	Name of the renderer.
         */
        function unregisterRenderer( $name ) {
            if ( array_key_exists( $name, $this->_regRenderers ) ) { unset( $this->_regRenderers[ $name ] ); }
        }

        /**
         *	This function will set the default values for the form.
         *
         *	@param $array	Associative array containing the default values.
         */
        function setDefaults( $array ) {

            // Will be used for new elements
            $this->_defaults = $array;
            
            // Update the values for the existing elements
            if ( ! $this->isSubmitted() ) {
                foreach ( $this->_elements as $name=>$element ) {
                    if ( isset( $this->_defaults[ $element->_name ] ) ) {
                        $element->setValue( $this->_defaults[ $element->_name ] );
                        $this->_elements[ $name ] = $element;
                    }
                }
            }
            
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
                YDInclude( $this->_regElements[ $type ]['file'] );
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
                    $elementVars[ preg_replace( '/^' . $this->_name . '_/', '', $var ) ] = $value;
                }
            }

            // If there is nothing that matches, use the default
            if ( sizeof( $elementVars ) == 0 )  {
                if ( ! $this->isSubmitted() ) {
                    if ( isset( $this->_defaults[ $name ] ) ) {
                        $instance->setValue( $this->_defaults[ $name ] );
                    }
                }
            } elseif ( sizeof( $elementVars ) == 1 ) {
                $instance->setValue( $elementVars[ $name ] );
            } else {
                $instance->setValue( $elementVars );
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
         *	@param	$element	The element to apply the filter on. If you specify an array, it will add the filter for
         *						each element in the array.
         *	@param	$filter		The name of the filter to apply.
         */
        function addFilter( $element, $filter ) {

            // Check if the element is an array or not
            if ( is_array( $element ) ) {

                // Add the rule for each element
                foreach ( $element as $e ) {
                    $this->addFilter( $e, $filter );
                }

                // Return
                return;

            }

            // Check if it's a known filter
            if ( ! array_key_exists( $filter, $this->_regFilters ) ) {
                trigger_error( 'Unknown filter "' . $filter . '" for element "' . $element . '"', YD_ERROR );
            }

            // Include the filter file
            if ( ! empty( $this->_regFilters[ $filter ]['file'] ) ) {
                YDInclude( $this->_regFilters[ $filter ]['file'] );
            }

            // Initialize the element
            if ( ! @ is_array( $this->_filters[ $element ] ) ) { $this->_filters[ $element ] = array(); }

            // Add the filter
            array_push( $this->_filters[ $element ], $filter );

        }

        /**
         *	Add a rule to the form for the specified field.
         *
         *	@param	$element	The element to apply the rule on. If you specify an array, it will add the rule for each
         *						element in the array.
         *	@param	$rule		The name of the rule to apply.
         *	@param	$error		The error message to show if an error occured.
         *	@param	$options	(optional) The options to pass to the validator function.
         */
        function addRule( $element, $rule, $error, $options=null ) {

            // Check if the element is an array or not
            if ( is_array( $element ) ) {

                // Add the rule for each element
                foreach ( $element as $e ) {
                    $this->addRule( $e, $rule, $error, $options );
                }

                // Return
                return;

            }

            // Check if it's a known filter
            if ( ! array_key_exists( $rule, $this->_regRules ) ) {
                trigger_error( 'Unknown rule "' . $rule . '" for element "' . $element . '"', YD_ERROR );
            }

            // Include the rule file
            if ( ! empty( $this->_regRules[ $rule ]['file'] ) ) {
                YDInclude( $this->_regRules[ $rule ]['file'] );
            }

            // Initialize the element
            if ( ! @ is_array( $this->_rules[ $element ] ) ) { $this->_rules[ $element ] = array(); }

            // Add the rule
            array_push( $this->_rules[ $element ], array( 'rule' => $rule, 'error' => $error, 'options' => $options ) );

        }

        /**
         *	Add a rule to compare different form elements with each other.
         *
         *	@param	$elements	The array of elements to compare with each other.
         *	@param	$rule		The name of the rule to apply. This can be "equal", "asc" or "desc".
         *	@param	$error		The error message to show if an error occured.
         */
        function addCompareRule( $elements, $rule, $error ) {

            // Check if we have a valid rule
            if ( ! in_array( strtolower( $rule ), array( 'equal', 'asc', 'desc' ) ) ) {
                trigger_error( 'Unknown compare rule "' . $rule . '"', YD_ERROR );
            }

            // Check if we have an array as list of elements
            if ( ! is_array( $elements ) ) {
                trigger_error( 'The addCompareRule function requires the list elements to be an array.', YD_ERROR );
            }

            // Check if we have enough elements
            if ( sizeof( $elements ) < 2 ) {
                trigger_error(
                    'A compare rule needs at least 2 elements, only ' . sizeof( $elements ) . ' given.', YD_ERROR
                );
            }

            // Add the compare rule
            array_push(
                $this->_comparerules,
                array( 'elements' => $elements, 'rule' => strtolower( $rule ), 'error' => $error )
            );

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
            $element = & $this->getElement( $name );
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

            // Add the timestamp if needed
            if ( method_exists( $element, 'getTimeStamp' ) ) {
                if ( ! is_array( $value ) ) {
                    $element->setValue( $value );
                    $value = $element->_value;
                }
                @ $value['timestamp'] = $element->getTimeStamp();
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

            // Get the right variables
            if ( $this->_method == 'get' ) {
                $vars = $_GET;
            } else {
                $vars = $_POST;
            }

            // Loop over the post variables
            foreach ( $vars as $key=>$value ) {

                // Remove the form name from the element name
                $key = preg_replace( '/^' . $this->_name . '_/', '', $key );

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
            if ( $this->isSubmitted() == false ) {
                return false; 
            }

            // Check if there are any rules, if not, form is valid and return true
            if ( sizeof( $this->_rules ) == 0 && sizeof( $this->_formrules ) == 0 ) {
                return true;
            }

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

                        // Get the value of a form element
                        $value = $this->getValue( $element );
                        if ( is_array( $value ) && isset( $value['name'] ) ) {
                            if ( ! in_array( $rule['rule'], array( 'uploadedfile', 'maxfilesize', 'mimetype', 'filename', 'extension' ) ) ) {
                                $value = $value['name'];
                            }
                        }

                        // Check the rule
                        // @todo Are we able to handle arrays?
                        $result = call_user_func( 
                            $ruleDetails['callback'], $value, $rule['options'] 
                        );

                        // If the result is false, add the error
                        if ( $result == false ) {
                            if ( ! isset( $this->_errors[ $element ] ) ) {
                                $this->_errors[ $element ] = $rule['error'];
                            }
                        }

                    }

                }

            }

            // Check for errors
            if ( sizeof( $this->_errors ) > 0 ) {
                return false;
            }

            // Execute the compare rules
            foreach ( $this->_comparerules as $rule ) {

                // Check the type of the rule
                switch ( $rule['rule'] ) {

                    // Equal rule
                    case 'equal':

                        // Get the values for each element
                        $values = array();
                        foreach ( $rule['elements'] as $element ) {
                            $obj = & $this->getElement( $element );
                            if ( method_exists( $obj, 'getTimeStamp' ) ) {
                                array_push( $values, $obj->getTimeStamp() );
                            } else {
                                array_push( $values, $this->getValue( $element ) );
                            }
                        }

                        // Check if the value is the same for each element
                        if ( sizeof( array_unique( $values ) ) > 1 ) {
                            $this->_errors[ $rule['elements'][0] ] =  $rule['error'];
                            return false;
                        }

                        // Rule passed
                        break;

                    // Asc rule
                    case 'asc':
                    case 'desc':

                        // Get the values for each element
                        $values = array();
                        foreach ( $rule['elements'] as $element ) {
                            $obj = & $this->getElement( $element );
                            if ( method_exists( $obj, 'getTimeStamp' ) ) {
                                array_push( $values, $obj->getTimeStamp() );
                            } else {
                                array_push( $values, $this->getValue( $element ) );
                            }
                        }

                        // Check that all the values are unique
                        if ( sizeof( $values ) != sizeof( array_unique( $values ) ) ) {
                            $this->_errors[ $rule['elements'][0] ] =  $rule['error'];
                            return false;
                        }

                        // Make a copy of our array
                        $sorted = $values;

                        // Check if the order of the elements is correct
                        if ( $rule['rule'] == 'asc' ) {
                            sort( $sorted, SORT_NUMERIC );
                        }
                        if ( $rule['rule'] == 'desc' ) {
                            rsort( $sorted, SORT_NUMERIC );
                        }

                        // Test if they are the same
                        if ( $sorted != $values ) {
                            $this->_errors[ $rule['elements'][0] ] =  $rule['error'];
                            return false;
                        }

                        // Rule passed
                        break;

                }

            }

            // Check for errors
            if ( sizeof( $this->_errors ) > 0 ) {
                return false;
            }

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
                    foreach ( $result as $element => $error ) {
                        $this->_errors[ $element ] = $error;
                    }
                    break;
                }
            }

            // Check for errors
            if ( sizeof( $this->_errors ) > 0 ) {
                return false;
            }

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

            // Add the do parameter if it's a get form
            if ( $this->_method == 'get' ) {
                $form[YD_ACTION_PARAM] = array();
                $form[YD_ACTION_PARAM]['name'] = YD_ACTION_PARAM;
                $form[YD_ACTION_PARAM]['value'] = YDRequest::getActionName();
                $form[YD_ACTION_PARAM]['type'] = 'hidden';
                $form[YD_ACTION_PARAM]['label'] = '';
                $form[YD_ACTION_PARAM]['options'] = array();
                $form[YD_ACTION_PARAM]['placeLabel'] = 'before';
                $form[YD_ACTION_PARAM]['html'] = '<input type="hidden" name="' . YD_ACTION_PARAM . '" value="' . YDRequest::getActionName() . '">';
                $form[YD_ACTION_PARAM]['isButton'] = false;
                $form[YD_ACTION_PARAM]['error'] = '';
                $form[YD_ACTION_PARAM]['required'] = false;
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
            return $this->render( 'html' );
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

        /**
         *	This function will render a form and return the rendered contents.
         *
         *	@param	$type	The renderer to use.
         *
         *	@returns	The rendered form.
         */
        function render( $type ) {

            // Check if the renderer is known
            if ( ! array_key_exists( $type, $this->_regRenderers ) ) {
                trigger_error( 'Unknown for renderer type "' . $type . '".', YD_ERROR );
            }

            // Include the renderer file
            if ( ! empty( $this->_regRenderers[ $type ]['file'] ) ) {
                YDInclude( $this->_regRenderers[ $type ]['file'] );
            }

            // Check if the class exists
            $class = $this->_regRenderers[ $type ]['class'];

            // Create the instance
            $instance = new $class( $this );

            // Return the rendered form
            return $instance->render();

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
            $this->_form = & $form;
            $this->_name = $name;
            $this->_label = $label;
            $this->_attributes = $attributes;
            $this->_options = $options;
            $this->_type = '';
            $this->_value = '';
            $this->_isButton = false;
            //$this->_autoRules = array();

            // Indicate where the label should be
            $this->_labelPlace = 'before';

            // Indicate if filters need to be applied
            $this->_applyFilters = true;

        }

        /**
         *	This function sets the value for the date element.
         *
         *	@param	$val	(optional) The value for this object.
         */		
        function setValue( $val='' ) {
            $this->_value = $val;
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

    /**
     *	This is the class that is able to render a form object to whatever.
     */
    class YDFormRenderer extends YDBase {

        /**
         *	This is the class constructor for the YDFormRenderer_html class.
         *
         *	@param $form		The form that needs to be rendered.
         */
        function YDFormRenderer( $form ) {
            $this->_form = & $form;
        }

        /**
         *	This function will render the form.
         *
         *	@returns	The rendered form.
         */
        function render() {
            return '';
        }

    }

?>