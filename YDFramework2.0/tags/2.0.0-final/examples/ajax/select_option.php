<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDAjax.php' );
	

    // Class definition
    class select_option extends YDRequest {

		function select_option() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create form
			$form = new YDForm('myform');
			$form->addElement( 'text',   'mytext', 'Write something, eg: "David" (case sensitive)' );
			$form->addElement( 'select', 'items',  '', array(), array( 'Francisco' => 'Francisco', 'Pieter' => 'Pieter', 'David' => 'David' ) );

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl, $form );

			// assign event to mytext
			$this->ajax->addEvent( 'mytext', array( & $this, 'setSelect' ), 'mytext', 'onkeyup' );

			// process all events
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'Change selected value on-the-fly example' );
			$this->tpl->assign( 'form',  $form->render('html') );
			$this->tpl->display( 'general' );
		}


		// call invoked by ajax
		function setSelect( $text ){
			
			// assign selection
			$this->ajax->addResult( 'items', $text ) ;

			// return response to client browser
			return $this->ajax->processResults();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>