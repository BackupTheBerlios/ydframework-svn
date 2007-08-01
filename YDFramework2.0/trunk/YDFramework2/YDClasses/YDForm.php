<?php

    /*

        Yellow Duck Framework version 2.1
        (c) Copyright 2002-2007 Pieter Claerhout

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

    /**
     *  @addtogroup YDForm Core - Form
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Define the path to XML HTML SAX 3 (needed for safehtml)
    if ( ! defined( 'XML_HTMLSAX3' ) ) {
        define( 'XML_HTMLSAX3', YD_DIR_HOME . '/3rdparty/safehtml/classes/' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDRequest.php');
    include_once( YD_DIR_HOME_CLS . '/YDUrl.php');

    /**
     *  This class defines an object oriented HTML form.
     *
     *  @ingroup YDForm
     */
    class YDForm extends YDBase {

        /**
         *  This is the class constructor for the YDForm class.
         *
         *  @param $name        The name of the form.
         *  @param $method      (optional) Method used for submitting the form.
         *                      Normally, this is either POST or GET.
         *  @param $action      (optional) Action used for submitting the form.
         *                      If not specified, it will default to the URI of
         *                      the current request.
         *  @param $target      (optional) HTML target for the form.
         *  @param $attributes  (optional) Attributes for the form.
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
            $this->_legend = null;
            $this->_defaultItem = null;

            // The list of known elements, rules, filters and validators
            $this->_regElements = array();
            $this->_regRules = array();
            $this->_regFilters = array();
            $this->_regValidators = array();
            $this->_regRenderers = array();

            // The list of elements, rules and filters
            $this->_elements = array();
            $this->_rules = array();
            $this->_comparerules = array();
            $this->_formrules = array();
            $this->_filters = array();

            // The list of errors
            $this->_errors = array();

            // The list of default values
            $this->_defaults = array();

            // Some static HTML things
            $this->_htmlRequiredStart =  '';

            // Check default translations
            if( !isset( $GLOBALS['t']['form_required'] ) ) $GLOBALS['t']['form_required'] = '(required)';
            if( !isset( $GLOBALS['t']['form_error'] ) )    $GLOBALS['t']['form_error'] = 'Error: ';

            $this->_htmlRequiredEnd = ' <font color="red">' . t( 'form_required' ) . '</font>';
            $this->_htmlErrorStart = '<font color="red">' . t( 'form_error' );
            $this->_htmlErrorEnd = '</font>';
            $this->_requiredNote = '';

            // Check for post or get variables
            if ( $this->_method == 'get' ) {
                $this->_formVars = $_GET;
            } else {
                $this->_formVars = $_POST;
            }

            // Add the standard elements
            $this->registerElement( 'bbtextarea', 'YDFormElement_BBTextArea', 'YDFormElement_BBTextArea.php' );
            $this->registerElement( 'button', 'YDFormElement_Button', 'YDFormElement_Button.php' );
            $this->registerElement( 'checkbox', 'YDFormElement_Checkbox', 'YDFormElement_Checkbox.php' );
            $this->registerElement( 'checkboxgroup', 'YDFormElement_CheckboxGroup', 'YDFormElement_CheckboxGroup.php' );
            $this->registerElement( 'date', 'YDFormElement_Date', 'YDFormElement_Date.php' );
            $this->registerElement( 'dateselect', 'YDFormElement_DateSelect', 'YDFormElement_DateSelect.php' );
            $this->registerElement( 'datetimeselect', 'YDFormElement_DateTimeSelect', 'YDFormElement_DateTimeSelect.php' );
            $this->registerElement( 'timeselect', 'YDFormElement_TimeSelect', 'YDFormElement_TimeSelect.php' );
            $this->registerElement( 'file', 'YDFormElement_File', 'YDFormElement_File.php' );
            $this->registerElement( 'hidden', 'YDFormElement_Hidden', 'YDFormElement_Hidden.php' );
            $this->registerElement( 'image', 'YDFormElement_Image', 'YDFormElement_Image.php' );
            $this->registerElement( 'password', 'YDFormElement_Password', 'YDFormElement_Password.php' );
            $this->registerElement( 'radio', 'YDFormElement_Radio', 'YDFormElement_Radio.php' );
            $this->registerElement( 'reset', 'YDFormElement_Reset', 'YDFormElement_Reset.php' );
            $this->registerElement( 'select', 'YDFormElement_Select', 'YDFormElement_Select.php' );
            $this->registerElement( 'selectimage', 'YDFormElement_SelectImage', 'YDFormElement_SelectImage.php' );
            $this->registerElement( 'submit', 'YDFormElement_Submit', 'YDFormElement_Submit.php' );
            $this->registerElement( 'text', 'YDFormElement_Text', 'YDFormElement_Text.php' );
            $this->registerElement( 'textarea', 'YDFormElement_TextArea', 'YDFormElement_TextArea.php' );
            $this->registerElement( 'textareacounter', 'YDFormElement_TextAreaCounter', 'YDFormElement_TextAreaCounter.php' );
            $this->registerElement( 'span', 'YDFormElement_Span', 'YDFormElement_Span.php' );
            $this->registerElement( 'img', 'YDFormElement_Img', 'YDFormElement_Img.php' );
            $this->registerElement( 'link', 'YDFormElement_Link', 'YDFormElement_Link.php' );
            $this->registerElement( 'div', 'YDFormElement_Div', 'YDFormElement_Div.php' );
            $this->registerElement( 'autocompleter', 'YDFormElement_Autocompleter', 'YDFormElement_Autocompleter.php' );
            $this->registerElement( 'switchmenu', 'YDFormElement_SwitchMenu', 'YDFormElement_SwitchMenu.php' );
            $this->registerElement( 'grid', 'YDFormElement_Grid', 'YDFormElement_Grid.php' );
            $this->registerElement( 'captcha', 'YDFormElement_Captcha', 'YDFormElement_Captcha.php' );
            $this->registerElement( 'timezone', 'YDFormElement_Timezone', 'YDFormElement_Timezone.php' );
            $this->registerElement( 'fieldset', 'YDFormElement_Fieldset', 'YDFormElement_Fieldset.php' );
            $this->registerElement( 'hr', 'YDFormElement_HR', 'YDFormElement_HR.php' );
            $this->registerElement( 'selectnumeric', 'YDFormElement_SelectNumeric', 'YDFormElement_SelectNumeric.php' );
            $this->registerElement( 'countryselect', 'YDFormElement_Countryselect', 'YDFormElement_Countryselect.php' );
            $this->registerElement( 'stateselect', 'YDFormElement_Stateselect', 'YDFormElement_Stateselect.php' );

            // Add the rules
            $this->registerRule( 'value', array( 'YDValidateRules', 'value' ), 'YDValidateRules.php' );
            $this->registerRule( 'required', array( 'YDValidateRules', 'required' ), 'YDValidateRules.php' );
            $this->registerRule( 'maxlength', array( 'YDValidateRules', 'maxlength' ), 'YDValidateRules.php' );
            $this->registerRule( 'minlength', array( 'YDValidateRules', 'minlength' ), 'YDValidateRules.php' );
            $this->registerRule( 'rangelength', array( 'YDValidateRules', 'rangelength' ), 'YDValidateRules.php' );
            $this->registerRule( 'regex', array( 'YDValidateRules', 'regex' ), 'YDValidateRules.php' );
            $this->registerRule( 'email', array( 'YDValidateRules', 'email' ), 'YDValidateRules.php' );
            $this->registerRule( 'not_email', array( 'YDValidateRules', 'not_email' ), 'YDValidateRules.php' );
            $this->registerRule( 'ip', array( 'YDValidateRules', 'ip' ), 'YDValidateRules.php' );
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
            $this->registerRule( 'i_in_array', array( 'YDValidateRules', 'i_in_array' ), 'YDValidateRules.php' );
            $this->registerRule( 'i_not_in_array', array( 'YDValidateRules', 'i_not_in_array' ), 'YDValidateRules.php' );
            $this->registerRule( 'maxwords', array( 'YDValidateRules', 'maxwords' ), 'YDValidateRules.php' );
            $this->registerRule( 'minwords', array( 'YDValidateRules', 'minwords' ), 'YDValidateRules.php' );
            $this->registerRule( 'callback', array( 'YDValidateRules', 'callback' ), 'YDValidateRules.php' );
            $this->registerRule( 'uploadedfile', array( 'YDValidateRules', 'uploadedfile' ), 'YDValidateRules.php' );
            $this->registerRule( 'maxfilesize', array( 'YDValidateRules', 'maxfilesize' ), 'YDValidateRules.php' );
            $this->registerRule( 'mimetype', array( 'YDValidateRules', 'mimetype' ), 'YDValidateRules.php' );
            $this->registerRule( 'filename', array( 'YDValidateRules', 'filename' ), 'YDValidateRules.php' );
            $this->registerRule( 'extension', array( 'YDValidateRules', 'extension' ), 'YDValidateRules.php' );
            $this->registerRule( 'date', array( 'YDValidateRules', 'date' ), 'YDValidateRules.php' );
            $this->registerRule( 'time', array( 'YDValidateRules', 'time' ), 'YDValidateRules.php' );
            $this->registerRule( 'datetime', array( 'YDValidateRules', 'datetime' ), 'YDValidateRules.php' );
            $this->registerRule( 'minlength_escape', array( 'YDValidateRules', 'minlength_escape' ), 'YDValidateRules.php' );
            $this->registerRule( 'maxlength_escape', array( 'YDValidateRules', 'maxlength_escape' ), 'YDValidateRules.php' );
            $this->registerRule( 'rangelength_escape', array( 'YDValidateRules', 'rangelength_escape' ), 'YDValidateRules.php' );
            $this->registerRule( 'httpurl', array( 'YDValidateRules', 'httpurl' ), 'YDValidateRules.php' );
            $this->registerRule( 'maxhyperlinks', array( 'YDValidateRules', 'maxhyperlinks' ), 'YDValidateRules.php' );
            $this->registerRule( 'captcha', array( 'YDValidateRules', 'captcha' ), 'YDValidateRules.php' );
            $this->registerRule( 'timezone', array( 'YDValidateRules', 'timezone' ), 'YDValidateRules.php' );
            $this->registerRule( 'country', array( 'YDValidateRules', 'country' ), 'YDValidateRules.php' );
            $this->registerRule( 'state', array( 'YDValidateRules', 'state' ), 'YDValidateRules.php' );

            // Add the filters
            $this->registerFilter( 'trim', 'trim' );
            $this->registerFilter( 'lower', 'strtolower' );
            $this->registerFilter( 'upper', 'strtoupper' );
            $this->registerFilter( 'utf8_decode', 'utf8_decode' );
            $this->registerFilter( 'strip_html', 'strip_tags' );
            $this->registerFilter( 'safe_html', 'YDFormFilter_safe_html' );
            $this->registerFilter( 'dateformat', 'YDFormFilter_dateformat' );

            // Add the renderers
            $this->registerRenderer( 'array', 'YDFormRenderer_array', 'YDFormRenderer_array.php' );
            $this->registerRenderer( 'html', 'YDFormRenderer_html', 'YDFormRenderer_html.php' );
            $this->registerRenderer( 'xml', 'YDFormRenderer_xml', 'YDFormRenderer_xml.php' );

            // Clean the action URL
            $url = new YDUrl( $this->_action );
            $this->_action = $url->getUri();

        }

        /**
         *  This function sets the legend for a form. This will generate a fieldset tag and legend tag in the HTML.
         *
         *  @param  $legend     The legend to set for the field.
         */
        function setLegend( $legend ) {
            $this->_legend = $legend;
        }

        /**
         *  This function sets the form name.
         *
         *  @param  $name     Form name.
         */
        function setName( $name ) {
            $this->_name = $name;
        }

        /**
         *  This function will set the HTML that is added before and after the element
         *  label to indicate that the element is required. This only has affect if
         *  you use the default toHtml function.
         *
         *  @param $start   The HTML that should be added before the label.
         *  @param $end     (Optional) The HTML that should be added after the label.
         */
        function setHtmlRequired( $start, $end = '' ) {
            $this->_htmlRequiredStart = $start;
            $this->_htmlRequiredEnd = $end;
        }

        /**
         *  This function set the HTML that is added before and after the element
         *  label to indicate that the element has an errir. This only has affect if
         *  you use the default toHtml function.
         *
         *  @param $start   The HTML that should be added before the label.
         *  @param $end     The HTML that should be added after the label.
         */
        function setHtmlError( $start, $end ) {
            $this->_htmlErrorStart = $start;
            $this->_htmlErrorEnd = $end;
        }

        /**
         *  This function set the text that will be added at the top of the form
         *  to indicate that there are required items.
         *
         *  @param $text    The text to show.
         */
        function setRequiredNote( $text ) {
            $this->_requiredNote = $text;
        }

        /**
         *  This function registers a new element type.
         *
         *  @param $name    Name of the element.
         *  @param $class   The class name of the element definition.
         *  @param $file    (optional) The file containing the class definition for this element.
         */
        function registerElement( $name, $class, $file='' ) {
            $this->_regElements[ $name ] = array( 'class' => $class, 'file' => $file );
        }

        /**
         *  This function unregisters the element type.
         *
         *  @param $name    Name of the element.
         */
        function unregisterElement( $name ) {
            if ( array_key_exists( $name, $this->_regElements ) ) { unset( $this->_regElements[ $name ] ); }
        }

        /**
         *  This function registers a new validation rule.
         *
         *  @param $name        Name of the validation rule.
         *  @param $callback    The function name of the rule definition.
         *  @param $file        (optional) The file containing the class definition for this validation rule.
         */
        function registerRule( $name, $callback, $file='' ) {
            $this->_regRules[ $name ] = array( 'callback' => $callback, 'file' => $file );
        }

        /**
         *  This function unregisters the validation rule.
         *
         *  @param $name    Name of the validation rule.
         */
        function unregisterRule( $name ) {
            if ( array_key_exists( $name, $this->_regRules ) ) { unset( $this->_regRules[ $name ] ); }
        }

        /**
         *  This function will register a new filter.
         *
         *  @param $name        Name of the filter.
         *  @param $callback    The function name of the filter.
         *  @param $file        (optional) The file containing the definition for this filter.
         */
        function registerFilter( $name, $callback, $file='') {
            $this->_regFilters[ $name ] = array( 'callback' => $callback, 'file' => $file );
        }

        /**
         *  This function unregisters the filter.
         *
         *  @param $name    Name of the filter.
         */
        function unregisterFilter( $name ) {
            if ( array_key_exists( $name, $this->_regFilters ) ) { unset( $this->_regFilters[ $name ] ); }
        }

        /**
         *  This function registers a new form renderer.
         *
         *  @param $name    Name of the render.
         *  @param $class   The class name of the renderer definition.
         *  @param $file    (optional) The file containing the class definition for this renderer.
         */
        function registerRenderer( $name, $class, $file='' ) {
            $this->_regRenderers[ $name ] = array( 'class' => $class, 'file' => $file );
        }

        /**
         *  This function unregisters the renderer.
         *
         *  @param $name    Name of the renderer.
         */
        function unregisterRenderer( $name ) {
            if ( array_key_exists( $name, $this->_regRenderers ) ) { unset( $this->_regRenderers[ $name ] ); }
        }

        /**
         *  This function set the default value for a form element.
         *
         *  @param  $name     Name of the form element
         *  @param  $value    Default value for the form element
         *  @param  $raw      (optional) Indicates if the value is a raw value.
         */
        function setDefault( $name, $value, $raw=false ) {
            if ( array_key_exists( $name, $this->_elements ) ) {
                $element = & $this->getElement( $name );
                $element->setDefault( $value, $raw );
                if ( ! $this->isSubmitted() ) {
                    if ( $raw ) {
                        $element->setRawValue( $value );
                    } else {
                        $element->setValue( $value );
                    }
                }
            } else {
                $this->_defaults[ $name ] = $value;
            }
        }

        /**
         *  This function set the default values for the form.
         *
         *  @param $array   Associative array containing the default values.
         */
        function setDefaults( $array ) {
            foreach ( $array as $key => $val ) {
                $this->setDefault( $key, $val );
            }
        }

        /**
         *  This function set the default item for the form.
         *
         *  @param $name   The name of the default form item.
         */
        function setDefaultItem( $name ) {
            $this->_defaultItem = $name;
        }

        /**
         *  This function will add a new element to the form.
         *
         *  @param $type        The type of element to add.
         *  @param $name        The name of the form element.
         *  @param $label       (optional) The label for the form element.
         *  @param $attributes  (optional) The attributes for the form element.
         *  @param $options     (optional) The options for the elment.
         *
         *  @returns    A reference to the element that was added.
         */
        function & addElement( $type, $name, $label='', $attributes=array(), $options=array() ) {

            // Check if the element type is known
            if ( ! array_key_exists( $type, $this->_regElements ) ) {
                trigger_error( 'Unknown form element type "' . $type . '" for element "' . $name . '".', YD_ERROR );
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
            if ( isset( $this->_defaults[ $name ] ) ) {
                $instance->setDefault( $this->_defaults[ $name ] );
                unset( $this->_defaults[ $name ] );
            }
            if ( sizeof( $elementVars ) == 0 )  {
                if ( ! $this->isSubmitted() ) {
                    if ( ! is_null( $instance->_default ) ) {
                        $instance->setValue( $instance->getDefault() );
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
         *  This function will add multiple form elements at once.
         *
         *  @param $type        The type of element to add.
         *  @param $names       Array with the names of the elements that should be added.
         *  @param $label       (optional) The label for the form element.
         *  @param $attributes  (optional) The attributes for the form element.
         *  @param $options     (optional) The options for the elment.
         */
        function addElements( $type, $names, $label='', $attributes=array(), $options=array() ) {
            foreach ( $names as $name ) {
                $this->addElement( $type, $name, $label, $attributes, $options );
            }
        }

        /**
         *  This function removes the specified form element.
         *
         *  @param $name    The name of the form element.
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
         *  This function returns a reference to the specified form element.
         *
         *  @param $name    The name of the form element.
         *
         *  @returns    A reference to the specified form element.
         */
        function & getElement( $name ) {

            // Check if the element exists
            if ( ! $this->isElement( $name ) ) {
                trigger_error( 'The specified element "' . $name . '" does not exist.', YD_ERROR );
            }

            // Get the element
            $element = & $this->_elements[ $name ];

            // Return a reference to the element
            return $element;

        }


        /**
         *  This function returns all elements of a given type.
         *
         *  @param $type    Form element type.
         *
         *  @returns    Array of form elements
         */
        function & getElementsByType( $type ){

            $elements = array();
            foreach( $this->_elements as $name => $el ){
                if ( $el->getType() == $type ){
                    array_push( $elements, & $this->_elements[ $name ] );
                }
            }

            return $elements;
        }


        /**
         *  This function returns all form elements
         *
         *  @returns    An array with form elements
         */
        function getElements() {
            return $this->_elements;
        }


        /**
         *  Checks if a form element name is already defined.
         *
         *  @param  $name    The element name
         *  
         *  @returns  A boolean indicating if the element exists.
         */
        function isElement( $name ) {
            return array_key_exists( $name, $this->_elements ) ? true : false;
        }

        /**
         *  Add a filter to the form for the specified field.
         *
         *  @param  $element       The element to apply the filter on. If you specify an array, it will add the filter for
         *                         each element in the array.
         *  @param  $filter        The name of the filter to apply.
         *  @param  $options       (Optional) Custom filter options.
         */
        function addFilter( $element, $filter, $options = null ) {
            if ( is_string( $element) && $element == '__ALL__' ) {
                $element = array_keys( $this->_elements );
            }
            if ( is_array( $element ) ) {
                foreach ( $element as $e ) {
                    $this->addFilter( $e, $filter, $options );
                }
                return;
            }
            if ( ! array_key_exists( $filter, $this->_regFilters ) ) {
                trigger_error( 'Unknown filter "' . $filter . '" for element "' . $element . '"', YD_ERROR );
            }
            if ( ! empty( $this->_regFilters[ $filter ]['file'] ) ) {
                YDInclude( $this->_regFilters[ $filter ]['file'] );
            }
            if ( ! @ is_array( $this->_filters[ $element ] ) ) {
                $this->_filters[ $element ] = array();
            }
            array_push( $this->_filters[ $element ], array( $filter, $options ) );
        }

        /**
         *  This function adds the filter to every element specified in the array.
         *
         *  @param $elements Array of elements to add the filter to.
         *  @param  $filter     The name of the filter to apply.
         */
        function addFilters( $elements, $filter ) {
            if ( is_array( $elements ) ) {
                foreach ( $elements as $element ) {
                    $this->addFilter( $element, $filter );
                }
            }
        }

        /**
         *  Add a rule to the form for the specified field.
         *
         *  @param  $element    The element to apply the rule on. If you specify an array, it will add the rule for each
         *                      element in the array. If you specify '__ALL__', it will add the rule to all form elements
         *  @param  $rule       The name of the rule to apply.
         *  @param  $error      The error message to show if an error occured.
         *  @param  $options    (optional) The options to pass to the validator function.
         */
        function addRule( $element, $rule, $error, $options=null ) {
            if ( is_string( $element) && $element == '__ALL__' ) {
                $element = array_keys( $this->_elements );
            }
            if ( is_array( $element ) ) {
                foreach ( $element as $e ) {
                    $this->addRule( $e, $rule, $error, $options );
                }
                return;
            }
            if ( ! array_key_exists( $rule, $this->_regRules ) ) {
                trigger_error( 'Unknown rule "' . $rule . '" for element "' . $element . '"', YD_ERROR );
            }
            if ( ! empty( $this->_regRules[ $rule ]['file'] ) ) {
                YDInclude( $this->_regRules[ $rule ]['file'] );
            }
            if ( ! @ is_array( $this->_rules[ $element ] ) ) {
                $this->_rules[ $element ] = array();
            }
            array_push( $this->_rules[ $element ], array( 'rule' => $rule, 'error' => $error, 'options' => $options ) );

            if ( $rule == 'captcha' ) $this->addRule( $element, 'required', $error, $options );
        }

        /**
         *  Add a rule to compare different form elements with each other.
         *
         *  @param  $elements   The array of elements to compare with each other.
         *  @param  $rule       The name of the rule to apply. This can be "equal", "asc" or "desc".
         *  @param  $error      The error message to show if an error occured.
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
         *  Add rule that point to a custom function and is not bound to a specific form element. The callback function
         *  should return an associative array with the names of the fields and the errors. You can use the special name
         *  __ALL__ to add a form wide error.
         *
         *  @param $callback    The callback of the funtion to perform for this form rule.
         *  @param $options     (Optional) Custom rule options
         */
        function addFormRule( $callback, $options = null ) {
            array_push( $this->_formrules, array( $callback, $options ) );
        }

        /**
         *  This function returns the value of the specified form element.
         *
         *  @param $name    The name of the form element.
         *
         *  @returns    The value to the specified form element.
         */
        function getValue( $name ) {

            // Get the actual element value
            $element = & $this->getElement( $name );
            $type = $element->getType();
            $value = $element->getValue();
            $applyFilters = $element->_applyFilters;

            // Filters should only be applied if the form is submitted and if the element type supports it.
            if ( $this->isSubmitted() ) {
                if ( $applyFilters == true ) {
                    $value = $this->_applyFilter( $name, $value );
                }
            } else {
                if ( ! is_null( $element->_default ) ) {
                    $value = $element->getDefault();
                }
            }

            // Special treatment for GET forms
            if ( $this->_method == 'get' && ! $this->isSubmitted() ) {
                if ( ! isset( $_GET[$this->_name . '_' . $name] ) && ! is_null( $element->_default ) ) {
                    $value = $element->getDefault();
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

            // Sort the value if an array
            if ( is_array( $value ) ) {
                ksort( $value );
            }

            // Return the value
            return $value;

        }

        /**
         *  This function returns all the values for the form as an associative array.
         *  NOTE: spans are ignored
         *
         *  @returns    The values for the form as an associative array.
         */
        function getValues() {
            $vars = array();
            foreach ( $this->_elements as $name => $element ) {
                $elType = $element->getType();
                if ( $elType == 'span' || $elType == 'fieldset' || $elType == 'hr' ) {
                    continue;
                }
                $vars[ $name ] = $this->getValue( $name );
            }
            return $vars;
        }

        /**
         *  This function returns all active error messages
         *
         *  @param $asString  (Optional) Boolean value that defines if we want a string or an array
         *  @param $separator  (Optional) String that separates all messages (used when we want errors as string)         *
         *  @param $initial  (Optional) String to prepend to all messages (used when we want errors as string)
         *
         *  @returns    An array/string containing all active error messages
         */
        function getErrors( $asString = false, $separator = "\n", $initial = '' ) {
            $errors = array_unique( array_values( $this->_errors ) );
            if ( $asString ) {
                $errors = $initial . implode( $separator, $errors );
            }
            return $errors;
        }

        /**
         *  Gets the name of the form.
         *
         *  @returns        The name of the form.
         */
        function getName() {
            return $this->_name;
        }

        /**
         *  This function will check if the form was submitted or not.
         *
         *  @returns    Boolean indicating if the form was submitted or not.
         */
        function isSubmitted() {
            $vars = ( $this->_method == 'get' ) ? $_GET : $_POST;
            foreach ( $vars as $key=>$value ) {
                $key = preg_replace( '/^' . $this->_name . '_/', '', $key );
                if ( array_key_exists( $key, $this->_elements ) ) {
                    return true;
                };
            }
            return false;
        }

        /**
         *  This function will check if the specified button was clicked or not.
         *
         *  @param $button  The name of the button.
         *
         *  @returns    Boolean indicating if the button was clicked or not.
         */
        function isClicked( $button ) {
            $element = $this->getElement( $button );
            if ( $element->isButton() === true ) {
                if ( array_key_exists( $this->_name . '_' . $element->_name, $this->_formVars ) ) {
                    return true;
                }
            }
            return false;
        }

        /**
         *  This function will return if an element have been modified from it's default value.
         *
         *  @param $name  The element name.
         *
         *  @returns  A boolean indicating if the element value was modified.
         */
        function isModified( $name ) {
            if ( $this->isSubmitted() ) {
                $element = & $this->getElement( $name );
                return $element->isModified();
            }
            return false;
        }

        /**
         *  This function will return true if the form is valid. If not, it will return false.
         *
         *  If no rules for the form, the form is considered to be valid.
         *  If no values for this form where submitted, the form is considered to be valid.
         *
         *  It will iterate over all the rules and apply them to each field after having applies the filters.
         *  Errors will be put on the error stack. In the end, it returns false or true.
         *
         *  @param  $customvalues   (Optional) custom values to use for validation
         *
         *  @returns    Boolean indicating if the form is valid or not.
         */
        function isValid( $customvalues = null ) {
            return $this->validate( $customvalues );
        }

        /**
         *  This function will return true if the form is valid. If not, it will return false.
         *
         *  If no rules for the form, the form is considered to be valid.
         *  If no values for this form where submitted, the form is considered to be valid.
         *
         *  It will iterate over all the rules and apply them to each field after having applies the filters.
         *  Errors will be put on the error stack. In the end, it returns false or true.
         *
         *  @param  $customvalues   (Optional) custom values to use for validation
         *
         *  @returns    Boolean indicating if the form is valid or not.
         */
        function validate( $customvalues = null ) {

            // Form should be submitted
            if ( is_null( $customvalues ) && $this->isSubmitted() == false ) {
                return false;
            }

            // If custom values are defined parse them
            if ( ! is_null( $customvalues ) ) {
                foreach ( $customvalues as $key => $value ) {

                    // Remove the form name from the element name
                    $key = preg_replace( '/^' . $this->_name . '_/', '', $key );

                    // Check if the element exists
                    if ( array_key_exists( $key, $this->_elements ) ) {

                        // Set the value
                        $this->_elements[ $key ]->setValue( $value );
                        $this->setDefault( $key, $value );

                    }
                }
            }

            // Check if there are any rules, if not, form is valid and return true
            if ( sizeof( $this->_rules ) == 0 && sizeof( $this->_formrules ) == 0 && sizeof( $this->_comparerules ) == 0) {
                return true;
            }

            // Apply the element rules
            foreach ( $this->_rules as $element=>$rules ) {

                // Check if the element exists
                if ( array_key_exists( $element, $this->_elements ) ) {

                    // Check if the element is required
                    $required = false;
                    foreach ( $rules as $rule ) {
                        if ( strtolower( $rule['rule'] ) == 'required' ) {
                            $required = true;
                            break;
                        }
                    }

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
                        
                        // Date elements and rules
                        if ( in_array( $rule['rule'], array( 'date', 'time', 'datetime' ) ) ) {
                            $el = & $this->getElement( $element );
                            $type = $el->getType();
                            if ( in_array( $type, array( 'date', 'dateselect', 'timeselect', 'datetimeselect' ) ) ) {
                                $rule['options'] = array ( 'options' => $el->getOptions(), 'elements' => $el->_getElements() );
                            }
                        }

                        // Get the value of a form element
                        $value = $this->getValue( $element );
                        if ( is_array( $value ) && isset( $value['name'] ) ) {
                            if ( ! in_array( $rule['rule'], array( 'uploadedfile', 'maxfilesize', 'mimetype', 'filename', 'extension' ) ) ) {
                                $value = $value['name'];
                            }
                        }

                        // If a field is required, we check if it's set and check the extra validations.
                        // If a field is NOT required and not set, no extra validation is done.
                        // If a field is NOT required and set, the extra validation is done.
                        $mandatory = true;
                        if ( ! $required ) {
                            if ( is_null( $value ) ) {
                                $mandatory = false;
                            } else if ( is_string( $value ) && ! strlen( $value ) ) {
                                $mandatory = false;
                            } else if ( is_array( $value ) ) {
                                if ( empty( $value ) ) {
                                    $mandatory = false;
                                } else if ( isset( $value['name'] ) && empty( $value['name'] ) ) {
                                    $mandatory = false;
                                }
                            }
                        }

                        // Check mandatory elements
                        if ( $mandatory ) {

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
                            if ( $obj->hasMethod( 'getTimeStamp' ) ) {
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
                            if ( $obj->hasMethod( 'getTimeStamp' ) ) {
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

//                // Check if the callback is valid
//                if ( ! is_callable( $rule[0] ) ) {
//                    trigger_error( 'The callback specified for the form "' . $rule[0] . '" is not valid', YD_ERROR );
//                }

                // Get the form values
                $values = $this->getValues();

                // Execute the rule
                if ( is_null( $rule[1] ) ) $result = call_user_func( $rule[0], $values );
                else                       $result = call_user_func( $rule[0], $values, $rule[1] );

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

                // Check all elements
                foreach ( $this->_elements as $key => $val ) {
                    if ( $val->_type == 'password' ) {
                        $this->_elements[$key]->setValue( '' );
                    }
                }

                // Return false
                return false;

            }

            // All went fine, return true
            return true;

        }

        /**
         *  This function will render a form and return the rendered contents.
         *
         *  @param  $type   The renderer to use.
         *
         *  @returns    The rendered form.
         */
        function render( $type ) {
            $instance = $this->getRenderer( $type );
            return $instance->render();
        }

        /**
         *  This function will use the import feature of the renderers to define
         *  the object with new settings from the content returned by the render method.
         *
         *  @param  $type      The renderer to use.
         *  @param  $content   The content.
         *  @param  $options   (optional) Additional options.
         */
        function import( $type, $content, $options=array() ) {
            $instance = $this->getRenderer( $type );
            $new_this = $instance->import( $content, $options );
            foreach ( get_object_vars( $new_this ) as $key => $val ) {
                $this->$key = $val;
            }
        }

        /**
         *  This function will return an instance of a renderer class.
         *
         *  @param  $type   The renderer to use.
         *
         *  @returns    The renderer object.
         */
        function getRenderer( $type ) {
            if ( ! array_key_exists( $type, $this->_regRenderers ) ) {
                trigger_error( 'Unknown for renderer type "' . $type . '".', YD_ERROR );
            }
            if ( ! empty( $this->_regRenderers[ $type ]['file'] ) ) {
                YDInclude( $this->_regRenderers[ $type ]['file'] );
            }
            $class = $this->_regRenderers[ $type ]['class'];
            return new $class( $this );
        }

        /**
         *  This function will return the form as an array.
         *
         *  @returns    The form as an array.
         */
        function toArray() {
            return $this->render( 'array' );
        }

        /**
         *  This function will return the form as HTML.
         *
         *  @returns    The form as HTML text.
         */
        function toHtml() {
            return $this->render( 'html' );
        }

        /**
         *  This function will output the form as HTML.
         */
        function display() {
            echo( $this->toHtml() );
        }

        /**
         *  This function will convert an associative array to it's HMTL equivalent using keys as attribute names and
         *  the values as attribute values.
         *
         *  @param $array   An associative array.
         *
         *  @returns    The associative array as HTML.
         *
         *  @internal
         */
        function _convertToHtmlAttrib( $array ) {
            if ( ! is_array( $array ) ) { return ''; }
            if ( sizeof( $array ) == 0 ) { return ''; }
            $out = '';
            foreach ( $array as $key=>$value ) {
                if ( ! is_object( $value ) ){
                    $out .= ' ' . strval( $key ) . '="' . str_replace( '&amp;', '&', htmlspecialchars( strval( $value ) ) ) . '"';
                }
            }
            return $out;
        }

        /**
         *  This function returns the default value of an element.
         *
         *  @param $name     The element name
         *  @param $options  (Optional) Custom options (eg, used on checkboxgroup to specify internal element)
         */
        function disable( $name, $options = null ) {

                $element = & $this->getElement( $name );
                $element->disable( $options );
        }

        /**
         *  Function that will apply the actual filters to the named element.
         *
         *  @param  $name   The name of the field to apply the filter to.
         *  @param  $value  The value to apply the filter on.
         */
        function _applyFilter( $name, $value ) {
            if ( array_key_exists( $name, $this->_filters ) ) {
                if ( is_array( $this->_filters[ $name ] ) ) {
                    foreach ( $this->_filters[ $name ] as $filter ) {
                        if ( ! is_callable( $this->_regFilters[ $filter[0] ]['callback'] ) ) {
                            trigger_error(
                                'The callback specified for the filter "' . $name . '" is not valid', YD_ERROR
                            );
                        }
                        if ( is_array( $value ) ) {

                            // check if element is special: 'date', 'dateselect' or 'datetimeselect'
                            $element = & $this->getElement( $name );

                            // if array is special we should pass all array to the function (instead of map the function to each element)
                            if ( in_array( $element->getType(), array( 'date', 'dateselect', 'datetimeselect' ) ) ){
                                if ( is_null( $filter[1] ) ) $value = call_user_func( $this->_regFilters[ $filter[0] ]['callback'], $value );
                                else                         $value = call_user_func( $this->_regFilters[ $filter[0] ]['callback'], $value, $filter[1] );

                            // otherwise we map the function to all array elements
                            }else{
                                foreach ( $value as $key=>$x ) {
                                    if ( is_null( $filter[1] ) ) $value[$key] = call_user_func( $this->_regFilters[ $filter[0] ]['callback'], $value[$key], $filter[1] );
                                    else                         $value[$key] = call_user_func( $this->_regFilters[ $filter[0] ]['callback'], $value[$key] );
                                }
                            }
                        } else {
                            if ( is_null( $filter[1] ) ) $value = call_user_func( $this->_regFilters[ $filter[0] ]['callback'], $value );
                            else                         $value = call_user_func( $this->_regFilters[ $filter[0] ]['callback'], $value, $filter[1] );
                        }
                    }
                }
            }
            return $value;
        }

        /**
         *  This function set raw default values for the form.
         *
         *  @param $array   Associative array containing the default values.
         */
        function setRawDefaults( $array ) {
            foreach ( $array as $key => $val ) {
                $this->setDefault( $key, $val, true );
            }
        }

        /**
         *  This function returns all raw values for the form as an associative array.
         *
         *  @returns  The values for the form as an associative array.
         */
        function getRawValues() {
            $vars = array();
            foreach ( $this->_elements as $name => $element ) {
                $vars[ $name ] = $element->getRawValue();
            }
            return $vars;
        }

        /**
         *  This function returns all default values of the form.
         *
         *  @returns  The default values of the form as an associative array.
         */
        function getDefaults() {
            $vars = array();
            foreach ( $this->_elements as $name => $element ) {
                if ( ! is_null( $element->_default ) ) {
                    $vars[ $name ] = $element->getDefault();
                }
            }
            return $vars;
        }

        /**
         *  This function returns the default value of an element.
         *
         *  @param $name  The element name.
         *
         *  @returns      The default value of the element.
         */
        function getDefault( $name ) {
            $element = & $this->getElement( $name );
            if ( ! is_null( $element->_default ) ) {
                return $element->getDefault();
            }
        }

        /**
         *  This function will return all elements that were modified from it's default value.
         *
         *  @returns  The modified elements and it's values as an associative array.
         */
        function getModifiedValues() {
            $modified = array();
            if ( $this->isSubmitted() ) {
                foreach ( $this->_elements as $name => $element ) {
                    if ( $element->isModified() ) {
                        $modified[ $name ] = $this->getValue( $name );
                    }
                }
            }
            return $modified;
        }
    }

    /**
     *  This is the base class for all form elements.
     *
     *  @ingroup YDForm
     */
    class YDFormElement extends YDBase {

        /**
         *  This is the class constructor for the YDFormElement class.
         *
         *  @param $form        The name of the form to which this element is connected.
         *  @param $name        The name of the form element.
         *  @param $label       (optional) The label for the form element.
         *  @param $attributes  (optional) The attributes for the form element.
         *  @param $options     (optional) The options for the elment.
         */
        function YDFormElement( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDBase();

            // Initialize the variables
            $this->_form = $form;
            $this->_name = $name;
            $this->_label = $label;
            $this->_attributes = $attributes;
            $this->_type = '';
            $this->_value = '';
            $this->_default = null;
            $this->_raw_default = false;
            $this->_isButton = false;
			$this->_label_attributes = array();

            // Initialize options
            if ( is_array( $options ) ){      
                $this->_options = $options;
            }else{
                $this->_options = array();
                foreach( explode( ';', $options ) as $opt ){
                    $this->_options[ trim( $opt ) ] = $opt;
                }
            }

            // Indicate where the label should be
            $this->_placeLabel = 'before';

            // Indicate if filters need to be applied
            $this->_applyFilters = true;

            // Set the default ID attribute
            if ( ! isset( $this->_attributes['id'] ) ) {
                $this->_attributes['id'] = $this->_form . '_' . $this->_name;
            }

        }

        /**
         *  Function to set the attribute of a form element
         *
         *  @param  $key    The name of the attribute
         *  @param  $val    The value of the attribute
         */
        function setAttribute( $key, $val ) {
            $this->_attributes[$key] = $val;
        }

        /**
         *  Function to set attributes of a form element
         *
         *  @param  $attributes    Associative array of attributes
         */
        function setAttributes( $attributes ) {
            foreach ( $attributes as $k => $v )
                $this->setAttribute( $k, $v );
        }

        /**
         *  Function to disable this element
         *
         *  @param  $options    (Optional) Custom options
         */
        function disable( $options = null ) {
            $this->setAttribute( 'disabled', 'disabled' );
        }

        /**
         *  Function to check if an item is disabled
         *
         *  @returns  TRUE if disabled. FALSE otherwise
         */
        function isDisabled() {
            return isset( $this->_attributes[ 'disabled' ] );
        }

        /**
         *  Function to enable this element
         *
         *  @param  $options    (Optional) Custom options
         */
        function enable( $options = null ) {
            $this->delAttribute( 'disabled' );
        }

        /**
         *  Function to delete an attribute of a form element
         *
         *  @param  $key    The name of the attribute
         */
        function delAttribute( $key ) {
            if ( isset( $this->_attributes[$key] ) ) unset( $this->_attributes[$key] );
        }

        /**
         *  Function to delete all attributes of a form element
         */
        function delAttributes() {
            $this->_attributes = array() ;
        }

        /**
         *  Function to set the option of a form element
         *
         *  @param  $key    The name of the option
         *  @param  $val    The value of the option
         */
        function setOption( $key, $val ) {
            $this->_options[$key] = $val;
        }

        /**
         *  Function to set options of a form element
         *
         *  @param  $array    Associative array with options
         */
        function setOptions( $array ) {
            foreach( $array as $key => $val )
                $this->setOption( $key, $val );
        }

        /**
         *  Function to delete an option of a form element
         *
         *  @param  $key    The name of the option
         */
        function delOption( $key ) {
            if ( isset( $this->_options[$key] ) ) unset( $this->_options[$key] );
        }

        /**
         *  Function to delete all options of a form element
         */
        function delOptions() {
            $this->_options = array();
        }

        /**
         *  This function sets the value for the element.
         *
         *  @param  $val    (optional) The value for this object.
         */
        function setValue( $val='' ) {
            $this->_value = $val;
        }

        /**
         *      This function returns the value of the element.
         *
         *      @returns        Value of this object.
         */
        function getValue() {
            return $this->_value;
        }

        /**
         *      This function sets raw value for the element.
         *
         *      @param  $val    (optional) The value for this object.
         */
        function setRawValue( $val='' ) {
            $this->_value = $val;
        }

        /**
         *      This function will return the raw value of the element.
         *
         *      @returns        Raw value of this object.
         */
        function getRawValue() {
            return $this->_value;
        }

        /**
         *      This function sets the default value of the element.
         *
         *      @param  $val    The default value of this object.
         *      @param  $raw    (optional) Boolean indicating if the default value is a raw value.
         */
        function setDefault( $val, $raw=false ) {
            $this->_default = $val;
            $this->_raw_default = $raw;
        }

        /**
         *      This function returns the default value of the element.
         *
         *      @returns        Default value of this object.
         */
        function getDefault() {
            return $this->_default;
        }

        /**
         *      Gets the type of the form element.
         *
         *      @returns        The type of the form element.
         */
        function getType() {
            return $this->_type;
        }


        /**
         *      Checks the type of the form element.
         *
         *      @param  $type	Element type or array of types to check
         *      @returns        Boolean true or false.
         */
        function isType( $type ) {
			
			// if element is a list of types check array
			if ( is_array( $type ) ){
				return in_array( $this->_type, $type );
			}
			
            return ( $type == $this->_type );
        }

        
        /**
         *      Gets a specific attribute
         *
         *      @param  $att    Attribute to search
         *      @returns        The value
         */
        function getAttribute( $att ) {
            return isset( $this->_attributes[ $att ] ) ? $this->_attributes[ $att ] : null ;
        }

        /**
         *      Gets a specific option
         *
         *      @param  $option    Attribute to search
         *      @returns           The value
         */
        function getOption( $option ) {
            return isset( $this->_options[ $option ] ) ? $this->_options[ $option ] : null ;
        }

        /**
         *      Gets the options of the form element.
         *
         *      @returns        The options of the form element.
         */
        function getOptions() {
            return $this->_options;
        }

        /**
         *      Gets the name of the form.
         *
         *      @returns        The name of the form.
         */
        function getForm() {
            return $this->_form;
        }

        /**
         *      Gets the name of the form element.
         *
         *      @returns        The name of the form element.
         */
        function getName() {
            return $this->_name;
        }

        /**
         *      Gets the label of the form element.
         *
         *      @param  $html   (Optional) Return label as html
         *      @returns        The label of the form element.
         */
        function getLabel( $html = false ) {

			if ( $html ) return '<span ' . YDForm::_convertToHtmlAttrib( $this->_label_attributes ) . '><label for="' . $this->_attributes['id'] . '">' . $this->_label . '</label></span>';

            return $this->_label;
        }


        /**
         *      Sets label attribute
         *
         *      @param  $attribute	Attribute name
         *      @param  $value 		Attribute value
         */
        function setLabelAttribute( $attribute, $value ) {

			$this->_label_attributes[ $attribute ] = $value;
        }


        /**
         *      This function returns a boolean indicating if the element value was
         *      modified from it's default value.
         *
         *      @returns        Boolean indicating if the element was modified.
         */
        function isModified() {
            if ( $this->_raw_default ) {
                if ( $this->getRawValue() != $this->getDefault() ) {
                    return true;
                }
            } else {
                if ( $this->getValue() != $this->getDefault() ) {
                    return true;
                }
            }
            return false;
        }

        /**
         *  Indicates if the element is a button or not.
         *
         *  @returns    Boolean indicating if the element is a button or not.
         */
        function isButton() {
            if ( $this->_isButton === true ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *  This function returns the element as an array.
         *
         *  @returns    The form element as an array.
         */
        function toArray() {
            if ( $this->_placeLabel != 'after' && $this->_placeLabel != 'none' ) {
                $this->_placeLabel = 'before';
            }
            return array(
                'name'        => $this->_name,
                'id'          => $this->_attributes['id'],
                'value'       => $this->_value,
                'default'     => $this->_default,
                'type'        => $this->_type,
                'label'       => $this->_label,
                'attributes'  => $this->_attributes,
                'label_html'  => $this->getLabel( true ),
                'options'     => $this->_options,
                'placeLabel'  => $this->_placeLabel,
                'html'        => $this->toHtml(),
                'isButton'    => $this->isButton(),
            );
        }

        /**
         *  This function returns the element as HTML.
         *
         *  @returns    The form element as HTML text.
         */
        function toHtml() {
        }


        /**
         *  This function returns the element as HTML with label
         *
         *  @returns    The form element as HTML text.
         */
        function toFullHtml( $separator = '', $begin = '', $end = '') {
		
			return $begin . $this->getLabel( true ) . $separator . $this->toHTML() . $end;
        }


        /**
         *	This function returns the default javascript event of this element
         */
        function getJSEvent(){ 
            die( 'Element type (' . $this->getType() . ') is not supported in YDAjax' );
        }

        /**
         *	This function gets an element value using javascript
         *
         *	@param $options		(optional) The options for the elment.
         */
        function getJS( $options = null ){ 
            die( 'Element type (' . $this->getType() . ') is not supported in YDAjax events' );
        }

        /**
         *	This function sets an element value using javascript
         *
         *	@param $result		The result value
         *	@param $attribute	(optional) Element attribute
         *	@param $options		(optional) The options for the elment.
         */
        function setJS( $result, $attribute = null, $options = null ){ 
            die( 'Element type (' . $this->getType() . ') is not supported in YDAjax responses' );
        }

    }

    /**
     *  This is the class that is able to render a form object to whatever.
     *
     *  @ingroup YDForm
     */
    class YDFormRenderer extends YDBase {

        /**
         *  This is the class constructor for the YDFormRenderer_html class.
         *
         *  @param $form        The form that needs to be rendered.
         */
        function YDFormRenderer( $form ) {
            $this->_form = & $form;
        }

        /**
         *  This function renders the form.
         *
         *  @returns    The rendered form.
         */
        function render() {
            return '';
        }

        /**
         *  This function will parse the contents of a render and return
         *  a new YDForm object.
         *
         *  @returns    A YDForm object.
         */
        function import( $content, $options=array() ) {
            trigger_error( '"' . $this->getClassName() . '" does not have an import method defined.', YD_ERROR );
        }

    }

    /**
     *  This filter checks data for any possible XSS HTML code.
     *
     *  It basically does the following:
     *  * opening tag without its closing tag
     *  * closing tag without its opening tag
     *  * any of these tags: "base", "basefont", "head", "html", "body", "applet", "object", 
     *    "iframe", "frame", "frameset", "script", "layer", "ilayer", "embed", "bgsound", 
     *    "link", "meta", "style", "title", "blink", "xml" etc.
     *  * any of these attributes: on*, data*, dynsrc
     *  * javascript:/vbscript:/about: etc. protocols
     *  * expression/behavior etc. in styles
     *  * any other active content
     *
     *  @param  $data   The data to filter.
     *
     *  @returns The filtered data as a string.
     */
    function YDFormFilter_safe_html( $data ) {
        require_once( YD_DIR_HOME . '/3rdparty/safehtml/classes/safehtml.php' );
        $safehtml = & new safehtml();
        return $safehtml->parse( $data );
    }

    /**
     *  This filter reads a date form element result and returns a custom result
     *
     *  @param  $data     The data to filter.
     *  @param  $option   (Optional) Filter option. By default, is returned the timestamp
     *
     *  @returns The filtered data as a string.
     */
    function YDFormFilter_dateformat( $data, $option = "timestamp" ) {
        if ( isset( $data[ $option ] ) ) {
            return $data[ $option ];
        }
        YDInclude( 'YDUtil.php' );
        return YDStringUtil::formatDate( $data, $option );
    }

?>