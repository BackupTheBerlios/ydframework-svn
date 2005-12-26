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
     *  This config defines the ajax response enconding
     *  Default: iso-8859-15.
     */
    YDConfig::set( 'YD_AJAX_ENCODING', "iso-8859-15", false );


    /**
     *  This config defines the prefix for javascript functions
     *  Default: __ydf.
     */
    YDConfig::set( 'YD_AJAX_PREFIX', "__ydf", false );


    /**
     *  This config defines the html tag to use
     *  Default: </head>
     */
    YDConfig::set( 'YD_AJAX_TAG', "</head>", false );


    /**
     *  This config defines if ajax code must be placed after tag (true) or before tag (false).
     *  Default: false.
     */
    YDConfig::set( 'YD_AJAX_AFTERTAG', false, false );


    /**
     *  This config defines custom debug value
     *  Default: null.
     */
    YDConfig::set( 'YD_AJAX_DEBUG', null, false );


    /**
     *  Include the needed libraries. 
     */
	require_once( dirname( __FILE__ ) . '/xajax.inc.php');
    YDInclude( 'YDRequest.php' );


    /**
     *  Class definition for the YDAjax addon.
     */
    class YDAjax extends xajax{

        /**
         *	This is the class constructor for the YDAjax class.
         *
         *	@param $template		Default template object
         *	@param $form			Default form object.
         */
        function YDAjax( & $template, & $form){

			// prefix is used in some methods
			$this->prefix = YDConfig::get( 'YD_AJAX_PREFIX' );

			// if YD_AJAX_DEBUG is set, lets use it. otherwise verify YD_DEBUG
			if (!is_null( YDConfig::get( 'YD_AJAX_DEBUG' ) )) $debug = YDConfig::get( 'YD_AJAX_DEBUG' );
			else if (YDConfig::get("YD_DEBUG") == 0)          $debug = false;
			else                                              $debug = true;

			// create default url
			$this->ajaxURL = YDRequest::getNormalizedUri();
		
			// create ajax object
			$this->xajax( "", $this->prefix, $debug );
			
			// initilize form
			$this->form = & $form;

			// initilize template
			$this->template = & $template;
			
			// custom javascript (we need more than the javascript provided by xajax)
			$this->customjs          = array();
			$this->customjsVariables = array();
			$this->customjsTop       = array();
			$this->customjsBottom    = array();

			// response object
			$this->response = new YDAjaxResponse( YDConfig::get( 'YD_AJAX_ENCODING' ) );
			$this->responseCharset = YDConfig::get( 'YD_AJAX_ENCODING' );
			
			// effects added
			$this->includeEffects = true;
		}
		

		// internal method. replace "<head>" with "<head> and all ajax javascript (containing our custom javascript too)
		function assignJavascript($tpl_source, &$smarty){
			
			$separator = strpos( $this->ajaxURL, '?' ) == false ? '?' : '&';
		
			// add xajax includes
			$html  = "\t<script type=\"text/javascript\">var xajaxRequestUri=\"". $this->ajaxURL ."\";</script>\n";
			$html .= "\t<script type=\"text/javascript\" src=\"". $this->ajaxURL . $separator ."xajaxjs=xajaxjs\"></script>\n";
			
			// add effect includes
			if ($this->includeEffects)
				$html .= "\t<script type=\"text/javascript\" src=\"". $this->ajaxURL . $separator ."ydajax_includes=ydajax_includes\"></script>\n";
		
			// add js code
			if (!empty($this->customjsVariables) || !empty($this->customjs) || !empty($this->customjsTop) || !empty($this->customjsBottom)){

				// add initial js tag
				$html .= "\t<script type=\"text/javascript\">\n";

				// add custom TOP code
				if (!empty($this->customjsTop))
					$html .= "\t\t". implode( "\n\t\t", $this->customjsTop ) ."\n";

				// add variables
				foreach( $this->customjsVariables as $variable => $declaration )
					$html .= "\t\tvar ". $variable ." = ". $declaration .";\n";

				// add functions
				foreach( $this->customjs as $function => $declaration )
					$html .= "\t\tfunction ". $function ."{". $declaration ."}\n";

				// add custom BOTTOM code
				if (!empty($this->customjsBottom))
					$html .= "\t\t". implode( "\n\t\t", $this->customjsBottom ) ."\n";

				// add end tag
				$html .= "\t</script>\n";
			}

			// get html tag to use
			$tag = YDConfig::get( 'YD_AJAX_TAG' );

			// add js code to template
			if ( YDConfig::get( 'YD_AJAX_AFTERTAG' )) return eregi_replace( $tag, $tag  ."\n". $html, $tpl_source );
			else                                      return eregi_replace( $tag, $html ."\n". $tag,  $tpl_source );
		}


        /**
         *	This function adds custom javascript to the template.
         *
         *	@param $js	Javascript code.
         *	@param $order	Zone to place code (top, bottom, topBefore, topAfter, bottomBefore, bottomAfer)
         */
		function addJavascript( $js, $order = 'top' ){
		
			switch( strtolower($order) ){
				case 'top' :
				case 'topafter' :  array_push( $this->customjsTop, $js ); break;
				case 'topbefore' : array_unshift( $this->customjsTop, $js ); break;
				
				case 'bottom' :    
				case 'bottomafter' :  array_push( $this->customjsBottom, $js ); break;
				case 'bottombefore' : array_unshift( $this->customjsBottom, $js ); break;
				
				default : die( "Order ". $order ." is not supported" );
			}
		}


        /**
         *	This function adds custom javascript to a response.
         *
         *	@param $js		Javascript code.
         */
		function addScript( $js ){

			return $this->response->addScript( $js );
		}


        /**
         *	This function enables/disables the js effects headers inclusion in template
         *
         *	@param $js		Javascript code.
         */
		function ignoreEffects( $flag = true ){

			$this->includeEffects = (bool) !$flag;
		}
		

        /**
         *	This function creates a effect object
         *
         *	@param $effect			Effect name (string)
         *	@param $elementToAffect		Form element name where the effect will be assigned
         *	@param $persistent		(Optional) If true, all next effects will wait to this effect stop
         *	@param $options			(Optional) Custom js options
         */
		function createEffect( $effect, $elementToAffect, $persistent = false, $options = array() ){

			$effect = strtolower( $effect );
			
			if (!in_array( $effect, array( 'fadeout', 'fadein', 'zoomin' ) ))
				die( 'Effect '. $effect .' is not supported');

			// include js effect headers
			$this->includeEffects = true;

			// if elementToAffect is null we affect the same element
			if (is_null( $elementToAffect )) $elementToAffect = "this";

			// if is a form element we get formname_element  TODO: get id ($elem->getAttribute('id')), else is a id
			else if ( $this->form->isElement( $elementToAffect ) ){
//				$elementToAffect = & $this->form->getElement( $elementToAffect );
//				$elementToAffect = $elementToAffect->getAttribute('id');
				$elementToAffect = $this->form->_name .'_'. $elementToAffect;
			}

			return array( 'element' => $elementToAffect, 'type' => $effect, 'persistent' => $persistent, 'options' => $options );
		}


        /**
         *	This class assigns ajax with a different form object
         *
         *	@param $form		Form object.
         */
		function setForm( & $form ){
			$this->form = $form;
		}


        /**
         *	This class enables/disables debug
         *
         *	@param $flag		Boolean argument
         */
		function setDebug( $flag = true){
			
			if ($flag == true)	{ $this->debugOn();  $this->statusMessagesOn();  }
			else				{ $this->debugOff(); $this->statusMessagesOff(); }
		}


        /**
         *	This class enables debug and it's an alias to setDebug
         */
		function dump(){
			
			$this->debugOn();
		}


        /**
         *	This method assigns a server function and arguments to a form element event.
         *
         *	@param $formElementName		Form element name (string) or js function.
         *	@param $serverFunction		Array with class and method.
         *	@param $arguments		Default arguments for this function (null, string or array of strings).
         *	@param $event			Event name (auto-detection by default when using null).
         *	@param $options			Custom options.
         *	@param $effects			Effect or array of effects to execute on event (but before ajax call).
         */		
		 function addEvent( $formElementName, $serverFunction, $arguments = null, $event = null, $options = null, $effects = null ){ 
		 
		 	if (!is_array($options)) $options = array( $options );
		 
			// register function in xajax
			$this->registerFunction( $serverFunction );

			// serverFunction must be an array with a class and the method (get function name)
			$serverFunctionName = $serverFunction[1];

			if (!is_array( $options )) $options = array( $options );
			
			// get function name
			$functionName = $this->sWrapperPrefix . $serverFunctionName .'('. $this->computeFunctionArguments( $arguments, $options ) .');';

			// process effects
			if (!is_null( $effects )){
		
				// if is one effect, we should create an array of effects
				if (isset( $effects['element'] )) $effects = array( $effects );

				// get last effect and delete it from array
				$effectLAST = array_pop( $effects );
			
				// compute js form effects (only the first ones)
				$effectsJs = $this->getJsEffect( $effectLAST, $functionName );

				// cycle effects (from last to first) and create js				
				foreach( array_reverse($effects) as $effect )
					$effectsJs = $this->getJsEffect( $effect, $effectsJs );

				// function is now the effect js created
				$functionName = $effectsJs;
			}

			// if formElementName is a js function we are not assigning an event to a form element but creating a js function
			if (ereg ("^(.*)\(\)$", $formElementName, $function)){
				$this->customjs[ $function[0] ] = $functionName;
				return;
			}

			// get element object
			$formElement = & $this->form->getElement( $formElementName );
			
			// if event is null we must check the form element type to compute the default event
			if (is_null( $event )) $event = $this->getDefaultEvent( $formElement->getType() );

			// parse event
			$event = strtolower( $event );

			// get previous attribute
			$previous = is_null( $formElement->getAttribute($event) ) ? '' : trim( $formElement->getAttribute($event) );

			// if previous attribute don't have ';' in the end we should add it
			if (strlen( $previous ) > 0 && $previous[ strlen( $previous ) - 1 ] != ';' ) $previous .= ';';

			if (in_array('replace', $options))
				return $formElement->setAttribute( $event, $functionName );

			if (in_array('prepend', $options))
				return $formElement->setAttribute( $event, $functionName . $previous );
			
			$formElement->setAttribute( $event, $previous . $functionName );
		}
		
		
		// internal method. return js for a effect
		function getJsEffect( $effect, $function ){

			// add persistense settings if there's a function
			if ($function != "" && $effect['persistent'])
				$effect['options']['afterFinish'] = 'function(effect) {'. $function .'}';

			switch( $effect['type'] ){
				case 'fadeout' :	// add fadeout custom options
									if (!isset( $effect['options']['from'] ))		$effect['options']['from'] = 0.99;
									if (!isset( $effect['options']['to'] ))			$effect['options']['to'] = 0;
									if (!isset( $effect['options']['duration'] ))	$effect['options']['duration'] = 1;

									$options = YDArrayUtil::implode( $effect['options'], ':', ',' );

									return "new Effect.Opacity('". $effect['element'] ."', Object.extend( {". $options ."} ) )";

				case 'fadein' :		// add fadein custom options
									if (!isset( $effect['options']['from'] ))		$effect['options']['from'] = 0;
									if (!isset( $effect['options']['to'] ))			$effect['options']['to'] = 0.99;
									if (!isset( $effect['options']['duration'] ))	$effect['options']['duration'] = 1;

									$options = YDArrayUtil::implode( $effect['options'], ':', ',' );

									return "new Effect.Opacity('". $effect['element'] ."', Object.extend( {". $options ."} ) )";
			
				case 'zoomin' : 	// add zoomin custom options
									if (!isset( $effect['options']['from'] ))		$effect['options']['from'] = 0;
									if (!isset( $effect['options']['to'] ))			$effect['options']['to'] = 0.99;
									if (!isset( $effect['options']['duration'] ))	$effect['options']['duration'] = 1;
									if (!isset( $effect['options']['scaleFrom'] ))			$effect['options']['scaleFrom'] = 0;
									if (!isset( $effect['options']['scaleFromCenter'] ))	$effect['options']['scaleFromCenter'] = 'true';
									
									return "new Effect.Parallel([ new Effect.Scale('". $effect['element'] ."', 100, { sync: true, scaleFrom: ". $effect['options']['scaleFrom'] .", scaleFromCenter: ". $effect['options']['scaleFromCenter'] ."}), 
																  new Effect.Opacity('". $effect['element'] ."', { sync: true, to: ". $effect['options']['to'] .", from: ". $effect['options']['from'] ." } ) ], 
																{ duration: ". $effect['options']['duration'] ." }
															 );" ;
			}

		}


        /**
         *	This method hides a html element
         *
         *	@param $element     Form element name or generic html id
         *	@param $hidden      Boolean flag (true = hide, false = show)
         *	@param $onlyIfIE    Hide element only if browser is IE (Optional)
         */	
		function hideElement( $element, $hidden = true, $onlyIfIE = false ){

			// if is a form element, get its id. otherwise use element as id
			if ($this->form->isElement( $element )){
				$element = & $this->form->getElement( $element );
				$element = $element->getAttribute('id');
			}

			$bi = new YDBrowserInfo();

			if ( ($onlyIfIE == true && $bi->browser == 'ie') || $onlyIfIE == false){
				if ($hidden) $this->response->addScript( "document.getElementById('". $element ."').style.display = 'none';" );
				else         $this->response->addScript( "document.getElementById('". $element ."').style.display = 'block';" );
			}
			
		}
		

        /**
         *	This method adds a effect to a response
         *
         *	@param $effectObj	Effect element
         */	
		function addEffect( $effectObj ){
		
			$this->response->addScript(  $this->getJsEffect( $effectObj, '')  );
		}


		// internal method. compute js arguments string 
		function computeFunctionArguments( $arguments, $options ){
			
			// if there are not arguments return empty string
			if (is_null($arguments)) return "";

			// if is one argument and it's the form name, we want the form values
			if (is_string( $arguments ) && $this->form->_name == $arguments) return "xajax.getFormValues('". $arguments . "')";

			// if there's only one argument or option, create arrays
			if (!is_array( $arguments )) $arguments = array( $arguments );
			if (!is_array( $options ))   $options   = array( $options );

			// initialize arguments
			$args = array();

			// cycle arguments to create js function to get values
			foreach ($arguments as $arg){

				// if argument is numeric just add it and continue
				if (is_numeric( $arg )) { $args[] = $arg; continue; }
					
				// if there's a form defined, test if this argument belongs to it
				if (!is_null( $this->form ) && $this->form->isElement( $arg )){
						
					// get element object
					$elem = & $this->form->getElement( $arg );

					// get element type to invoke the custom js to get the value
					switch( $elem->getType() ){
						case 'text' :			$args[] = $this->getValueText(           $elem, $options ); break;
						case 'password' :		$args[] = $this->getValuePassword(       $elem, $options ); break;
						case 'textarea' :		$args[] = $this->getValueTextarea(       $elem, $options ); break;
						case 'radio' :			$args[] = $this->getValueRadio(          $elem, $options ); break;
						case 'checkbox' :		$args[] = $this->getValueCheckbox(       $elem, $options ); break;
						case 'dateselect' :		$args[] = $this->getValueDateSelect(     $elem, $options ); break;
						case 'datetimeselect' :	$args[] = $this->getValueDateTimeSelect( $elem, $options ); break;
						case 'timeselect' :		$args[] = $this->getValueTimeSelect(     $elem, $options ); break;
						case 'span' :			$args[] = $this->getValueSpan(           $elem, $options ); break;
						case 'select' :			$args[] = $this->getValueSelect(         $elem, $options ); break;
																							
						default : die ('Element type "'. $elem->getType() .'" is not supported as dynamic argument');
					}
							
				continue;
				}
						
				// if it's not a form element just parse the argument string (we don't want " and ' on code)
				$args[] = "'". htmlentities( $arg ) ."'";
			}
						
			// convert arguments (we don't want " and ' on code)
			return implode( ",", $args );
		}


        /**
         *	This method adds confirmation to a element event
         *
         *	@param $formElementName		Form element name or js function.
         *	@param $message			Message to display
         *	@param $event			Event name (auto-detection by default).
         *	@param $dependence		Element name or array of names that this element depends of
         */		
		function addConfirmation( $formElementName, $message, $event = null, $dependence = null){

			if (!is_null( $dependence )){
				$jsvariable = $this->prefix . 'change'. $formElementName .'var';
				$this->addDependence( $dependence, $jsvariable );
			}

			// check element
			if (ereg ("^(.*)\(\)$", $formElementName, $function)){
			
				if (!isset( $this->customjs[ $function[0] ] )) die( "Function ". $function[0] ." is not defined." );
				
				// if this is a dependent element
				if (!is_null( $dependence )) $this->customjs[ $function[0] ] = 'if ('. $jsvariable .' == false || confirm("'. addslashes( $message ) .'")) { '. $this->customjs[ $function[0] ] .' }';
				else                         $this->customjs[ $function[0] ] = 'if (confirm("'. addslashes( $message ) .'")) { '. $this->customjs[ $function[0] ] .' }';

				return;				
			}

			// get element
			$elem = & $this->form->getElement( $formElementName );

			// check default event
			if (is_null($event)) $event = $this->getDefaultEvent( $elem->getType() );

			// check if attribute exist
			$attribute = $elem->getAttribute( $event );
			if (is_null($attribute)) die( "Element ". $formElementName ." doesn't have attribute ". $event );

			// create confirmation function name
			$function = $this->prefix .'confirm'. $event . $formElementName .'()';

			// if this is a dependent element
			if (!is_null( $dependence )) $this->customjs[ $function ] = 'if ('. $jsvariable .' == false || confirm("'. addslashes( $message ) .'")) { '. $attribute .' }';
			else                         $this->customjs[ $function ] = 'if (confirm("'. addslashes( $message ) .'")) { '. $attribute .' }';

			// override element attribute
			$elem->setAttribute( strtolower($event), $function );
		}


		// internal method. This adds dependence to form elements
		function addDependence( $dependence, $jsvariable ){
		
			// add variable to custom variables
			$this->customjsVariables[ $jsvariable ] = 'false';
		
			if (!is_array( $dependence )) $dependence = array( $dependence );
		
			// cycle form elements
			foreach ( $dependence as $formElementName ){

				if (!$this->form->isElement( $formElementName )) die( "Form element ". $formElementName ." doesn't exist");
				
				// get element
				$elem = & $this->form->getElement( $formElementName );
				
				// get previous attribute onchange
				$attribute = (is_null( $elem->getAttribute( 'onchange' ))) ? '' : $elem->getAttribute( 'onchange' );
				
				// compute new attribute 'onchange'
				$elem->setAttribute('onchange', $jsvariable .' = true;'. $attribute );
			}
		
		
		}


		// internal method. Returns the default event for a type
		function getDefaultEvent( $type ){

			switch( $type ){
				case 'submit' : trigger_error('Submit buttons cannot be used in ajax because they force submittion.'); break;
				case 'image' :  trigger_error('Images cannot be used in ajax because they force submittion. Use "img" element instead.'); break;
				case 'img' :		return 'onclick';
				case 'button' :		return 'onclick';
				case 'span' :		return 'onclick';
				case 'link' :		return 'onclick';
				case 'dateselect' :	return 'onchange';
				case 'radio' :		return 'onchange';
				case 'checkbox' :	return 'onchange';
				case 'text' :		return 'onchange';
				case 'textarea' :	return 'onchange';
				case 'bbtextarea' :	return 'onchange';
				case 'password' :	return 'onchange';
				case 'select' :		return 'onchange';
					
				// if element doesn't exist return a php error
				default : trigger_error('Element type ('. $type .') is not supported in YDAjax'); break;
			}
		}


        /**
         *	This method creates an alias (a js function) to a event
         *
         *	@param $formElementName		Form element name or js function.
         *	@param $functionName		Javascript function name (string).
         *	@param $event			Event name (auto-detection by default).
         */		
		function addAlias( $formElementName, $functionName, $event = null ){

			// check element
			if (ereg ("^(.*)\(\)$", $formElementName, $function)){
			
				if (!isset( $this->customjs[ $formElementName ] )) die( "Function ". $formElementName ." is not defined." );
				
				$this->customjs[ $functionName ] = $this->customjs[ $formElementName ];
				
				return;				
			}

			// get element object
			$elem = & $this->form->getElement( $formElementName );
			
			// if we don't define a event we must check default one
			if (is_null( $event )) $event = $this->getDefaultEvent( $elem->getType() );
			
			// get attribute from element
			$attribute = $elem->getAttribute( $event );
			
			// check if attribute exist
			if (!$attribute) $attribute = 'false';
		
			$this->customjs[$functionName] = $attribute;
		}


        /**
         *	This method will process all events added
         */	
		function processEvents(){
			
			// return code from all js effect libs
			if (isset($_GET['ydajax_includes'])) return $this->includeJSlibs();

			// change template object internals			
			$this->template->register_outputfilter( array( &$this, "assignJavascript") );

			// process all requests and exit
			return $this->processRequests();
		}


		// internal method. Displays all js effect libs
		function includeJSlibs(){
		
			YDInclude( 'YDFileSystem.php' );

			$content = '';

			// add contents
			foreach ( array( 'prototype.js', 'util.js', 'effects.js', 'dragdrop.js', 'controls.js' ) as $filename){
				$file = new YDFSFile( dirname( __FILE__ ) .'/js/'. $filename );
				$content .= $file->getContents() ."\n";
			}
		
			header("Content-type: text/javascript");
			print $content;
			exit();
		}


        /**
         *	This method add a result to a form element
         *
         *	@param $formElementName		Form element name or a generic id string.
         *	@param $result			Result string or array
         *	@param $attribute		Optional attributes (optional)
         *	@param $options			Specific options (optional)
         */	
		function addResult( $formElementName, $result, $attribute = null, $options = array() ){
		
			// test if is a form element
			if (!$this->form->isElement( $formElementName ))
				return $this->response->assignId( $formElementName, $result, $attribute, $options );

			// get element
			$elem = & $this->form->getElement( $formElementName );

			return $this->response->assignResult( $elem, $result, $attribute, $options);
		}


        /**
         *	This method will process all results added with addResult
         */	
		function processResults(){

			// return XML to client browser
			return $this->response->getXML();
		}
		
		
		// internal method to create the needed js function to retrieve a text value
		function getValueText( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';
		
			// add our custom js function to retrieve this text element value (it will replace possible js functions for this element because we cannot have 2 functions with the same name)
			$this->customjs[$jsfunction] = 'return document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName().'"].value;';

			// return function invocation
			return $jsfunction;
		}


		// internal method to create the needed js function to retrieve a select value
		function getValueSelect( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';

			// if we want all values and not only the select one
			if (in_array( 'all', $options )){

				$this->customjs[$jsfunction]  = 'var __ydtmparr = new Array();' . "\n";
				$this->customjs[$jsfunction] .= 'var __ydtmpsel = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'"];' . "\n";
				$this->customjs[$jsfunction] .= 'for (i = 0; i < __ydtmpsel.length; i++){' . "\n";
				$this->customjs[$jsfunction] .= '    __ydtmparr[ __ydtmpsel.options[i].value ] = __ydtmpsel.options[i].text;' . "\n";
				$this->customjs[$jsfunction] .= '}' . "\n";
				$this->customjs[$jsfunction] .= 'return __ydtmparr;';
		
				return $jsfunction;
			}
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'return document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'"].value;';

			// return function invocation
			return $jsfunction;
		}


		// internal method to create the needed js function to retrieve a password value
		function getValuePassword( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'return document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName().'"].value;';

			// return function invocation
			return $jsfunction;
		}
		

		// internal method to create the needed js function to retrieve a textarea value
		function getValueTextarea( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'return document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName().'"].value;';

			// return function invocation
			return $jsfunction;
		}
		
		
		// internal method to create the needed js function to retrieve a span value
		function getValueSpan( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'return document.getElementById("'. $element->getForm() .'_'. $element->getName().'").innerHTML;';

			// return function invocation
			return $jsfunction;
		}		
		

		// internal method to create the needed js function to retrieve a dateselect value
		function getValueDateSelect( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'var day   = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[day]"].value;' . "\n";
			$this->customjs[$jsfunction] .= 'var month = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[month]"].value - 1;' . "\n";
			$this->customjs[$jsfunction] .= 'var year  = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[year]"].value;' . "\n";
			$this->customjs[$jsfunction] .= 'var mydate = new Date( year, month, day ); ' . "\n";
			$this->customjs[$jsfunction] .= 'return mydate.getTime() / 1000;';

			// return function invocation
			return $jsfunction;
		}


		// internal method to create the needed js function to retrieve a timeselect value
		function getValueTimeSelect( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'var hours    = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[hours]"].value;' . "\n";
			$this->customjs[$jsfunction] .= 'var minutes  = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[minutes]"].value;' . "\n";
			$this->customjs[$jsfunction] .= 'var mydate = new Date( 1970, 1, 1, hours, minutes ); ' . "\n";
			$this->customjs[$jsfunction] .= 'return mydate.getTime() / 1000;';

			// return function invocation
			return $jsfunction;
		}		


		// internal method to create the needed js function to retrieve a datetimeselect value
		function getValueDateTimeSelect( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'var day      = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[day]"].value;' . "\n";
			$this->customjs[$jsfunction] .= 'var month    = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[month]"].value - 1;' . "\n";
			$this->customjs[$jsfunction] .= 'var year     = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[year]"].value;' . "\n";
			$this->customjs[$jsfunction] .= 'var hours    = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[hours]"].value;' . "\n";
			$this->customjs[$jsfunction] .= 'var minutes  = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'[minutes]"].value;' . "\n";
			$this->customjs[$jsfunction] .= 'var mydate = new Date( year, month, day, hours, minutes ); ' . "\n";
			$this->customjs[$jsfunction] .= 'return mydate.getTime() / 1000;';

			// return function invocation
			return $jsfunction;
		}				
		

		// internal method to create the needed js function to retrieve a checkbox value
		function getValueCheckbox( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction]  =	'if (document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName().'"].checked)' . "\n";
			$this->customjs[$jsfunction] .=	'	return 1;' . "\n";
			$this->customjs[$jsfunction] .=	'return 0;';

			// return function invocation
			return $jsfunction;
		}
		

		// internal method to create the needed js function to retrieve a radio value
		function getValueRadio( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix . 'get'. $element->getName() .'()';
			
			// add custom js function
			$this->customjs[$jsfunction]  = 'var __ydftmp = document.forms["'. $element->getForm() .'"].elements["'. $element->getForm() .'_'. $element->getName() .'"];' . "\n";
			$this->customjs[$jsfunction] .= 'for (counter = 0; counter < __ydftmp.length; counter++)' . "\n";
			$this->customjs[$jsfunction] .= '	if (__ydftmp[counter].checked) return __ydftmp[counter].value;' . "\n";
			$this->customjs[$jsfunction] .= 'return false;';

			// return function invocation
			return $jsfunction;
		}		


        /**
         *	This method returns the needed xml with form errors.
         *
         *	@param $form			Form object.
         *	@param $id			Optional id to assign error messages
         *	@param $separator		Optional message separator string
         */	
		function sendFormErrors( & $form, $id = null, $separator = "* " ){
		
			return $this->response->sendFormErrors( $form, $id, $separator );
		}


        /**
         *	This method adds a js alert to ajax response
         *
         *	@param $message			Message to include in alert.
         */		
		function addAlert( $message ){
			
			$this->response->addAlert( YDStringUtil::stripSpecialCharacters( $message ) );
		}


        /**
         *	This method returns the needed xml to create a js alert
         *
         *	@param $message			Message to include in alert.
         */		
		function sendAlert( $message ){
			
			$this->addAlert( $message );
			return $this->response->getXML();
		}
}




	
class YDAjaxResponse extends xajaxResponse{
	
		function YDAjaxResponse( $encoding ){
			$this->xajaxResponse( $encoding );
		}
		
		
        /**
         *	This method assign a server result to a form element in the client side.
         *
         *	@param $formElement			Form element object.
         *	@param $result				Result value.
         *	@param $attribute			Custom attribute (auto-detection by default).
         *	@param $options				Other options.
         */		
		function assignResult( & $formElement, $result, $attribute = null, $options = array()){
	
			// get informations about the element
			$formName        = $formElement->getForm();
			$formElementName = $formElement->getName();
			$formElementType = $formElement->getType();

			// depending on the type we must invoke the custom function for the element
			switch( strtolower($formElementType) ){

				case 'submit' :
				
				case 'button' :			$this->assignButton(    $formName, $formElementName, $result, $attribute, $options ); break;
				case 'checkbox' :		$this->assignCheckbox(  $formName, $formElementName, $result, $attribute, $options ); break;
				case 'dateselect' :		$this->assignDateSelect($formName, $formElementName, $result, $attribute, $options ); break;
				case 'datetimeselect' :	$this->assignDateTimeSelect( $formName, $formElementName, $result, $attribute, $options ); break;
				case 'timeselect' :		$this->assignTimeSelect(     $formName, $formElementName, $result, $attribute, $options ); break;
				case 'bbtextarea' :		$this->assignBBtextarea(     $formName, $formElementName, $result, $attribute, $options ); break;
				case 'img' :			$this->assignImg(      $formName, $formElementName, $result, $attribute, $options ); break;
				case 'hidden' :			$this->assignHidden(   $formName, $formElementName, $result, $attribute, $options ); break;
				case 'text' :			$this->assignText(     $formName, $formElementName, $result, $attribute, $options ); break;
				case 'textarea' :		$this->assignTextarea( $formName, $formElementName, $result, $attribute, $options ); break;
				case 'radio' :			$this->assignRadio(    $formName, $formElementName, $result, $attribute, $options ); break;
				case 'password' :		$this->assignPassword( $formName, $formElementName, $result, $attribute, $options ); break;
				case 'span' :			$this->assignSpan(     $formName, $formElementName, $result, $attribute, $options ); break;
				case 'select' :			$this->assignSelect(   $formName, $formElementName, $result, $attribute, $options ); break;
				case 'image' :			$this->addAlert('Input type image cannot be used. Please use a img instead.'); break;


				// if element doesn't exist return an js alert
				default : $this->addAlert('Element type "'. $formElementType .'" is not supported in YDAjax'); break;
			}
		}


        /**
         *	This method returns the needed xml with form errors.
         *
         *	@param $form			Form object.
         *	@param $id			Optional id to assign error messages
         *	@param $separator		Optional message separator string
         */		
		function sendFormErrors( & $form, $id, $separator){
		
			// if id is null we are sending a js alert. otherwise we are assign errors to a id
			if (is_null( $id )) $this->addAlert(      $separator . implode( "\n".     $separator, array_values( $form->_errors )) );
			else                $this->assignId( $id, $separator . implode( "<br />". $separator, array_values( $form->_errors )) );
			
			return $this->getXML();
		}


        /**
         *	This method assigns a server result to a element using just the id
         *
         *	@param $id		Html element id.
         *	@param $result		Result value.
         *	@param $attribute	Custom attribute (auto-detection by default).
         *	@param $option		Other options (not used).
         */	
		function assignId( $id, $result, $attribute = null, $options = null){
		
			// if event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'innerHTML';

			// if result is an array we should export to a valid js string
			if (is_array( $result )) $result =  str_replace( "\n", "<br>", var_export( $result, true ) ) ;
		
			// escape string
			$result = addslashes( $result );
			
			// assign result to form element using the id
			$this->addScript('document.getElementById("'. $id .'").'. $attribute .' = "'. $result .'";');
		}


		// internal method to assign a value to a select element
		function assignSelect( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';

			// if we want to replace all select options
			if (is_array( $result )){
			
				// compute options
				$options = '__ydfselect.options.length = 0;';
				foreach( $result as $key => $value ){
					$key   = addslashes( $key );
					$value = addslashes( $value );
					$options .= '    __ydfselect.options[ __ydfselect.options.length  ] = new Option("'. $value .'","'. $key .'"); ' ;
				}

			// if we want to define the selected option
			}else{
			
				$options = 'for (counter = 0; counter < __ydfselect.length; counter++){
							 	if (__ydfselect[counter].value == "'. addslashes( $result ) .'"){
								   __ydfselect.selectedIndex = counter;
								}
							}';
			}

			// assign result to form element
			$this->addScript('var __ydfselect = document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"];'.
							 $options );
		}


		// internal method to assign a value to a BBtextarea element
		function assignBBtextarea( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}
		
		
		// internal method to assign a value to a img element
		function assignImg( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'src';
			
			// if we want to display a new image, we must create a new src otherwise the browser won't replace
			if ($attribute == 'src') $result .= '?'. time();

			// assign result image
			$this->addScript('document.getElementById("'. $formName .'_'. $formElementName .'").'. $attribute .' = "'. $result .'";');
		}		

		
		// internal method to assign a value to a text element
		function assignText( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}


		// internal method to assign a value to a span element
		function assignSpan( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'innerHTML';

			// if result is an array we should export to a valid js string
			if (is_array( $result )) $result =  str_replace( "\n", "<br>", var_export( $result, true ) ) ;

			// escape string
			$result = addslashes( $result );
		
			// assign result to form element using the id
			$this->addScript('document.getElementById("'. $formName .'_'. $formElementName .'").'. $attribute .' = "'. $result .'";');
		}
		
				
		// internal method to assign a value to a textarea element
		function assignTextarea( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}
		

		// internal method to assign a value to a radio element
		function assignRadio( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// we must cycle all radio instances of this radio element
			$this->addScript('__ydftmp = document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"];
							 for (counter = 0; counter < __ydftmp.length; counter++)
							 	if (__ydftmp[counter].value == "'. $result .'") __ydftmp[counter].checked = true;
								else __ydftmp[counter].checked = false;');
		}		
		

		// internal method to assign a value to a password element
		function assignPassword( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}		
		

		// internal method to assign a value to a hidden element
		function assignHidden( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}


		// internal method to assign a value to a dateselect element
		function assignDateSelect( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// if result is a timestamp get values
			if (is_integer( $result )){
			
				$parsed = getdate( $result );
				$day    = $parsed['mday'];
				$month  = $parsed['mon'];
				$year   = $parsed['year'];

			}else $this->addAlert("DateSelect elements in YDAjax only support timestamps in assignResult");
			
			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[day]"].'.   $attribute .' = "'. $day .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[month]"].'. $attribute .' = "'. $month .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[year]"].'.  $attribute .' = "'. $year .'";');
		}		
		

		// internal method to assign a value to a datetimeselect element
		function assignDateTimeSelect( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// if result is a timestamp get values
			if (is_integer( $result )){
			
				$parsed  = getdate( $result );
				$day     = $parsed['mday'];
				$month   = $parsed['mon'];
				$year    = $parsed['year'];
				$hours   = $parsed['hours'];
				$minutes = $parsed['minutes'];

			}else $this->addAlert("DateSelect elements in YDAjax only support timestamps in assignResult");
			
			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[day]"].'.      $attribute .' = "'. $day     .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[month]"].'.    $attribute .' = "'. $month   .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[year]"].'.     $attribute .' = "'. $year    .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[hours]"].'.    $attribute .' = "'. $hours   .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[minutes]"].'.  $attribute .' = "'. $minutes .'";');
		}		

		
		// internal method to assign a value to a timeselect element
		function assignTimeSelect( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
		
			// if result is a timestamp get values
			if (is_integer( $result )){
			
				$parsed  = getdate( $result );
				$hours   = $parsed['hours'];
				$minutes = $parsed['minutes'];

			}else $this->addAlert("TimeSelect elements in YDAjax only support timestamps in assignResult");
			
			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[hours]"].'.   $attribute .' = "'. $hours   .'";');
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'[minutes]"].'. $attribute .' = "'. $minutes .'";');
		}	
		

		// internal method to assign a value to a button element
		function assignButton( $formName, $formElementName, $result, $attribute, $options = array()){

			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'value';
			
			// assign result to form element
			$this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');
		}
		
		
		// internal method to assign a value to a checkbox element
		function assignCheckbox( $formName, $formElementName, $result, $attribute, $options = array()){
		
			// if attribute event is not defined we must create a default event
			if (is_null( $attribute )) $attribute = 'checked';

			if ($attribute != 'checked')
				return $this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].'. $attribute .' = "'. $result .'";');

			// if attribute is 'checked' and result is true, check this checkbox
			if (is_bool($result) && $result == true)
				return $this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].checked = true;');

			// if attribute is 'checked' and result is false, clean checkbox selection
			if (is_bool($result) && $result == false)
				return $this->addScript('document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"].checked = false;');

			// if attribute is 'checked' and result is 'toggle', checkbox will have the opposite value
			if ($result == "toggle")
				return $this->addScript('var __ydftmp = document.forms["'. $formName .'"].elements["'. $formName .'_'. $formElementName .'"];
										 if (__ydftmp.checked == false) {__ydftmp.checked = true;} else {__ydftmp.checked = false;}');


		}
		
}
?>
