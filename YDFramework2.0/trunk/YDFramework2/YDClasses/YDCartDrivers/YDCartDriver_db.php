<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	YDInclude( 'YDCart.php' );
	YDInclude( 'YDDatabase.php' );
	
	/**
	 *	This class defines a cart driver for information in a database.
	 */	
	class YDCartDriver_db extends YDCartDriver {

		/**
		 *	Class constructor. Creates the database object.
		 *
		 *	@param $session_var		(optional) Name to use in the session var - $_SESSION[ $session_var ]
		 *	@param $options			(optional) Information used by the cart driver.
		 */					
		function YDCartDriver_db( $session_var='YDCart', $options=array() ) {			
			
			$this->YDCartDriver( $session_var, $options );
						
			$this->db = YDDatabase::getInstance( $this->_options['type'], 
												 $this->_options['db'], 
												 $this->_options['user'], 
												 $this->_options['pass'], 
												 $this->_options['host'] 
											   );
		}

		/**
		 *	This function will return all the product's information.
		 *
		 *	@param $id		Product ID.
		 *
		 *  @returns		An array with the information or false if it doesn't exists.
		 */			
		function getInfo( $id ) {				
			return $this->_loadInfo( $id );
		}

		/**
		 *	This function will return all the product's information by loading it
		 *  from the database and keeping it in a class var.
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
			if ( $arr = $this->db->getRecord( 'SELECT * FROM `' . $this->_options['table'] . '` WHERE id=' . $id ) ) {				
				$this->info[$id] = $arr;
				return is_null( $column ) ? $this->info[$id] : $this->info[$id][$column];
			} 
			return false;
			
		}

		/**
		 *	This function will return the product's name.
		 *
		 *	@param $id		Product ID.
		 */				
		function getName( $id ) {
			return $this->_check( $id, 'name' );	
		}

		/**
		 *	This function will return the product's price.
		 *
		 *	@param $id		Product ID.
		 */				
		function getPrice( $id ) {
			return $this->_check( $id, 'price' );
		}

		/**
		 *	This function will return the product's description.
		 *
		 *	@param $id		Product ID.
		 */				
		function getDescription( $id ) {
			return $this->_check( $id, 'description' );
		}

		/**
		 *	This function will return the product's quantity.
		 *
		 *	@param $id		Product ID.
		 */				
		function getQuantity( $id ) {			
			return $this->_check( $id, 'quantity' );
		}

		/**
		 *	This function will return the product's thumbnail location.
		 *
		 *	@param $id		Product ID.
		 */		
		function getThumbnail( $id ) {
			return $this->_check( $id, 'thumbnail' );
		}

		/**
		 *	This function will return the product's picture location.
		 *
		 *	@param $id		Product ID.
		 */				
		function getPicture( $id ) {
			return $this->_check( $id, 'picture' );
		}

		/**
		 *	This function will return the product's shipping.
		 *
		 *	@param $id		Product ID.
		 */				
		function getShipping( $id ) {
			return $this->_check( $id, 'shipping' );
		}
		
	}

?>