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
	
	// Constants
	define( 'YD_LOCALE_KEY', 'YD_LOCALE' );
	
	// This is the list of support languages
	$GLOBALS[ 'YD_LANGUAGES' ] = array(
		'en'  => array( 'eng', 'english', 'en_US' ),
		'cs'  => array( 'csy', 'czech' ),
		'csy' => array( 'czech', 'cs' ),
		'da'  => array( 'dan', 'danish' ),
		'dan' => array( 'danish', 'da' ),
		'nl'  => array( 'nld', 'dutch' ),
		'nld' => array( 'dutch', 'nl' ),
		'nlb' => array( 'belgian', 'dutch-belgian', 'nl_BE', 'nl' ),
		'ena' => array( 'australian', 'english-aus', 'en_AU', 'en' ),
		'enc' => array( 'canadian', 'english-can', 'en_CA', 'en' ),
		'enz' => array( 'english-nz', 'en_NZ', 'en' ),
		'eng' => array( 'uk', 'english-uk', 'en_UK', 'en' ),
		'us'  => array( 'american', 'american english', 'american-english', 'english-american', 'english-us', 'english-usa', 'enu', 'usa', 'en_US', 'en' ),
		'fi'  => array( 'fin', 'finnish' ),
		'fin' => array( 'finnish', 'fi' ),
		'fr'  => array( 'fra', 'french' ),
		'fra' => array( 'french', 'fr_FR', 'fr_FR', 'fr' ),
		'frb' => array( 'french-belgian', 'fr_BE', 'fr' ),
		'frc' => array( 'french-canadian', 'fr_CA', 'fr' ),
		'frs' => array( 'french-swiss', 'fr_SW', 'fr' ),
		'de'  => array( 'deu', 'german' ),
		'deu' => array( 'german', 'de' ),
		'dea' => array( 'german-austrian', 'de_AU', 'de' ),
		'des' => array( 'german-swiss', 'swiss', 'de_SW', 'de' ),
		'el'  => array( 'ell', 'greek' ),
		'ell' => array( 'greek', 'el' ),
		'hu'  => array( 'hun', 'hungarian' ),
		'hun' => array( 'hungarian', 'hu' ),
		'is'  => array( 'isl', 'icelandic' ),
		'isl' => array( 'icelandic', 'is' ),
		'it'  => array( 'ita', 'italian' ),
		'ita' => array( 'italian', 'it' ),
		'its' => array( 'italian-swiss', 'it', 'it_SW' ),
		'no'  => array( 'nor', 'norwegian' ),
		'nor' => array( 'norwegian-bokmal', 'no', 'no_BO' ),
		'non' => array( 'norwegian-nynorsk', 'no', 'no_NY' ),
		'pl'  => array( 'plk', 'polish' ),
		'plk' => array( 'polish', 'pl' ),
		'pt'  => array( 'ptg', 'portuguese' ),
		'ptg' => array( 'portuguese', 'pt' ),
		'ptb' => array( 'portuguese-brazil', 'pt_BR', 'pt' ),
		'ru'  => array( 'rus', 'russian' ),
		'rus' => array( 'russian', 'ru' ),
		'sk'  => array( 'sky', 'slovak' ),
		'sky' => array( 'slovak', 'sk' ),
		'es'  => array( 'spanish' ),
		'esp' => array( 'spanish', 'es' ),
		'esm' => array( 'spanish-mexican', 'es_ME', 'es' ),
		'esn' => array( 'spanish-modern', 'es_MO', 'es' ),
		'sv'  => array( 'sve', 'swedish' ),
		'sve' => array( 'swedish', 'sv' ),
		'tr'  => array( 'trk', 'turkish' ),
		'trk' => array( 'turkish', 'tr' ),
	);

	/**
	 *	This is the class that hold the locale information.
	 */
	class YDLocale extends YDBase {

		/**
		 *	@param	$locale	Sets the current locale
		 */
		function set( $locale ) {
		
			// Check if the locale exists or not
			if ( ! array_key_exists( strtolower( $locale ), $GLOBALS[ 'YD_LANGUAGES' ] ) ) {
				trigger_error( 'You specified an invalid locale: "' . strtolower( $locale ) . '"', YD_ERROR );
			}

			// Get the locales
			$locales = $GLOBALS[ 'YD_LANGUAGES' ][ $locale ];
			
			// Add the current locale
			array_unshift( $locales, $locale );
			array_unshift( $locales, LC_ALL );
			
			// Add another one
			array_push( $locales, substr( $locale, 0, 2 ) . '_' . strtoupper( substr( $locale, 0, 2 ) ) );
			
			// Set the locale
			$result = call_user_func_array( 'setlocale', array_unique( $locales ) );
			
			// Trigger an error if failed
			if ( ! $result ) {
				trigger_error( 'Your platform does not support the locale "' . strtolower( $locale ) . '"', YD_ERROR );
			}
			
			// Set the locale
			YDConfig::set( YD_LOCALE_KEY, $locale );
		
		}

		/**
		 *	@returns	The current locale.
		 */
		function get() {
		
			// Set the default locale
			YDConfig::set( YD_LOCALE_KEY, 'en', false );
		
			// Return the setting
			return strtolower( YDConfig::get( YD_LOCALE_KEY ) );
		
		}

	}

?>
