<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDForm2.php' );
	require_once( 'YDTemplate.php' );
	require_once( 'YDStringUtil.php' );

	// Class definition
	class fileuploadRequest extends YDRequest {

		// Class constructor
		function fileuploadRequest() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Mark the form as not valid
			$this->setVar( 'formValid', false );

			// Create the form
			$form = new YDForm2( 'uploadForm' );

			// Add the elements
			$file = & $form->addElement(  'file', 'file1', 'Select a file to upload:' );
			$form->addElement( 'submit', 'cmdSubmit', 'Send' );

			// Add a rule
			$form->addRule( 'file1', 'uploadedfile', 'You need to select a valid file' );

			// Process the form
			if ( $form->validate() ) {

				// Move the uploaded file
				if ( $file->isUploaded() ) { 
					$file->moveUpload( '.' );
				}

				// Mark the form as valid
				$this->setVar( 'formValid', true );

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