<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDBrowserInfo.php' );

	// Class definition
	class browserinfo extends YDRequest {

		// Class constructor
		function browserinfo() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			$browser = new YDBrowserInfo();
			YDDebugUtil::dump( $browser->getAgent(), 'Agent' );
			YDDebugUtil::dump( $browser->getBrowser(), 'Browser name' );
			YDDebugUtil::dump( $browser->getVersion(), 'Version' );
			YDDebugUtil::dump( $browser->getPlatform(), 'Platform' );
			YDDebugUtil::dump( $browser->getDotNetRuntimes(), 'Installed .NET runtimes' );
			YDDebugUtil::dump( $browser->getBrowserLanguages(), 'Languages supported by the browser' );
			YDDebugUtil::dump( $browser->getLanguage(), 'Negotiated language' );
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
