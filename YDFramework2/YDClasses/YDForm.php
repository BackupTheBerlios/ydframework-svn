<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Includes
    require_once( 'YDBase.php' );
    require_once( 'YDError.php' );
    require_once( 'YDDebugUtil.php' );
    require_once( 'HTML/QuickForm.php' );
    require_once( 'HTML/QuickForm/Renderer/Array.php' );

    // Register our custom form elements
    $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES']['wysiwygarea'] = array(
        'QuickForm/wysiwygarea.php', 'HTML_QuickForm_wysiwygarea'
    );

    /**
     *  This class implements an HTML form. This class is based on the
     *  HTML_QuickForm from the PEAR library. To instantiate the form, you need
     *  to specify the name of the form. To output the form as an array suitable
     *  for the template engine, you can use the toArray function.
     */
    class YDForm extends HTML_QuickForm {

        /**
         *  The class constructor for the YDForm class. When you instantiate
         *  this class, you need to specify the name of the form. The form will
         *  always point to itself as the URL. The form will also use the POST
         *  method for submitting the data.
         *
         *  @param $name       The name of the form.
         *  @param $method     (optional) Method used for submitting the form.
         *                     Most of the times, this is either POST or GET.
         *  @param $action     (optional) Action used for submitting the form.
         *                     If not specified, it will default to the current
         *                     URI.
         *  @param $target     (optional) HTML target for the form.
         *  @param $attributes (optional) Attributes for the form.
         */
        function YDForm(
            $name, $method='', $action='', $target='_self', $attributes=null
        ) {

            // Default to the post method
            if ( empty( $method ) ) {
                $method = 'post';
            }

            // Default to ourselves for the action
            if ( empty( $action ) ) {
                $action = YD_SELF_URI;
            }

            // Default parameters
            $params = array( 'enctype' => 'multipart/form-data' );

            // Initialize the parent class
            $this->HTML_QuickForm(
                $name, $method, YD_SELF_URI, $target, $attributes
            );

            // Aggregate the functions from the YDBase class
            aggregate( $this, 'YDBase' );
            YDBase::YDBase();

        }

        /**
         *  The function will convert the form to an array suitable for use in
         *  a Smarty template. You need to specify the template object as the
         *  parameter for this function.
         *
         *  @param $template Smarty or YDTemplate template object.
         *
         *  @return Array representing the form in a suitable format for use in
         *          the template.
         */
        function toArray( $template ) {

            // Create the renderer object
            $renderer = & new HTML_QuickForm_Renderer_Array( $template );

            // Connect the form to the renderer
            $this->accept( $renderer );

            // Create the array
            $array = $renderer->toArray();

            // Loop over the elements
            foreach ( $array['elements'] as $element ) {

                // Date elements need special treatment
                if ( $element['type'] == 'date' ) {

                    // Check if the element has a value
                    if ( $element['value'] ) {

                        // Loop over the values
                        foreach ( $element['value'] as $key => $value ) {

                            // Remove the original
                            unset( $element['value'][$key] );

                            // Add them again
                            $element['value'][$key] = $value[0];

                        }

                    }

                }

                // Add it to the root of the array
                $array[ $element['name'] ] = $element;

            }

            // Remove the original elements array
            unset( $array['elements'] );

            // Just a cosmetic change
            $array['attributes'] = trim( $array['attributes'] );

            // If debugging, show contents
            if ( YD_DEBUG ) {
                YDDebugUtil::debug( YDDebugUtil::r_dump( $array ) );
            }

            // Return the array
            return $array;

        }

    }

?>