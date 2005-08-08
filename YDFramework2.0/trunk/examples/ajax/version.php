<?php

	// Standard include
	include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDForm.php' );
	YDInclude( 'YDAjax.php' );


	// version invoked by ajax
	function getversion(){
		
		// create ajax response object
		$response = new YDAjaxResponse();

		// assign span 'myspanresult' of 'myform' with ydf version name
		$response->assignResult('myform', 'myspanresult', 'span', YD_FW_NAMEVERS);

		// return response to client browser
		return $response->getXML();
	}
	

	// Class definition
	class version extends YDRequest {

		function version() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
		}


		// Default action
		function actionDefault() {

			// create a form with a span and a button
			$form = new YDForm('myform');
			$form->addElement('span',   'myspanresult', '&nbsp;', array('style' => 'WIDTH:350px;BACKGROUND-COLOR:#ccccff') );
			$form->addElement('button', 'mybutton',     'Get YDFramework version');

			
			// create ajax object
			$ajax = new YDAjax();

			// define template that we will use (YDAjax will assign all js needed)
			$ajax->setTemplate( $this->tpl );
			
			// define which default form we will use (this way we don't need always to define a form in registerElement)
			$ajax->setForm( $form );

			// register element mybutton (mybutton will be assigned with 'getversion' call in the client side)
			$ajax->registerElement( 'mybutton', 'getversion' );
			

			// assign form and display template
			$this->tpl->assign( 'title', 'This is a simple ajax example');
			$this->tpl->assign( 'form',  $form->tohtml() );
			$this->tpl->display( 'general' );
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );
?>
