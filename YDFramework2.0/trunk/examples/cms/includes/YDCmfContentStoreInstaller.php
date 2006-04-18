<?php

    // Include the YDDatabaseMetaData class
    include_once( YD_DIR_HOME_CLS . '/YDDatabaseMetaData.php' );

    // Get the metadata object
    $meta = new YDDatabaseMetaData( $this->db );

    // Get the list of tables in the database
    $tables = $meta->getTables();

    // Install the categories table
    if ( ! in_array( 'categories', $tables ) ) {

        // The SQL to create the categories table
        $sql  = 'CREATE TABLE categories ( ' . YD_CRLF;
        $sql .= '  id int(11) NOT NULL auto_increment, ' . YD_CRLF;
        foreach ( array_values( $this->languages ) as $language ) {
            $sql .= sprintf( '  category_%s longtext NOT NULL, ', $language ) . YD_CRLF;
        }
        $sql .= '  PRIMARY KEY (id)' . YD_CRLF;
        $sql .= ');';

        // Create the table
        $this->db->executeSql( $sql );

    }

    // If we don't have the table, create it
    if ( ! in_array( $this->table, $tables ) ) {

        // SQL to create the table
        $sql  = sprintf( 'CREATE TABLE %s ( ', $this->table ) . YD_CRLF;
        $sql .= sprintf( '  %s int(11) NOT NULL auto_increment, ', $this->field_id ) . YD_CRLF;
        $sql .= sprintf( '  %s int(11) NOT NULL default \'0\', ', $this->field_parent_id ) . YD_CRLF;
        $sql .= sprintf( '  %s varchar(255) NOT NULL default \'\', ', $this->field_sort ) . YD_CRLF;
        $sql .= sprintf( '  nleft int(11) NOT NULL default \'0\', ' ) . YD_CRLF;
        $sql .= sprintf( '  nright int(11) NOT NULL default \'0\', ' ) . YD_CRLF;
        $sql .= sprintf( '  nlevel int(11) NOT NULL default \'0\', ' ) . YD_CRLF;
        $sql .= sprintf( '  created int(11) NOT NULL default \'0\', ' ) . YD_CRLF;
        $sql .= sprintf( '  created_by varchar( 255 ) NOT NULL default \'\', ' ) . YD_CRLF;
        $sql .= sprintf( '  modified int(11) NOT NULL default \'0\', ' ) . YD_CRLF;
        $sql .= sprintf( '  modified_by varchar( 255 ) NOT NULL default \'\', ' ) . YD_CRLF;
        $sql .= sprintf( '  content_type varchar( 255 ) NOT NULL default \'%s\', ', $this->default_obj_type ) . YD_CRLF;
        $sql .= sprintf( '  can_delete tinyint( 1 ) NOT NULL default \'1\', ' ) . YD_CRLF;

        // Add the title fields
        foreach ( array_values( $this->languages ) as $language ) {
            $sql .= sprintf( '  title_%s longtext NOT NULL, ', $language ) . YD_CRLF;
        }

        // Add the property fields
        foreach ( $this->fields as $field ) {
            if ( YDStringUtil::startsWith( $field, 'property_' ) ) {
                $sql .= sprintf( '  %s longtext NULL, ', $field ) . YD_CRLF;
            }
        }

        // Add the indexes
        $sql .= sprintf( '  PRIMARY KEY (%s), ', $this->field_id ) . YD_CRLF;
        $sql .= sprintf( '  KEY %s_%s (%s), ', $this->table, $this->field_parent_id, $this->field_parent_id ) . YD_CRLF;
        $sql .= sprintf( '  KEY %s_nleft (nleft), ', $this->table ) . YD_CRLF;
        $sql .= sprintf( '  KEY %s_nright (nright), ', $this->table ) . YD_CRLF;
        $sql .= sprintf( '  KEY %s_nlevel (nlevel), ', $this->table ) . YD_CRLF;
        $sql .= sprintf( '  KEY %s_content_type (content_type), ', $this->table ) . YD_CRLF;
        $sql .= sprintf( '  KEY %s_can_delete (can_delete), ', $this->table ) . YD_CRLF;
        $sql .= sprintf( '  UNIQUE KEY %s_%s (%s) ', $this->table, $this->field_sort, $this->field_sort ) . YD_CRLF;
        $sql .= ');';

        // Create the table
        $this->db->executeSql( $sql );

    }

    // Empty the table
    $this->db->executeSql( 'DELETE FROM ' . $this->table );

    // Create a new node
    $node = new YDCmfContentNode();
    $node->setValue( 'id',              1 );
    $node->setValue( 'parent_id',       0 );
    $node->setValue( 'name',            'home' );
    $node->setValue( 'title',           'Titel Nederlands', 'nl' );
    $node->setValue( 'title',           'Title English', 'en' );
    $node->setValue( 'title',           'Titre Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property',        'Property Nederlands', 'nl' );
    $node->setValue( 'property',        'Property English', 'en' );
    $node->setValue( 'property',        'Property Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property_other',  'Property1' );

    // Add it to the store
    $this->addNode( $node );

    // Create a new node
    include( YDCMS_DIR_MOD . '/ydcmfhome/ydcmfhome.php' );
    $node = new YDCmfHome();
    $node->setValue( 'id',              2 );
    $node->setValue( 'parent_id',       1 );
    $node->setValue( 'name',            'home2' );
    $node->setValue( 'title',           'Titel2 Nederlands', 'nl' );
    $node->setValue( 'title',           'Title2 English', 'en' );
    $node->setValue( 'title',           'Titre2 Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property',        'Property2 Nederlands', 'nl' );
    $node->setValue( 'property',        'Property2 English', 'en' );
    $node->setValue( 'property',        'Property2 Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property_other',  'Property21' );

    // Add it to the store
    $this->addNode( $node );

    // Create a new node
    $node = new YDCmfHome();
    $node->setValue( 'id',              3 );
    $node->setValue( 'parent_id',       1 );
    $node->setValue( 'name',            'news' );
    $node->setValue( 'title',           'News Nederlands', 'nl' );
    $node->setValue( 'title',           'News English', 'en' );
    $node->setValue( 'title',           'News Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property',        'Property Nederlands', 'nl' );
    $node->setValue( 'property',        'Property English', 'en' );
    $node->setValue( 'property',        'Property Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property_other',  'Property1' );

    // Add it to the store
    $this->addNode( $node );

    // Create a new node
    $node = new YDCmfHome();
    $node->setValue( 'id',              4 );
    $node->setValue( 'parent_id',       3 );
    $node->setValue( 'name',            'news/20060417_krabrally_2006' );
    $node->setValue( 'title',           'News Krabrally 2006 Nederlands', 'nl' );
    $node->setValue( 'title',           'News Krabrally 2006 English', 'en' );
    $node->setValue( 'title',           'News Krabrally 2006 Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property',        'Property Nederlands', 'nl' );
    $node->setValue( 'property',        'Property English', 'en' );
    $node->setValue( 'property',        'Property Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property_other',  'Property1' );

    // Add it to the store
    $this->addNode( $node );

    // Create a new node
    $node = new YDCmfHome();
    $node->setValue( 'id',              5 );
    $node->setValue( 'parent_id',       3 );
    $node->setValue( 'name',            'news/20060418_krabrally_2007' );
    $node->setValue( 'title',           'News Krabrally 2007 Nederlands', 'nl' );
    $node->setValue( 'title',           'News Krabrally 2007 English', 'en' );
    $node->setValue( 'title',           'News Krabrally 2007 Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property',        'Property Nederlands', 'nl' );
    $node->setValue( 'property',        'Property English', 'en' );
    $node->setValue( 'property',        'Property Fran&ccedil;ais', 'fr' );
    $node->setValue( 'property_other',  'Property1' );

    // Add it to the store
    $this->addNode( $node );

?>
