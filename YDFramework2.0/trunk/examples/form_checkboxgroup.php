<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDForm.php' );
    YDInclude( 'YDTemplate.php' );

    // Class definition
    class form_checkboxgroup extends YDRequest {

        // Class constructor
        function form_checkboxgroup() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Create the form
            $form = new YDForm( 'form1' );

            // Add the checkboxgroup
            $form->addElement( 'checkboxgroup', 'choose_multiple',  'Choose multiple default',                       array(), array( 0 => 'choice 1', 1 => 'choice 2' ) );
            $form->addElement( 'checkboxgroup', 'choose_multiple2', 'Choose multiple HORIZONTAL',       array('sep' => 'h' ), array( 0 => 'choice 1', 1 => 'choice 2' ) );
            $form->addElement( 'checkboxgroup', 'choose_multiple3', 'Choose multiple VERTICAL',         array('sep' => 'v' ), array( 0 => 'choice 1', 1 => 'choice 2' ) );
            $form->addElement( 'checkboxgroup', 'choose_multiple4', 'Choose multiple LEFT',             array('sep' => 'l' ), array( 0 => 'choice 1', 1 => 'choice 2' ) );
    $el = & $form->addElement( 'checkboxgroup', 'choose_multiple5', 'Choose multiple RIGHT',            array('sep' => 'r' ), array( 0 => 'choice 1', 1 => 'choice 2' ) );
            $form->addElement( 'checkboxgroup', 'choose_multiple6', 'Choose multiple VERTICAL LEFT',    array('sep' => 'vl'), array( 0 => 'choice 1', 1 => 'choice 2' ) );
            $form->addElement( 'checkboxgroup', 'choose_multiple7', 'Choose multiple VERTICAL RIGHT',   array('sep' => 'vr'), array( 0 => 'choice 1', 1 => 'choice 2' ) );
            $form->addElement( 'checkboxgroup', 'choose_multiple8', 'Choose multiple HORIZONTAL LEFT',  array('sep' => 'hl'), array( 0 => 'choice 1', 1 => 'choice 2' ) );
            $form->addElement( 'checkboxgroup', 'choose_multiple9', 'Choose multiple HORIZONTAL RIGHT', array('sep' => 'hr'), array( 0 => 'choice 1', 1 => 'choice 2' ) );

            $form->addElement( 'checkboxgroup', 'choose_multiple10', 'Custom Separator "|"',                                                  array('separator' => '&nbsp;|&nbsp;'), array( 0 => 'choice 1', 1 => 'choice 2' ) );
            $form->addElement( 'checkboxgroup', 'choose_multiple11', 'Custom Separator "XXX" and LEFT',           array('sep' => 'l', 'separator' => '&nbsp;&nbsp;XXX&nbsp;&nbsp;'), array( 0 => 'choice 1', 1 => 'choice 2' ) );
            $form->addElement( 'checkboxgroup', 'choose_multiple12', 'Custom Separator "...", LEFT and VERTICAL',                   array('sep' => 'l', 'separator' => '...<br />'), array( 0 => 'choice 1', 1 => 'choice 2' ) );

            $form->addElement( 'checkboxgroup', 'choose_multiple_big',  'Choose with column format', array(), array( 0 => 'choice 1', 'choice 2', 'choice 3', 'choice 4', 'choice 5', 'choice 6', 'choice 7', 'choice 8' ) );
   $el2 = & $form->addElement( 'checkboxgroup', 'choose_multiple_big2', 'Choose with column format (2 columns)', array(), array( 0 => 'choice 1', 'choice 2', 'choice 3', 'choice 4', 'choice 5', 'choice 6', 'choice 7', 'choice 8' ) );
			$el2->setColumns( 2 );

   $el3 = & $form->addElement( 'checkboxgroup', 'choose_multiple_big3', 'Choose with column format (3 columns and a default "select all")', array(), array( 0 => 'choice 1', 'choice 2', 'choice 3', 'choice 4', 'choice 5', 'choice 6', 'choice 7', 'choice 8', 'choice 9' ) );
			$el3->setColumns( 3 );
			$el3->addSelectAll();

   $el4 = & $form->addElement( 'checkboxgroup', 'choose_multiple_big4', 'Choose with column format (3 columns and a "select all" on top)', array(), array( 0 => 'choice 1', 'choice 2', 'choice 3', 'choice 4', 'choice 5', 'choice 6', 'choice 7', 'choice 8' ) );
			$el4->setColumns( 3 );
			$el4->addSelectAll( false );


			// demonstrate disable on checkboxgroup
            $form->disable( 'choose_multiple',  1 );
            $form->disable( 'choose_multiple2', array( 0, 1 ) );
            $form->disable( 'choose_multiple3' );

			// Add and example about 'select all' button
			$el->addSelectAll();

            // Add the submit button
            $form->addElement( 'submit', 'btnSubmit', 'submit' );

            // Set the defaults
            $form->setDefaults( array( 'choose_multiple' => array( 0=>0, 1=>1 ) ) );

            // Process the form
            if ( $form->validate() === true ) {
                YDDebugUtil::dump( $form->getValues() );
            }

            // Add the template
            $tpl = new YDTemplate();

            // Add the form to the template
            $tpl->assign ( 'form', $form->toHTML() );

            // Display the template
            $tpl->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>