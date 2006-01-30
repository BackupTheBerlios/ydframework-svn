<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );


	// Class definition
	class autocompleter extends YDRequest {

		function autocompleter() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with a 2 texts, a button and a span for result
			$form = new YDForm('myform');
			$form->addElement('text',   'arg1', 'Country with standard style:' );
			$form->addElement('text',   'arg2', 'Just a simple text box without autocompleter');
			$form->addElement('text',   'arg3', 'Country with custom style:', array('style' => 'width:300px; background-color:#CCFFFF;') );

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl, $form );

			// add autocompleter using 'arg1'/'arg3' and sending to 'getCountry' all we have in 'arg1'/'arg3'
			$this->ajax->addCompleter( 'arg1', array( & $this, 'getCountry' ), array('arg1') );
			$this->ajax->addCompleter( 'arg3', array( & $this, 'getCountry' ), array('arg3') );

			// process events added
			$this->ajax->processEvents();

			// assign form and display template
			$this->tpl->assign( 'title', 'Just start typing a coutry name in the box to see the effect' );
			$this->tpl->assign( 'form',  $form->tohtml() );
			$this->tpl->display();
		}


		// compute call invoked by ajax client
		function getCountry( $text ){
		
			// simple db emulation
			$db =  array("Alabama","Alaska","American Samoa","Arizona","Arkansas","California","Brasil",
						"Colorado","Connecticut","Delaware","District of Columbia",
						"Federated States of Micronesia","Florida","Georgia","Guam","Hawaii","Idaho","Illinois",
						"Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine",
						"Marshall Islands","Maryland","Massachusetts","Michigan","Minnesota","Mississippi",
						"Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico",
						"New York","North Carolina","North Dakota",
						"Northern Mariana Islands","Ohio","Oklahoma","Oregon","Palau","Pennsylvania","Portugal","Puerto Rico",
						"Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont",
						"Virgin Islands","Virginia","Washington","West Virginia","Wisconsin","Wyoming",
						"Armed Forces Africa","Armed Forces Americas","Armed Forces Canada",
						"Armed Forces Europe","Armed Forces Middle East","Armed Forces Pacific");

			// initialize result array
			$result = array();
			
			// check db for text matches and store them in result array
			foreach( $db as $el )
				if (eregi("^". $text ."+", $el)) $result[] = $el;
	
			// assign result to current completer
			$this->ajax->addCompleterResult( $result );

			// return response to client browser
			return $this->ajax->processResults();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>