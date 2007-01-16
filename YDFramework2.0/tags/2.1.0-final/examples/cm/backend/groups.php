<?php

	// include global parameters
    include_once( dirname( __FILE__ ) . '/../cm.php' );

	// include libs
    YDInclude( 'YDRequest.php' );
	YDInclude( 'YDCMGroup.php' );


    // Class definition
    class groups extends YDRequest {

        // Class constructor
        function groups() {

			// init parent class
            $this->YDRequest();

			// create a group object
			$this->groups = new YDCMGroup();
        }


        // Default action (that shows group details)
        function actionDefault() {

			// get group 2
			YDDebugUtil::dump( $this->groups->getGroup( 2 ), '$this->groups->getGroup( 2 );' );

			// get group 4 (don't exist because node is a user)
			YDDebugUtil::dump( $this->groups->getGroup( 4 ), '$this->groups->getGroup( 4 ); // 4 is a user' );

			// get group with name 'Administrators'
			YDDebugUtil::dump( $this->groups->getGroup( 'Administrators' ), '$this->groups->getGroup( "Administrators" );' );
        }


        // Edit group example
        function actionEdit() {

			// add editing form of group id 2
			$this->groups->addFormEdit( 2 );

			// get form
			$form = & $this->groups->getForm();

			// if form is not submitted just show it
			if ( ! $form->isSubmitted() ) return $form->display();

			// update user 6 with submitted information, magic isn't it ;)
			$result = $this->groups->saveFormEdit( 2 );

			// print result message
			if ( $result->ok ) print( 'OK: ' . $result->message );
			else               print( 'Error: ' . $result->message );

			// display form again
			$form->display();
        }


		// this action will add a new group
		function actionNew(){

			// add form for adding a new user
			$this->groups->addFormNew();

			// get form
			$form = & $this->groups->getForm();

			// if form is not submitted just show it
			if ( ! $form->isSubmitted() ) return $form->display();

			// add group
			$result = $this->groups->saveFormNew(4);
			
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
