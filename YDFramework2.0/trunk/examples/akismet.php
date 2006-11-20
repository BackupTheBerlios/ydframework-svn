<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDAkismet.php' );

    // Class definition
    class akismet extends YDRequest {

        // Class constructor
        function akismet() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // The comments to check
            $comments = array(
                array(
                    'comment' => 'Fuck%XXKEVDJX!!http://porn.z0rder.com/anal-porn.htm [url= http://porn.z0rder.com/anal-porn.htm ]http://porn.z0rder.com/anal-porn.htm[/url]',
                    'author'  => 'PCNFZUII',
                ),
                array(
                    'comment' => 'Een commentaar',
                    'author'  => 'Pieter Claerhout',
                ),
            );

            // Create a new client
            $akismet = new YDAkismet( 'http://www.yellowduck.be/', 'd60025a2e94e' );

            // Check the comments
            foreach ( $comments as $key => $comment ) {

                // Check the comment
                $result = $akismet->checkComment( $comment['comment'], $comment['author'] );
                YDDebugUtil::dump( $result, 'comment-' . $key );

            }

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
