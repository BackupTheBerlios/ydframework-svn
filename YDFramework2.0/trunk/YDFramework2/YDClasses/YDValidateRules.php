<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

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
			if ( empty( $val ) ) {
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
			if ( strlen( $val ) < $opts ) {
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
			if ( strlen( $val ) > $opts ) {
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
			if ( ( strlen( $val ) > $opts[0] ) && ( strlen( $val ) < $opts[1] ) ) {
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
			return YDValidateRules::regex( $val, '/^[a-zA-Z]+$/' );
		}

		/** 
		 *	This function returns true if the variable contains only letters and numbers.
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	(not required)
		 */
		function alphanumeric( $val, $opts ) {
			return YDValidateRules::regex( $val, '/^[a-zA-Z0-9]+$/' );
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
		 */
		function nonzero( $val, $opts ) {
			return YDValidateRules::regex( $val, '/^-?[1-9][0-9]*/' );
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
			return is_uploaded_file( $val['tmp_name'] );
		}

		/** 
		 *	This rule checks if a file upload exceeded the file size or not.
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	The maximum file size in bytes.
		 */
		function maxfilesize( $val, $opts ) {
			return ( $val['size'] <= intval( $opts ) );
		}

		/** 
		 *	This rule checks if a file upload had the right mime type.
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	The required mime type.
		 */
		function mimetype( $val, $opts ) {
			return ( $val['type'] == $opts );
		}

		/** 
		 *	This rule checks if a file upload had the right mime type.
		 *
		 *	@param $val		The value to test.
		 *	@param $opts	The regex to which the filename should match.
		 */
		function filename( $val, $opts ) {
			return YDValidateRules::regex( $val['name'], $opts );
		}

	}

?>