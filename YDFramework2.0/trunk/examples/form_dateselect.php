<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	require_once( 'YDForm.php' );
	require_once( 'YDRequest.php' );

	// Class definition
	class form_dateselect extends YDRequest {

		// Class constructor
		function form_dateselect() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Create the form
			$form = new YDForm( 'form1' );
			$element = $form->addElement( 'dateselect', 'dateSelect1', 'Enter data:' );
			$form->addRule( 'dateSelect1', 'required', 'dateSelect1 required' );
			$form->setDefaults( array( 'dateSelect1' => array( 'm'=>4, 'd'=>4, 'y'=>2002 ) ) );
			$form->addElement( 'submit', 'cmd1', 'Send' );
			if ( YD_DEBUG == 1 ) {
				YDDebugUtil::dump( $form->_regElements, 'Registered elements' );
				YDDebugUtil::dump( $form->_regRules, 'Registered rules' );
				YDDebugUtil::dump( $form->_regFilters, 'Registered filters' );
				YDDebugUtil::dump( $form->_filters, 'Filters' );
				YDDebugUtil::dump( $form->_rules, 'Rules' );
				YDDebugUtil::dump( $form->_formrules, 'Form Rules' );
				YDDebugUtil::dump( $form->getValue( 'txt1' ), 'txt1' );
				YDDebugUtil::dump( $form->getValue( 'txt2' ), 'txt2' );
				YDDebugUtil::dump( $_POST, '$_POST' );
				YDDebugUtil::dump( $_FILES, '$_FILES' );
			}
			if ( $form->validate() ) {
				YDDebugUtil::dump( $form->getValues(), 'Form values' );
				YDDebugUtil::dump( $element->getTimeStamp(), 'getTimeStamp()' );
				YDDebugUtil::dump( $element->getTimeStamp( '%d/%m/%Y' ), 'getTimeStamp( "%d/%m/%Y" )' );
				YDDebugUtil::dump( date( 'M-d-Y', $element->getTimeStamp() ), 'date( getTimeStamp() )' );
			}
			$form->display();

		}

	}

	// Process the request
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>