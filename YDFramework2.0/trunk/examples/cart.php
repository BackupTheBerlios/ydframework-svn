<?php

	// Standard include
	require_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

	// Includes
	YDInclude( 'YDRequest.php' );
	YDInclude( 'YDTemplate.php' );
	YDInclude( 'YDCart.php' );

	// The global array with the different products
	$GLOBALS['products'] = array(
		1 => array(
				'name' => 'Product 1',
				'price' => 10,
				'quantity' => 2,
				'description' => 'This is product\'s 1 description'
			),
		2 => array(
				'name' => 'Product 2',
				'price' => 20,
				'quantity' => 100,
				'description' => 'This is product\'s 2 description'
			),
		3 => array(
				'name' => 'Product 3',
				'price' => 30,
				'quantity' => 150,
				'description' => 'This is product\'s 3 description'
			),
		4 => array(
				'name' => 'Product 4',
				'price' => 40,
				'quantity' => 200,
				'description' => 'This is product\'s 4 description'
			),
	);

	// Class definition for MyCart
	class MyCart extends YDCart {

		// The product array
		var $product = array();

		// The class constructor
		function MyCart( $session_var='YDCart', $options=array() ) {

			// Initializes YDCart
			$this->YDCart( $session_var );

			// Define the different producs
			$this->product = $GLOBALS['products']; 

		}

		// Get the number of available products
		function _getAvailable( $id, $quantity, $add ) {

			$available = $this->product[$id]['quantity'];

			if ( $add ) {
				$incart = isset( $this->item[$id] ) ? $this->item[$id] : 0;
			}

			$available -= isset( $incart ) ? $incart : 0;

			if ( $quantity > $available ) {
				return $available;
			}

			return $quantity;

		}

		// Set an item
		function setItem( $id, $quantity, $add=false ) {

			if ( $quantity == 0 ) {
				$this->remItem( $id );
				return $quantity;
			}

			$quantity = $this->_getAvailable( $id, $quantity, $add );

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
				return $this->item[$id] = $quantity;
			}

		}

		// Get the list of products
		function getProducts() {
			return $this->product;
		}

		// Convert the cart to an array
		function toArray() {

			$arr = array();

			foreach ( $this->item as $id => $quantity ) {

				$arr[$id] = $this->product[$id];
				$arr[$id]['total'] = $quantity * $arr[$id]['price'];
				$arr[$id]['count'] = $quantity;

			}

			return $arr;

		}

		// Get the total
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

		// Class constructor
		function cart() {

			$this->YDRequest();
			$this->template = new YDTemplate();
			$this->template->assign( 'title', 'YDCart example' );

			$this->cart = new MyCart( 'YDCart' );

		}

		// Default action
		function actionDefault() {

			$this->template->assign( 'cart_item',    $this->cart->toArray() );
			$this->template->assign( 'cart_isempty', $this->cart->isEmpty() );
			$this->template->assign( 'cart_count',   $this->cart->getCount() );
			$this->template->assign( 'cart_total',   $this->cart->getTotal() );
			$this->template->assign( 'product',      $this->cart->getProducts() );
			
			$this->template->display();

		}

		// Add an item to the cart
		function actionAdd() {

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

			$this->forward( 'default' );

		}

		// Remove an item from the cart
		function actionRem() {


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


			$this->forward( 'default' );

		}

		// Empty the cart
		function actionEmpty() {

			$this->cart->emptyCart();

			$this->template->assign( 'result', 'The cart is now empty.' );

			$this->forward( 'default' );

		}

		// Modify the cart
		function actionModify() {

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

			$this->forward( 'default' );

		}

		// Show the cart contents
		function actionShow() {

			if ( ! isset ( $_GET['id'] ) ) {
				$this->redirectToAction();
			}

			$this->template->assign( 'item_name',        $this->cart->product[$_GET['id']]['name'] );
			$this->template->assign( 'item_price',       $this->cart->product[$_GET['id']]['price'] );
			$this->template->assign( 'item_quantity',    $this->cart->product[$_GET['id']]['quantity'] );
			$this->template->assign( 'item_description', $this->cart->product[$_GET['id']]['description'] );

			$this->forward( 'default' );

		}

	}

	YDInclude( 'YDF2_process.php' );

?>