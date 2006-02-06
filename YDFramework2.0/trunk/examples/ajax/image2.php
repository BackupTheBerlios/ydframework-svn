<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDAjax.php' );


    // Class definition
    class image2 extends YDRequest {

		function image2() {

			if (!extension_loaded('gd')) die('GD extension must be loaded. See http://www.php.net/gd');

			$this->YDRequest();
			$this->tpl  = new YDTemplate();
			$this->form = new YDForm('myform');
		}


		// Default action
		function actionDefault() {

			// create form elements
			$this->form->addElement( 'link',    'myspan1',  'Rebuild chart 1', '', '#' );
			$this->form->addElement( 'link',    'myspan2',  'Rebuild chart 2', '', '#' );
			$this->form->addElement( 'link',    'myspan3',  'Rebuild both charts', '', '#' );
			$this->form->addElement( 'link',    'myspan4',  'Rebuild chart 1 and apply "resize" with "modify(50, 100) to chart 2"', '', '#' );
			$this->form->addElement( 'link',    'myspan5',  'Apply fade to chart 1 using the response (not on ajax call) and simulate a slow server', '', '#' );

			$this->form->addElement( 'img',     'myimage1', '', array( 'width' => 550, 'height' => 250 ) );
			$this->form->addElement( 'img',     'myimage2', '', array( 'width' => 550, 'height' => 250 ) );

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl, $this->form );
			$this->ajax->useWaitingMessage();

			// create effect
			$ef1 = new YDAjaxEffect( 'myimage2', 'resize',   "modify(50, 100)" );

			// create events for each span
			$this->ajax->addEvent( 'myspan1', array( & $this, 'generatechart' ), 1);
			$this->ajax->addEvent( 'myspan2', array( & $this, 'generatechart' ), 2);
			$this->ajax->addEvent( 'myspan3', array( & $this, 'generatechart' ), 3);
			$this->ajax->addEvent( 'myspan4', array( & $this, 'generatechart' ), 1, null, null, $ef1 );
			$this->ajax->addEvent( 'myspan5', array( & $this, 'response2' ) );
			
			// process all events
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'This example will demonstrates the Waiting Message and effects creation' );
			$this->tpl->assign( 'form',  $this->form->render('html') );
			$this->tpl->display( 'general' );
		}


		// generatechart call invoked by ajax
		function generatechart( $chartnumber ){ 
			
			// generate charts
			if ( $chartnumber == 1 || $chartnumber == 3 ) $this->createGraph( 'ajaxchart1.png', 'bar' );
			if ( $chartnumber == 2 || $chartnumber == 3 ) $this->createGraph( 'ajaxchart2.png', 'area' );

			// assign results
			if ( $chartnumber == 1 || $chartnumber == 3 ) $this->ajax->addResult( 'myimage1', 'ajaxchart1.png' ) ;
			if ( $chartnumber == 2 || $chartnumber == 3 ) $this->ajax->addResult( 'myimage2', 'ajaxchart2.png' ) ;

			// return response to client browser
			return $this->ajax->processResults();
		}


		// response2 call invoked by ajax
		function response2(){
		
			// simulate slow server (to better see the waiting message)
			sleep( 3 );
		
			// create fade effect
			$fade = new YDAjaxEffect( 'myimage1', 'opacity', "custom(1, 0.5)" );
		
			// add it to the response
			$this->ajax->addEffect( $fade );

			// return response to client browser
			return $this->ajax->processResults();
		}
		
		
		// method for graphs creation
		function createGraph( $name, $type ){
		
			YDInclude( 'YDGraph.php' );
			
			// generate chart
			$values = array();
			for ( $i = 0; $i < 12 ; $i ++)
				$values[] = rand(20, 60);

			$labels = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );

			// Create a new graph
			$g1 = new YDGraph(550, 250 );
			$g1->setLimits( 0, 20 );
			$g1->setOffset( 5 );
			$g1->setFormat( 1, ',', '.' );
			$g1->addSeries( $values, $type, 'Series1', SOLID, '#444444', '#4682B4' );
			$g1->setYAxis( '#4682B4', SOLID, 5, 'example' );
			$g1->setLabels( $labels, '#000000', 1, HORIZONTAL );
			$g1->plot( dirname( __FILE__ ) .'/'. $name ); 
		}

	}

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
