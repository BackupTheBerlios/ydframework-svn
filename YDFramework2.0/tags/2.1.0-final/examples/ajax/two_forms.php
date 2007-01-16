<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDAjax.php' );
	

    // Class definition
    class two_forms extends YDRequest {

        function two_forms() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();

			$this->form1 = new YDForm( 'myform1' );
			$this->form2 = new YDForm( 'myform2' );
        }


        // Default action
        function actionDefault() {

			// create elements of form1
			$this->form1->addElement( 'text',   'myname_1',       'Name' );
			$this->form1->addElement( 'text',   'myemail_1',      'Email' );
			$this->form1->addElement( 'button', 'mybutton_1',     'Send form with YDAjax' );

			// create elements of form2
			$this->form2->addElement( 'text',   'myname_2',       'Name (2nd)' );
			$this->form2->addElement( 'text',   'myemail_2',      'Email (2nd)' );
			$this->form2->addElement( 'button', 'mybutton_2',     'Send it !' );

			// create form 1 rules
			$this->form1->addRule( 'myname_1',  'required', 'Name is required' );
			$this->form1->addRule( 'myemail_1', 'required', 'Email is required' );
			$this->form1->addRule( 'myemail_1', 'email',    'Email is not valid' );

			// create ajax object and add forms to the ajax object
			$this->ajax = new YDAjax( $this->tpl );
			$this->ajax->addForm( $this->form1 );
			$this->ajax->addForm( $this->form2 );

			// assign element mybutton_1 (of form1) with method 'processform' and mybutton_2 with method 'msg'
			$this->ajax->addEvent( 'mybutton_1', array( & $this, 'processform' ), $this->form1->getName() );
			$this->ajax->addEvent( 'mybutton_2', array( & $this, 'msg' ), array( 'myname_2', 'myemail_2' ) );
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'YDAjax form submition' );
			$this->tpl->assign( 'form',  $this->form1->render('html') . $this->form2->render('html') );
			$this->tpl->display( 'general' );
        }


		// process form call invoked by mybutton_1
		function processform( $formvalues = null ){

			// if form is not valid, send errors to client side using an alert
			if ( !$this->form1->validate( $formvalues ) )
				return YDAjax::message( $this->form1->getErrors( true, "\n* ", "* " ) );

			// return a simple result
			return YDAjax::message( $this->form1->getValues() );
		}


		// process form call invoked by mybutton_2
		function msg( $name = '', $email = '' ){

			return YDAjax::message( "Values of 2nd form\n*Name: $name\n*Email: $email" );
		}

   }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>