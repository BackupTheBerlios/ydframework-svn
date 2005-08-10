<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDAjax.php' );
	

    // Class definition
    class form extends YDRequest {

        function form() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();

			$this->form = new YDForm('myform');
        }


        // Default action
        function actionDefault() {

			// create form elements
			$this->form->addElement( 'text',   'myname',       'Name' );
			$this->form->addElement( 'text',   'myemail',      'Email' );
			$this->form->addElement( 'span',   'myspanresult', '&nbsp;' );
			$this->form->addElement( 'button', 'mybutton',     'Send form with YDAjax' );

			// create form rules
			$this->form->addRule( 'myname',  'required', 'Name is required' );
			$this->form->addRule( 'myemail', 'required', 'Email is required' );
			$this->form->addRule( 'myemail', 'email',    'Email is not valid' );

			// create ajax object
			$ajax = new YDAjax();

			// define template object and form object
			$ajax->setTemplate( $this->tpl );
			$ajax->setForm( $this->form );

			// assign element 'mybutton' with method 'processform' with 'myform' values as argument
			$ajax->registerElement( 'mybutton', array( & $this, 'processform' ), 'myform' );
			$ajax->processRequests();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'YDAjax form submition' );
			$this->tpl->assign( 'form',  $this->form->render('html') );
			$this->tpl->display( 'general' );
        }


		// processform call invoked by ajax
		function processform( $formvalues ){

			// create ajax response object
			$response = new YDAjaxResponse();
			
			// if form is not valid, send errors to client side
			if (!$this->form->validate($formvalues)) 
				return $response->sendFormErrors( $this->form );

			// assign span 'myspanresult' form dump as result
			$response->assignResult( 'myform', 'myspanresult', 'span',   YDDebugUtil::r_dump($this->form->getValues(), true) );
			$response->assignResult( 'myform', 'mybutton',     'button', 'Send form with YDAjax again :)' );


			// return response to client browser
			return $response->getXML();
		}

   }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>