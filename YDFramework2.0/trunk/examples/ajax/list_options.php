<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDAjax.php' );
	
    // Class definition
    class list_options extends YDRequest {

		function list_options() {

			$this->YDRequest();
			$this->tpl  = new YDTemplate();
			$this->form = new YDForm('myform');
		}


		// Default action
		function actionDefault() {

			// create form elements
			$this->form->addElement( 'span',   'myspan', 'Click here to add an item to the dropdown' );
			$this->form->addElement( 'select', 'items',  '', array(), array( 'Click on the link above' ) );

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl, $this->form );
			$this->ajax->ignoreEffects();

			// assign element 'mybutton' with method 'getcontries'
			$this->ajax->addEvent( 'myspan', array( & $this, 'addentry' ), 'items', null, 'all' );

			// if you want to see what's going on, use YDAjax::dump()
			// $this->ajax->dump();

			// process all events
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'Add options to a select element with YDAjax' );
			$this->tpl->assign( 'form',  $this->form->render('html') );
			$this->tpl->display( 'general' );
		}


		// addentry call invoked by ajax
		function addentry( $items ){
			
			// create element name
			$element = time();
			
			// add element name and value to previous items
			$items[ $element ] = 'New option added at '. YDStringUtil::formatDate( time(), '%d %B %Y %H:%M:%S' );
			
			// assign items to select box (because it's an array, will replace all values)
			$this->ajax->addResult( 'items', $items );

			// define the last element as the element selected (because it's a string will define a new selection)
			$this->ajax->addResult( 'items', $element );

			// return response to client browser
			return $this->ajax->processResults();
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>