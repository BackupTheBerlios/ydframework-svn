<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDXml.php' );

    // Class definition
    class xml_example extends YDRequest {

        // Class constructor
        function xml_example() {
            
            $this->YDRequest();
            
            $this->file = 'xml_example.xml';
            $this->url = 'http://ydframework.berlios.de/ydf2_changelog_summary.xml';
            
            $this->contents = '
<people>
    <person age="29">
        <name splitted="true">
            <first>David</first>
            <middle />
            <last>Bittencourt</last>
        </name>
        <job>Webmaster</job>
    </person>

    <person age="31">
        <name splitted="false">Pieter Claerhout</name>
        <job>Chef</job>
    </person>

</people>';
            
        }

        // Default action
        function actionDefault() {
            
            echo 'Getting a XML from a STRING<br /><br />';
            
            $xml = new YDXml();
            $xml->loadString( $this->contents );
            
            YDDebugUtil::dump( $xml->toString( false ), 'YDXml::toString ugly' );
            
            YDDebugUtil::dump( $xml->toString(), 'YDXml::toString pretty' );
            
            YDDebugUtil::dump( $xml->traverse(), 'YDXml::traverse' );
            
            YDDebugUtil::dump( $xml->toArray(), 'YDXml::toArray' );
            
        }
        
        function actionFile() {
            
            echo 'Getting a XML from an FILE - ' . $this->file . '<br /><br />';
            
            $xml = new YDXml();
            $xml->loadFile( $this->file );
            
            YDDebugUtil::dump( $xml->traverse(), 'YDXml::traverse' );
            YDDebugUtil::dump( $xml->toArray(), 'YDXml::toArray' );
            
        }
        
        function actionUrl() {
            
            echo 'Getting a XML from an URL - ' . $this->url . '<br /><br />';
            
            $xml = new YDXml();
            $xml->loadUrl( $this->url );
            
            YDDebugUtil::dump( $xml->traverse(), 'YDXml::traverse' );
            YDDebugUtil::dump( $xml->toArray(), 'YDXml::toArray' );
            
        }
        
        function actionArray() {
            
            echo 'Getting a XML from an ARRAY<br /><br />';
            
            $xml = new YDXml();
            $xml->loadString( $this->contents );
            $array = $xml->toArray();
            
            YDDebugUtil::dump( YDXml::traverse( $array ), 'YDXml::traverse of the ARRAY - static call' );
            
            $xml = new YDXml(); // not really needed - loadArray always resets the object before loading an array
            $xml->loadArray( $array );
            
            YDDebugUtil::dump( $xml->traverse(), 'YDXml::traverse after loading the ARRAY' );
            
        }
        
        function actionSave() {
            
            echo 'Saving the XML STRING in the TEMP folder<br /><br />';
            
            $xml = new YDXml();
            $xml->loadString( $this->contents );
            $xml->save( YD_DIR_TEMP . '/xml_save_' . date( 'H_i_s' ) . '.xml' );
            
            echo 'Done. <a href="../YDFramework2/temp/xml_save_' . date( 'H_i_s' ) . '.xml">' . YD_DIR_TEMP . '/xml_save_' . date( 'H_i_s' ) . '.xml</a>';
            
        }

    }
    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
