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
			$this->ajax = new YDAjax( $this->tpl, $this->form);
			$this->ajax->ignoreEffects();

			// assign element 'mybutton' with method 'processform' with 'myform' values as argument
			$this->ajax->addEvent( 'mybutton', array( & $this, 'processform' ), 'myform' );
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'YDAjax form submition' );
			$this->tpl->assign( 'form',  $this->form->render('html') );
			$this->tpl->display( 'general' );
        }


		// processform call invoked by ajax
		function processform( $formvalues ){

			// if form is not valid, send errors to client side
			if (!$this->form->validate($formvalues)) 
				return $this->ajax->sendFormErrors( $this->form );

			// assign span 'myspanresult' form dump as result
			$this->ajax->addResult( 'myspanresult',  $this->form->getValues() ) ;
			$this->ajax->addResult( 'mybutton',     'Send form with YDAjax again :)' );

			// return response to client browser
			return $this->ajax->processResults();
		}

   }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>