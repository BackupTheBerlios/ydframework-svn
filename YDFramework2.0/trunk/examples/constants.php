<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );

    // Class definition
    class constants extends YDRequest {

        // Class constructor
        function constants() {
            $this->YDRequest();
            $this->template = new YDTemplate();
        }

        // Default action
        function actionDefault() {
            $this->template->display();
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>