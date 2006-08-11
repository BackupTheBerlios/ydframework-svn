<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDRequest.php' );

    // Class definition
    class form_filters extends YDRequest {

        // Class constructor
        function form_filters() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the form
            $form = new YDForm( 'form1' );
            
            // Add elements
            $form->addElement( 'text',           'username', 'Fill a username that starts with spaces' );
            $form->addElement( 'text',           'name',     'Fill a small caps name' );
            $form->addElement( 'datetimeselect', 'date1',    'Date1' );
            $form->addElement( 'date',           'date2',    'Date2' );
            $form->addElement( 'datetimeselect', 'date3',    'Date3' );
            $form->addElement( 'datetimeselect', 'date4',    'Date4' );
            $form->addElement( 'submit', '_cmdSubmit', 'Submit' );

            // Some defaults
			$form->setDefault( 'username', '    myuser' );
			$form->setDefault( 'name',     'igor' );
			
            // Add filters
            $form->addFilter( 'username', 'trim' );                       // must return eg 'myuser'
            $form->addFilter( 'name',     'upper' );                      // must return eg 'IGOR'

            if ( $form->validate() ) {
	
	            // IMPORTANT NOTE: 'dateformat' filters must be always added AFTER form validation !
	            // On form validation checking (by $form->validate()), 'dateformat' filter is applyed and
	            // element is not populated again because date result array is lost.
	            // This is why form is not displayed correctly after validation 
				// (if some rule is broken you would not get the date element with correct values)
            	$form->addFilter( 'date2', 'dateformat', 'day' );          // must return eg '20'
	        	$form->addFilter( 'date3', 'dateformat', 'datetimesql' );  // must return eg '2005-02-01 10:00:00'
	        	$form->addFilter( 'date4', 'dateformat', "%b" );           // must return eg 'Aug'

		        YDDebugUtil::dump( $form->getValues(), '$form->getValues()' );

			}else{
  	            // Display form
	            $form->display();
			}
		}

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>