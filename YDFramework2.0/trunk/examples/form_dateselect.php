<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDRequest.php' );

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
			$elementDate = $form->addElement( 'dateselect', 'dateSelect1', 'Enter data:' );
			$elementDate = $form->addElement( 'dateselect', 'dateSelect2', 'Enter data:' );
			$elementTime = $form->addElement( 'timeselect', 'timeSelect1', 'Enter data:' );
			$elementDateTime = $form->addElement( 'datetimeselect', 'datetimeSelect1', 'Enter data:' );
			$form->addElement( 'submit', 'cmd1', 'Send' );
			$form->setDefaults( array( 'dateSelect1' => array( 'month'=>4, 'day'=>4, 'year'=>2002 ) ) );
			if ( YD_DEBUG == 1 ) {
				YDDebugUtil::dump( $form->_regElements, 'Registered elements' );
				YDDebugUtil::dump( $form->_regRules, 'Registered rules' );
				YDDebugUtil::dump( $form->_regFilters, 'Registered filters' );
				YDDebugUtil::dump( $form->_filters, 'Filters' );
				YDDebugUtil::dump( $form->_rules, 'Rules' );
				YDDebugUtil::dump( $form->_formrules, 'Form Rules' );
				YDDebugUtil::dump( $form->getValue( 'dateSelect1' ), 'dateSelect1' );
				YDDebugUtil::dump( $form->getValue( 'timeSelect1' ), 'timeSelect1' );
				YDDebugUtil::dump( $form->getValue( 'datetimeSelect1' ), 'datetimeSelect1' );
				YDDebugUtil::dump( $form->getValues(), '$form->getValues()' );
				YDDebugUtil::dump( $_POST, '$_POST' );
				YDDebugUtil::dump( $_FILES, '$_FILES' );
			}
			if ( $form->validate() ) {
				YDDebugUtil::dump( $form->getValues(), '$form->getValues()' );
				YDDebugUtil::dump( $elementDate->getTimeStamp(), '$elementDate->getTimeStamp()' );
				YDDebugUtil::dump( $elementDate->getTimeStamp( '%d/%m/%Y' ), '$elementDate->getTimeStamp( "%d/%m/%Y" )' );
				YDDebugUtil::dump( date( 'M-d-Y', $elementDate->getTimeStamp() ), '$elementDate->gdate( getTimeStamp() )' );
				YDDebugUtil::dump( $elementTime->getTimeStamp(), '$elementTime->getTimeStamp()' );
				YDDebugUtil::dump( $elementTime->getTimeStamp( '%H:%M' ), '$elementTime->getTimeStamp( "%H:%M" )' );
				YDDebugUtil::dump( $elementDateTime->getTimeStamp(), '$elementDateTime->getTimeStamp()' );
				YDDebugUtil::dump( $elementDateTime->getTimeStamp( '%d/%m/%Y %H:%M' ), '$elementDateTime->getTimeStamp( "%H:%M" )' );
			}
			$form->display();

		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
