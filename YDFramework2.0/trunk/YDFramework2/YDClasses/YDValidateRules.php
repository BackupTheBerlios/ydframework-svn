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
		function email( $val, $opts ) {
			return YDValidateRules::regex( $val, '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/' );
		}

		/** 
		 *	This function returns true if the variable contains only letters.
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	(not required)
		 */
		function lettersonly( $val, $opts ) {
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
		function character( $val, $opts ) {
			return ( strlen( strval( $val ) ) == 1 ) && YDValidateRules::lettersonly( $val, array() );
		}

		/** 
		 *	This function returns true if the variable contains only letters and numbers.
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	(not required)
		 */
		function alphanumeric( $val, $opts ) {
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
		function numeric( $val, $opts ) {
			return YDValidateRules::regex( $val, '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/' );
		}

		/** 
		 *	This function returns true if the variable contains only contains one digit (0-9).
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	(not required)
		 */
		function digit( $val, $opts ) {
			return ( strlen( strval( $val ) ) == 1 ) && YDValidateRules::numeric( $val, array() );
		}

		/** 
		 *	This function returns true if the variable contains no punctuation characters.
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	(not required)
		 */
		function nopunctuation( $val, $opts ) {
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
		function nonzero( $val, $opts ) {
			$result = YDValidateRules::regex( $val, '/^-?[1-9][0-9]*/' );
			if ( $result === true ) {
				$result = YDValidateRules::numeric( $val, array() ) ? true : false;
			}
			return $result;
		}

		/** 
		 *	This function returns true if the variable is in the array specified in the options.
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	(not required)
		 */
		function in_array( $val, $opts ) {
			return in_array( $val, $opts, true );
		}

		/** 
		 *	This function returns true if the variable is not in the array specified in the options.
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	(not required)
		 */
		function not_in_array( $val, $opts ) {
			return ! in_array( $val, $opts, true );
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
		function uploadedfile( $val, $opts ) {
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

	}

?>