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

			// create first form form
			$form1 = new YDForm( 'myform' );
			$form1->addElement( 'text',   'text1', 'Write something, eg: "David" (case sensitive)' );
			$form1->addElement( 'select', 'items',  '', array(), array( 'Francisco' => 'Francisco', 'Pieter' => 'Pieter', 'David' => 'David' ) );

			// second form
			$form2 = new YDForm( 'second' );
			$form2->addElement( 'text',   'text2', 'Write something, eg: "Pieter" (case sensitive)' );

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl );
			$this->ajax->addForm( $form1 );
			$this->ajax->addForm( $form2 );

			// assign event to mytext
			$this->ajax->addEvent( 'text1', array( & $this, 'setSelect' ), 'text1', 'onkeyup' );
			$this->ajax->addEvent( 'text2', array( & $this, 'setSelect' ), 'text2', 'onkeyup' );

			// process all events
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'Change selected value on-the-fly example' );
			$this->tpl->assign( 'form',  $form1->toHtml() . $form2->toHtml() );
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