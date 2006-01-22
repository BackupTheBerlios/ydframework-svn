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

			// image dimensions
			$this->width  = 550;
			$this->height = 250;			
		}


		// Default action
		function actionDefault() {

			// create form elements
			$this->form->addElement( 'link',    'myspan1',  'Click here to change chart 1', '', '#' );
			$this->form->addElement( 'link',    'myspan2',  'Click here to change chart 2', '', '#' );
			$this->form->addElement( 'link',    'myspan3',  'Click here to change both charts', '', '#' );
			$this->form->addElement( 'img',     'myimage1', '', array( 'width' => $this->width, 'height' => $this->height ) );
			$this->form->addElement( 'img',     'myimage2', '', array( 'width' => $this->width, 'height' => $this->height ) );

			// create ajax object
			$this->ajax = new YDAjax( $this->tpl, $this->form );

			// create effects
			$ef1 = $this->ajax->createEffect( 'fadeOut', 'myimage1', true );
			$ef2 = $this->ajax->createEffect( 'fadeOut', 'myimage2', true );

			// assign element 'mybutton' with method 'getcontries'
			$this->ajax->addEvent( 'myspan1', array( & $this, 'generatechart' ), 1, null, null, $ef1 );
			$this->ajax->addEvent( 'myspan2', array( & $this, 'generatechart' ), 2, null, null, $ef2 );
			$this->ajax->addEvent( 'myspan3', array( & $this, 'generatechart' ), 3, null, null, array( $ef1, $ef2 ) );

			// process all events
			$this->ajax->processEvents();

			// assign title, form and display template
			$this->tpl->assign( 'title', 'This example will generate new graphs (on the server side) and replace them (in the client side) with YDAjax' );
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

			// apply effects (fadeIn) to images
			if ( $chartnumber == 1 || $chartnumber == 3 ) $this->ajax->addEffect(  $this->ajax->createEffect( 'fadeIn', 'myimage1' )  );
			if ( $chartnumber == 2 || $chartnumber == 3 ) $this->ajax->addEffect(  $this->ajax->createEffect( 'fadeIn', 'myimage2', false, array( 'duration' => 0.2 ) ) );

			// return response to client browser
			return $this->ajax->processResults();
		}

		
		function createGraph( $name, $type ){
		
			YDInclude( 'YDGraph.php' );
			
			// generate chart
			$values = array();
			for ( $i = 0; $i < 12 ; $i ++)
				$values[] = rand(20, 60);

			$labels = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );

			// Create a new graph
			$g1 = new YDGraph( $this->width, $this->height );
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
