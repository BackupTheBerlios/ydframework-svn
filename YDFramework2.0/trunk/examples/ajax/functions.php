<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );

	// Class definition
	class functions extends YDRequest {

		function functions() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with a span and a button
			$form = new YDForm('myform');
			$form->addElement('span',   'myspanresult', '&nbsp;', array('style' => 'BACKGROUND-COLOR:#ccccff') );

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl, $form );

			// register custom function
			$this->ajax->addEvent( 'customFunction()', array( & $this, 'getversion' ) );

			// process ajax events
			$this->ajax->processEvents();


			// assign form and display template
			$this->tpl->assign( 'title', 'This is a simple ajax example');
			$this->tpl->assign( 'form',  $form->tohtml() );
			$this->tpl->display();
		}


		// getversion invoked by ajax client
		function getversion(){
 
			// assign result to 'myspanresult'
			$this->ajax->addResult('myspanresult', YD_FW_NAMEVERS);

			// return response to client browser
			return $this->ajax->processResults();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );
?>
