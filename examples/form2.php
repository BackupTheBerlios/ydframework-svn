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
			$form = new YDForm2( 'form1' );
			$form->registerFilter( 'reverse', 'strrev' );
			$form->setDefaults( array( 'txt1' => 'First text', 'txt3' => "two\nlines" ) );
			$form->addElement( 'text', 'txt1', 'Enter text 1:' );
			$form->addElement( 'text', 'txt2', 'Enter text 2:', array( 'class' => 'textInputClass', 'name' => 'x' ) );
			$form->addElement( 'textarea', 'txt3', 'Enter text 2:' );
			$form->addElement( 'radio', 'rad1', 'Select a value 1:', array(), array( 1 => 'een', 2=>'twee' ) );
			$form->addElement( 'radio', 'rad2', 'Select a value 2:', array(), array( 1 => 'een<br/>', 2=>'twee' ) );
			$form->addElement( 'submit', 'cmd1', 'Send' );
			$form->addFilter( '__ALL__', 'upper' );
			$form->addFilter( 'txt1', 'trim' );
			$form->addFilter( 'txt2', 'reverse' );
			if ( YD_DEBUG == 1 ) {
				YDDebugUtil::dump( $form->_regElements, 'Registered elements' );
				YDDebugUtil::dump( $form->_regRules, 'Registered rules' );
				YDDebugUtil::dump( $form->_regFilters, 'Registered filters' );
				YDDebugUtil::dump( $form->_filters, 'Filters' );
				YDDebugUtil::dump( $form->toArray(), '$form as array' );
				YDDebugUtil::dump( $form->getValue( 'txt1' ), 'txt1' );
				YDDebugUtil::dump( $form->getValue( 'txt2' ), 'txt2' );
				YDDebugUtil::dump( $_POST, '$_POST' );
			}
			$form->display();

			// Create the form
			$form2 = new YDForm2( 'form2' );
			$form2->setDefaults( array( 'txt1' => 'First text' ) );
			$form2->addElement( 'text', 'txt1', 'Enter text 1:' );
			$form2->addElement( 'text', 'txt2', 'Enter text 2:' );
			$form2->addElement( 'submit', 'cmd1', 'Send' );
			$form2->display();

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
