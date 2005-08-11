<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDAjax.php' );
    YDInclude( 'YDPersistent.php' );	

    // Class definition
    class persistent_store extends YDRequest {

		function persistent_store() {

			$this->YDRequest();
			$this->tpl  = new YDTemplate();
			$this->form = new YDForm('myform');
		}


		// Default action
		function actionDefault() {

			// create form elements
			$this->form->addElement( 'text',   'variable',   'Value' );
			$this->form->addElement( 'span',   'spanresult', '&nbsp;' );
			$this->form->addElement( 'button', 'write',      'Write value in server' );
			$this->form->addElement( 'button', 'read',       'Read last value stored' );
 
 			$this->form->setDefault( 'variable', 'example' );
			
			// create ajax object
			$this->ajax = new YDAjax( $this->tpl, $this->form );

			// add write and read events
			$this->ajax->addEvent( 'write', array( & $this, 'write' ), 'variable' );
			$this->ajax->addEvent( 'read',  array( & $this, 'read' ) );

			// process all events
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'Persistent store without form validation' );
			$this->tpl->assign( 'form',  $this->form->render('html') );
			$this->tpl->display( 'general' );
		}


		// read call invoked by ajax
		function read(){

			// put select inside span
			$this->ajax->addResult( 'spanresult', YDPersistent::get( 'xpto', "Variable doesn't exist") );

			// return response to client browser
			return $this->ajax->processResults();
		}


		// read call invoked by ajax
		function write( $var ){

			// write valiable
			YDPersistent::set( 'xpto', $var );

			// add result string to span
			$this->ajax->addResult( 'spanresult', 'Variable stored' );

			// clean text box
			$this->ajax->addResult( 'variable', '' );

			// return response to client browser
			return $this->ajax->processResults();
		}



	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>