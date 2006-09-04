<?php

    // initialize the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	YDInclude( 'YDDatabase.php' );

    // set YDDatabase instance connection
    YDDatabase::registerInstance( 'default', 'mysql', 'xpto', 'root', '', 'localhost' );

	// set portal language. Currently you can user 'en' and 'pt'.
	YDLocale::set( 'en' );

	// set admin template path
	YDConfig::set( 'YDCMTEMPLATES_ADMIN_PATH', dirname( __FILE__ ) . '/backend/templates' );

?>
