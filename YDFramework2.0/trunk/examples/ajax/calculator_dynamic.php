<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );

	// compute call invoked by ajax client
	function compute( $arg1, $arg2, $oper ){
		
		switch( $oper ){
			case 0 : $result = $arg1 + $arg2; break;
			case 1 : $result = $arg1 - $arg2; break;
			case 2 : $result = $arg1 * $arg2; break;
			case 3 : $result = $arg1 / $arg2; break;
			default : $result = '&nbsp;';
		}

		// create ajax response object
		$response = new YDAjaxResponse();

		// assign span 'myspanresult' of 'myform' with result from operation
		$response->assignResult('myform', 'myspanresult', 'span', $result );

		// return response to client browser
		return $response->getXML();
	}


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
			$ajax = new YDAjax();

			// define template object (YDAjax will assign all js to this template)
			$ajax->setTemplate( $this->tpl );

			// define which default form we will use (this way we don't need to define form in registerElement)
			$ajax->setForm( $form );

			// assign 'mybutton' with 'compute' call with dynamic values from form elements 'arg1', 'arg2' and 'operation'
			$ajax->registerElement( 'mybutton', 'compute', array('arg1', 'arg2', 'operation') );

			// process ajax
			$ajax->processRequests();

			// assign form and display template
			$this->tpl->assign( 'title', 'This a dynamic calculator example (you can choose operation)');
			$this->tpl->assign( 'form',  $form->tohtml() );
			$this->tpl->display( 'general' );
		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>