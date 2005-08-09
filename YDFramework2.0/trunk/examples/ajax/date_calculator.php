<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );
	YDInclude( 'YDUtil.php' );
	YDInclude( 'YDDateUtil.php' );

	// result call invoked by ajax client
	function result( $currentdate, $operation, $number, $type ){

		// create date object with timestamp from the 'currentdate' form element
		$date = new YDDateUtil();
		$date->set( intval($currentdate) );

		// if operation is 1 we want subtract the number
		if ($operation == 1) $number = - intval($number);
		else                 $number =   intval($number);
		
		// add number to date
		switch ( intval( $type )){
			case 0 : $date->addMinute( $number ); break;
			case 1 : $date->addDay( $number );    break;
			case 2 : $date->addMonth( $number );  break;
			case 3 : $date->addYear( $number );   break;
		}

		// create ajax response object
		$response = new YDAjaxResponse();

		// assign span 'myspanresult' of 'myform' with computed date
		$response->assignResult('myform', 'myspanresult', 'span', YDStringUtil::formatDate( $date->getTimestamp(), 'datetime' ));

		// return response to client browser
		return $response->getXML();
	}
	

	// Class definition
	class date_calculator extends YDRequest {

		function date_calculator() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with a datetimeselect, 3 selects, a button and a span for result
			$form = new YDForm('myform');
			$form->addElement('datetimeselect', 'currentdate',   'Current date');
			$form->addElement('select',         'operation',     '', array(), array( '+', '-' ) );
			$form->addElement('select',         'number',        '', array(), array( 1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ) );
			$form->addElement('select',         'type',          '', array(), array( 'minutes', 'days', 'months', 'years' ) );
			$form->addElement('span',           'myspanresult',  '?');
			$form->addElement('button',         'mybutton',      '=');

			// create ajax object
			$ajax = new YDAjax();

			// define template object (YDAjax will assign all js to this template)
			$ajax->setTemplate( $this->tpl );
			
			// define which default form we will use (this way we don't need to define form in registerElement)
			$ajax->setForm( $form );

			// register button 'mybutton' with event 'result' using dynamic values from form elements
			$ajax->registerElement( 'mybutton',  'result', array( 'currentdate', 'operation', 'number', 'type' ) );
			$ajax->processRequests();

			// assign form and display template
			$this->tpl->assignForm( 'form', $form );
			$this->tpl->display();
		}


	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>