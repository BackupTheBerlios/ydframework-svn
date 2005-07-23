<?

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

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

    // Include the needed libraries
    include_once( dirname( __FILE__ ) . '/../../YDClasses/YDForm.php' );
    include_once( dirname( __FILE__ ) . '/xmlize.inc.php');

    /**
     *  Class definition for the YDXMLForm addon.
     */
    class YDXMLForm extends YDForm{

        /**
         *	This is the class constructor for the YDXMLForm class.
         *
         *	@param $name		The name of the form.
         *	@param $method		(optional) Method used for submitting the form. Normally, this is either POST or GET.
         *	@param $action		(optional) Action used for submitting the form. If not specified, it will default to the
         *						URI of the current request.
         *	@param $target		(optional) HTML target for the form.
         *	@param $attributes	(optional) Attributes for the form.
         */
        function YDXMLForm( $name, $method='post', $action='', $target='_self', $attributes=array() ) {

            // Initialize YDForm
            $this->YDForm( $name, $method='post', $action='', $target='_self', $attributes=array() );

            // Register the XML renderer
            $this->registerRenderer( 'xml', 'YDFormRenderer_xml', dirname( __FILE__ ) . '/YDFormRenderer_xml.php' );

        }

        /**
         *  Create the form using the XML data.
         *
         *  @param  $xml    The XML data containing the form.
         *  @param  $id     (optional) The id of the form.
         */
        function useXML( $xml, $id = 'default'){

            // parse xml structure
            $form = xmlize($xml);

            // get form NAME
            $this->_name = $form['form']['@']['name'];

            // get form METHOD
            $this->_method = $form['form']['@']['method'];

            // get form ACTION
            $this->_action = $form['form']['@']['action'];

            // get form TARGET
            $this->_target = $form['form']['@']['target'];

            // get form attributes
            $this->_attributes = array();

            // loop attributes if they exist
            if (is_array($form['form']['#']['attributes'][0]['#']))
                foreach ($form['form']['#']['attributes'][0]['#']['attribute'] as $attribute)
                    $this->_attributes[$attribute['@']['name']] = $attribute['@']['value'];

            // array to store default values of elements
            $default_values = array();

            // add ELEMENTS if exist
            if (is_array($form['form']['#']['elements'][0]['#']))
                foreach ($form['form']['#']['elements'][0]['#']['element'] as $element){

                    // get TYPE
                    $type = $element['@']['type'];

                    // get NAME
                    $name = $element['@']['name'];

                    // get LABEL based on id
                    $label = '';

                    // check if exist 'default' labels or custom label defined by id
                    foreach ($element['#']['labels'][0]['#']['label'] as $lab){

                            // if there's a default label store value
                            if ($lab['@']['id'] == 'default') $label = $lab['@']['value'];

                            // if there's a custom label store value too
                            else if ($lab['@']['id'] == $id) $label_extra = $lab['@']['value'];
                    }

                    // if custom label was found use it instead of default
                    if (isset($label_extra)) $label = $label_extra;

                    // get VALUE based on id
                    $value = '';

                    // check if exist 'default' value or custom label defined by id
                    foreach ($element['#']['values'][0]['#']['value'] as $val){

                            // if there's a default value store value
                            if ($val['@']['id'] == 'default') $value = $val['@']['value'];

                            // if there's a custom value store it
                            else if ($val['@']['id'] == $id) $value_extra = $val['@']['value'];
                    }

                    // if custom value was found use it instead of default
                    if (isset($value_extra)) $value = $value_extra;

                    // add value to default values array
                    $default_values[$name] = $value;


                    // get ATTRIBUTES
                    $attributes = array();
                    $tmp = $element['#']['attributes'][0]['#'];

                    // if has attributes create push names and values to array
                    if ($tmp != '')
                        foreach ($tmp['attribute'] as $attribute)
                            $attributes[$attribute['@']['name']] = $attribute['@']['value'];

                    // get OPTIONS
                    $options = array();
                    $tmp = $element['#']['options'][0]['#'];

                    // if has options create array ( name => value )
                    if ($tmp != '')
                        foreach ($tmp['option'] as $option)
                                $options[$option['@']['name']] = $option['@']['value'];


                    // add element to form
                    $this->addElement($type, $name, $label, $attributes, $options);
                }

            // set form default based on values store in $default_values
            $this->setDefaults($default_values);

            // add RULES if exist
            if (is_array($form['form']['#']['rules'][0]['#']))
                foreach ($form['form']['#']['rules'][0]['#']['rule'] as $rule){

                    // get rule type
                    $type = $rule['@']['type'];

                    // get element name
                    $element = $rule['@']['element'];

                    // get label error based on id
                    $label = '';

                    // check if exist 'default' labels or custom label defined by id
                    foreach ($rule['#']['errors'][0]['#']['error'] as $error){

                            // if there's a default label store value
                            if ($error['@']['id'] == 'default') $label = $error['@']['value'];

                            // if there's a custom label store value too
                            else if ($error['@']['id'] == $id) $label_extra = $error['@']['value'];
                    }

                    // if custom label was found use it instead of default
                    if (isset($label_extra)) $label = $label_extra;

                    // get options
                    $options = array();

                    // test if it has option or options push to array
                    if (is_array($rule['#']['options'][0]['#']))
                        foreach ($rule['#']['options'][0]['#']['option'] as $option)
                            $options[$option['@']['id']] = $option['@']['value'];

                    // add form rule
                    // if option is empty don't add it
                    if (empty($options)) $this->addRule( $element, $type, $label);

                    // if is one option get value and add value instead of array
                    else if (count($options) == 1) $this->addRule( $element, $type, $label, $options['']);

                    // otherwise add rule with an array of options
                    else $this->addRule( $element, $type, $label, $options);
                }

            // add COMPARE RULES if exist
            if (is_array($form['form']['#']['comparerules'][0]['#']))
                foreach ($form['form']['#']['comparerules'][0]['#']['rule'] as $rule){

                    // get rule type
                    $type = $rule['@']['type'];

                    // get elements
                    $elements = array();
                    foreach ($rule['#']['elements'][0]['#']['element'] as $element)
                        array_push($elements, $element['@']['name']);

                    // get error
                    $label = '';

                    // check if exist 'default' error or custom error defined by id
                    foreach ($rule['#']['errors'][0]['#']['error'] as $error){

                            // if there's a default error store value
                            if ($error['@']['id'] == 'default') $label = $error['@']['value'];

                            // if there's a custom error store value too
                            else if ($error['@']['id'] == $id) $label_extra = $error['@']['value'];
                    }

                    // if custom error was found use it instead of default
                    if (isset($label_extra)) $label = $label_extra;

                    // add compare rule to form
                    $this->addCompareRule($elements, $type, $label);
                }

                // add FILTERS if exist
                if (is_array($form['form']['#']['filters'][0]['#']))
                    foreach ($form['form']['#']['filters'][0]['#']['element'] as $element){

                        // insert filter name with filter type
                        $this->addFilter($element['@']['name'], $element['@']['filter']);
                    }

        }


    }

?>