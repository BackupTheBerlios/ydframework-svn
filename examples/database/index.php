<?php

	// Initialize the Yellow Duck Framework
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );
	require_once( dirname( __FILE__ ) . '/config.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDForm.php' );
	require_once( 'YDDatabase.php' );

	// Class definition for the index request
	class index extends YDRequest {

		// Class constructor
		function index() {

			// Initialize the parent class
			$this->YDRequest();

			// Make the database connection
			$this->db = new YDDatabase(
				$GLOBALS['db']['type'], $GLOBALS['db']['name'], $GLOBALS['db']['user'], $GLOBALS['db']['pass'] 
			);

			// Create the add form
			$this->form = new YDForm( 'addEntryForm' );

			// Add the elements
			$this->form->addElement( 'text', 'NoteTitle', 'Title:' );
			$this->form->addElement( 'textarea', 'NoteContents', 'Contents:' );
			$this->form->addElement( 'submit', '_cmdSubmit', 'Save' );

			// Apply filters
			$this->form->addFilter( 'NoteTitle', 'trim' );
			$this->form->addFilter( 'NoteContents', 'trim' );

			// Add a rule
			$this->form->addRule( 'NoteTitle', 'required', 'Title is required' );
			$this->form->addRule( 'NoteContents', 'required', 'Contents is required' );

		}

		// Default action
		function actionDefault() {

			// Add the entries to the template
			$this->setVar( 'entries', $this->db->getRecords( 'SELECT * FROM Notes' ) );

			// Add the title to the form
			$this->setVar( 'title', 'Notes' );

			// Output the template
			$this->outputTemplate();

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
			$this->setVar( 'form', $this->form->toHtml() );

			// Add the title to the form
			$this->setVar( 'title', 'Add Note' );

			// Output the template
			$this->outputTemplate();

		}

		// Edit a note
		function actionEditNote() {

			// Add the hidden field
			$this->form->addElement( 'hidden', '_NoteID' );

			// Process the form
			if ( $this->form->validate() ) {

				// Get the form entries
				$entry = $this->form->getValues();

				// Save them
				$this->db->executeUpdate( 'Notes', $entry, 'NoteID = ' . $entry['_NoteID'] );

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
			$this->setVar( 'form', $this->form->toHtml() );

			// Add the title to the form
			$this->setVar( 'title', 'Edit note' );

			// Output the template
			$this->outputTemplate();

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
	require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_process.php' );

?>