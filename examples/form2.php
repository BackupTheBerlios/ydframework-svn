<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDRequest.php' );
	require_once( 'YDForm2.php' );
	require_once( 'YDDebugUtil.php' );

	// Class definition
	class form2Request extends YDRequest {

		// Class constructor
		function form2Request() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Create the form
			$form = new YDForm2( 'firstForm' );
			$form->setDefaults( array( 'txt1' => 'First text' ) );
			$form->addElement( 'text', 'txt1', 'Enter text:' );
			$form->addElement( 'text', 'txt2', 'Enter text:', array( 'class' => 'textInputClass', 'name' => 'x' ) );
			$form->addElement( 'submit', 'cmd1', 'Send' );

			YDDebugUtil::dump( $form->toArray(), '$form as array' );

			$form->display();

			// Get the values
			YDDebugUtil::dump( $form->getValue( 'txt1' ), 'txt1' );
			YDDebugUtil::dump( $form->getValue( 'txt2' ), 'txt2' );

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
