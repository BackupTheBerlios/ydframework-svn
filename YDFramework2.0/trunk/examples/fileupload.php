<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );

	// Class definition
	class fileupload extends YDRequest {

		// Class constructor
		function fileupload() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {

			// Mark the form as not valid
			$this->template->assign( 'formValid', false );

			// Create the form
			$form = new YDForm( 'uploadForm' );

			// Add the elements
			$file = & $form->addElement(  'file', 'file1', 'Select a file to upload:' );
			$form->addElement( 'submit', 'cmdSubmit', 'Send' );

			// Add a rule
			$form->addRule( 'file1', 'uploadedfile', 'You need to select a valid file' );
			$form->addRule( 'file1', 'maxfilesize', 'Maximum filesize of 10 KB is exceeded!', 10*1024 );
			$form->addRule( 'file1', 'extension', 'File extension should be txt!', 'txt' );

			// Process the form
			if ( $form->validate() ) {

				// Move the uploaded file
				if ( $file->isUploaded() ) {
				
					// Move the upload
					$file->moveUpload( '.' );
					
					// Mark the form as valid
					$this->template->assign( 'formValid', true );
					
				}

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