<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Configure the template class
    YDConfig::set( 'YD_TEMPLATE_ENGINE', 'php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDUtil.php' );
    YDInclude( 'YDTemplate.php' );

    // Class definition
    class template_php extends YDRequest {

        // Class constructor
        function template_php() {

            // Initialize the parent
            $this->YDRequest();

            // Create the template object
            $this->tpl = new YDTemplate();

            // Assign some stuff
            $browser = new YDBrowserInfo();
            $this->tpl->assign( 'browser', $browser );

        }

        // Default action - short open tags
        function actionDefault() {

            // Display the template
            $this->tpl->display( 'template_php.tpl' );

        }

        // Complete PHP tags
        function actionComplete() {
            $this->tpl->display( 'template_php2.tpl' );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>