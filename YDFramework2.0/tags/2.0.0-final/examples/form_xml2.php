<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    //YDInclude( 'YDXMLForm.php' );
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDForm.php' );

    // Class definition
    class form_xml2 extends YDRequest {

        // Class constructor
        function form_xml2() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the form
            $form = new YDForm( 'form1', 'GET', '', '_self', array( 'class' => 'myform' ) );

            // Add elements
            $form->addElement( 'text', 'txt1', 'Enter text 1:' );
            $form->addElement( 'text', 'txt2', 'Enter text 2:' );
            $form->addElement( 'submit', 'cmdSubmit', 'submit' );
            
            $form->addRule( 'txt1', 'required', 'txt1 is required' );
            $form->addRule( 'txt1', 'maxlength', 'txt1 must be smaller than 15', 15 );
            $form->addCompareRule( array( 'txt1', 'txt2' ), 'equal', 'txt1 and txt2 must be equal' );
            $form->addFormRule( 'formrule1' );
            $form->addFormRule( array( 'YDValidateRule', 'formrule2' ) );
            $form->addFilter( 'txt1', 'trim' );
            $form->addFilter( 'txt2', 'trim' );

            // Convert the form to XML
            $xml = $form->render( 'xml' );
            YDDebugUtil::dump( $xml, 'Form as XML data' );
            
            //YDDebugUtil::dump( $form );

            // Recreate a new form from the XML data
            $form2 = new YDForm( 'form1' );
            $form2->import( 'xml', $xml );
            
            //YDDebugUtil::dump( $form2 );
            
            YDDebugUtil::dump( array_diff_assoc( $form->toArray(), $form2->toArray() ), 'toArray difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_attributes, $form2->_attributes ), '_attributes difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_elements, $form2->_elements ), '_elements difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_rules, $form2->_rules ), '_rules difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_filters, $form2->_filters ), '_filters difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_comparerules, $form2->_comparerules ), '_comparerules difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_formrules, $form2->_formrules ), '_formrules difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_regElements, $form2->_regElements ), '_regElements difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_regRules, $form2->_regRules ), '_regRules difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_regFilters, $form2->_regFilters ), '_regFilters difference' );
            YDDebugUtil::dump( array_diff_assoc( $form->_regRenderers, $form2->_regRenderers ), '_regRenderers difference' );
            

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
