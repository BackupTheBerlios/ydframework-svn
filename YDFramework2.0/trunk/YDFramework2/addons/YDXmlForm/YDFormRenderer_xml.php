<?php

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

    // Includes
    include_once( dirname( __FILE__ ) . '/../../YDClasses/YDForm.php' );

    /**
     *	This is the class that is able to render a form object to XML.
     */
    class YDFormRenderer_xml extends YDFormRenderer {

        /**
         *	This is the class constructor for the YDFormRenderer_xml class.
         *
         *	@param $form		The form that needs to be rendered.
         */
        function YDFormRenderer_xml( $form ) {

            // Initialize the parent
            $this->YDFormRenderer( $form );

        }

        /**
         *	This function will render the form.
         *
         *	@returns	The rendered form.
         */
        function render() {

            // form string
            $xml = "<form name=\"". $this->_form->_name ."\" method=\"". $this->_form->_method ."\" action=\"". $this->_form->_action ."\" target=\"". $this->_form->_target ."\">\n";

            // add ATTRIBUTES
            $xml .= "\t<attributes>\n";

            // loop attributes
            foreach ($this->_form->_attributes as $name => $value)
                $xml .= "\t\t<attribute name=\"". $name ."\" value=\"". $value ."\" />\n";

            $xml .= "\t</attributes>\n";

            // add ELEMENTS
            $xml .= "\t<elements>\n";

            // Get elements from form
            $elements = $this->_form->_elements;

            foreach ( $elements as $name => $elementobj ) {
                $element = $elementobj->toArray();

                // add element name and type
                $xml .= "\t\t<element name=\"". $element['name'] ."\" type=\"". $element['type'] ."\">\n";

                // add labels (this form export support only default labels)
                $xml .= "\t\t\t<labels>\n";
                $xml .= "\t\t\t\t<label id=\"default\" value=\"". $element['labelname'] ."\" />\n";
                $xml .= "\t\t\t</labels>\n";

                // add element values (this form export support only default values)
                $xml .= "\t\t\t<values>\n";
                $xml .= "\t\t\t\t<value id=\"default\" value=\"". $element['value'] ."\" />\n";
                $xml .= "\t\t\t</values>\n";

                // add attributes
                $xml .= "\t\t\t<attributes>\n";

                foreach ($element['attributes'] as $name=>$value)
                    $xml .= "\t\t\t\t<attribute name=\"". $name ."\" value=\"". $value ."\" />\n";

                $xml .= "\t\t\t</attributes>\n";

                // add options
                $xml .= "\t\t\t<options>\n";

                foreach ($element['options'] as $name=>$value)
                    $xml .= "\t\t\t\t<option name=\"". $name ."\" value=\"". $value ."\" />\n";

                $xml .= "\t\t\t</options>\n";

                // end element
                $xml .= "\t\t</element>\n";
            }
            $xml .= "\t</elements>\n";


            // add RULES
            $xml .= "\t<rules>\n";

            // Get rules from form
            $rules_element = $this->_form->_rules;

            foreach($rules_element as $element => $rules)
                foreach($rules as $rule){

                    // add element name and type
                    $xml .= "\t\t<rule element=\"". $element ."\" type=\"". $rule['rule'] ."\">\n";

                    // add errors (this form export support only default values)
                    $xml .= "\t\t\t<errors>\n";
                    $xml .= "\t\t\t\t<error id=\"default\" value=\"". $rule['error'] ."\" />\n";
                    $xml .= "\t\t\t</errors>\n";

                    // add options
                    $xml .= "\t\t\t<options>\n";

                    // if is a integer, float or string create just one option with id = ""
                    if (is_scalar($rule['options']))
                        $xml .= "\t\t\t\t<option id=\"\" value=\"". $rule['options'] ."\" />\n";

                    // if is a array create all options
                    if (is_array($rule['options']))
                        foreach ($rule['options'] as $id => $value)
                            $xml .= "\t\t\t\t<option id=\"". $id ."\" value=\"". $value ."\" />\n";

                    // end options
                    $xml .= "\t\t\t</options>\n";

                    $xml .= "\t\t</rule>\n";
                }

            $xml .= "\t</rules>\n";

            // add COMPARE RULES
            $xml .= "\t<comparerules>\n";

            // loop compare rules
            foreach($this->_form->_comparerules as $rule){

                // add rule type
                $xml .= "\t\t<rule type=\"". $rule['rule'] ."\">\n";

                // add elements
                $xml .= "\t\t\t<elements>\n";
                foreach($rule['elements'] as $element)
                    $xml .= "\t\t\t\t<element name=\"". $element ."\" />\n";
                $xml .= "\t\t\t</elements>\n";

                // add errors (this form export support only default values)
                $xml .= "\t\t\t<errors>\n";
                $xml .= "\t\t\t\t<error id=\"default\" value=\"". $rule['error'] ."\" />\n";
                $xml .= "\t\t\t</errors>\n";

                $xml .= "\t\t</rule>\n";
            }

            $xml .= "\t</comparerules>\n";

            // add FILTERS
            $xml .= "\t<filters>\n";

            // loop filters
            foreach($this->_form->_filters as $element => $filters)
                foreach ($filters as $filter)
                    $xml .= "\t\t<element name=\"". $element ."\" filter=\"". $filter ."\" />\n";

            $xml .= "\t</filters>\n";

            $xml .= "</form>";

        return $xml;
        }

    }

?>