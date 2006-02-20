<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../include/YDWeblog_init.php' );

    // We don't limit the execution time
    set_time_limit( 0 );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDFileSystem.php' );
    YDInclude( 'YDUrl.php' );

    // Config setting
    YDConfig::set( 'updateDbUrl',   'http://ydframework.berlios.de/weblog_updater.php' );
    YDConfig::set( 'currentBranch', '/YDFramework2.0/trunk/' );
    YDConfig::set( 'chkoutUrl',     'http://svn.berlios.de/viewcvs/*checkout*/ydframework' );

    // Logger class
    class YDUpdateLog {

        // Flush a message to the browser
        function flushMsg( $msg, $fatal ) {
            echo( $msg . '<br/>' . YD_CRLF );
            @ob_flush();
            @flush();
            if ( $fatal ) {
                die();
            }
        }

        // Log an info message
        function info( $msg, $fatal=false ) {
            YDUpdateLog::flushMsg( '<font color="#009900">[ INFO    ] ' . $msg . '</font>', $fatal );
        }

        // Log an warning message
        function warning( $msg, $fatal=false ) {
            YDUpdateLog::flushMsg( '<font color="#FF9900">[ WARNING ] ' . $msg . '</font>', $fatal );
        }

        // Log an error message
        function error( $msg, $fatal=true ) {
            if ( $fatal ) {
                die( '</p><p class="title">' . $msg . '</p>' );
            } else {
                YDUpdateLog::flushMsg( '<font color="#CC0000">[ ERROR   ] ' . $msg . '</font>', $fatal );
            }
        }

    }

    // Update database class
    class YDUpdateDB {

        // Class constructor
        function YDUpdateDB() {
            $url = new YDUrl( YDConfig::get( 'updateDbUrl' ) . '?revision=' . YD_FW_REVISION );
            $this->changes = @ $url->getContents( false, false );
            if ( ! $this->changes ) {
                YDUpdateLog::error( 'Failed to connect to the update server.' );
            }
            if ( @ unserialize( base64_decode( $this->changes ) ) === false ) {
                YDUpdateLog::error( $this->changes );
            }
            $this->changes = unserialize( base64_decode( $this->changes ) );
        }

        // Function to check if there are updates
        function hasUpdates() {
            return ( sizeof( $this->changes ) == 0 ) ? false : true;
        }

        // Function to check if a file is updated
        function isUpdated( $file ) {
            $changes = $this->getChangedFiles();
            return array_key_exists( YDUpdateTools::fullPath( $file ), $changes );
        }

        // Get the last revision
        function getLastRevision() {
            $revisions = array_keys( $this->changes );
            return array_shift( $revisions );
        }

        // Get the list of changed files
        function getChangedFiles() {
            $changes_reversed = array_reverse( $this->changes );
            $changed_files = array();
            foreach ( $changes_reversed as $change ) {
                foreach ( $change['files'] as $file ) {
                    if ( $file[1] != YDConfig::get( 'currentBranch' ) . 'examples/weblog/install.php' ) {
                        $changed_files[ $file[1] ] = $file[0];
                    }
                }
            }
            ksort( $changed_files );
            return $changed_files;
        }

        // Download an update file
        function downloadFile( $file ) {
            YDUpdateLog::info( 'Downloading: ' . YDUpdateTools::shortPath( $file ) );
            $url = new YDUrl( YDConfig::get( 'chkoutUrl' ) . $file );
            $data = $url->getContents( false );
            if ( strpos( $data, 'Index of' ) === false ) {
                $f = new YDFSFile( YDUpdateTools::tempPath( $file ), true );
                $f->setContents( $data );
            }
        }

        // Save the new version number
        function saveVersionNumber() {
            YDUpdateLog::info( 'Recording the new version number...' );
            YDUpdateTools::replaceInFile(
                '%YD_FW_HOME%/YDF2_init.php',
                '@define( \'YD_FW_REVISION\', \'' . YD_FW_REVISION . '\' );',
                '@define( \'YD_FW_REVISION\', \'' . $this->getLastRevision() . '\' );'
            );
            YDUpdateTools::replaceInFile(
                '%YD_FW_HOME%/YDF2_init.php',
                '@define( \'YD_FW_REVISION\', \'unknown\' );',
                '@define( \'YD_FW_REVISION\', \'' . $this->getLastRevision() . '\' );'
            );
        }

    }

    // Update tools
    class YDUpdateTools {

        // Function to fix the path of the file
        function shortPath( $path ) {
            $path = str_replace( YDConfig::get( 'currentBranch' ), '', $path );
            $path = str_replace( 'YDFramework2', '%YD_FW_HOME%', $path );
            $path = str_replace( 'examples/weblog', '%YD_WEBLOG_HOME%', $path );
            return $path;
        }

        // Function to get the full path of a file
        function fullPath( $path ) {
            $path = str_replace( '%YD_FW_HOME%', 'YDFramework2', $path );
            $path = str_replace( '%YD_WEBLOG_HOME%', 'examples/weblog', $path );
            return YDConfig::get( 'currentBranch' ) . $path;
        }

        // Convert to a real path
        function realPath( $path ) {
            $path = YDUpdateTools::shortPath( $path );
            $path = str_replace( '%YD_FW_HOME%', YD_DIR_HOME, $path );
            $path = str_replace( '%YD_WEBLOG_HOME%', dirname( dirname( __FILE__ ) ), $path );
            if ( realpath( $path ) !== false ) {
                $path = realpath( $path );
            }
            return $path;
        }
        
        // Convert to a temporary path
        function tempPath( $path ) {
            return YDPath::join( YD_DIR_TEMP, 'YDF_U_' . md5( $path ) . '.upd' );
        }

        // Install a file
        function installFile( $file, $action ) {
            YDUpdateLog::info( 'Installing [' . $action . ']: ' . YDUpdateTools::shortPath( $file ) );
            if ( $action == 'D' ) {
                @unlink( YDUpdateTools::realPath( $file ) );
            } elseif ( $action == 'A' ) {
                @copy( YDUpdateTools::tempPath( $file ), YDUpdateTools::realPath( $file ) );
            } else {
                if ( file_exists( YDUpdateTools::realPath( $file ) ) ) {
                    @copy( YDUpdateTools::tempPath( $file ), YDUpdateTools::realPath( $file ) );
                } else {
                    YDUpdateLog::warning( 'Skipped: ' . YDUpdateTools::shortPath( $file ) . ' (not installed)' );
                }
            }
        }

        // Function to search and replace in a file
        function replaceInFile( $file, $search, $replace ) {
            YDUpdateLog::info( 'Updating file: ' . YDUpdateTools::shortPath( $file ) );
            $file = new YDFSFile( YDUpdateTools::realPath( $file ) );
            $data = $file->getContents();
            $data = str_replace( $search, $replace, $data );
            $file->setContents( $data );
        }

    }

    // Class with database tools
    class YDDatabaseTools {

        // Function to get a connection
        function getConnection() {
            $weblog = new YDWeblogAPI();
            return $weblog->db;
        }

        // Check if a database table exists
        function tableExists( $table ) {
            $db = YDDatabaseTools::getConnection();
            $tables = $db->getValuesByName( 'show tables', 'tables_in_' . $db->_db );
            return in_array( YDConfig::get( 'db_prefix', '' ) . 'locked', $tables );
        }


        // Check the locking of the database
        function checkLockDb() {
            $db = YDDatabaseTools::getConnection();
            if ( YDDatabaseTools::tableExists( $db, 'locked' ) ) {
                YDUpdateLog::error( 'Another update is already in progress!' );
            }
        }

        // Lock the database
        function lockDb() {
            YDUpdateLog::warning( 'Locking the weblog application...' );
            $db = YDDatabaseTools::getConnection();
            $db->executeSql( 'CREATE TABLE ' . YDConfig::get( 'db_prefix', '' ) . 'locked ( id int(3) )' );
        }

        // Lock the database
        function unlockDb() {
            YDUpdateLog::warning( 'Unlocking the weblog application...' );
            $db = YDDatabaseTools::getConnection();
            $db->executeSql( 'DROP TABLE IF EXISTS ' . YDConfig::get( 'db_prefix', '' ) . 'locked' );
        }

        // Install database update
        function installDbUpdate( $file ) {
            $update_name = YDPath::getFileNameWithoutExtension( $file );
            YDUpdateLog::info( 'Performing database update: ' . $update_name );
            YDInclude( YDUpdateTools::realPath( $file ) );
            $db = YDDatabaseTools::getConnection();
            $update_inst = new $update_name( $db );
            $update_inst->update();
        }

    }

    // Class definition
    class autoupdate extends YDWeblogAdminRequest {

        // Class constructor
        function autoupdate() {

            // Initialize the parent
            $this->YDWeblogAdminRequest();

            // The default action
            if ( $this->getActionName() != 'default' ) {

                // Show as plain text
                echo( '<html><head><title>YDWeblog Automatic Updater</title></head>' );
                echo( '<link rel="stylesheet" type="text/css" href="manage.css" />' );
                echo( '<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"><p>' );

                // Check if we can determine the installed version
                if ( ! is_numeric( YD_FW_REVISION ) ) {
                    YDUpdateLog::error( 'Failed to determine installed version number' );
                }

                // Instantiate the update database
                YDUpdateLog::info( 'Getting the list of updates...' );
                $this->updateDb = new YDUpdateDB();

                // Stop if the software is up to date
                if ( ! $this->updateDb->hasUpdates() ) {
                    YDUpdateLog::error( 'There are no new updates available', true );
                }

                // Get the latest revision
                YDUpdateLog::info( 'Installed version: ' . YD_FW_REVISION );
                YDUpdateLog::info( 'Latest released version: ' . $this->updateDb->getLastRevision() );

                // Get the list of changed files
                $this->changed_files = $this->updateDb->getChangedFiles();

                // Show how many files needs changing
                YDUpdateLog::info( 'Found ' . sizeof( $this->changed_files ) . ' changed file(s)' );

            }

        }

        // Default action
        function actionDefault() {

            // Display the template
            $this->display();

        }

        // Check for updates action
        function actionCheck() {

            // Show the changed files
            foreach ( $this->changed_files as $file=>$action ) {
                YDUpdateLog::info( '[' . $action . '] ' . YDUpdateTools::shortPath( $file ) );
            }

            // Show a message
            YDUpdateLog::warning( '<b>Your software needs to be updated</b>' );

            // Confirm the update
            echo( '</pre><p class="title"><a href="' . YD_SELF_SCRIPT . '?do=update">Click here to update the software</a></p>' );

        }

        // Perform the actual update
        function actionUpdate() {

            // Check if a software update is going on
            YDDatabaseTools::checkLockDb();

            // Download the needed files
            foreach ( $this->changed_files as $file=>$action ) {
                if ( $action != 'D' ) {
                    $this->updateDb->downloadFile( $file );
                }
            }

            // Lock the weblog application
            YDDatabaseTools::lockDb();

            // Install the new files
            foreach ( $this->changed_files as $file=>$action ) {
                YDUpdateTools::installFile( $file, $action );
            }

            // Update the init file
            if ( $this->updateDb->isUpdated( '%YD_WEBLOG_HOME%/include/YDWeblog_init.php' ) ) {
                YDUpdateTools::replaceInFile(
                    '%YD_WEBLOG_HOME%/include/YDWeblog_init.php',
                    '\'/../../../YDFramework2/YDF2_init.php\' );',
                    '\'/YDFramework2/YDF2_init.php\' );'
                );
            }

            // Update the database if needed
            foreach ( $this->changed_files as $file=>$action ) {
                if ( YDStringUtil::startsWith( YDUpdateTools::shortPath( $file ), '%YD_WEBLOG_HOME%/include/dbupdates/' ) ) {
                    YDDatabaseTools::installDbUpdate( $file );
                }
            }

            // Save the new version number
            $this->updateDb->saveVersionNumber();

            // Unlock the weblog application
            YDDatabaseTools::unlockDb();

            // Delete the downloaded files
            YDUpdateLog::info( 'Cleaning up the file downloads...' );
            foreach ( $this->changed_files as $file=>$action ) {
                @unlink( YDUpdateTools::tempPath( $file ) );
            }

            // Clear the weblog cache
            YDUpdateLog::info( 'Clearing the weblog cache...' );
            $cachePatterns = array(
                YD_WEBLOG_CACHE_PREFIX . '*.' . YD_WEBLOG_CACHE_SUFFIX, YD_TMP_PRE . 'N_*.*', '*.wch', '*.tpl.php', YD_TMP_PRE . 'U_*.upd'
            );
            $dir = new YDFSDirectory( YD_DIR_TEMP );
            foreach ( $cachePatterns as $pattern ) {
                $filesToDelete = $dir->getContents( $pattern, null );
                foreach ( $filesToDelete as $file ) {
                    @unlink( $file->getAbsolutePath() );
                }
            }

            // Show a message
            echo( '</p><p class="title">Software is now updated to version ' . $this->updateDb->getLastRevision() . '</p>' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
