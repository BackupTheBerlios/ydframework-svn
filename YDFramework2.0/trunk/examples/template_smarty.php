<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDUtil.php' );
	YDInclude( 'YDDatabase.php' );
	YDInclude( 'YDTemplateSmarty.php' );

	// Class definition
	class template_smarty extends YDRequest {

		// Class constructor
		function template_smarty() {

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
			$tpl->assign( 'array', $browser->toArray() );
		
			// Display the template
			$tpl->display();

		}

		// Caching action
		function actionCaching() {

			// Create the template object
			$tpl = new YDTemplate();
			$tpl->caching = true;
			
			// Check if the template is cached
			if ( ! $tpl->is_cached( YD_SELF_SCRIPT ) ) {

				// Make some extensive database calls
				$db = YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' );
				$db->connect();
				$tpl->assign( 'processList', $db->getRecords( 'show processlist' ) );
				$tpl->assign( 'status', $db->getRecords( 'show status' ) );
				$tpl->assign( 'variables', $db->getRecords( 'show variables' ) );
				$tpl->assign( 'version', $db->getServerVersion() );
				$tpl->assign( 'sqlcount', $db->getSqlCount() );

				// Indicate the template was not cached
				echo( 'not cached' );

			}

			// Display the template
			$tpl->display( 'tplcache.tpl', YD_SELF_SCRIPT );

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
