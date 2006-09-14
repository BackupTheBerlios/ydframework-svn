<?php

    // initialize the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	YDInclude( 'YDDatabase.php' );
	YDInclude( 'YDUrl.php' );

    // BASIC CONFIGURATION: set YDDatabase instance connection
    YDDatabase::registerInstance( 'default', 'mysql', 'xpto', 'root', '', 'localhost' );

    // BASIC CONFIGURATION: set portal language. Currently you can use 'en' and 'pt'.
	YDLocale::set( 'en' );


	// set admin template path
	YDConfig::set( 'YDCMTEMPLATES_ADMIN_PATH', dirname( __FILE__ ) . '/backend/templates' );

	YDConfig::set( 'YDCMTEMPLATES_ADMIN_URL', YDUrl::makeLinkAbsolute( './templates' ) );
?>
