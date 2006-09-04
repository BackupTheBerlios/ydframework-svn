<?php

	// include global parameters
    include_once( dirname( __FILE__ ) . '/../cm.php' );

    YDInclude( 'YDRequest.php' );
	YDInclude( 'YDCMUser.php' );


    // Class definition
    class userdetails extends YDRequest {

        // Class constructor
        function userdetails() {

			// init parent class
            $this->YDRequest();

			// create a user object
			$this->users = new YDCMUser();
        }


        // Default action (that shows user details form with some editable fields)
        function actionDefault() {

			// get user 6 
			YDDebugUtil::dump( $this->users->getUser( 6 ), '$this->users->getUser( 6 );' );

			// get user 4 (with details translated)
			YDDebugUtil::dump( $this->users->getUser( 4, true ), '$this->users->getUser( 4, true ); // details translated' );

			// get user 3 (don't exist because node is a group)
			YDDebugUtil::dump( $this->users->getUser( 3 ), '$this->users->getUser( 3 ); // 3 is a group' );

			// get user with username 'francisco'
			YDDebugUtil::dump( $this->users->getUser( 'francisco' ), '$this->users->getUser( "francisco" );' );
        }


        // Edit user example
        function actionEdit() {

			// add editing form (with details edition) with defaults of user 6
			$this->users->addFormEdit( 6, 'details' );

			// get form
			$form = & $this->users->getForm();

			// if form is not submitted just show it
			if ( ! $form->isSubmitted() ) return $form->display();

			// update user 6 with submitted information, magic isn't it ;)
			$result = $this->users->saveFormEdit( 6 );

			// print result message
			if ( $result->ok ) print( 'OK: ' . $result->message );
			else               print( 'Error: ' . $result->message );

			// display form again
			$form->display();
        }


		// this action will add a new sub-user of the root (super-administrator: id 100)
		function actionNew(){

			// add form for adding a new user to group 2
			$this->users->addFormNew( 2, array( 'username', 'password', 'state', 'details' ) );

			// get form
			$form = & $this->users->getForm();

			// if form is not submitted just show it
			if ( ! $form->isSubmitted() ) return $form->display();

			// add user to group 2
			$result = $this->users->saveFormNew( 2 );
			
			// print result message
			if ( $result->ok ) print( 'OK: ' . $result->message );
			else               print( 'Error: ' . $result->message );

			// display form again
			$form->display();
		}


    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
