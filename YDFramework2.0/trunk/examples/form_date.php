<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDRequest.php' );

    // Class definition
    class form_date extends YDRequest {

        // Class constructor
        function form_date() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the form
            $form = new YDForm( 'form1' );
            
            // Add a first set of elements
            $form->addElement( 'date', 'date1', 'Date1', '' );
            $form->addElement( 'date', 'date2', 'Date2 (monthabbr = true)', '', array( 'monthabbr' => true ) );
            $form->addElement( 'date', 'date3', 'Date3 (monthnumber = true)', '', array( 'monthnumber' => true ) );
            $form->addElement( 'date', 'date4', 'Date4 (monthucfirst = true)', '', array( 'monthucfirst' => true ) );
            $form->addElement( 'date', 'date5', 'Date5 (yearstart = 1970, yearend=2007, yeartwodigits = true)', '', array( 'yearstart' => 1970, 'yearend' => 2007, 'yeartwodigits' => true ) );
            $form->addElement( 'date', 'time1', 'Time1', '', array( 'time' ) );
            $form->addElement( 'date', 'time2', 'Time2 (minutesoffset = 10)', '', array( 'time', 'minutesoffset' => 10 ) );
            $form->addElement( 'date', 'time3', 'Time3 (secondsoffset = 15)', '', array( 'time', 'secondsoffset' => 15 ) );
            
            $form->addElement( 'date', 'datetime1', 'DateTime1', '', array( 'datetime' ) );
            
            $form->addElement( 'date', 'date6', 'Date6 (month, year)', '', array( 'month', 'year' ) );
            $form->addElement( 'date', 'date7', 'Date7 (day, month)', '', array( 'day', 'month' ) );
            $form->addElement( 'date', 'date8', 'Date8 (day, hours, minutes)', '', array( 'day', 'hours', 'minutes' ) );
            
            $form->addElement( 'submit', '_cmdSubmit', 'Submit' );
            
            // Add rules
            $form->addRule( array( 'date1', 'date2', 'date3', 'date4', 'date5', 'time1', 'time2', 'time3', 'datetime1', 'date6', 'date7', 'date8' ), 'date', 'must be a valid date' );
            
            if ( $form->validate() ) {
                YDDebugUtil::dump( $form->getValues(), '$form->getValues()' );
            }
            
            // Display form
            $form->display();
            
            
            
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>