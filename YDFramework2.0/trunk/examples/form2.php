<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDRequest.php' );

	// Class definition
	class form2 extends YDRequest {

		// Class constructor
		function form2() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {

			// Create the form
			$form = new YDForm( 'form1' );
			$form->registerFilter( 'reverse', 'strrev' );
			$form->setDefaults( 
				array( 
					'txt2' => 'First text', 'txt3' => "2\nlines",  'hid1' => 'me hidden', 'chk1' => 'x',  'sel1' => 2
				)
			);
			$text = & $form->addElement( 'text', 'txt1', 'Enter text 1:' );
			$text->_label = 'new label for txt1';
			$form->addElement( 'text', 'txt2', 'Enter text 2:', array( 'class' => 'textInputClass', 'name' => 'x' ) );
			$form->addElement( 'textarea', 'txt3', 'Enter text 2:' );
			$form->addElement( 'radio', 'rad1', 'Select a value 1:', array(), array( 1 => 'een', 2=>'twee' ) );
			$form->addElement( 'radio', 'rad2', 'Select a value 2:', array(), array( 1 => 'een<br/>', 2=>'twee' ) );
			$form->addElement( 'hidden', 'hid1', '' );
			$form->addElement( 'hidden', 'hid2', '', array(), 'i am also hidden' );
			$form->addElement( 'image', 'img1', '', array(), 'http://www.scripting.com/images/xml.gif' );
			$form->addElement( 'password', 'pas1', 'Enter your password' );
			$form->addElement( 'bbtextarea', 'bbt1', 'Enter your BBCode' );
			$form->addElement( 'checkbox', 'chk1', 'Select me please' );
			$form->addElement( 'checkbox', 'chk2', 'Select me please' );
			$form->addElement( 'select', 'sel1', 'Select an option:', array(), array( 1 => 'een', 2=>'twee' ) );
			$form->addElement( 'file', 'fil1', 'Select an file:' );
			$form->addElement( 'submit', 'cmd1', 'Send' );
			$form->addElement( 'reset', 'res1', 'Reset' );
			$form->addFilter( '__ALL__', 'upper' );
			$form->addFilter( 'txt1', 'trim' );
			$form->addFilter( 'txt2', 'reverse' );
			$form->addRule( 'txt1', 'required', 'txt1 is required' );
			$form->addRule( 'chk2', 'required', 'chk2 is required' );
			$form->addFormRule( array( & $this, 'formrule' ), 'txt1 is required' );
			if ( YDConfig::get( 'YD_DEBUG' ) == 1 || YDConfig::get( 'YD_DEBUG' ) == 2 ) {
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
				YDDebugUtil::dump( $form->toArray() );
			}
			if ( $form->validate() ) {
				YDDebugUtil::dump( $form->getValues(), 'Form values' );
			} else {
				$form->display();
			}

			// Create the form
			$form2 = new YDForm( 'form2' );
			$form2->setDefaults( array( 'txt1' => 'First text' ) );
			$form2->addElement( 'text', 'txt1', 'Enter text 1:' );
			$form2->addElement( 'text', 'txt2', 'Enter text 2:' );
			$form2->addElement( 'submit', 'cmd1', 'Send' );
			$form2->display();

		}

		function formrule() {
			//return array( '__ALL__' => 'fields are missing' );
			return true;
		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>
