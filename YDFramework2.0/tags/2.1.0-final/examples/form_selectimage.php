<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDTemplate.php' );

    // Class definition
    class form_selectimage extends YDRequest {

        // Class constructor
        function form_selectimage() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

			// create options and attributes
			$options    = array( 'pt' => 'Portugal', 'br' => 'Brasil', 'be' => 'Belgium' );
			$attributes = array( 'src' => YDUrl::makeLinkAbsolute( './flags/' ), 'ext' => 'gif' );
			
            // Create the form and add element
            $form = new YDForm( 'form1' );
            $form->addElement( 'selectimage', 'si',  'Select country', $attributes, $options );

            // Display the template
            $form->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>