<?php

    include_once( dirname( __FILE__ ) . '/../cm.php' );

	YDInclude( 'YDCMUsers.php' );

    // Class definition
    class userdetails extends cm {

        // Class constructor
        function userdetails() {

			// init parent class
            $this->cm();

			// create a user object
			$this->users = new YDCMUsers();
        }


        // Default action (that shows user details form with some editable fields)
        function actionDefault() {

			// get user id 101 details and display them
			YDDebugUtil::dump( $this->users->getUser( 101, true ), '$this->users->getUser( 101, true )' );
        }


        // Edit user example
        function actionEdit() {

			// add editing form of user 101.
			// user cannot edit username, cannot see password, cannot see login details, can edit user details, cannot see access info, cann edit permissions
			$this->users->addFormEdit( 101, false, null, null, true, null, true );

			// get form
			$form = & $this->users->getForm();

			// if form is not submitted just show it
			if ( ! $form->isSubmitted() ) return $form->display();

			// update user 101 with submitted information, magic isn't it ;) 100 is our id (created user, for loggin)
			$result = $this->users->saveFormEdit( 101, 100 );

			// check if result is a array (form error messages)
			if ( is_array( $result ) ) return $form->display();

			// if update was sucessfull, show ok message in current language
			if ( $result == 1 ) return print( t( 'user details updated' ) );

			// if form is valid but details are not changed (because are same as the db ones)
			if ( $result == 0 ) return print( t( 'user not updated' ) );
        }


		// this action will add a new sub-user of the root (super-administrator: id 100)
		function actionNew(){

			// add form for adding a new user as child of user 100. 
			// user can edit username, can edit password, can edit login details, can edit user details, cannot see access info, can edit permissions
			$this->users->addFormNew( 100, true, true, true, true, null, true );

			// get form
			$form = & $this->users->getForm();

			// if form is not submitted just show it
			if ( ! $form->isSubmitted() ) return $form->display();

			// add user ( 1st argument 100: parent of new user is 100; 2nd arg 100: we (user id that is creating this element) have id 100
			$result = $this->users->saveFormNew( 100, 100 );
			
			// check if result has form errors
			if ( is_array( $result ) ) return $form->display();

			// if creation is sucessfull, show ok message in current language
			print( t( 'user added' ) );
		}


    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
