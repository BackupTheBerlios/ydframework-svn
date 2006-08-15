<?php

    include_once( dirname( __FILE__ ) . '/../cm.php' );

    // Class definition
    class userdetails extends cm {

        // Class constructor
        function userdetails() {

			// init parent class
            $this->cm();

			// create a user object
			$this->users = YDCMComponent::module( 'YDCMUsers' );
        }


        // Default action (that shows user details form with some editable fields)
		// User should only update his details eg name, email
        function actionDefault() {

			// add form. User cannot edit username, cannot see password, cannot edit login details, can edit user details
			$this->users->addFormDetails( false, false, false, true );

			// get info of user id 100
			$defaults = $this->users->getUser( 100 );

			// get user form (filled with previous defaults)
			$form = $this->users->getForm( $defaults );

			// show form :)
			$form->display();
        }


        // Change details example
        function actionChangeDetails() {

			// add form. User cannot edit username, cannot see password, cannot edit login details, can edit user details
			$this->users->addFormDetails( false, false, false, true );

			// get form filled with details from user id 100
			$defaults = $this->users->getUser( 100 );

			// get user details form with a submit button
			$form = $this->users->getForm( $defaults );
			$form->addElement( 'submit', '_cmdSubmit', 'Submit' );

			// if we have submitted the form, update user
			if ( $form->isSubmitted() ){

				// update user 100 with submitted information, magic isn't it ;)
				$result = $this->users->changeUserForm( 100 );

				// check if result is a array (form error messages)
				if( is_array( $result ) ) return print( implode( '<br>', $result ) );

				// if update was sucessfull, show ok message in current language
				if ( $result == 1 ) return print( t( 'user details updated' ) );

				// if form is valid but details are not changed (because are same as the db ones)
				if ( $result == 0 ) return print( t( 'user not updated' ) );
			}

			// show form :)
			$form->display();
        }


    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
