<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 */
	class YDValidateRules extends YDBase {

		// Value is not empty
		function required( $val, $opts ) {
			return true;
		}

		// Value must not exceed n characters
		function maxlength( $val, $opts ) {
			return true;
		}

		// Value must have more than n characters
		function minlength( $val, $opts ) {
			return true;
		}

		// Value must have between m and n characters
		function rangelength( $val, $opts ) {
			return true;
		}

		// Value must pass the regular expression
		function regex( $val, $opts ) {
			return true;
		}

		// Value is a correctly formatted email
		function email( $val, $opts ) {
			return true;
		}

		// Value must contain only letters
		function lettersonly( $val, $opts ) {
			return true;
		}

		// Value must contain only letters and numbers
		function alphanumeric( $val, $opts ) {
			return true;
		}

		// Value must be a number
		function numeric( $val, $opts ) {
			return true;
		}

		// Value must not contain punctuation characters
		function nopunctuation( $val, $opts ) {
			return true;
		}

		// Value must be a number not starting with 0
		function nonzero( $val, $opts ) {
			return true;
		}

		// This rule allows to use an external function/method for validation, either by registering it or by passing a
		// callback as a format parameter.
		function callback( $val, $opts ) {
			return true;
		}

		// The rule allows to compare the values of two form fields. This can be used for e.g. 'Password repeat must 
		// match password' kind of rule. 
		function compare( $val, $opts ) {
			return true;
		}

	}

?>