<?php

    // Class definition
    class UPD_004_CommentsUriAgent {

        // Class constructor
        function UPD_004_CommentsUriAgent( $db ) {

            // Publish the database
            $this->db = $db;

        }

        // Install the update
        function update() {

            // The SQL to add the useragent field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'comments ADD useragent varchar(255) AFTER userip';
            $this->db->executeSql( $sql );

            // The SQL to add the userrequrl field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'comments ADD userrequrl varchar(255) AFTER useragent';
            $this->db->executeSql( $sql );

        }

    }

?>