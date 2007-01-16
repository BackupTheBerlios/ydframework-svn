<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDInstaller.php' );

    class index extends YDInstaller {
    
        function index() {
        
            $title = 'Example of YDInstaller';
            $description = 'This installer will update a directory with new files.';
                
            $this->YDInstaller( $title, $description );
            
        }
        
        function actionDefault() {
            
            $this->resetFields();
            $this->resetLog();
            
            $this->addText( 'Before we begin, please make sure you have the following executed:' );
            
            $this->addPre(
'chmod 777 /var/www/html/test/
chmod 777 /var/www/html/example/
chmod 777 /var/www/html/super/
' );
            
            $this->addText( 'Please click the "Continue" button bellow to start the installation.' );
            
            $this->addNavButtons( 'Continue', 'Cancel', '../', false );
            
            if ( $this->isValid() ) {
                $this->goTo( 'A' );
            } 
            
            $this->display();
        }
        
        function actionA() {
            
            $this->addHeader( 'License' );
            $this->addText( 'To continue, read the following license.' );
            
            $this->addField( 'textarea', 'license', '', array( 'readonly' => 'readonly' ) );
            $this->addField( 'checkbox', 'agree', 'I agree with above license' );
            
            $this->setDefault( 'license', file_get_contents( YD_DIR_HOME . '/../license.txt' ) );
            
            $this->addRule( 'agree', 'exact', 'You must agree with the license', 1 );
            
            $this->addNavButtons( 'Continue', 'Back', 'default' );
            
            $this->saveField( 'agree' );
            
            if ( $this->isValid() ) {
                $this->goTo( 'B' );
            }
            
            $this->display();
            
        }
        
        function actionB() {
            
            $this->addHeader( 'Directory' );
            $this->addText( 'Define the target directory where the new files will be copied.
            This path is relative to the current directory.' );
            
            $this->addField( 'text', 'target_dir', 'Relative path' );
            
            $this->setDefault( 'target_dir', '/old_files' );
            $this->addRule( 'target_dir', 'required', 'The path to the target directory is required' );
            
            $this->addNavButtons( 'Continue', 'Back', 'A' );
            
            if ( $this->isValid() ) {
                
                $target_dir = YDPath::join( $this->_target_dir, $this->getValue( 'target_dir' ) );
                
                if ( ! $this->isDirectory( $target_dir ) ) {
                    $this->addError( 'The target directory does not exist', true );
                    return;
                }
                if ( ! $this->isWriteable( $target_dir ) ) {
                    $this->addError( 'The target directory is not writeable', true );
                    return;
                }
                
                $this->setField( 'target_dir', $this->getValue( 'target_dir' ) );
                
                $this->goTo( 'C' );
            }
            
            $this->display();
        }
        
        function actionC() {
            
            $this->addHeader( 'Copying files...' );
            
            $this->setSourceDirectory( 'new_files' );
            $this->setTargetDirectory( YDPath::join( $this->_target_dir, $this->target_dir ) );
            
            $res = $this->copyDirectory( 'sub', false );
            $res2 = $this->copyDirectory( 'sub2' );
            $res3 = $this->copyFileByPattern( '*.*' );
            $res4 = $this->deleteDirectory( 'sub3' );
            $res5 = $this->deleteFileByPattern( '*.bmp' );
            $res6 = $this->createDirectory( 'sub4' );
            $res7 = $this->deleteDirectory( 'sub4' );
            
            $results = array_merge( $res, $res2, $res3, $res4, $res5, $res6, $res7 );
            
            $this->addText( YDInstaller::getReport( $results ) );
            
            $this->addSeparator();
            $this->addNavButtons( 'Continue' );
            
            $this->setField( 'installed', true );
            
            if ( $this->isValid() ) {
                $this->goTo( 'D' );
            }
            
            $this->display();
            
        }
        
        function actionD() {
            
            $this->addHeader( 'Complete' );
            $this->addText( 'The installation process is complete.' );
            $this->addLink( YDConfig::get( 'YD_LOG_FILE' ), 'Click here for the installation log' );
            $this->addText( 'Here\'s an example of the usage:' );
            
            $this->addPhp(
'<?php

    // Instantiate the class
    $my = new MyClass();
    
    // Add the file
    $my->addFile( "/index.tpl" );
    
    // Show the result
    $my->show();
    
?>' );
            
            // $this->addSeparator();
            
            // $this->addAlert( 'This is an alert' );
            
            $this->addImage( '../../YDFramework2/images/PoweredByYDF.jpg', array( 'align' => 'right' ) );
            
            $this->addText( 'Make sure you access these links:' );
            
            $this->addNumericList( array(
                '<a href="' . YD_FW_HOMEPAGE . '">Yellow Duck Framework Homepage</a>',
                '<a href="' . YD_FW_HOMEPAGE . 'doc/index.html">Yellow Duck Framework User Guide</a>',
                '<a href="' . YD_FW_HOMEPAGE . 'api/index.html">Yellow Duck Framework API Guide</a>'
            ) );
            
            $this->addSeparator();
            $this->addNavButtons( 'Finish' );
            
            if ( $this->isValid() ) {
                $this->redirect( '../' );
            }
            
            $this->display();
            
        }
        
        function isActionAllowed() {
            
            $current = strtolower( $this->getActionName() );
            
            if ( $current == 'default' ) {
                return true;
            }
            
            if ( $current >= 'b' ) {
                if ( ! $this->agree ) { $this->goTo(); }
            }
            if ( $current >= 'c' ) {
                if ( ! $this->target_dir ) { $this->goTo( 'B' ); }
            }
            if ( $current >= 'd' ) {
                if ( ! $this->installed ) { $this->goTo(); }
            }
            
            return true;
            
        }
        
    }
    
    // Process the request
    YDInclude( 'YDF2_process.php' );

?>