<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	YDInclude( 'YDCart.php' );

	/**
	 *	This class defines a cart driver for information in an array.
	 */	
	class YDCartDriver_array extends YDCartDriver {
		
		/**
		 *	Class constructor.
		 *
		 *	@param $session_var		(optional) Name to use in the session var - $_SESSION[ $session_var ]
		 *	@param $options			(optional) Information used by the cart driver.
		 */	
		function YDCartDriver_array( $session_var='YDCart', $options=array() ) {			
			
			$this->YDCartDriver( $session_var, $options );			
			$this->info = & $GLOBALS['products'];	
			
		}

		/**
		 *	This function will return all the product's information.
		 *
		 *	@param $id		Product ID.
		 *
		 *  @returns		An array with the information.
		 */			
		function getInfo( $id ) {
			return $this->_check( $id );
		}
		
		
		/**
		 *	This function will return the product's information if exists.
		 *
		 *	@param $id		Product ID.
		 *  @param $column	(optional) The column to be returned. If null, all the columns are returned.
		 *
		 *  @returns		The value/array of the information or false if it doesn't exists.
		 *  @internal
		 */					
		function _check( $id, $column=null ) {
		
			if ( array_key_exists( $id, $this->info ) ) {			
				return is_null( $column ) ? $this->info[$id] : $this->info[$id][$column];
			} 
			return false;
			
		}
				
		function getName( $id ) {
			return $this->_check( $id, 'name' );
		}
		
		function getPrice( $id ) {
			return $this->_check( $id, 'price' );
		}
		
		function getDescription( $id ) {
			return $this->_check( $id, 'description' );
		}
		
		function getQuantity( $id ) {			
			return $this->_check( $id, 'quantity' );
		}

		function getThumbnail( $id ) {
			return $this->_check( $id, 'thumbnail' );
		}
		
		function getPicture( $id ) {
			return $this->_check( $id, 'picture' );
		}
		
		function getShipping( $id ) {
			return $this->_check( $id, 'shipping' );
		}		
		
	}

?>