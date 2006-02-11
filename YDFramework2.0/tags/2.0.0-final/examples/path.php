<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDFileSystem.php' );

    // Class definition
    class path extends YDRequest {

        // Class constructor
        function path() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {
            YDDebugUtil::dump( YDPath::getDirectorySeparator(), 'getDirectorySeparator()' );
            YDDebugUtil::dump( YDPath::getPathSeparator(), 'getPathSeparator()' );
            YDDebugUtil::dump( YDPath::getVolumeSeparator(), 'getVolumeSeparator()' );
            YDDebugUtil::dump( YDPath::changeExtension( __FILE__, '.tpl' ), 'changeExtension( __FILE__, ".tpl" )' );
            YDDebugUtil::dump( YDPath::getDirectoryName( __FILE__ ), 'getDirectoryName( __FILE__ )' );
            YDDebugUtil::dump( YDPath::getExtension( __FILE__ ), 'getExtension( __FILE__ )' );
            YDDebugUtil::dump( YDPath::getFileName( __FILE__ ), 'getFileName( __FILE__ )' );
            YDDebugUtil::dump( YDPath::getFileNameWithoutExtension( __FILE__ ), 'getFileNameWithoutExtension( __FILE__ )' );
            YDDebugUtil::dump( YDPath::getFilePath( __FILE__ ), 'getFilePath( __FILE__ )' );
            YDDebugUtil::dump( YDPath::getFilePathWithoutExtension( __FILE__ ), 'getFilePathWithoutExtension( __FILE__ )' );
            YDDebugUtil::dump( YDPath::getFullPath( __FILE__ ), 'getFullPath( __FILE__ )' );
            YDDebugUtil::dump( YDPath::getTempFileName(), 'getTempFileName()' );
            YDDebugUtil::dump( YDPath::getTempPath(), 'getTempPath()' );
            YDDebugUtil::dump( YDPath::hasExtension( __FILE__ ), 'hasExtension( __FILE__ )' );
            YDDebugUtil::dump(
                YDPath::hasExtension( YDPath::getFileNameWithoutExtension( __FILE__ ) ),
                'hasExtension( YDPath::getFileNameWithoutExtension( __FILE__ ) )'
            );
            YDDebugUtil::dump( YDPath::isAbsolute( __FILE__ ), 'isAbsolute( __FILE__ )' );
            YDDebugUtil::dump( YDPath::isAbsolute( YDPath::getFileName( __FILE__ ) ), 'isAbsolute( getFileName( __FILE__ ) )' );
            YDDebugUtil::dump( YDPath::join( __FILE__ ), 'join( __FILE__ )' );
            YDDebugUtil::dump(
                YDPath::join( YDPath::getTempPath(), YDPath::getFileName( __FILE__ ) ), 
                'join( getTempPath(), getFileName( __FILE__ ) )'
            );
            YDDebugUtil::dump(
                YDPath::join( YDPath::getTempPath(), __FILE__ ), 
                'join( getTempPath(), __FILE__ )'
            );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>