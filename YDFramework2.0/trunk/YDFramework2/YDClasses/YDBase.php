<?php

    /*

        Yellow Duck Framework version 2.0
        Copyright (C) (c) copyright 2004 Pieter Claerhout

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
     *	This is the base class for all other YD classes.
     */
    class YDBase {

        /**
         *	Class constructor for the YDBase class.
         */
        function YDBase() {
        }

        /**
         *	This function returns the name of the current class.
         *
         *	@returns	The name of the current class in lowercase.
         */
        function getClassName() {
            return strtolower( get_class( $this ) );
        }

        /**
         *	This function returns true if the specified method is existing in the current class.
         *
         *	@param		$name	The name of the class method to look for.
         *
         *	@returns	Boolean indicating if the class method exists or not.
         */
        function hasMethod( $name ) {
            return method_exists( $this, $name );
        }

        /**
         *	This function checks if this object instance is of a specific class or is based on a derived class of the
         *	given class. The class name is case insensitive.
         *
         *	@param $class	The object type you want to check against.
         *
         *	@returns	Boolean indicating if this object is of the specified class.
         */
        function isSubClass( $class ) {
            return YDObjectUtil::isSubClass( $this, $class );
        }

        /**
         *	Function to get all the ancestors of this class. The list will contain the parent class first, and then it's
         *	parent class, etc. You can pass both the name of the class or an object instance to this function
         *
         *	@returns	Array with all the ancestors.
         */
        function getAncestors() {
            return YDObjectUtil::getAncestors( $this->getClassName() );
        }

        /**
         *	This function will serialize the object.
         */
        function serialize( $obj ) {
            return YDObjectUtil::serialize( $this );
        }

    }

?>
