<?php

    // Class definition
    class UPD_001_AllowComments {

        // Class constructor
        function UPD_001_AllowComments( $db ) {

            // Publish the database
            $this->db = $db;

        }

        // Install the update
        function update() {

            // The SQL to add the `allow_comments` field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'items ADD allow_comments TINYINT(1)  DEFAULT "1" NOT NULL AFTER `num_comments`';
            $this->db->executeSql( $sql );

            // The SQL to add the `allow_comments` field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'items ADD INDEX allow_comments (allow_comments)';
            $this->db->executeSql( $sql );

        }

    }

?>