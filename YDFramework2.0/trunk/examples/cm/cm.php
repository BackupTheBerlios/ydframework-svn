<?php

    // Initialize the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDDatabase.php' );
    YDInclude( 'YDCMComponent.php' );

    // set YDDatabase instance connection
    YDDatabase::registerInstance( 'default', 'mysql', 'ph', 'root', '', 'localhost' );

	// set portal language. Currently you can user 'en' and 'pt'.
	YDLocale::set( 'en' );

	// set admin template path
	YDConfig::set( 'YDCMTEMPLATES_ADMIN_PATH', dirname( __FILE__ ) . '/backend/templates' );

    // Class definition for the index request
    class cm extends YDRequest {

        // Class constructor
        function cm() {

            $this->YDRequest();
            $this->template = new YDTemplate();
        }

    }
?>
