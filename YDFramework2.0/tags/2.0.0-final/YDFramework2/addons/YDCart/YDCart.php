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
    
    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDEncryption.php' );
    
    // Constants
    YDConfig::set( 'YD_CART_PASSWORD', 'YD_CART_PASSWORD', false );

    /**
     *	This class defines a shopping cart object.
     */
    class YDCart extends YDAddOnModule {

        /**
         *  Array with the cart's items.
         */
        var $item = array();

        /**
         *	Class constructor for the YDCart class.
         *
         *	@param $session_var		(optional) Name to use in the session var - $_SESSION[ $session_var ]
         */
        function YDCart( $session_var='YDCart' ) {

            // Initializes YDBase
            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'David Bittencourt';
            $this->_version = '2.0';
            $this->_copyright = '(c) 2005 David Bittencourt, muitocomplicado@hotmail.com';
            $this->_description = 'This class defines a shopping cart object.';

            if ( ! isset( $_SESSION[ $session_var ] ) ) {
                $_SESSION[ $session_var ] = & $this->item;
            } else {
                $this->item = & $_SESSION[ $session_var ];
            }

        }

        /**
         *	This function adds an item to the cart.
         *
         *	@param $id			Product ID.
         *	@param $quantity	(optional) Number of items of the product.
         */
        function addItem( $id, $quantity=1 ) {
            return $this->setItem( $id, $quantity, true );
        }

        /**
         *	This function removes an item from the cart.
         *
         *	@param $id			Product ID to be removed.
         *	@param $quantity	(optional) Number of items to remove. If null, remove all of them.
         */
        function remItem( $id, $quantity=null ) {

            if ( $this->inCart( $id ) ) {

                if ( ! is_null( $quantity ) ) {

                    $result = $this->getItem( $id ) - (int) $quantity;

                    if ( $result > 0 ) {
                        return $this->setItem( $id, $result );
                    }

                }

                unset( $this->item[ YDCart::encrypt( $id ) ] );
                return true;

            }

            return false;

        }

        /**
         *	This function sets an item's quantity in the cart.
         *
         *	@param $id			Product ID.
         *	@param $quantity	Number of items to set.
         *	@param $add			(optional) Boolean: true - $quantity will be added to the current quantity.
         *											false - $quantity will be the new quantity for that product.
         *
         *  @returns 			Current number of items or the result.
         */
        function setItem( $id, $quantity, $add=false ) {
            
            if ( $add && $this->inCart( $id ) ) {
                $quantity += YDCart::decrypt( $this->item[ YDCart::encrypt( $id ) ] );
            }
            
            if ( $quantity > 0 ) {
                $this->item[ YDCart::encrypt( $id ) ] = YDCart::encrypt( $quantity );
                return $quantity;
            } else {
                return $this->remItem( $id );
            }
        }


        /**
         *	This function will return the decrypted array of items.
         *
         *  @returns  The decrypted array.
         */
        function toArray() {
            $arr = array();
            foreach ( $this->item as $id => $quantity ) {
                $arr[ YDCart::decrypt( $id ) ] = YDCart::decrypt( $quantity );
            }
            return $arr;
        }
        
        /**
         *	This function encrypts a value using the YD_CART_PASSWORD.
         *
         *  @returns  The encrypted value
         *  @static
         */
        function encrypt( $value ) {
            return YDEncryption::encrypt( YDConfig::get( 'YD_CART_PASSWORD' ), $value );
        }
        
        /**
         *	This function decrypts a value using the YD_CART_PASSWORD.
         *
         *  @returns  The decrypted value
         *  @static
         */
        function decrypt( $value ) {
            return YDEncryption::decrypt( YDConfig::get( 'YD_CART_PASSWORD' ), $value );
        }

        /**
         *	This function will check if the cart is empty.
         *
         *  @returns  Boolean indicating if the cart is empty.
         */
        function isEmpty() {
            return ! (boolean) $this->getCount();
        }

        /**
         *	This function will empty the cart.
         */
        function emptyCart() {
            $this->item = array();
        }

        /**
         *	This function will check if there is a product in the cart.
         *
         *  @param $id	Product ID.
         *
         *  @returns	Boolean indicating if the product is already in the cart.
         */
        function inCart( $id ) {
            return array_key_exists( YDEncryption::encrypt( YDConfig::get( 'YD_CART_PASSWORD' ), $id ), $this->item );
        }

        /**
         *	This function will return the number of distinct items in the cart.
         *
         *  @returns	Number of distinct items in the cart.
         */
        function getCount() {
            return sizeof( $this->item );
        }

        /**
         *	This function will return the item's quantity in the cart.
         *
         *  @param $id	Product ID.
         *
         *  @returns	The item's quantity in the cart.
         */
        function getItem( $id ) {
            if ( $this->inCart( $id ) ) {
                return YDCart::decrypt( $this->item[ YDCart::encrypt( $id ) ] );
            }
            return 0;
        }

    }

?>