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
     *  This class houses all the object and class related utility functions.
     *  All the methods are implemented as static methods and do not require you
     *  to create a class instance in order to use them.
     */
    class YDObjectUtil extends YDBase {

        /**
         *  This function checks if an object instance is of a specific class
         *  or is based on a derived class of the given class. The class name is 
         *  case insensitive.
         *
         *  This function uses the is_a function from PHP if it's available, and if
         *  not, it falls back on either the get_class or is_subclass_of functions.
         *
         *  @param $obj The object instance to check.
         *  @param $class The object type you want to check against.
         *
         *  @return Boolean indicating if the object is of the specified class.
         */
        function isSubClass( $obj, $class ) {

            // Always use the lowercase class name
            $class = strtolower( $class );

            // Check for the is_a function
            if ( function_exists( 'is_a' ) ) {

                // Use the is_a function
                return is_a( $obj, $class );

            // Create fallback for older PHP versions
            } else {

                // Check that the object is indeed an object
                if ( is_object( $obj ) ) {

                    // Check if the class matches
                    if ( get_class( $obj ) == $class ) {
                        return true;
                    }

                    // Check if it's a subclass
                    if ( is_subclass_of( $obj, $class ) ) {
                        return true;
                    }

                }

            }
     
            // None of the above, return false
            return false;
       
        }

        /**
         *  Function to get all the ancestors of a class. The list will contain the
         *  parent class first, and then it's parent class, etc. You can pass both
         *  the name of the class or an object instance to this function
         *
         *  The following examples shows you how it works:
         *
         *  @code
         *  // Base class
         *  class BaseClass {
         *  }
         *
         *  // Class extending the base class
         *  class ExtendsBaseClass extends BaseClass {
         *  }
         *
         *  // Class extending the ExtendBaseClass
         *  class ExtendsExtendsBaseClass extends ExtendsBaseClass {
         *  }
         *
         *  var_dump( YDGetAncestors( 'ExtendsExtendsBaseClass' );
         *  @endcode
         *
         *  Executing the code above will give you the following result:
         *
         *  @code
         *  array(2) {
         *      [0]=> string(16) "ExtendsBaseClass"
         *      [1]=> string(9) "BaseClass"
         *  } 
         *  @endcode
         *
         *  @param $classname Name of the class or object.
         *
         *  @return Array with all the ancestors.
         */
        function getAncestors( $classname ) {
            
            // If the variable is an object, get the class name
            if ( is_object( $classname ) ) {
                $classname = get_class( $classname );
            }

            // Start with no ancestors
            $ancestors = array();

            // Get the parent class
            $father = get_parent_class( $classname );

            // Recursive find the ancestors
            if ( $father != '' ) {
                $ancestors = YDObjectUtil::getAncestors( $father );
                $ancestors[] = $father;
            }

            // Return the list of ancestors
            return array_reverse( $ancestors );

        }

        /**
         *  This function will check if the specified object has the method 
         *  specified, and will raise a fatal error if not. This function does not
         *  return any value.
         *
         *  @param $obj Object you want to check.
         *  @param $method Method you are looking for.
         */
        function failOnMissingMethod( $obj, $method ) {

            // Check if the method exists and raise an error if needed
            if ( ! method_exists( $obj, $method ) ) {
                new YDFatalError(
                    'Class "' . $obj . '" does  not have a function called "'
                    . $method . '" which is required for proper operation.'
                );   
            }

        }

        /**
         *  This function will serialize an object. The serialized output is
         *  GZip compressed to save space.
         *
         *  @param $obj Object to serialize.
         */
        function serialize( $obj ) {

            // We first serialize and then compress
            return gzcompress( serialize( $obj ) );

        }

        /**
         *  This function will unserialize an object.
         *
         *  @param $obj Object to unserialize.
         */
        function unserialize( $obj ) {

            // We first decompress and then deserialize
            return unserialize( gzuncompress( $obj ) );

        }

    }

?>
