<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );

    // Class definition
    class xul extends YDRequest {

        // Class constructor
        function xul(){

            // Initialize the parent
            $this->YDRequest();

            // Create a template object
            $this->tpl = new YDTemplate();

        }

        // Default action
        function actionDefault() {

            // Set the content type
            header( "Content-type: application/vnd.mozilla.xul+xml" );

            // Assign some variables
            $this->tpl->assign( 'test', true );
            $this->tpl->assign( 'otherbutton', '<button id="cancel-button" label="Cancel"/>' );

            // Display the template
            $this->tpl->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>