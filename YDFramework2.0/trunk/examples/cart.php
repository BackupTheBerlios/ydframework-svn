<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDCart.php' );
	
	$GLOBALS['products'] = array(
			
		1 => array( 'name' => 'Product 1',
					'price' => 10,
					'quantity' => 2,
					'description' => 'This is product\'s 1 description'
				),		
		2 => array( 'name' => 'Product 2',
					'price' => 20,
					'quantity' => 100,
					'description' => 'This is product\'s 2 description'
				),
		3 => array( 'name' => 'Product 3',
					'price' => 30,
					'quantity' => 150,
					'description' => 'This is product\'s 3 description'
				),
		4 => array( 'name' => 'Product 4',
					'price' => 40,
					'quantity' => 200,
					'description' => 'This is product\'s 4 description'
				),
	
	);		

	class MyCart extends YDCart {
	
		var $product = array();
		
		function MyCart( $session_var='YDCart', $options=array() ) {
		
			// Initializes YDCart
			$this->YDCart( $session_var );		
			
			$this->product = $GLOBALS['products']; 
		
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

			$available = $this->product[$id]['quantity'];			
			$incart = isset( $this->item[$id] ) ? $this->item[$id] : 0;
			
			$available -= $incart;
			
			if ( $quantity > $available ) {
				return $available;
			}			
			return $quantity;
		
		}		
		
		function setItem( $id, $quantity, $add=false ) {
		
			$quantity = $this->_getAvailable( $id, $quantity );
		
			if ( $add ) {
			
				if ( $quantity ) {
						
					if ( $this->inCart( $id ) ) {
						$this->item[$id] += $quantity;
					} else {
						$this->item[$id] = $quantity;
					}				
					
				} 
				
				return $quantity;	
				
			} 
			
			if ( $quantity ) {
				$this->item[$id] = $quantity;
			} 
			
			return $quantity;
			
			$this->remItem( $id );
			
			return null;
		
		}
		
		function getProducts() {
			
			return $this->product;
			
		}
		
		function toArray() {
		
			$arr = array();
			
			foreach ( $this->item as $id => $quantity ) {
				
				$arr[$id] = $this->product[$id];
				$arr[$id]['total'] = $quantity * $arr[$id]['price'];
				$arr[$id]['count'] = $quantity;
											
			}
			
			return $arr;
		
		}
		
		/**
		 *	This function will return the total cost of the cart.
		 *
		 *  @returns	The total cost of the cart.
		 */			
		function getTotal() {
		
			$total = 0;
			
			foreach ( $this->item as $id => $quantity ) {
				$total += $this->product[$id]['price'] * $quantity;
			}
			
			return $total;
			
		}	
		
	}
	
	// Class definition
	class cart extends YDRequest {
	
		function cart() {

			$this->YDRequest();		
			$this->template = new YDTemplate();
			$this->template->assign( 'title', 'YDCart example' );	

			// Create a cart object
			$this->cart = new MyCart( 'YDCart' );			

			
		}
		
		function actionDefault() {
		
			$this->template->assign( 'cart_item',    $this->cart->toArray() );				
			$this->template->assign( 'cart_isempty', $this->cart->isEmpty() );	
			$this->template->assign( 'cart_count',   $this->cart->getCount() );
			$this->template->assign( 'cart_total',   $this->cart->getTotal() );
			$this->template->assign( 'product',      $this->cart->getProducts() );
			
			$this->template->display();

		}
		
		function actionAdd() {
		
			// Redirect to default if no ID is set
			if ( ! isset ( $_GET['id'] ) ) {
				$this->redirectToAction();
			}
			
			$qtd = isset( $_GET['quantity'] ) ? $_GET['quantity'] : 1;
			$res = $this->cart->addItem( $_GET['id'], $qtd );
			
			if ( $res ) {
				$this->template->assign( 'result', $res . ' item(s) id=' . $_GET['id'] . ' added to the cart.' );
			} else {
				$this->template->assign( 'result', $qtd . ' item(s) id=' . $_GET['id'] . ' not added to the cart. No more items available.' );
			}
			
			// Forwards to default action
			$this->forward( 'default' );
		
		}
		
		function actionRem() {
		
			// Redirect to default if no ID is set
			if ( ! isset ( $_GET['id'] ) ) {
				$this->redirectToAction();
			}
			
			$res = $this->cart->remItem( $_GET['id'], isset( $_GET['quantity'] ) ? $_GET['quantity'] : null );
			
			if ( is_bool( $res ) ) {
				if ( $res ) {
					$this->template->assign( 'result', 'Item id=' . $_GET['id'] . ' removed from the cart.' );
				} else {
					$this->template->assign( 'result', 'Item id=' . $_GET['id'] . ' is not in the cart.' );
				}
			} else {
				$this->template->assign( 'result', $res . ' item(s) id=' . $_GET['id'] . ' is not in the cart.' );	
			}
			
			// Forwards to default action
			$this->forward( 'default' );
		
		}
		
		function actionEmpty() {
		
			$this->cart->emptyCart();
			
			$this->template->assign( 'result', 'The cart is now empty.' );
			
			// Forwards to default action
			$this->forward( 'default' );
		
		}
		
		
		function actionModify() {
		
			// Redirect to default if no ID is set
			if ( ! isset ( $_GET['id'] ) || ! isset( $_GET['quantity'] ) ) {
				$this->redirectToAction();
			}
			
			$res = $this->cart->setItem( $_GET['id'], $_GET['quantity'] );
			
			if ( is_bool( $res ) ) {
				if ( $res ) {
					$this->template->assign( 'result', 'Item id=' . $_GET['id'] . ' modified in the cart.' );
				} else {
					$this->template->assign( 'result', 'Item id=' . $_GET['id'] . ' not modified.' );
				}
			} else {
				$this->template->assign( 'result', $res . ' item(s) id=' . $_GET['id'] . ' in the cart.' );	
			}
			
			// Forwards to default action
			$this->forward( 'default' );
		
		}	

		function actionShow() {
		
			// Redirect to default if no ID is set
			if ( ! isset ( $_GET['id'] ) ) {
				$this->redirectToAction();
			}
			
			$this->template->assign( 'item_name',        $this->cart->product[$_GET['id']]['name'] );
			$this->template->assign( 'item_price',       $this->cart->product[$_GET['id']]['price'] );
			$this->template->assign( 'item_quantity',    $this->cart->product[$_GET['id']]['quantity'] );
			$this->template->assign( 'item_description', $this->cart->product[$_GET['id']]['description'] );
			
			// Forwards to default action
			$this->forward( 'default' );
		
		}			
		
	
	}
	
	YDInclude( 'YDF2_process.php' );

?>