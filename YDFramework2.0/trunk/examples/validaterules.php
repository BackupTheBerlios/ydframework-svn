<?php

    // Standard include
    require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDValidateRules.php' );

    // Class definition
    class validaterules extends YDRequest {

        // Class constructor
        function validaterules() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {

            // The test cases
            // Format: string / options / rule / expected result
            $tests = array(
                'required' => array( 
                    array( '', array(), false ),
                    array( 'PieterClaerhout', array(), true ),
                    array( null, array(), false ),
                ),
                'maxlength' => array( 
                    array( '', 5, true ),
                    array( '1234', 5, true ),
                    array( 'PieterClaerhout', 5, false ),
                    array( null, 5, true ),
                ),
                'minlength' => array( 
                    array( '', 5, false ),
                    array( '1234', 5, false ),
                    array( 'PieterClaerhout', 5, true ),
                    array( null, 5, false ),
                ),
                'rangelength' => array( 
                    array( '', array( 2, 4 ), false ),
                    array( '1234', array( 2, 4 ), true ),
                    array( 'PieterClaerhout', array( 2, 4 ), false ),
                    array( null, array( 2, 4 ), false ),
                ),
                'email' => array( 
                    array( 'pieter', array(), false ),
                    array( 'pieter@localhost', array(), false ),
                    array( 'pieter@localhost.', array(), false ),
                    array( 'pieter@localhost.x', array(), true ),
                    array( 'pieter@yellowduck.be', array(), true ),
                    array( 'pieter@yellowduck.info', array(), true ),
                    array( 'pieter@127.0', array(), false ),
                    array( 'pieter@127.0.0.1', array(), true ),
                ),
                'lettersonly' => array(
                    array( '1234', array(), false ),
                    array( '1,2,3,4', array(), false ),
                    array( 'PieterClaerhout', array(), true ),
                    array( 'PieterClaerhout1234', array(), false ),
                    array( 'PieterClaerhout$£%', array(), false ),
                    array( 'PieterClaerhoutAtençãoMaçãMêra', array(), true ),
                    array( 'PieterClaerhout,Atenção,Maçã,Pêra', array(), false ),
                ),
                'character' => array( 
                    array( 'a', array(), true ),
                    array( 'B', array(), true ),
                    array( '0', array(), false ),
                    array( '1', array(), false ),
                    array( '1234', array(), false ),
                    array( '1,2,3,4', array(), false ),
                    array( 'PieterClaerhout', array(), false ),
                    array( 'PieterClaerhout1234', array(), false ),
                    array( 'PieterClaerhout$£%', array(), false ),
                    array( 'PieterClaerhoutAtençãoMaçãMêra', array(), false ),
                    array( 'Pieter,Claerhout,Atenção,Maçã,Mêra', array(), false ),
                ),
                'alphanumeric' => array( 
                    array( '1234', array(), true ),
                    array( 'PieterClaerhout', array(), true ),
                    array( 'PieterClaerhout1234', array(), true ),
                    array( 'PieterClaerhout$£%', array(), false ),
                    array( 'PieterClaerhoutAtençãoMaçãMêra', array(), true ),
                    array( 'Pieter,Claerhout,Atenção,Maçã,Mêra', array(), false ),
                ),
                'numeric' => array( 
                    array( '1234', array(), true ),
                    array( '1,2,3,4', array(), false ),
                    array( 'PieterClaerhout', array(), false ),
                    array( 'PieterClaerhout1234', array(), false ),
                    array( 'PieterClaerhout$£%', array(), false ),
                    array( 'PieterClaerhoutAtençãoMaçãMêra', array(), false ),
                    array( 'Pieter,Claerhout,Atenção,Maçã,Mêra', array(), false ),
                ),
                'digit' => array( 
                    array( 'a', array(), false ),
                    array( 'B', array(), false ),
                    array( '0', array(), true ),
                    array( '1', array(), true ),
                    array( '1234', array(), false ),
                    array( '1,2,3,4', array(), false ),
                    array( 'PieterClaerhout', array(), false ),
                    array( 'PieterClaerhout1234', array(), false ),
                    array( 'PieterClaerhout$£%', array(), false ),
                    array( 'PieterClaerhoutAtençãoMaçãMêra', array(), false ),
                    array( 'Pieter,Claerhout,Atenção,Maçã,Mêra', array(), false ),
                ),
                'nopunctuation' => array( 
                    array( '1234', array(), true ),
                    array( '1,2,3,4', array(), false ),
                    array( 'PieterClaerhout', array(), true ),
                    array( 'PieterClaerhout1234', array(), true ),
                    array( 'PieterClaerhout$£%', array(), false ),
                    array( 'PieterClaerhoutAtençãoMaçãMêra', array(), true ),
                    array( 'Pieter,Claerhout,Atenção,Maçã,Mêra', array(), false ),
                ),
                'nonzero' => array( 
                    array( '0', array(), false ),
                    array( '1', array(), true ),
                    array( '01', array(), false ),
                    array( '02', array(), false ),
                    array( '20', array(), true ),
                    array( '1,2,3,4', array(), false ),
                ),
                'in_array' => array( 
                    array( '0', array( 0, '1', '2' ), false ),
                    array( 1, array( 0, '1', '2' ), false ),
                    array( 0, array( 0, '1', '2' ), true ),
                    array( '1', array( 0, '1', '2' ), true ),
                    array( 'a', array( 'a', 'b', 'c' ), true ),
                    array( 'A', array( 'a', 'b', 'c' ), false ),
                ),
                'not_in_array' => array( 
                    array( '0', array( 0, '1', '2' ), true ),
                    array( 1, array( 0, '1', '2' ), true ),
                    array( 0, array( 0, '1', '2' ), false ),
                    array( '1', array( 0, '1', '2' ), false ),
                    array( 'a', array( 'a', 'b', 'c' ), false ),
                    array( 'A', array( 'a', 'b', 'c' ), true ),
                ),
            );

            // Perform the tests
            $results = array();
            foreach ( $tests as $rule=>$cases ) {
                $results[ $rule ] = array();
                foreach ( $cases as $test ) {
                    $result = call_user_func( array( 'YDValidateRules', $rule ), $test[0], $test[1] );
                    array_push( $results[ $rule ], array( $test[0], $result, $test[2] ) );
                }
            }

            // Show the result
            foreach ( $results as $rule=>$cases ) {
                echo( '<h2>YDValidateRules::' . $rule . '</h2>' );
                echo( '<table border="1" cellspacing="0" cellpadding="4">' );
                echo( '<tr><th>Test string</th><th>Result</th><th>Expected</th><th>Passed</th></tr>' );
                foreach ( $cases as $result ) {
                    echo( '<tr>' );
                    if ( is_null( $result[0] ) ) {
                        echo( '<td><i>null</i></td>' );
                    } else {
                        echo( '<td>' . $result[0] . '&nbsp;</td>' );
                    }
                    if ( $result[1] ) {
                        echo( '<td bgcolor="green"><font color="white">true</font></td>' );
                    } else {
                        echo( '<td bgcolor="red"><font color="white">false</font></td>' );

                    }
                    if ( $result[2] ) {
                        echo( '<td bgcolor="green"><font color="white">true</font></td>' );
                    } else {
                        echo( '<td bgcolor="red"><font color="white">false</font></td>' );

                    }
                    if ( $result[2] === $result[1] ) {
                        echo( '<td bgcolor="green"><font color="white">passed</font></td>' );
                    } else {
                        echo( '<td bgcolor="red"><font color="white">failed</font></td>' );
                    }
                    echo( '</tr>' );
                }
                echo( '</table>' );
            }

        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
