<?php

    // standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // YDF includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDRequest.php' );

    class form_captcha extends YDRequest {

        // constructor
        function form_captcha() {
            $this->YDRequest();
        }

        // default action
        function actionDefault() {

            // create form
            $form = new YDForm( 'myform' );

			// add elements
            $form->addElement( 'text',    'txt', 'Enter your name:' );
            $form->addElement( 'captcha', 'cap', 'Enter code:' );
            $form->addElement( 'submit',  'cmd', 'Send' );

			// add rules
            $form->addRule( 'txt', 'required', 'Your name is required' );
            $form->addRule( 'cap', 'captcha',  'Security code is not valid' );

            if ( $form->validate() ) {
                YDDebugUtil::dump( $form->getValues(), 'Form values' );
            } else {
                $form->display();
            }

        }

		// Reserved action that creates the image
        function actionShowCaptcha() {

			// include captcha lib
			YDInclude( 'YDCaptcha.php' );

			// create captcha object
			$captcha = new YDCaptcha();
//			$captcha->useColour( true );
//			$captcha->setNumChars( 3 );

			// return image
			return $captcha->Create();
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
