<?php

    // Initialize the Yellow Duck Framework
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );
    require_once( 'YDFSDirectory.php' );
    require_once( 'YDObjectUtil.php' );
    require_once( 'YDForm.php' );

    // Class definition for the index request
    class indexRequest extends YDRequest {

        // Class constructor
        function indexRequest() {

            // Initialize the parent class
            $this->YDRequest();

            // Set the path to the data directory
            $this->dataDir = new YDFSDirectory( dirname( __FILE__ ) . '/data/' );

        }

        // Default action
        function actionDefault() {

            // Start with an empty list of entries
            $entries = array();

            // Loop over the data directory contents
            foreach( $this->dataDir->getContents( '*.dat' ) as $entry ) {

                // Get the contents
                $entry = $entry->getContents();

                // Unserialize
                $entry = YDObjectUtil::unserialize( $entry );

                // Add it to the list of entries
                array_push( $entries, $entry );

            }

            // Add the entries to the template
            $this->setVar( 'entries', $entries );

            // Output the template
            $this->outputTemplate();

        }

        // Add Note action
        function actionAddNote() {

            // Create the add form
            $form = new YDForm( 'addEntryForm' );

            // Add the elements
            $form->addElement( 'text', 'title', 'Title:' );
            $form->addElement( 'textarea', 'body', 'Contents:' );
            $form->addElement( 'submit', 'cmdSubmit', 'Save' );

            // Apply filters
            $form->applyFilter( 'title', 'trim' );
            $form->applyFilter( 'body', 'trim' );

            // Add a rule
            $form->addRule( 'title', 'Title is required', 'required' );
            $form->addRule( 'body', 'Contents is required', 'required' );

            // Process the form
            if ( $form->validate() ) {

                // Save the entries in an array
                $entry = array(
                    'id' => md5(
                        $form->exportValue( 'title' ) . $form->exportValue( 'body' )
                    ),
                    'title' => $form->exportValue( 'title' ),
                    'body' => $form->exportValue( 'body' )
                );

                // Save the serialized entry to a file
                $this->dataDir->createFile(
                    $entry['id'] . '.dat', YDObjectUtil::serialize( $entry )
                );

                // Forward to the list view
                $this->forward( 'default' );

                // Return
                return;

            }

            // Add the form to the template
            $this->addForm( 'form', $form );


            // Output the template
            $this->outputTemplate();

        }

        // Delete note action
        function actionDeleteNote() {

            // Delete the file related to the entry
            $this->dataDir->deleteFile( $_GET['id'] . '.dat' );

            // Forward to the list view
            $this->forward( 'default' );

        }

    }

    // Process the request
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_process.php' );

?>
