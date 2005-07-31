<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDRequest.php' );

    // Class definition
    class form_modified extends YDRequest {

        // Class constructor
        function form_modified() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the form
            $form = new YDForm( 'form1' );

            $form->addElement( 'text', 'text1', 'Enter text 1:' );
            $form->addElement( 'text', 'text2', 'Enter text 2:' );
            $form->addElement( 'textarea', 'textarea', 'Enter text 3:' );
            $form->addElement( 'radio', 'radio1', 'Select a value 1:', array(), array( 1 => 'one', 2=>'two' ) );
            $form->addElement( 'radio', 'radio2', 'Select a value 2:', array(), array( 1 => 'one', 2=>'two' ) );
            $form->addElement( 'hidden', 'hidden1', '' );
            $form->addElement( 'hidden', 'hidden2', '', array(), 'i am also hidden' );
            $form->addElement( 'image', 'image', '', array(), 'http://www.scripting.com/images/xml.gif' );
            $form->addElement( 'password', 'password', 'Enter your password' );
            $form->addElement( 'bbtextarea', 'bbtextarea', 'Enter your BBCode' );
            $form->addElement( 'checkbox', 'checkbox1', 'Check me 1' );
            $form->addElement( 'checkbox', 'checkbox2', 'Check me 2' );
            $form->addElement( 'select', 'select', 'Select an option:', array(), array( 1 => 'one', 2=>'two' ) );
            $form->addElement( 'dateselect', 'dateselect', 'Select a date:' );
            $form->addElement( 'datetimeselect', 'datetimeselect', 'Select a date:' );
            $form->addElement( 'timeselect', 'timeselect', 'Select a date:' );
            $form->addElement( 'file', 'file', 'Select an file:' );
            $form->addElement( 'submit', 'submit', 'Send' );
            $form->addElement( 'button', 'button', 'Button' );
            $form->addElement( 'reset', 'resest', 'Reset' );
            
            $form->setDefaults( array( 'radio1' => 1, 'radio2' => 2, 'text1' => 'my text one', 'select' => 1, 'checkbox1' => 'on' ) );
            
            if ( $form->validate() ) {
                YDDebugUtil::dump( $form->getDefaults(), 'Form default values' );
                YDDebugUtil::dump( $form->getModifiedValues(), 'Form modified values' );
                YDDebugUtil::dump( $form->getValues(), 'Form values' );
                
            } else {
                $form->display();
            }


        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
