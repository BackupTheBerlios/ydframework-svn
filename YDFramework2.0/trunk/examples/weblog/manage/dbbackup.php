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

            // The backup types we have
            $bck_types = array(
                0 => t( 'bck_full' ) . '<br/>',
                1 => t( 'bck_structure_only' ) . '<br/>',
                2 => t( 'bck_data_only' ) . '<br/>'
            );

            // Create the backup form
            $form = new YDWeblogForm( 'dbBackupForm' );
            $form->addElement( 'text', 'bck_name', t( 'bck_name' ), array( 'class' => 'tfM' ) );
            $form->addElement( 'checkbox', 'bck_gzip', t( 'gz_compress' ), array( 'style' => 'border: none;' ) );
            $form->addElement( 'radio', 'bck_type', t( 'bck_type' ), array( 'style' => 'border: none;' ), $bck_types );
            $form->addElement( 'submit', '_cmdSubmit', t( 'backup' ), array( 'class' => 'button' ) );
            $form->setDefaults( array( 'bck_name' => '%Y-%m-%d_%DBNAME', 'bck_gzip' => 1, 'bck_type' => 0 ) );

            // Add the rules
            $form->addRule( 'bck_name', 'required', t( 'err_bck_name' ) );

            // Add the filters
            $form->addFilters( array( 'bck_name' ), 'strip_html' );

            // Validate the form
            if ( $form->validate() == true ) {

                // Create a backup object
                $bck = new YDMysqlDump( $this->weblog->db );

                // Configure the backup
                $bck->displayComments( true );
                if ( $form->getValue( 'bck_type' ) == '1' ) {
                    $bck->displayDrops( true );
                    $bck->displayStructure( true );
                    $bck->displayData( false );
                } elseif ( $form->getValue( 'bck_type' ) == '2'  ) {
                    $bck->displayDrops( false );
                    $bck->displayStructure( false );
                    $bck->displayData( true );
                } else {
                    $bck->displayDrops( true );
                    $bck->displayStructure( true );
                    $bck->displayData( true );
                }

                // Get the backup data
                $bck_data = $bck->backup();

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
