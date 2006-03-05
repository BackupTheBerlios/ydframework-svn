<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );

	// Class definition
	class waiting extends YDRequest {

		function waiting() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with a span and a button
			$form = new YDForm('myform');
			$form->addElement('span',   'myspanresult', '&nbsp;', array('style' => 'BACKGROUND-COLOR:#ccccff') );
			$form->addElement('button', 'mybutton',     'Get version');

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl );
			$this->ajax->addForm( $form );

			// create custom effects for waiting message
			$onStart = new YDAjaxEffect('', 'opacity', 'hide()', 0 );
			$onShow  = new YDAjaxEffect('', 'opacity', 'custom(0,1)', 0 );
			$onHide  = new YDAjaxEffect('', 'opacity', "toggle()" );

			// use waiting message with custom effects
			$this->ajax->useWaitingMessage( "<h2>&nbsp; Please wait ... &nbsp;<\/h2>", array(), $onStart, $onShow, $onHide );

			// to use default waiting message just try:
			// $this->ajax->useWaitingMessage();

			// register element mybutton (mybutton will be assigned with 'getversion' call in the client side)
			$this->ajax->addEvent( 'mybutton', array( & $this, 'getversion' ) );

			// process ajax events
			$this->ajax->processEvents();

			// assign form and display template
			$this->tpl->assign( 'title', 'This example demonstrates the waiting message with custom parameters on a slow server');
			$this->tpl->assign( 'form',  $form->tohtml() );
			$this->tpl->display( 'general' );
		}


		// getversion invoked by ajax client
		function getversion(){
 
 			sleep( 4 );
 	
			// assign result to 'myspanresult'
			$this->ajax->addResult('myspanresult', YD_FW_NAMEVERS);

			// return response to client browser
			return $this->ajax->processResults();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );
?>
