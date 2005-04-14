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
     *	This class contains abstract functions implementing the validation rules.
     */
    class YDValidateRules extends YDBase {

        /**
         *	This function returns false if the variable is empty, otherwise, it returns true.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function required( $val, $opts='' ) {
            if ( is_null( $val ) || strlen( $val ) == 0 ) {
                return false;
            } else {
                return true;
            }
        }

        /**
         *	This function returns true if the variable is smaller than the specified length, otherwise, it returns
         *	false.
         *
         *	@param $val		The value to test.
         *	@param $opts	The maximum length of the variable.
         */
        function maxlength( $val, $opts ) {
            if ( strlen( $val ) <= intval( $opts ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the variable is bigger than the specified length, otherwise, it returns
         *	false.
         *
         *	@param $val		The value to test.
         *	@param $opts	The minimum length of the variable.
         */
        function minlength( $val, $opts ) {
            if ( strlen( $val ) >= $opts ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the length of the variable is contained in the indicated range.
         *
         *	@param $val		The value to test.
         *	@param $opts	Array containing the minimum and maximum length.
         */
        function rangelength( $val, $opts ) {
            if ( ( strlen( $val ) >= intval( $opts[0] ) ) && ( strlen( $val ) <= intval( $opts[1] ) ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the variable matches the given regular expression (PCRE syntax), otherwise, it
         *	returns false.
         *
         *	@param $val		The value to test.
         *	@param $opts	The regular expression to use (PCRE syntax).
         */
        function regex( $val, $opts ) {
            if ( preg_match( $opts, $val ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the variable is a correctly formatted email address.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function email( $val, $opts='' ) {
            return YDValidateRules::regex( $val, '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/' );
        }

        /**
         *	This function returns true if the variable contains only letters.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function lettersonly( $val, $opts='' ) {
            $result = YDValidateRules::regex( $val, '/([\D^ ]+)$/' );
            if ( $result === true ) {
                $result = YDValidateRules::nopunctuation( $val, array() ) ? true : false;
            }
            return $result;
        }

        /**
         *	This function returns true if the variable contains only single character.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function character( $val, $opts='' ) {
            return ( strlen( strval( $val ) ) == 1 ) && YDValidateRules::lettersonly( $val, array() );
        }

        /**
         *	This function returns true if the variable contains only letters and numbers.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function alphanumeric( $val, $opts='' ) {
            $result = YDValidateRules::regex( $val, '/([\w^ ]+)$/' );
            if ( $result === true ) {
                $result = YDValidateRules::nopunctuation( $val, array() ) ? true : false;
            }
            return $result;
        }

        /**
         *	This function returns true if the variable contains only numbers.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function numeric( $val, $opts='' ) {
            return YDValidateRules::regex( $val, '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/' );
        }

        /**
         *	This function returns true if the variable contains only contains one digit (0-9).
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function digit( $val, $opts='' ) {
            return ( strlen( strval( $val ) ) == 1 ) && YDValidateRules::numeric( $val, array() );
        }

        /**
         *	This function returns true if the variable contains no punctuation characters.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function nopunctuation( $val, $opts='' ) {
            return YDValidateRules::regex( $val, '/^[^().\/\*\^\?#!@$%+=,\"\'><~\[\]{}]+$/' );
        }

        /**
         *	This function returns true if the variable is a number not starting with 0.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         *
         *	@todo
         *		Allows things other than digits
         */
        function nonzero( $val, $opts='' ) {
            $result = YDValidateRules::regex( $val, '/^-?[1-9][0-9]*/' );
            if ( $result === true ) {
                $result = YDValidateRules::numeric( $val, array() ) ? true : false;
            }
            return $result;
        }

        /**
         *	This function returns true if the variable is exactly as specified in the options.
         *
         *	@param $val		The value to test.
         *	@param $opts	The value to compare with.
         */
        function exact( $val, $opts ) {
            return $val === $opts;
        }

        /**
         *	This function returns true if the variable is in the array specified in the options.
         *
         *	@param $val		The value to test.
         *	@param $opts	The array in which the value should be.
         */
        function in_array( $val, $opts ) {
            return in_array( $val, $opts, true );
        }

        /**
         *	This function returns true if the variable is not in the array specified in the options.
         *
         *	@param $val		The value to test.
         *	@param $opts	The array in which the value should not be.
         */
        function not_in_array( $val, $opts ) {
            return ! in_array( $val, $opts, true );
        }

        /**
         *	This function returns true if the variable is in the array specified in the options. This function is
         *  case insensitive.
         *
         *	@param $val		The value to test.
         *	@param $opts	The array in which the value should be.
         */
        function i_in_array( $val, $opts ) {
            foreach ( $val as $i=>$j ) {
                if ( is_string( $val ) ) {
                    $val[$i] = strtolower( $i );
                }
            }
            return in_array( $val, $opts, true );
        }

        /**
         *	This function returns true if the variable is not in the array specified in the options. This function is
         *  case insensitive.
         *
         *	@param $val		The value to test.
         *	@param $opts	The array in which the value should not be.
         */
        function i_not_in_array( $val, $opts ) {
            foreach ( $val as $i=>$j ) {
                if ( is_string( $val ) ) {
                    $val[$i] = strtolower( $i );
                }
            }
            return ! in_array( $val, $opts, true );
        }

        /**
         *	This rule checks if a string contains the maximum specified words or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	The maximum number of words allowed.
         */
        function maxwords( $val, $opts ) {
            return $opts <= explode( ' ', trim( $val ) );
        }

        /**
         *	This rule checks if a string contains the minimum specified words or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	The minimum number of words allowed.
         */
        function minwords( $val, $opts ) {
            return $opts >= explode( ' ', trim( $val ) );
        }

        /**
         *	This rule allows to use an external function/method for validation, either by registering it or by passing a
         *	callback as a format parameter.
         *
         *	@param $val		The value to test.
         *	@param $opts	The name of the function to use.
         */
        function callback( $val, $opts ) {
            return call_user_func( $opts, $val );
        }

        /**
         *	This rule checks if a file was uploaded.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function uploadedfile( $val, $opts='' ) {
            return is_uploaded_file( $val['tmp_name'] ) && ( filesize( $val['tmp_name'] ) > 0 );
        }

        /**
         *	This rule checks if a file upload exceeded the file size or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	The maximum file size in bytes.
         */
        function maxfilesize( $val, $opts ) {
            return ( filesize( $val['tmp_name'] ) <= intval( $opts ) );
        }

        /**
         *	This rule checks if a file upload had the right mime type.
         *
         *	@param $val		The value to test.
         *	@param $opts	The required mime type or an array of allowed mime types.
         */
        function mimetype( $val, $opts ) {
            if ( ! is_array( $opts ) ) {
                $opts = array( $opts );
            }
            return in_array( $val['type'], $opts );
        }

        /**
         *	This rule checks if a file upload had the filename based on a regular expression.
         *
         *	@param $val		The value to test.
         *	@param $opts	The regex to which the filename should match.
         */
        function filename( $val, $opts ) {
            return YDValidateRules::regex( $val['name'], $opts );
        }

        /**
         *	This rule checks if a file upload has the right file extension.
         *
         *	@param $val		The value to test.
         *	@param $opts	The file extension it should match (can also be an array of extensions).
         */
        function extension( $val, $opts ) {
            if ( ! is_array( $opts ) ) {
                $opts = array( $opts );
            }
            ereg( ".*\.([a-zA-Z0-9]{0,5})$", $val['name'], $regs );
            return in_array( $regs[1], $opts );
        }

        /**
         *	This checks if the array with day, month and year elements is a valid date or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function date( $val, $opts='' ) {
            if ( ! is_array( $val ) ) {
                return false;
            }
            if ( ! isset( $val['month'] ) || ! is_numeric( $val['month'] ) ) {
                return false;
            }
            if ( ! isset( $val['day'] ) || ! is_numeric( $val['day'] ) ) {
                return false;
            }
            if ( ! isset( $val['year'] ) || ! is_numeric( $val['year'] ) ) {
                return false;
            }
            return checkdate( intval( $val['month'] ), intval( $val['day'] ), intval( $val['year'] ) );
        }

        /**
         *	This checks if the array with hours and minutes elements is a valid time or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function time( $val, $opts='' ) {
            if ( ! is_array( $val ) ) {
                return false;
            }
            if ( ! isset( $val['hours'] ) || ! is_numeric( $val['hours'] ) ) {
                return false;
            }
            if ( ! isset( $val['minutes'] ) || ! is_numeric( $val['minutes'] ) ) {
                return false;
            }
            if ( intval( $val['hours'] ) < 0 || intval( $val['hours'] ) > 23 ) {
                return false;
            }
            if ( intval( $val['minutes'] ) < 0 || intval( $val['minutes'] ) > 59 ) {
                return false;
            }
            if ( isset( $val['seconds'] ) && intval( $val['seconds'] ) < 0 || intval( $val['seconds'] ) > 59 ) {
                return false;
            }
            return true;
        }

        /**
         *	This checks if the array with hours and minutes, days, months and years elements is a valid datetime or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function datetime( $val, $opts='' ) {
            return YDValidateRules::date( $val ) && YDValidateRules::time( $val );
        }

    }

?>
