<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/YDFramework2/YDF2_init.php' );

    // Class definition
    class indexRequest extends YDRequest {

        // Class constructor
        function indexRequest() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {
            $this->redirect( 'examples/index.php' );
        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/YDFramework2/YDF2_process.php' );

?>
