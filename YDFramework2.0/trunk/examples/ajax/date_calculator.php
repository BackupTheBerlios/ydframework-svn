<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );
	YDInclude( 'YDUtil.php' );
	YDInclude( 'YDDate.php' );


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
			$this->ajax = new YDAjax( $this->tpl, $form);
			$this->ajax->ignoreEffects();

			// register button 'mybutton' with event 'result' and arguments form elements
			$this->ajax->addEvent( 'mybutton',  array( & $this, 'result' ), array( 'currentdate', 'operation', 'number', 'type' ) );
			$this->ajax->processEvents();

			// assign form and display template
			$this->tpl->assignForm( 'form', $form );
			$this->tpl->display();
		}


		// result call invoked by ajax client
		function result( $currentdate, $operation, $number, $type ){

			// create date object with timestamp from the 'currentdate' form element
			$date = new YDDate();
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

			// assign span
			$this->ajax->addResult('myspanresult', YDStringUtil::formatDate( $date->getTimestamp(), 'datetime' ));

			// return response to client browser
			return $this->ajax->processResults();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>