<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../ydf2/YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once( 'YDForm.php' );

    // Class definition
    class indexRequest extends YDRequest {

        // Class constructor
        function indexRequest() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // The options for the html area
            $options = array( 'url' => '/htmlarea/', 'lang' => 'en' );
            //$attributes = array( 'rows' => 25, 'cols' => 80, 'style' => 'border-width: 1px' );
            $config = array( 'width' => '600px', 'height' => '300px' );

            $form = new YDForm( 'wysiwygForm' );
            $form->setDefaults( array( 'txtBody' => 'hello world' ) );
            $form->addElement( 'htmlarea', 'txtBody', 'text', $options );
            $form->addElement( 'submit', 'cmdSubmit', 'Send' );

            $htmlarea = &$form->getElement('txtBody');
            $htmlarea->setConfig( $config );


            if ( $form->validate() ) {
                echo( $form->exportValue( 'txtBody' ) );

            }

            $form->display();

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../ydf2/YDFramework2/YDF2_process.php' );

?>
