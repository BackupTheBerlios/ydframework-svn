<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDUtil.php' );

    // Class definition
    class callbacks extends YDRequest {

        // Class constructor
        function callbacks() {
            $this->YDRequest();
            $this->template = new YDTemplate();

            // these are callbacks that will be executed for all actions
            $this->registerCallback( 'before_action_callback', 'action', true );
            $this->registerCallback( 'after_action_callback',  'action', false );

            // this is an example of an action-specific callback
            $this->registerCallback( 'before_actionEdit_callback',  'actionEdit', false );

            // uncomment this to test unregisterCallback
            //$this->unregisterCallback( 'before_action_callback' );
            //$this->unregisterCallback( 'after_action_callback' );
            //$this->unregisterCallback( 'before_actionEdit_callback', 'actionEdit' );

        }

        // Default action - note: display is handled by the 'after_action_callback' callback
        function actionDefault() {
            $this->template->assign( 'title', 'callbacks::actionDefault' );
        }

        // Edit action
        function actionEdit() {
            $this->template->assign( 'title', 'callbacks::actionEdit' );
        }

        // Browser information
        function actionBrowserInfo() {
            $this->template->assign( 'title', 'callbacks::actionBrowserInfo' );
        }

        // Forward to BrowserInfo
        function actionForward() {
            $this->forward( 'BrowserInfo' );
        }

        // Redirect to BrowserInfo
        function actionRedirect() {
            $this->redirectToAction( 'BrowserInfo' );
        }

        // this method will be executed before all actions
        function before_action_callback() {
            $browser = new YDBrowserInfo(); 
            $agent = YDDebugUtil::r_dump( $browser->agent, 'Agent' );
            $this->template->assign( 'browserinfo', '<pre>' . $agent . '</pre>' );
        }

        // this method will be executed AFTER all actions
        function after_action_callback() {
            $this->template->display();
        }

        // this method will be executed BEFORE the actionEdit action
        function before_actionEdit_callback() {
            $browser = new YDBrowserInfo(); 
            $agent = YDDebugUtil::r_dump( $browser->agent, 'Agent' );
            $additional_information = "<p>Additional Information added by the before_actionEdit_callback() callback</p>";
            $this->template->assign( 'browserinfo', '<pre>' . $agent . '</pre>' . $additional_information );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?> 