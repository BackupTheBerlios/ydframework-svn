<?php

    // Standard include
    include_once( 'YDF2_init.php' );

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
            //$form->addRule( 'file1', 'maxlength', 'Path can only be 8 characters', 8 );
            $form->addRule( 'file1', 'maxfilesize', 'Maximum filesize of 1000 KB is exceeded!', 1000*1024 );
            //$form->addRule( 'file1', 'extension', 'File extension should be txt!', 'txt' );

            // Process the form
            if ( $form->validate() ) {

                // Move the uploaded file
                if ( $file->isUploaded() ) {
                
                    // You may fetch the name before the file hits the FS
                    $temp_filename = $file->getBaseName();
                
                    // Move the upload
                    $file->moveUpload( './tmp' );
                    //$file->moveUpload( './tmp', 'TEST_' . $temp_filename );
                    //$file->moveUpload( './tmp', md5(time()) );
                    //$file->moveUpload( './tmp', md5(time()), true );

                    // Mark the form as valid
                    $this->template->assign( 'formValid', true );
                    
                    // Display file information
                    $this->template->assign( 'filename', $file->getBaseName() );
                    $this->template->assign( 'filesize', $file->getSize() );
                    $this->template->assign( 'ext', $file->getExtension() );
                    $this->template->assign( 'path', $file->getPath() );
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