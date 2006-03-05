<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );


	// Class definition
	class calculator_dynamic extends YDRequest {

		function calculator_dynamic() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with a 2 texts, a select to choose operation, a button and a span for result
			$form = new YDForm('myform');
			$form->addElement('text',   'arg1',         'X:');
			$form->addElement('select', 'operation',    'Operation', array(), array( 0 => '+', '-', 'x', '/') );
			$form->addElement('text',   'arg2',         'Y:');
			$form->addElement('span',   'myspanresult', '&nbsp;');
			$form->addElement('button', 'mybutton',     'Calc');

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl );
			$this->ajax->addForm( $form );

			// assign 'mybutton' with 'compute' call with dynamic values from form elements 'arg1', 'arg2' and 'operation'
			$this->ajax->addEvent( 'mybutton', array( & $this, 'compute' ), array('arg1', 'arg2', 'operation') );

			// process events added
			$this->ajax->processEvents();

			// assign form and display template
			$this->tpl->assign( 'title', 'This a dynamic calculator example (you can choose operation)');
			$this->tpl->assign( 'form',  $form->tohtml() );
			$this->tpl->display( 'general' );
		}


		// compute call invoked by ajax client
		function compute( $arg1, $arg2, $oper ){
		
			switch( $oper ){
				case 0 : $result = $arg1 + $arg2; break;
				case 1 : $result = $arg1 - $arg2; break;
				case 2 : $result = $arg1 * $arg2; break;
				case 3 : $result = $arg1 / $arg2; break;
				default : $result = '&nbsp;';
			}

			// assign result to span 
			$this->ajax->addResult( 'myspanresult', $result );

			// return response to client browser
			return $this->ajax->processResults();
		}


	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>