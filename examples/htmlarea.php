<?php

    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    require_once( 'YDRequest.php' );
    require_once( 'YDForm.php' );

    class htmlareaRequest extends YDRequest {

        function htmlareaRequest() {
            $this->YDRequest();
        }

        function actionDefault() {

            $options = array( 'url' => '/htmlarea/', 'lang' => 'en' );
            $config = array( 'width' => '600px', 'height' => '300px' );

            $form = new YDForm( 'wysiwygForm' );
            $form->setDefaults( array( 'txtBody' => 'hello world' ) );
            $form->addElement( 'htmlarea', 'txtBody', 'text', $options );
            $form->addElement( 'submit', 'cmdSubmit', 'Send' );

            $htmlarea = &$form->getElement('txtBody');
            $htmlarea->setConfig( $config );

            if ( $form->validate() ) {
                echo( '<hr>' . $form->exportValue( 'txtBody' ) . '<hr>' );
            }

            $form->display();

        }

    }

    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_process.php' );

?>
