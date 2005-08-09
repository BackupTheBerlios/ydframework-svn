<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );


	// compute call invoked by ajax client
	function compute( $arg1, $arg2 ){
		
		$result = $arg1 + $arg2;

		// create ajax response object
		$response = new YDAjaxResponse();

		// assign span 'myspanresult' of 'myform' with sum result
		$response->assignResult('myform', 'myspanresult', 'span', $result );

		// return response to client browser
		return $response->getXML();
	}
	

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
			$ajax = new YDAjax();

			// define template object (YDAjax will assign all js to this template)
			$ajax->setTemplate( $this->tpl );
			
			// define which default form we will use (this way we don't need to define form in registerElement)
			$ajax->setForm( $form );

			// assign 'mybutton' with 'compute' call with dynamic values from form elements 'arg1' and 'arg2'
			$ajax->registerElement( 'mybutton', 'compute', array('arg1', 'arg2') );

			// process ajax
			$ajax->processRequests();

			// assign form and display template
			$this->tpl->assign( 'title', 'This a calculator example. Fill X and Y');
			$this->tpl->assign( 'form',  $form->tohtml() );
			$this->tpl->display( 'general' );
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>