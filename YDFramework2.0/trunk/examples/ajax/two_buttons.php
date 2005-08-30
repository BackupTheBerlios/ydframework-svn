<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );


	// Class definition
	class two_buttons extends YDRequest {

		function two_buttons() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with a span and two buttons
			$form = new YDForm('myform');
			$form->addElement('span',   'myspanresult', '&nbsp;', array('style' => 'WIDTH:350px;BACKGROUND-COLOR:#ccccff') );
			$form->addElement('button',	'mybutton',     'Get YDFramework version');
			$form->addElement('button',	'mybutton2',    'Get time');

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl, $form );
			$this->ajax->ignoreEffects();

			// register element mybutton with event result and fixed argument '1' and mybutton2 with event 'result' with argument 2
			$this->ajax->addEvent( 'mybutton',  array( & $this, 'result' ), 1 );
			$this->ajax->addEvent( 'mybutton2', array( & $this, 'result' ), 2 );

			// process events added
			$this->ajax->processEvents();
			
			// assign form and display template
			$this->tpl->assign( 'title', 'This is a two buttons example with a different event assigned for each one');
			$this->tpl->assign( 'form', $form->tohtml() );
			$this->tpl->display( 'general' );
		}



		// result call invoked by ajax client
		function result( $option ){

			// compute message
			switch ( $option ){
				case 1 :  $message = YD_FW_NAMEVERS; break;
			
				default : YDInclude( 'YDUtil.php' ); 
				          $message = YDStringUtil::formatDate( time(), '%d %B %Y %H:%M:%S' );
			}

			// assign span 'myspanresult' of 'myform' with dynamic message
			$this->ajax->addResult( 'myspanresult', $message );

			// return response to client browser
			return $this->ajax->processResults();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>