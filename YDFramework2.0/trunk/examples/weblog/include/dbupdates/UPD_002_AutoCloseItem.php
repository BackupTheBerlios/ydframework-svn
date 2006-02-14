<?php

    // Class definition
    class UPD_002_AutoCloseItem {

        // Class constructor
        function UPD_002_AutoCloseItem( $db ) {

            // Publish the database
            $this->db = $db;

        }

        // Install the update
        function update() {

            // The SQL to add the `allow_comments` field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'items ADD auto_close TINYINT(1)  DEFAULT "1" NOT NULL AFTER allow_comments';
            $this->db->executeSql( $sql );

            // The SQL to add the `allow_comments` field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'items ADD INDEX auto_close (auto_close)';
            $this->db->executeSql( $sql );

        }

    }

?>