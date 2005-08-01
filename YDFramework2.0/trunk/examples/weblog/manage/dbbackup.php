<?php

    // Include the Yellow Duck Framework
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // Includes
    YDInclude( 'YDMysqlDump.php' );

    // Class definition
    class dbbackup extends YDWeblogAdminRequest {

        // Class constructor
        function dbbackup() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

        }

        // Default action
        function actionDefault() {

            // Create the backup form
            $form = new YDForm( 'dbBackupForm' );
            $form->addElement( 'text', 'bck_name', t( 'bck_name' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'checkbox', 'bck_gzip', t( 'gz_compress' ), array( 'style' => 'border: none;' ) );
            $form->addElement( 'submit', '_cmdSubmit', t( 'backup' ), array( 'class' => 'button' ) );
            $form->setDefaults( array( 'bck_name' => $this->weblog->db->_db . '.sql.gz', 'bck_gzip' => 1 ) );

            // Add the rules
            $form->addRule( 'bck_name', 'required', t( 'err_bck_name' ) );

            // Validate the form
            if ( $form->validate() == true ) {

                // Create a backup object
                $bck = new YDMysqlDump( $this->weblog->db );

                // Get the backup data
                $bck_data = $bck->backup();

                // Compress with GZip
                $bck_data = gzencode( $bck_data );

                // The name of the backup
                $name = $form->getValue( 'bck_name' );

                // Dump the data
                header( 'Content-Type: application/force-download; name="' . $name . '"');
                header( 'Content-Disposition: attachment; filename="' . $name . ' "');
                header( 'Cache-Control: public' );
                header( 'Content-Transfer-Encoding: binary' );
                header( 'Content-length: ' . strlen( $bck_data ) );
                echo( $bck_data );
                die();

            }

            // Add the form to the template
            $this->tpl->assignForm( 'form', $form );

            // Display the template
            $this->display();

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
