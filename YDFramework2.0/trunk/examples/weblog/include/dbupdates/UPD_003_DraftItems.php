<?php

    // Class definition
    class UPD_003_DraftItems {

        // Class constructor
        function UPD_003_DraftItems( $db ) {

            // Publish the database
            $this->db = $db;

        }

        // Install the update
        function update() {

            // The SQL to add the is_draft field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'items ADD is_draft TINYINT(1)  DEFAULT "0" NOT NULL AFTER auto_close';
            $this->db->executeSql( $sql );

            // The SQL to add the is_draft field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'items ADD INDEX is_draft (is_draft)';
            $this->db->executeSql( $sql );

            // The SQL to add the is_draft field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'pages ADD is_draft TINYINT(1)  DEFAULT "0" NOT NULL AFTER body';
            $this->db->executeSql( $sql );

            // The SQL to add the is_draft field to the database
            $sql = 'ALTER TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'pages ADD INDEX is_draft (is_draft)';
            $this->db->executeSql( $sql );

        }

    }

?>