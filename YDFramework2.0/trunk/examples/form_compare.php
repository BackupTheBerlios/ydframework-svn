<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDRequest.php' );

	// Class definition
	class form_compare extends YDRequest {

		// Class constructor
		function form_compare() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Create the form
			echo( '<p><b>Compare rule: equal</b></p>' );
			$form1 = new YDForm( 'form_equal' );
			$form1->addElement( 'text', 'txt1', 'Enter text 1:' );
			$form1->addElement( 'text', 'txt2', 'Enter text 2:' );
			$form1->addElement( 'text', 'txt3', 'Enter text 3:' );
			$form1->addElement( 'submit', 'cmd1', 'equal' );

			// Add the rules
			$form1->addRule( 'txt1', 'numeric', 'txt1 should be numeric' );
			$form1->addRule( 'txt2', 'numeric', 'txt2 should be numeric' );
			$form1->addRule( 'txt3', 'numeric', 'txt2 should be numeric' );
			$form1->addCompareRule( array( 'txt1', 'txt2', 'txt3' ), 'equal', 'txt1, txt2 and txt3 should be equal' );

			// Validate or show the form
			if ( $form1->validate() ) {
				YDDebugUtil::dump( $form1->getValues(), 'Form1 values' );
			} else {
				$form1->display();
			}

			// Create the form
			echo( '<p><b>Compare rule: asc</b></p>' );
			$form2 = new YDForm( 'form_asc' );
			$form2->addElement( 'text', 'txt1', 'Enter text 1:' );
			$form2->addElement( 'text', 'txt2', 'Enter text 2:' );
			$form2->addElement( 'text', 'txt3', 'Enter text 3:' );
			$form2->addElement( 'submit', 'cmd1', 'asc' );

			// Add the rules
			$form2->addRule( 'txt1', 'numeric', 'txt1 should be numeric' );
			$form2->addRule( 'txt2', 'numeric', 'txt2 should be numeric' );
			$form2->addRule( 'txt3', 'numeric', 'txt2 should be numeric' );
			$form2->addCompareRule( array( 'txt1', 'txt2', 'txt3' ), 'asc', 'txt1 < txt2 < txt3' );

			// Validate or show the form
			if ( $form2->validate() ) {
				YDDebugUtil::dump( $form2->getValues(), 'Form2 values' );
			} else {
				$form2->display();
			}

			// Create the form
			echo( '<p><b>Compare rule: desc</b></p>' );
			$form3 = new YDForm( 'form_desc' );
			$form3->addElement( 'text', 'txt1', 'Enter text 1:' );
			$form3->addElement( 'text', 'txt2', 'Enter text 2:' );
			$form3->addElement( 'text', 'txt3', 'Enter text 3:' );
			$form3->addElement( 'submit', 'cmd1', 'desc' );

			// Add the rules
			$form3->addRule( 'txt1', 'numeric', 'txt1 should be numeric' );
			$form3->addRule( 'txt2', 'numeric', 'txt2 should be numeric' );
			$form3->addRule( 'txt3', 'numeric', 'txt2 should be numeric' );
			$form3->addCompareRule( array( 'txt1', 'txt2', 'txt3' ), 'desc', 'txt1 > txt2 > txt3' );

			// Validate or show the form
			if ( $form3->validate() ) {
				YDDebugUtil::dump( $form3->getValues(), 'Form3 values' );
			} else {
				$form3->display();
			}

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
