<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Include server compatibility support (useful if this server don't support gettext)
	YDIncludeCompatibility();

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );

	// set locale: you can use 'en', 'pt' and 'fr' and use gettext engine with 'test' filenames inside 'locale' language directory
	YDLocale::set( 'pt', 'test', dirname( __FILE__ ) . '/locale/' );


	// Class definition
	class index extends YDRequest {

		function index() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			$this->tpl->assign( 'var',  'variable' );
			$this->tpl->display();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );
?>
