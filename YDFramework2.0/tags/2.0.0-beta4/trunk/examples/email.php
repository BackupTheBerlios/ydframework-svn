<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDEmail.php' );
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );

	// Class definition
	class email extends YDRequest {

		// Class constructor
		function email() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {

			// Mark the form as not valid
			$this->template->assign( 'formValid', false );

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
				$this->template->assign( 'formValid', true );

				// Parse the template for the email
				$emlTpl = new YDTemplate();
				$emlTpl->assign( 'email', $form->getValue( 'email' ) );
				$body = $emlTpl->fetch( 'email_template', null );

				// Send the email
				$eml = new YDEmail();
				$eml->setFrom( 'pieter@yellowduck.be', YD_FW_NAME );
				$eml->addTo( $form->getValue( 'email' ), 'Yellow Duck' );
				$eml->setSubject( 'Hello from Pieter & Fiona!' );
				$eml->setHtmlBody( $body );
				$eml->addAttachment( 'email.tpl' );
				$eml->addHtmlImage( 'fsimage.jpg', 'image/jpeg' );
				$result = $eml->send();

				// Add the result
				$this->template->assign( 'result', $result );

			}

			// Add the form to the template
			$this->template->assign( 'form_html', $form->toHtml() );
			$this->template->assignForm( 'form', $form );

			// Output the template
			$this->template->display();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>