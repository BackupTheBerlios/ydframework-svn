<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDForm.php' );
	require_once( 'YDEmail.php' );
	require_once( 'YDTemplate.php' );

	// Class definition
	class emailRequest extends YDRequest {

		// Class constructor
		function emailRequest() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Mark the form as not valid
			$this->setVar( 'formValid', false );

			// Create the form
			$form = new YDForm( 'emailForm' );

			// Add the elements
			$form->addElement( 'text', 'email', 'Enter your email address:', array( 'style' => 'width: 300px;' ) );
			$form->addElement( 'submit', 'cmdSubmit', 'Send' );

			// Apply a filter
			$form->addFilter( 'email', 'trim' );

			// Add a rule
			$form->addRule( 'email', 'email', 'Please enter a valid email address' );

			// Process the form
			if ( $form->validate() ) {

				// Mark the form as valid
				$this->setVar( 'formValid', true );

				// Parse the template for the email
				$emlTpl = new YDTemplate();
				$emlTpl->setVar( 'email', $form->getValue( 'email' ) );
				$body = $emlTpl->getOutput( 'email_template' );

				// Send the email
				$eml = new YDEmail();
				$eml->setFrom( 'pieter@yellowduck.be', YD_FW_NAME );
				$eml->addTo( $form->getValue( 'email' ), 'Yellow Duck' );
				$eml->setSubject( 'Hello from Pieter & Fiona!' );
				$eml->setHtmlBody( $body );
				$eml->addAttachment( 'email.tpl' );
				$eml->addHtmlImage( 'fsimage.jpg', 'image/jpeg' );
				$eml->send();

			}

			// Add the form to the template
			$this->setVar( 'form_html', $form->toHtml() );
			$this->addForm( 'form', $form );

			// Output the template
			$this->outputTemplate();

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>