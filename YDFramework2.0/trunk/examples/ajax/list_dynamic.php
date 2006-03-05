<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDAjax.php' );
	

    // Class definition
    class list_dynamic extends YDRequest {

		function list_dynamic() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
			$this->form = new YDForm('myform');
		}


		// Default action
		function actionDefault() {

			// create form elements
			$this->form->addElement( 'span', 'myspan', 'Click here to load contry list' );

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl );
			$this->ajax->addForm( $this->form );

			// assign element 'mybutton' with method 'getcontries'
			$this->ajax->addEvent( 'myspan', array( & $this, 'getcontries' ) );

			// process all events
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'Dynamic select' );
			$this->tpl->assign( 'form',  $this->form->render('html') );
			$this->tpl->display( 'general' );
		}


		// getcontries call invoked by ajax
		function getcontries(){

			// include Select lib
			YDInclude( 'YDFormElement_Select.php' );

			// create new select element assigned to form 'myform'
			$contries = new YDFormElement_Select( $this->form->getName(), 'contries', '', '', array( 'Portugal', 'Brasil', 'Belgium' ) );
			
			// assign form element code with span
			$this->ajax->addResult( 'myspan', $contries->toHtml() ) ;

			// disable previous event (myspan was assigned with a call to 'getcountries')
			$this->ajax->addResult( 'myspan', 'return false;', 'onclick' ) ;

			// return response to client browser
			return $this->ajax->processResults();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>