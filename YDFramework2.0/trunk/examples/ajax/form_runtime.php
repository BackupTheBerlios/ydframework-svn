<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDAjax.php' );


    // Class definition
    class form_runtime extends YDRequest {

        function form_runtime() {

			$this->YDRequest();
			$this->tpl = new YDTemplate();
        }


        // Default action
        function actionDefault() {

			$this->form = new YDForm( 'myform' );

			// create form elements
			$this->form->addElement( 'text',     'myname',     'Name' );
			$this->form->addElement( 'text',     'mybill',     'Billing address' );
			$this->form->addElement( 'checkbox', 'ship',       'Shipping address is not billing address' );
			$this->form->addElement( 'span',     'shipform',   '' );
			$this->form->addElement( 'button',   'mybutton',   'Send form details' );

			// create form rules
			$this->form->addRule( 'myname', 'required', 'Name is required' );
			$this->form->addRule( 'mybill', 'required', 'Billing address is required' );

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl );
			$this->ajax->addForm( $this->form );

			// add event to button (to send form details)
			$this->ajax->addEvent( 'mybutton', array( & $this, 'processform' ), $this->form->getName() );

			// add event to checkbox to compute shipping address form extra elements
			$this->ajax->addEvent( 'ship', array( & $this, 'sendShipForm' ), 'ship' );

			// process all events
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'YDAjax form submition' );
			$this->tpl->assign( 'form',  $this->form->toHTML() );
			$this->tpl->display( 'general' );
        }


		// process form call invoked by ajax
		function processform( $formvalues ){

			// if form is not valid, send errors to client side using an alert
			if ( ! $this->form->validate( $formvalues ) )
				return YDAjax::message( $this->form->getErrors( true, "\n* ", "* " ) );

			// if checkbox is ON we must add extra fields and validate them
			if ( $this->form->getValue( 'ship' ) ){
				
				// add extra fields
				$this->_computeExtra();
				
				// validate again
				if ( ! $this->form->validate( $formvalues ) )
 					return YDAjax::message( $this->form->getErrors( true, "\n* ", "* " ) );
			}

			// return OK message
			return YDAjax::message( "Thanks! \nHere goes what i got:\n\n" . var_export( $this->form->getValues(), true ) );
		}


		// Helper method to compute extra fields
		function _computeExtra(){
		
			// lets's add some elements..
			$this->form->addElement( 'text', 'sendname', 'Shipping name' );
			$this->form->addElement( 'text', 'sendaddr', 'Shipping address' );

			// add rules too
			$this->form->addRule( 'sendaddr', 'required', "You want to send to a different addr that is empty?!" );
			
			// we need to get the html of these new elements
			$arr = $this->form->toarray();
			$extraHTML  = '';
			$extraHTML .= $arr[ 'sendname' ][ 'label_html' ] . '<br />' . $arr[ 'sendname' ][ 'html' ] . '<br /><br />';
			$extraHTML .= $arr[ 'sendaddr' ][ 'label_html' ] . '<br />' . $arr[ 'sendaddr' ][ 'html' ] . '<br /><br />';

			return $extraHTML;
		}


		// send shipping address form (based on checkbox value)
		function sendShipForm( $chk ){
		
			// test if checkbox is ON
			if ( $chk ) $this->ajax->addResult( 'shipform', $this->_computeExtra() );
			else        $this->ajax->addResult( 'shipform', '' );

			return $this->ajax->processResults();
		}

   }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>