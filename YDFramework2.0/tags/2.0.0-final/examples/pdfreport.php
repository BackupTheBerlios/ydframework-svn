<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDPdfReport.php' );

    // Class definition
    class pdfreport extends YDRequest {


        // Class constructor
        function pdfreport() {

            // Initialize the parent
            $this->YDRequest();

        }

        // Default action
        function actionDefault() {

            // create a pdfReport object 
            $pdf = new YDPdfReport(); 

            // set Report title (optional) 
            $pdf->setTitle( YD_FW_NAMEVERS . ' PDF Report'); 

            // set pdf author, a pdf meta-tag (optional) 
            $pdf->setAuthor( 'ximian' ); 

            // create translations (optional) 
            $pdf->setLanguage( array(
                'page'          => 'Page ',
                'createdby'     => 'Created by the YDF addon (v'. $pdf->_version .') on '. YDStringUtil::formatDate(time(), "datetime"),
                'createdlink'   => '',
                'pageseparator' => ' of '
            ) );

            // use this html 
            $pdf->setHTML( "<h1>Example 1</h1>Hello World!" ); 

            // create pdf file 
            $pdf->output( 'inline' ); 

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>