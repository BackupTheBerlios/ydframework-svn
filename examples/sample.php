<?php

    /*
     *  This examples demonstrates:
     *  - How you can define multiple actions in one request
     *
     *  How to run this example
     *  Actions are specified using the do parameter in the url
     *
     *  Sample urls:
     *  - sample1.php -> executes actionDefault
     *  - sample1.php?do=edit -> executes actionEdit
     *  - sample1.php?do=undefined -> raises an error
     *
     *  If you want to have a different behaviour when an action was not found,
     *  you can override the errorMissingAction function of the YDRequest class.
     */

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );

    // Class definition
    class sampleRequest extends YDRequest {

        // Class constructor
        function sampleRequest() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {
            $this->setVar( 'title', 'sample1Request::actionDefault' );
            $this->outputTemplate();
        }

        // Edit action
        function actionEdit() {
            $this->setVar( 'title', 'sample1Request::actionEdit' );
            $this->outputTemplate();
        }

        // Browser information
        function actionBrowserInfo() {
            $this->setVar( 'browser', $this->browser );
            $this->outputTemplate();
        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
