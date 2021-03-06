<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDEncryption.php' );

    // Class definition
    class encryption extends YDRequest {

        // Class constructor
        function encryption() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // Show the current object
            YDDebugUtil::dump( $this, 'Original object before encryption' );

            // Get the serialized version of ourselves
            //$data = $this->serialize();
            YDDebugUtil::dump( $this, 'Input data' );

            // Show the password we are using
            YDDebugUtil::dump( YD_SELF_SCRIPT, 'Encryption password' );

            // Encrypt the data
            $data_encrypted = YDEncryption::encrypt( YD_SELF_SCRIPT, $this );
            YDDebugUtil::dump( $data_encrypted, 'Encrypted data' );

            // Decrypt the data
            $data_decrypted = YDEncryption::decrypt( YD_SELF_SCRIPT, $data_encrypted );
            //YDDebugUtil::dump( $data_decrypted, 'Decrypted data' );

            // Show the decrypted object
            YDDebugUtil::dump( $data_decrypted, 'Original object after decryption' );

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
