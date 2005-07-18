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

        }

        // Default action
        function actionDefault() {

            // Create the template object
            $tpl = new YDTemplate();
        
            // Assign some stuff
            $browser = new YDBrowserInfo();
            $tpl->assign( 'browser', $browser );

            // Display the template
            $tpl->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
