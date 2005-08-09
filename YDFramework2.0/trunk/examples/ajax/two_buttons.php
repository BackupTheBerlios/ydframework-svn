<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );


	// version invoked by ajax
	function result( $option ){

		// compute message
		switch ( $option ){
			case 1 :  $message = YD_FW_NAMEVERS; break;
			
			default : YDInclude( 'YDUtil.php' ); 
			          $message = YDStringUtil::formatDate( time(), '%d %B %Y %H:%M:%S' );
		}
		
		// create ajax response object
		$response = new YDAjaxResponse();

		// assign span 'myspanresult' of 'myform' with ydf version name
		$response->assignResult('myform', 'myspanresult', 'span', $message);

		// return response to client browser
		return $response->getXML();
	}
	

	// Class definition
	class two_buttons extends YDRequest {

		function two_buttons() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with a span and a button
			$form = new YDForm('myform');
			$form->addElement('span',   'myspanresult', '&nbsp;', array('style' => 'WIDTH:350px;BACKGROUND-COLOR:#ccccff') );
			$form->addElement('button',	'mybutton',     'Get YDFramework version');
			$form->addElement('button',	'mybutton2',    'Get time');

			// create ajax object
			$ajax = new YDAjax();

			// define template that we will use (YDAjax will assign all js needed)
			$ajax->setTemplate( $this->tpl );
			
			// define which default form we will use (this way we don't need to define form in registerElement)
			$ajax->setForm( $form );

			// register element mybutton (mybutton will be assigned with 'getversion' call in the client side)
			$ajax->registerElement( 'mybutton',  'result', 1 );
			$ajax->registerElement( 'mybutton2', 'result', 2 );
			
			$ajax->processRequests();
			
			// assign form and display template
			$this->tpl->assign( 'title', 'This is a two buttons example with a different event assigned for each one');
			$this->tpl->assign( 'form', $form->tohtml() );
			$this->tpl->display( 'general' );
		}


	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>