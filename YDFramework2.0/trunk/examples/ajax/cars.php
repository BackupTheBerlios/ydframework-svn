<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );

	// getmodel call invoked by ajax client
	function getmodel( $car ){

		switch ( intval( $car ) ){
			case 0  : $models = array(); break;
			case 1  : $models = array( 'F40', 'F50' ); break;
			case 2  : $models = array( 'Punto', 'Brava', 'Bravo' ); break;
			default : $models = array( 'Z3', 'Z4', 'M3' ); break;
		}

		// create ajax response object
		$response = new YDAjaxResponse();

		// assign select element 'model' of 'myform' with an array
		$response->assignResult('myform', 'model', 'select', $models);

		// return response to client browser
		return $response->getXML();
	}
	

	// Class definition
	class cars extends YDRequest {

		function cars() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with two select buttons
			$form = new YDForm( 'myform' );
			$form->addElement( 'select', 'car',   '', array(), array( 'Please select your car', 'Ferrari', 'Fiat', 'BMW' ) );
			$form->addElement( 'select', 'model', '', array() );

			// create ajax object
			$ajax = new YDAjax();

			// define template object (YDAjax will assign all js to this template)
			$ajax->setTemplate( $this->tpl );
			
			// define which default form we will use (this way we don't need to define form in registerElement)
			$ajax->setForm( $form );
			
			// register element 'car' with event 'getmodel' using dynamic value from 'car' element
			$ajax->registerElement( 'car', 'getmodel', array( 'car' ) );
			$ajax->processRequests();

			// assign form and display template
			$this->tpl->assign( 'title', 'Dependency with two select elements:' );
			$this->tpl->assign( 'form',  $form->render('html') );
			$this->tpl->display( 'general' );
		}


	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>