<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDAjax.php' );

	// Class definition
	class functions extends YDRequest {

		function functions() {

			$this->YDRequest();
		}


		// Default action
		function actionDefault() {

			// create template object
			$tpl = new YDTemplate();

			// create ajax object with waiting message
			$this->ajax = new YDAjax( $tpl );
			$this->ajax->useWaitingMessage();

			// add custom event
			$this->ajax->addEvent( 'customFunction(x)', array( & $this, 'getResult' ), 'var x' );

			// process ajax events
			$this->ajax->processEvents();

			// assign title and display template
			$tpl->assign( 'title', 'This example demonstrates YDAjax without YDForm');
			$tpl->display();
		}


		// getResult invoked by ajax client
		function getResult( $var ){
 
			// message to client browser
			return YDAjax::message( 'Argument submitted : ' . $var );
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );
?>
