<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 *	This class houses all the object and class related utility functions. All the methods are implemented as static
	 *	methods and do not require you to create a class instance in order to use them.
	 */
	class YDObjectUtil extends YDBase {

		/**
		 *	This function checks if an object instance is of a specific class or is based on a derived class of the
		 *	given class. The class name is case insensitive.
		 *
		 *	@param $obj		The object instance to check.
		 *	@param $class	The object type you want to check against.
		 *
		 *	@returns	Boolean indicating if the object is of the specified class.
		 */
		function isSubClass( $obj, $class ) {
			$class = strtolower( $class );
			if ( function_exists( 'is_a' ) ) {
				return is_a( $obj, $class );
			} else {
				if ( is_object( $obj ) ) {
					if ( get_class( $obj ) == $class ) return true;
					if ( is_subclass_of( $obj, $class ) ) return true;
				}
			}
			return false;
		}

		/**
		 *	Function to get all the ancestors of a class. The list will contain the parent class first, and then it's
		 *	parent class, etc. You can pass both the name of the class or an object instance to this function
		 *
		 *	@param $classname	Name of the class or object.
		 *
		 *	@returns	Array with all the ancestors.
		 */
		function getAncestors( $classname ) {
			if ( is_object( $classname ) ) {
				$classname = get_class( $classname );
			}
			$ancestors = array();
			$father = get_parent_class( $classname );
			if ( $father != '' ) {
				$ancestors = YDObjectUtil::getAncestors( $father );
				$ancestors[] = $father;
			}
			return array_reverse( $ancestors );
		}

		/**
		 *	This function will serialize an object.
		 *
		 *	@param $obj	Object to serialize.
		 */
		function serialize( $obj ) {
			$obj = serialize( $obj );
			if ( ! $obj ) { YDFatalError( 'Failed serializing the object' ); }
			return $obj;
		}

		/**
		 *	This function will unserialize an object.
		 *
		 *	@param $obj	Object to unserialize.
		 */
		function unserialize( $obj ) {
			$obj = unserialize( $obj );
			if ( ! $obj ) { YDFatalError( 'Failed unserializing the object' ); }
			return $obj;
		}

	}

?>
