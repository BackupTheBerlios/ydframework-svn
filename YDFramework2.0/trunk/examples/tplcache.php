<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDTemplate.php' );
	require_once( 'YDDatabase.php' );

	// Class definition
	class tplcache extends YDRequest {

		// Class constructor
		function tplcache() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Setup the template
			$tpl = new YDTemplate();

			// Enable caching
			$tpl->cache = true;
			$tpl->cache_dir = YD_DIR_TEMP;

			// Check if the template is cached
			if ( ! $tpl->is_cached( 'tplcache.tpl' ) ) {

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
			$tpl->display( 'tplcache.tpl' );

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
