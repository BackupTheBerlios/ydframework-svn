<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

        This library is free software; you can redistribute it and/or
        modify it under the terms of the GNU Lesser General Public
        License as published by the Free Software Foundation; either
        version 2.1 of the License, or (at your option) any later version.

        This library is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
        Lesser General Public License for more details.

        You should have received a copy of the GNU Lesser General Public
        License along with this library; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

    */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    /**
     *  This class is able to encrypt and decrypt data.
     */
    class YDEncryption extends YDBase {

        /**
         *  Encrypt data.
         *
         *  @param  $passwd The password to use for the encryption
         *  @param  $data   The data to encrypt
         *
         *  @returns    The encrypted data as base64 encoded data.
         *
         *  @static
         */
        function encrypt( $passwd, $data ) {
            return base64_encode( YDEncryption::_encrypt( $passwd, serialize( $data ) ) );
        }

        /**
         *  Decrypt data.
         *
         *  @param  $passwd The password to use for the encryption
         *  @param  $data   The data to decrypt. Should be formatted as base64.
         *
         *  @returns    The decrypted data.
         *
         *  @static
         */
        function decrypt( $passwd, $data ) {
            return unserialize( YDEncryption::_encrypt( $passwd, base64_decode( $data ) ) );

        }

        /**
         *  A helper function to do the encryption.
         *
         *  @param  $passwd The password to use for the encryption
         *  @param  $data   The data to encrypt
         *
         *  @returns    The encrypted data as base64 encoded data.
         * 
         *  @internal
         *
         *  @static
         */
        function _encrypt( $passwd, $data ) {
            $key[] = '';
            $box[] = '';
            $cipher = '';
            $pwd_length = strlen( $passwd );
            $data_length = strlen( $data );
            for ( $i = 0; $i < 256; $i++ ) {
                $key[$i] = ord( $passwd[$i % $pwd_length] );
                $box[$i] = $i;
            }
            for ( $j = $i = 0; $i < 256; $i++ ) {
                $j = ( $j + $box[$i] + $key[$i] ) % 256;
                $tmp = $box[$i];
                $box[$i] = $box[$j];
                $box[$j] = $tmp;
            }
            for ( $a = $j = $i = 0; $i < $data_length; $i++ ) {
                $a = ( $a + 1 ) % 256;
                $j = ( $j + $box[$a] ) % 256;
                $tmp = $box[$a];
                $box[$a] = $box[$j];
                $box[$j] = $tmp;
                $k = $box[ ( ( $box[$a] + $box[$j] ) % 256 ) ];
                $cipher .= chr( ord( $data[$i] ) ^ $k );
            }
            return $cipher;
        }

    }

?>