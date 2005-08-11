<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDRequest.php' );

    // Class definition
    class form_dateselect extends YDRequest {

        // Class constructor
        function form_dateselect() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the form
            $form = new YDForm( 'form1' );

            // Add a first set of elements
            $elementDate = $form->addElement( 'dateselect', 'dateSelect1', 'Enter data:' );
            $elementTime = $form->addElement( 'timeselect', 'timeSelect1', 'Enter data:' );
            $elementDateTime = $form->addElement( 'datetimeselect', 'datetimeSelect1', 'Enter data:' );
            
            // Add a second set of elements
            $form->addElement(
                'dateselect', 'dateSelect2', 'Enter data:',
                array(), array( 'yearstart' => 1970, 'yearend' => '2050' )
            );
            $form->addElement( 'timeselect', 'timeSelect2', 'Enter data:' );
            $form->addElement(
                'datetimeselect', 'datetimeSelect2', 'Enter data:',
                array(), array( 'yearstart' => 1970, 'yearend' => '2050' )
            );
            
            $form->addElement( 'datetimeselect', 'datetimeSelect3', 'Enter data with seconds:', array(), array( 'seconds' => true ) );

            // Add the send button
            $form->addElement( 'submit', 'cmd1', 'Send' );

            // Set the defaults
            $form->setDefaults(
                array(
                    'dateSelect1' => array( 'month'=>4, 'day'=>4, 'year'=>2002 ),
                    'dateSelect2' => strval( time() ),
                    'timeSelect2' => strval( time() ),
                    'datetimeSelect2' => time() + 3600 * 24,
                )
            );
            
            // Display the form
            $form->display();

            // Show the contents of the form
            if ( YDConfig::get( 'YD_DEBUG' ) == 1 ) {
                YDDebugUtil::dump( $form->_regElements, 'Registered elements' );
                YDDebugUtil::dump( $form->_regRules, 'Registered rules' );
                YDDebugUtil::dump( $form->_regFilters, 'Registered filters' );
                YDDebugUtil::dump( $form->_filters, 'Filters' );
                YDDebugUtil::dump( $form->_rules, 'Rules' );
                YDDebugUtil::dump( $form->_formrules, 'Form Rules' );
                YDDebugUtil::dump( $form->getValue( 'dateSelect1' ), 'dateSelect1' );
                YDDebugUtil::dump( $form->getValue( 'timeSelect1' ), 'timeSelect1' );
                YDDebugUtil::dump( $form->getValue( 'datetimeSelect1' ), 'datetimeSelect1' );
                YDDebugUtil::dump( $form->getValues(), '$form->getValues()' );
                YDDebugUtil::dump( $_POST, '$_POST' );
                YDDebugUtil::dump( $_FILES, '$_FILES' );
            }
            if ( $form->validate() ) {
                YDDebugUtil::dump( $form->getValues(), '$form->getValues()' );
                YDDebugUtil::dump( $elementDate->getTimeStamp(), '$elementDate->getTimeStamp()' );
                YDDebugUtil::dump( $elementDate->getTimeStamp( '%d/%m/%Y' ), '$elementDate->getTimeStamp( "%d/%m/%Y" )' );
                YDDebugUtil::dump( date( 'M-d-Y', $elementDate->getTimeStamp() ), 'date( "M-d-Y",  $elementDate->getTimeStamp() )' );
                YDDebugUtil::dump( $elementTime->getTimeStamp(), '$elementTime->getTimeStamp()' );
                YDDebugUtil::dump( $elementTime->getTimeStamp( '%H:%M' ), '$elementTime->getTimeStamp( "%H:%M" )' );
                YDDebugUtil::dump( $elementDateTime->getTimeStamp(), '$elementDateTime->getTimeStamp()' );
                YDDebugUtil::dump( $elementDateTime->getTimeStamp( '%d/%m/%Y %H:%M' ), '$elementDateTime->getTimeStamp( "%d/%m/%Y %H:%M" )' );
                YDDebugUtil::dump( YDStringUtil::formatDate( $elementDateTime, 'datetime', 'pt' ), 'YDStringUtil::formatDate' );
            }

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>