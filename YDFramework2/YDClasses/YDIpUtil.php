<?php

    /*

       Yellow Duck Framework version 2.0
       (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

    */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    /**
     *  This class houses all the TCP/IP related utility functions. All the 
     *  methods are implemented as static methods and do not require you to
     *  create a class instance in order to use them.
     */
    class YDIpUtil extends YDBase {

        /**
         *  This function generates an IPv4 Internet network address from its 
         *  internet standard format (dotted string) representation.
         *
         *  @param $ip_address The IP address in it's internet standard format.
         *
         *  @returns Integer with the internet network address of the IP address.
         */
        function YDIpToNumber( $ip_address ) {

            // Split up the address
            $ip_address = preg_split( '/[.]+/', $ip_address );

            // Calculate the number
            $ip = ( double ) ( $ip_address[0] * 16777216 )
                + ( $ip_address[1] * 65536 )
                + ( $ip_address[2] * 256 )
                + ( $ip_address[3] );

            // Return the number
            return $ip;

        }

        /**
         *  This function generates an IPv4 Internet network address to its 
         *  internet standard format (dotted string) representation.
         *
         *  @param $proper_address Integer with the internet network address of
         *  the IP address.
         *
         *  @returns The IP address in it's internet standard format.
         */
        function YDNumberToIp( $proper_address ) {

            // Calculate the 4 parts
            $a = ( $proper_address / 16777216 ) % 256;
            $b = ( $proper_address / 65536 ) % 256;
            $c = ( $proper_address / 256 ) % 256;
            $d = ( $proper_address ) % 256;

            // Combine them
            $dotted = $a . '.'  .$b . '.' . $c . '.' . $d;

            // Return the result
            return $dotted;

        }
    
    }

?>
