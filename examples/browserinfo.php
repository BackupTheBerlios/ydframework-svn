<?php

    /*

        This examples demonstrates:
        - How you get information about the browser.

    */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Class definition
    class browserinfoRequest extends YDRequest {

        // Class constructor
        function browserinfoRequest() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {
            $this->setVar( 'browser', $this->browser );
            $this->outputTemplate();
        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
