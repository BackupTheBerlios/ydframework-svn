<?php

    // ydf include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDResult.php' );


    // Class definition
    class result extends YDRequest {

        // Class constructor
        function result() {

			// init parent class and test class
            $this->YDRequest();
			$this->test = new mytest();
        }


        // Default action, demonstrates the simplest way of handle results
        function actionDefault() {

			// try to div by 0
			$res = $this->test->div( 10 );
			
			// check if div processing was ok or we get errors
			if ( $res->ok ) print $res->message;
			else            print 'ERROR: ' . $res->message;
        }


        // Check error types
        function actionErrors() {

			// set internal test 5 ;) (we will get an error)
			$this->test->set( 5 );

			// try to div by 10
			$res = $this->test->div( 10 );
			
			// check if div processing was ok
			if ( $res->ok ) return print $res->message . ' ' . $res->value;
			else            return $res->dump();
        }


    }


	class mytest{
	
		function mytest(){
			$this->a = 10;
		}


		function set( $a ){
			$this->a = $a;
		}


		function div( $x ){
		
			// test if internal x is valid and test if $x is valid
			if ( $x == 0 )       return YDResult::fatal( 'Argument cannot be zero', 0 );
			if ( $this->a == 5 ) return YDResult::fatal( 'Internal value cannot be five :D', 1);
			
			// compute result value
			$result = ( $this->a / 10 );
			
			// return a ok result
			return YDResult::ok( 'Div made with sucess!', $result );
		}

	}
	

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
