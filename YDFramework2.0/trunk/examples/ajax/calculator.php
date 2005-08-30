<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );


	// Class definition
	class calculator extends YDRequest {

		function calculator() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with a 2 texts, a button and a span for result
			$form = new YDForm('myform');
			$form->addElement('text',   'arg1',   'X:');
			$form->addElement('text',   'arg2',   'Y:');
			$form->addElement('button',	'mybutton',     'Sum');
			$form->addElement('span',   'myspanresult', '&nbsp;');

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl, $form );
			$this->ajax->ignoreEffects();

			// assign 'mybutton' with 'compute' call with dynamic values from form elements 'arg1' and 'arg2'
			$this->ajax->addEvent( 'mybutton', array( & $this, 'compute' ), array('arg1', 'arg2') );

			// process events added
			$this->ajax->processEvents();

			// assign form and display template
			$this->tpl->assign( 'title', 'This a calculator example. Fill X and Y' );
			$this->tpl->assign( 'form',  $form->tohtml() );
			$this->tpl->display( 'general' );
		}


		// compute call invoked by ajax client
		function compute( $x, $y ){
		
			// assign result to span
			$this->ajax->addResult( 'myspanresult', $x + $y );

			// return response to client browser
			return $this->ajax->processResults();
		}


	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>