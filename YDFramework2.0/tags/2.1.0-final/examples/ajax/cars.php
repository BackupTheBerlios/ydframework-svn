<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );

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
			$this->ajax = new YDAjax( $this->tpl );
			$this->ajax->addForm( $form );
			
			// register event to element 'car' and send to 'getmodel' method its dynamic value
			$this->ajax->addEvent( 'car', array( & $this, 'getmodel' ), array( 'car' ) );
			$this->ajax->processEvents();

			// assign form and display template
			$this->tpl->assign( 'title', 'Dependency with two select elements:' );
			$this->tpl->assign( 'form',  $form->render('html') );
			$this->tpl->display( 'general' );
		}


		// getmodel call invoked by ajax client
		function getmodel( $car ){

			switch ( intval( $car ) ){
				case 0  : $models = array(); break;
				case 1  : $models = array( 'F40', 'F50' ); break;
				case 2  : $models = array( 'Punto', 'Brava', 'Bravo' ); break;
				default : $models = array( 'Z3', 'Z4', 'M3' ); break;
			}

			// assign models to 'model' select form element
			$this->ajax->addResult( 'model', $models );

			// return response to client browser
			return $this->ajax->processResults();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>