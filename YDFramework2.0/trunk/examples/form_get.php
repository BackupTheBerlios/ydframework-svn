<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );

	// Class definition
	class form_get extends YDRequest {

		// Class constructor
		function form_get() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {

			// Display the template
			$this->template->display();

		}

		// The action to show the form
		function actionForm() {

			// Create the form
			$form = new YDForm( 'form1', 'GET' );

			// Add elements
			$form->addElement( 'text', 'txt', 'Enter text:' );
			$form->addElement( 'submit', 'cmdSubmit', 'submit' );

			// Display the form
			$this->template->assign( 'form', $form->toHtml() );

			// Display the template
			$this->template->display();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
