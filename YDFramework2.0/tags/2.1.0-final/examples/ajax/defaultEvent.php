<?php
	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );


	class defaultEvent extends YDRequest {


		function defaultEvent() {

			$this->YDRequest();

			$this->template = new YDTemplate();
			$this->ajax = new YDAjax( & $this->template );
		}	


		function actionDefault() {


			// crete a simple form
			$form = new YDForm( 'myform' );
			$form->addElement( 'button', 'mybutton', 'Standard form button event' );

			// add an event to form button, JS event and default event handling
			$this->ajax->addForm( $form );
			$this->ajax->addEvent( 'mybutton',       array( &$this, 'buttonEvent' ) );
			$this->ajax->addEvent( 'customEvent(x)', array( &$this, 'customEvent' ), 'var x' );
			$this->ajax->addEvent( '*',              array( &$this, 'defaultOne' ) );

			// process all events
			$this->ajax->processEvents();

			$this->template->assign( 'form',  $form->toHTML() );
			$this->template->display();
		}


		// ajax response to button
		function buttonEvent(){

			return YDAjax::message( 'You have clicked on the form button, right?' );
		}


		// ajax response to custom JS event
		function customEvent( $x ){

			return YDAjax::message( 'You have clicked on custom button.. and it have send: "' . $x . '"' );
		}


		// ajax response to all other events
		function defaultOne(){

			// get function name and arguments
			list( $f_name, $f_args ) = func_get_args();

			return YDAjax::message( "Here goes what i got:\n* Function:  '" . $f_name . "'\n* Arguments:  '" . implode( "'; '", $f_args ) . "'" );
		}

}


    YDInclude( 'YDF2_process.php' );
?>