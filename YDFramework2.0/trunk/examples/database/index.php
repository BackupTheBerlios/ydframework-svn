<?php

    // Initialize the Yellow Duck Framework
    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );
    require_once( dirname( __FILE__ ) . '/config.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDDatabase.php' );

    // Class definition for the index request
    class index extends YDRequest {

        // Class constructor
        function index() {

            // Initialize the parent class
            $this->YDRequest();

            // Initialize the template object
            $this->template = new YDTemplate();

            // Make the database connection
            $this->db = YDDatabase::getInstance(
                $GLOBALS['db']['type'], $GLOBALS['db']['name'], $GLOBALS['db']['user'], $GLOBALS['db']['pass'], $GLOBALS['db']['host']
            );

            // Create the add form
            $this->form = new YDForm( 'addEntryForm' );

            // Add the elements
            $this->form->addElement( 'text', 'notetitle', 'Title:' );
            $this->form->addElement( 'textarea', 'notecontents', 'Contents:' );
            $this->form->addElement( 'submit', '_cmdSubmit', 'Save' );

            // Apply filters
            $this->form->addFilter( 'NoteTitle', 'trim' );
            $this->form->addFilter( 'NoteContents', 'trim' );

            // Add a rule
            $this->form->addRule( 'notetitle', 'required', 'Title is required' );
            $this->form->addRule( 'notecontents', 'required', 'Contents is required' );

        }

        // Default action
        function actionDefault() {

            // Add the entries to the template
            $this->template->assign( 'entries', $this->db->getRecords( 'SELECT * FROM Notes' ) );

            // Add the title to the form
            $this->template->assign( 'title', 'Notes' );

            // Output the template
            $this->template->display();

        }

        // Add Note action
        function actionAddNote() {

            // Process the form
            if ( $this->form->validate() ) {

                // Get the form entries
                $entry = $this->form->getValues();

                // Save them
                $this->db->executeInsert( 'Notes', $entry );

                // Forward to the list view
                $this->forward( 'default' );

                // Return
                return;

            }

            // Add the form to the template
            $this->template->assign( 'form', $this->form->toHtml() );

            // Add the title to the form
            $this->template->assign( 'title', 'Add Note' );

            // Output the template
            $this->template->display();

        }

        // Edit a note
        function actionEditNote() {

            // Add the hidden field
            $this->form->addElement( 'hidden', '_noteid' );

            // Process the form
            if ( $this->form->validate() ) {

                // Get the form entries
                $entry = $this->form->getValues();

                // Save them
                $this->db->executeUpdate( 'Notes', $entry, 'NoteID = ' . $entry['_noteid'] );

                // Forward to the list view
                $this->forward( 'default' );

                // Return
                return;

            } else {

                // Get the note details
                $entry = $this->db->getRecord( 'SELECT NoteID as _NoteID, NoteTitle, NoteContents FROM Notes WHERE NoteID = ' . $_GET['id'] );

                // Add the defaults
                $this->form->setDefaults( $entry );

            }

            // Add the form to the template
            $this->template->assign( 'form', $this->form->toHtml() );

            // Add the title to the form
            $this->template->assign( 'title', 'Edit note' );

            // Output the template
            $this->template->display();

        }

        // Delete note action
        function actionDeleteNote() {

            // Delete the node
            $this->db->executeSql( 'DELETE FROM Notes WHERE NoteID = ' . $_GET['id'] );

            // Forward to the list view
            $this->forward( 'default' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>