<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );

	class MyClass extends YDBase {

		function MyClass() {
			$this->var1 = 'a';
			$this->var2 = 1;
			$this->var3 = array( 'a', 'b', 'c' );
			$this->Var4 = 'xxx';
			$this->_hiddenVar1 = 'hidden1';
			$this->__hiddenVar2 =  'hidden2';
		}

	}

	// Class definition
	class objects_to_arrays extends YDRequest {

		// Class constructor
		function objects_to_arrays() {
			$this->YDRequest();
		}

		// Default action
		function actionDefault() {
			$obj = new MyClass();
			YDDebugUtil::dump( $obj->toArray() );
		}

	}

	// Process the request
	YDInclude( 'YDF2_process.php' );

?>