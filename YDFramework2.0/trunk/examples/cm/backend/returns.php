<?php

    include_once( dirname( __FILE__ ) . '/../cm.php' );

    // Class definition
    class returns extends cm {

        // Class constructor
        function returns() {

			// init parent class and test class
            $this->cm();
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

			// get error
			if ( $res->custom == 0 ){

				// do something about 'x' argument being 0
				print $res->message;
			}

			if ( $res->custom == 1 ){

				// do something about internal 'a' variable being 5
				print $res->message;
			}

			// in any case of error, show all information
			$res->dump();
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
