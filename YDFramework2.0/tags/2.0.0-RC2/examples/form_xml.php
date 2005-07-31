<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDXMLForm.php' );
    YDInclude( 'YDRequest.php' );

    // Class definition
    class form_xml extends YDRequest {

        // Class constructor
        function form_xml() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the form
            $form = new YDXMLForm( 'form1', 'GET' );

            // Add elements
            $form->addElement( 'text', 'txt', 'Enter text:' );
            $form->addElement( 'submit', 'cmdSubmit', 'submit' );

            // Convert the form to XML
            $xml = $form->render( 'xml' );
            YDDebugUtil::dump( $xml, 'Form as XML data' );

            // Recreate a new form from the XML data
            $form2 = new YDXMLForm( 'form1' );
            $form2->useXml( $xml );
            YDDebugUtil::dump( $form2 );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
