<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDTemplate.php' );

    // Class definition
    class form_checkboxgroup extends YDRequest {

        // Class constructor
        function form_checkboxgroup() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the form
            $form = new YDForm( 'form1' );

            // Add the radiobutton
            $form->addElement( 'checkboxgroup', 'choose_multiple', 'Choose multiple', array(), array( 0 => 'choice 1', 1 => 'choice 2' ) );

            // Add the submit button
            $form->addElement( 'submit', 'btnSubmit', 'submit' );

            // Set the defaults
            $form->setDefaults( array( 'choose_multiple' => array( 0=>0, 1=>1 ) ) );

            // Process the form
            if ( $form->validate() === true ) {
                YDDebugUtil::dump( $form->getValues() );
            }

            // Add the template
            $tpl = new YDTemplate();

            // Add the form to the template
            $tpl->assignForm ( 'form',$form );

            // Display the template
            $tpl->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>