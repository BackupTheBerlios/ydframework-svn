<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
	
	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}
	
	/**
	 *	This class defines a shopping cart object.
	 */
	class YDCart extends YDBase {

		/**
		 *  Array with the cart's items.
		 */
		var $item = array();

		/**
		 *  Array with the cart's options.
		 *
		 *  @internal
		 */
		var $_options = array();

		/**
		 *	Class constructor for the YDCart class.
		 *
		 *	@param $driver			Name of the cart driver.
		 *	@param $session_var		(optional) Name to use in the session var - $_SESSION[ $session_var ]
		 *	@param $options			(optional) Information used by the cart driver.
		 *
		 *	@deprecated	 			Use the static method getInstance to get a new YDCart class instance.
		 */			
		function YDCart( $driver, $session_var='YDCart', $options=array() ) {
			trigger_error( 'Use the static method getInstance to get a new YDCart class instance.', YD_ERROR );
		}
		
		/**
		 *	Using this static function, you can get an instance of a YDCartDriver class.
		 *
		 *	@param $driver			Name of the cart driver.
		 *	@param $session_var		(optional) Name to use in the session var - $_SESSION[ $session_var ]
		 *	@param $options			(optional) Information used by the cart driver.
		 */
		function getInstance( $driver, $session_var='YDCart', $options=array() ) {
		
			// The list of known drivers
			$regDrivers = array();

			// Register the standard drives
			$regDrivers[ strtolower( 'array' ) ] = array(
				'class' => 'YDCartDriver_array', 'file' => 'YDCartDriver_array.php'
			);
			$regDrivers[ strtolower( 'db' ) ] = array(
				'class' => 'YDCartDriver_db', 'file' => 'YDCartDriver_db.php'
			);
//			$regDrivers[ strtolower( 'xml' ) ] = array(
//				'class' => 'YDCartDriver_xml', 'file' => 'YDCartDriver_xml.php'
//			);

			// Check if the driver exists
			if ( ! array_key_exists( strtolower( $driver ), $regDrivers ) ) {
				trigger_error( 'Unsupported Cart driver: "' . $driver . '".', YD_ERROR );
			}

			// Include the driver
			if ( ! empty( $regDrivers[ strtolower( $driver ) ]['file'] ) ) {
				require_once( 'YDCartDrivers/' . $regDrivers[ strtolower( $driver ) ]['file'] );
			}

			// Make a new object and return it
			$className = $regDrivers[ strtolower( $driver ) ]['class'];
			return new $className( $session_var, $options );
			
				
		}

		/**
		 *	The real constructor for the cart controller.
		 *
		 *	@param $session_var		(optional) Name to use in the session var - $_SESSION[ $session_var ]
		 *	@param $options			(optional) Information used by the cart driver.
		 *
		 *	@internal		 
		 */		
		function _initCart( $session_var = 'YDCart', $options=array() ) {
		
			if ( ! isset( $_SESSION[$session_var] ) ) {
				$_SESSION[$session_var] = & $this->item;	
			} else {			
				$this->item = & $_SESSION[$session_var];
			}
			
			$this->_options = $options;
		
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
				
					$result = $this->item[$id] - (int) $quantity;
					
					if ( $result > 0 ) {
						$this->item[$id] = $result;
						return true;
					}
					
				}
				
				unset( $this->item[$id] );				
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
		 */			
		function setItem( $id, $quantity, $add=false ) {
		
			$quantity = $this->_getAvailable( $id, $quantity );
			
			if ( $add && $this->inCart( $id ) ) {
				$quantity += $this->item[$id];
			}
			
			if ( $quantity > 0 ) {
				$this->item[$id] = $quantity;
				return true;
			} else {
				return $this->remItem( $id );
			}
		
		}

		/**
		 *	This function compares if the quantity needed is available.
		 *
		 *	@param $id			Product ID.
		 *	@param $quantity	Quantity needed of the product.
		 *
		 *  @returns 			The available quantity.
		 *
		 *	@internal
		 */			
		function _getAvailable( $id, $quantity ) {

			$available = $this->getQuantity( $id );
			if ( $quantity >= $available ) {
				$quantity = $available;
			}			
			return $quantity;
		
		}

		/**
		 *	This function will return the array of items of the basket.
		 *
		 *  @returns  The cart's items array.
		 */					
		function getItems() {
		
			return $this->item;
			
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

			return array_key_exists( $id, $this->item );
										
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
		 *	This function will return the total cost of the cart.
		 *
		 *  @returns	The total cost of the cart.
		 */			
		function getTotal() {
		
			$total = 0;
			
			foreach ( $this->item as $id => $quantity ) {
				$total += $this->getPrice( $id ) * $quantity;
			}
			
			return $total;
			
		}

		/**
		 *	This function will return the total cost of an item in the cart.
		 *
		 *  @param $id	Product ID.
		 *
		 *  @returns	The total cost of an item.
		 */				
		function getItemTotal( $id ) {
		
			$total = 0;
			
			if ( $this->inCart( $id ) ) {			
				$total = $this->getPrice( $id ) * $this->item[$id];					
			}
			
			return $total;
				
		}

		/**
		 *	This function will return the item's quantity in the cart.
		 *
		 *  @returns	The item's quantity in the cart.
		 */				
		function getItemQuantity( $id ) {
		
			$quantity = 0;
			
			if ( $this->inCart( $id ) ) {			
				$quantity = $this->item[$id];					
			}
			
			return $quantity;
				
		}		
	
	}

	/**
	 *	This class defines a cart driver.
	 */	
	class YDCartDriver extends YDCart {

		/**
		 *	Class constructor.
		 *
		 *	@param $session_var		(optional) Name to use in the session var - $_SESSION[ $session_var ]
		 *	@param $options			(optional) Information used by the cart driver.
		 */			
		function YDCartDriver( $session_var = 'YDCart', $options=array() ) {			
			$this->_initCart( $session_var, $options );			
		}

		/**
		 *	This function will return all the product's information.
		 *
		 *	@param $id		Product ID.
		 *
		 *  @returns		An array with the information or false if an error ocurred.
		 */			
		function getInfo( $id ) {
		
			return array();
		
		}

		/**
		 *	This function will return the product's name.
		 *
		 *	@param $id		Product ID.
		 */			
		function getName( $id ) {
		}
		
		/**
		 *	This function will return the product's price.
		 *
		 *	@param $id		Product ID.
		 */			
		function getPrice( $id ) {
		}
		
		/**
		 *	This function will return the product's description.
		 *
		 *	@param $id		Product ID.
		 */			
		function getDescription( $id ) {
		}
		
		/**
		 *	This function will return the product's quantity available.
		 *
		 *	@param $id		Product ID.
		 */			
		function getQuantity( $id ) {			
		}

		/**
		 *	This function will return the product's thumbnail location.
		 *
		 *	@param $id		Product ID.
		 */			
		function getThumbnail( $id ) {
		}

		/**
		 *	This function will return the product's picture location.
		 *
		 *	@param $id		Product ID.
		 */					
		function getPicture( $id ) {
		}
		
		/**
		 *	This function will return the product's shipping information.
		 *
		 *	@param $id		Product ID.
		 */			
		function getShipping( $id ) {
		}
		
	}

?>