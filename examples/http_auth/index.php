<?php

    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    require_once( 'MyLoginRequest.php' );

    class indexRequest extends MyLoginRequest {

        function indexRequest() {
            $this->MyLoginRequest();
        }

        function actionDefault() {
            $this->outputTemplate();
        }

    }

    require_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_process.php' );

?>
