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

			// Initialize the parent
			$this->YDRequest();

			// Initialize the template
			$this->tpl = new YDTemplate();

			// Enable caching
			$this->tpl->caching = true;

		}

		// Default action
		function actionDefault() {

			// Check if the template is cached
			if ( ! $this->tpl->is_cached( YD_SELF_SCRIPT ) ) {

				// Make some extensive database calls
				$db = YDDatabase::getInstance( 'mysql', 'test', 'root', '', 'localhost' );
				$db->connect();
				$this->tpl->assign( 'processList', $db->getRecords( 'show processlist' ) );
				$this->tpl->assign( 'status', $db->getRecords( 'show status' ) );
				$this->tpl->assign( 'variables', $db->getRecords( 'show variables' ) );
				$this->tpl->assign( 'version', $db->getServerVersion() );
				$this->tpl->assign( 'sqlcount', $db->getSqlCount() );

				// Indicate the template was not cached
				echo( 'not cached' );

			}

			// Display the template
			$this->tpl->display( 'tplcache.tpl', YD_SELF_SCRIPT );

		}

		// Function to clear the action
		function actionClearCache() {
			$this->tpl->clear_cached( YD_SELF_SCRIPT );
			$this->redirectToAction( 'default' );
		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
