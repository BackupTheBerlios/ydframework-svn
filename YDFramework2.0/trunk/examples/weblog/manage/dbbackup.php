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
            $form = new YDWeblogForm( 'dbBackupForm' );
            $form->addElement( 'text', 'bck_name', t( 'bck_name' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'checkbox', 'bck_gzip', t( 'gz_compress' ), array( 'style' => 'border: none;' ) );
            $form->addElement( 'submit', '_cmdSubmit', t( 'backup' ), array( 'class' => 'button' ) );
            $form->setDefaults( array( 'bck_name' => '%Y-%m-%d_%DBNAME', 'bck_gzip' => 1 ) );

            // Add the rules
            $form->addRule( 'bck_name', 'required', t( 'err_bck_name' ) );

            // Validate the form
            if ( $form->validate() == true ) {

                // Create a backup object
                $bck = new YDMysqlDump( $this->weblog->db );

                // Get the backup data
                $bck_data = $bck->backup();

                // Fix the backup data
                $bck_data = str_replace( "\n\nINSERT ", "\nINSERT ", $bck_data );
                $bck_data = str_replace( "TYPE=MyISAM;\nINSERT ", "TYPE=MyISAM;\n\nINSERT ", $bck_data );

                // Compress with GZip
                if ( $form->getValue( 'bck_gzip' ) == 1 ) {
                    $bck_data = gzencode( $bck_data );
                }

                // The name of the backup
                $name = $form->getValue( 'bck_name' );
                $name = str_replace( '%DBNAME', $this->weblog->db->_db, $name );
                $name = strftime( $name, time() );

                // Add the extension to the name of the backup
                if ( $form->getValue( 'bck_gzip' ) == 1 ) {
                    $name .= '.sql.gz';
                } else {
                    $name .= '.sql';
                }

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
