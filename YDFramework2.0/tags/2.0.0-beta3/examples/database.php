<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDTemplate.php' );

	// Class definition
	class database extends YDRequest {

		// Class constructor
		function database() {
			$this->YDRequest();
			$this->template = new YDTemplate();
		}

		// Default action
		function actionDefault() {

			// Make the database connection
			$db = @mysql_connect( 'localhost', 'root', '' );
			$result = @mysql_select_db( 'test' );

			// Check for errors
			if ( ! $db || ! $result ) {
				$this->template->assign( 'error', mysql_error() );
				$this->template->assign( 'result', null );
			} else {
				$data = $this->_query_db( $db, 'show processlist' );
				$this->template->assign( 'processList', $data );
				$data = $this->_query_db( $db, 'show status' );
				$this->template->assign( 'status', $data );
				$data = $this->_query_db( $db, 'show variables' );
				$this->template->assign( 'variables', $data );
			}

			// Close the database query
			@mysql_close( $db );

			// Output the template
			$this->template->display();

		}

		// Function to do the database query
		function _query_db( $db, $sql ) {
			
			// Perform the query
			$result = mysql_query( $sql, $db );

			// Start with an empty dataset
			$dataset = array();

			// Fetch the array
			while ( $line = mysql_fetch_assoc( $result ) ) {
				array_push( $dataset, $line );
			}

			// Free the result
			mysql_free_result( $result );

			// Return the dataset
			return $dataset;

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
