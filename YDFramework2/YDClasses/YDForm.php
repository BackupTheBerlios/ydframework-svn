<?php

    /*

       Yellow Duck Framework version 2.0
       (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

    */

    /**
     *  This class implements an HTML form. This class is based on the
     *  HTML_QuickForm from the PEAR library. To instantiate the form, you need
     *  to specify the name of the form. To output the form as an array suitable
     *  for Smarty, you can use the toArray function.
     *
     *  Here's an example form:
     *
     *  @code
     *  // Create the form
     *  $form = new YDForm( 'firstForm' );
     *  
     *  // Set the defaults
     *  $form->setDefaults( array( 'name' => 'Joe User' ) );
     *  
     *  // Add the elements
     *  $form->addElement(
     *      'text', 'name', 'Enter your name:', array( 'size' => 50 )
     *  );
     *  $form->addElement( 'submit', 'cmdSubmit', 'Send' );
     *  
     *  // Apply a filter
     *  $form->applyFilter( 'name', 'trim' );
     *  
     *  // Add a rule
     *  $form->addRule( 'name', 'Please enter your name', 'required', null );
     *  
     *  // Process the form
     *  if ( $form->validate() ) {
     *  
     *      // Mark the form as valid
     *      $this->setVar( 'formValid', true );
     *
     *  }
     *  @endcode
     *
     *  The accompanying Smarty template then can look as follows:
     *
     *  @code
     *  {if $formValid}
     *      Welcome to <b>{$form.name.value}</b>!
     *  {else}
     *      {if $form.errors}
     *          <p style="color: red"><b>Errors during processing:</b>
     *          {foreach from=$form.errors item="error"}
     *              <br>{$error}
     *          {/foreach}
     *          </p>
     *      {/if}
     *      <form {$form.attributes}>
     *          <p>
     *              {$form.name.label}
     *              <br>
     *              {$form.name.html}
     *          </p>
     *          <p>
     *              {$form.cmdSubmit.html}
     *          </p>
     *      </form>
     *  {/if}
     *  @endcode
     */
    class YDForm extends HTML_QuickForm {

        /**
         *  The class constructor for the YDForm class. When you instantiate
         *  this class, you need to specify the name of the form. The form will
         *  always point to itself as the URL. The form will also use the POST
         *  method for submitting the data.
         *
         *  @param $name The name of the form.
         *  @param $method (optional) Method used for submitting the form. Most
         *                 of the times, this is either POST or GET.
         *  @param $action (optional) Action used for submitting the form. If
         *                 not specified, it will default to the current script.
         */
        function YDForm( $name, $method='', $action='' ) {

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
                $name, $method, YD_SELF_URI, '', $params
            );

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

            // Include the right renderer
            require_once( YD_DIR_3RDP_PEAR . '/HTML/QuickForm/Renderer/ArraySmarty.php' );

            // Create the renderer object
            $renderer = & new HTML_QuickForm_Renderer_ArraySmarty( $template );

            // Connect the form to the renderer
            $this->accept( $renderer );

            // If debugging, show contents
            if ( YD_DEBUG ) {
                YDDebugUtil::dump( $renderer->toArray() );
            }

            // Return the array
            return $renderer->toArray();

        }

    }

?>