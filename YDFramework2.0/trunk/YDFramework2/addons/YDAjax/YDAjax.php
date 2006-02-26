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
     *  This config defines the prefix for javascript functions and variables
     *  Default: __ydf.
     */
    YDConfig::set( 'YD_AJAX_PREFIX', "__ydf", false );


    /**
     *  This config defines custom debug value
     *  Default: false
     */
    YDConfig::set( 'YD_AJAX_DEBUG', false, false );


    /**
     *  Include the needed libraries. 
     */
	require_once( dirname( __FILE__ ) . '/xajax.inc.php');


    /**
     *  Class definition for the YDAjax addon.
     */
    class YDAjax extends xajax{

        /**
         *	This is the class constructor for the YDAjax class.
         *
         *	@param $template		Template object.
         *	@param $form			Form object.
         */
        function YDAjax( & $template, & $form){

			// prefix string used in js function names
			$this->prefix = YDConfig::get( 'YD_AJAX_PREFIX' );

			// initialize xajax object
			$this->xajax( "", $this->prefix, YDConfig::get( 'YD_AJAX_ENCODING') );
			
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

			// by default we don't use effects (then js effects lib won't be loaded)
			$this->effects = array();
			
			// waiting message code
			$this->waitingMessageCode = '';
			$this->waitingMessageCodeFunction = '';
			
			// autocompleter code
			$this->autocompleterCode = '';
			$this->autocompleterCss  = '';
			$this->autocompleterCodeFunctions = array();
		}
		

		// internal method to add js to the template
		function __assignTemplateCode(){
			
			// use default url
			$html  = "var xajaxRequestUri     = \"". YDRequest::getNormalizedUri() ."\";\n";

			// check debug option
			$html .= "var xajaxDebug          = ". (YDConfig::get( 'YD_AJAX_DEBUG') ? "true" : "false") .";\n";
			$html .= "var xajaxStatusMessages = ". (YDConfig::get( 'YD_AJAX_DEBUG') ? "true" : "false") .";\n";
			$html .= "var xajaxWaitCursor     = ". (YDConfig::get( 'YD_AJAX_DEBUG') ? "true" : "false") .";\n";

			// use post
			$html .= "var xajaxDefinedGet     = 0;\n";
			$html .= "var xajaxDefinedPost    = 1;\n";

			// get standard xajax code
			$html .= file_get_contents( dirname( __FILE__ ) . '/js/xajax.js');

			// include generic effects lib .. yes, it must be ALWAYS included
			$html .= "\n". file_get_contents( dirname( __FILE__ ) . '/js/prototype.lite.js_moo.fx.js_moo.fx.pack.js');

			// send autocompleter code
			$html .= "\n" . $this->autocompleterCode;

			// compute ONLOAD code
			$onload = '';

				// export effects js
				foreach( $this->effects as $eff_name => $eff_code )
					$onload  .= $eff_code . "\n\t";

				// send js waiting message creation code
				if ($this->waitingMessageCode != '')
					$onload  .= $this->waitingMessageCode . "\n\t";

				// send autocompleter functions code
				$onload  .= implode( "\n\t", $this->autocompleterCodeFunctions );

			// if there are on load js just export it
			if ($onload != '') $html .= "\nwindow.onload = function() {\n\t". $onload . "\n}";

			// send js function to hide waiting message
			$html .= $this->waitingMessageCodeFunction . "\n";

			// loop xajax functions created on-the-fly
			foreach($this->aFunctions as $sFunction => $bExists)
				$html .= $this->_wrap($sFunction,$this->aFunctionRequestTypes[$sFunction]);

			// add custom js TOP code
			if (!empty($this->customjsTop))
				$html .= implode( "\n", $this->customjsTop ) ."\n";

			// add YDAjax js variables
			foreach( $this->customjsVariables as $variable => $declaration )
				$html .= "var ". $variable ." = ". $declaration .";\n";

			// add YDAjax js functions
			foreach( $this->customjs as $function => $declaration )
				$html .= "function ". $function ."{". $declaration ."}\n";

			// add js custom BOTTOM code
			if (!empty($this->customjsBottom))
				$html .= implode( "\n", $this->customjsBottom ) ."\n";

			// add all code to template html
			$this->template->addJavascript( $html, true );
		}


        /**
         *	This function adds custom javascript to YDAjax specific js.
         *
         *	@param $js      Javascript code.
         *	@param $order   Zone to place code (top, bottom, topBefore, topAfter, bottomBefore, bottomAfer)
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
         *	This function sets YDAjax to automagically handle a waiting message
         *
         *	@param $html        (Optional) Html to use in the message.
         *	@param $options     (Optional) Js options for the html object
         *	@param $effectStart (Optional) YDAjaxEffect object on page load (by default a simple 'hide' will be used)
         *	@param $effectShow  (Optional) YDAjaxEffect object when submitting requests (by default a simple 'show' will be used)
         *	@param $effectHide  (Optional) YDAjaxEffect object on response (by default a simple 'hide' will be used)
         */
		function useWaitingMessage( $html = '<h3>&nbsp; Please wait ... &nbsp;<\/h3>', $options = array(), $effectStart = null, $effectShow = null, $effectHide = null ){
		
			// if we already had invoke this method, then we don't need to add another js code for message creation
			if (isset($this->wtID)) return;
		
			// create js variable name for html message element
			$this->wtID = "waitingmessage";

			// check custom options
			if (!isset($options['style.backgroundColor'])) $options['style.backgroundColor'] = '#cccccc';
			if (!isset($options['style.top']))             $options['style.top']             = '40%';
			if (!isset($options['style.left']))            $options['style.left']            = '44%';

			// static values
			$options['id']             = $this->wtID ."id";
			$options['innerHTML']      = $html;
			$options['style.position'] = "absolute";
			$options['style.position'] = "absolute";
			$options['style.zindex']   = 9999;

			// create js for html element creation
			$this->waitingMessageCode  = "\n\tvar ". $this->wtID ." = document.createElement('div');";
			
			// add custom options
			foreach ($options as $name => $value)
				$this->waitingMessageCode .= $this->wtID .".". $name ." = '". $value . "';";
			
			// append div box to document body
			$this->waitingMessageCode .= "document.body.appendChild(". $this->wtID .");";

			// create start effect
			if (is_null( $effectStart ))
				$effectStart = new YDAjaxEffect( '', 'hide', '', 0 );

			// append start code
			$this->waitingMessageCode .= $effectStart->getJSHead($this->wtID ."id") . $effectStart->getJSBody($this->wtID ."id");

			// create js function headers to show/hide waiting message div
			$this->wtFunctionShow = $this->wtID ."show()";
			$this->wtFunctionHide = $this->wtID ."hide()";

			// create show effect
			if (is_null( $effectShow ))
				$effectShow = new YDAjaxEffect( '', 'show', '', 0 );

			// create hide effect
			if (is_null( $effectHide )) 
				$effectHide = new YDAjaxEffect( '', 'hide', '', 0 );

			// create js functions to show/hide div
			$this->waitingMessageCodeFunction  = "\nfunction ". $this->wtFunctionShow ."{". $effectShow->getJSHead($this->wtID ."id") . $effectShow->getJSBody($this->wtID ."id") ."}";
			$this->waitingMessageCodeFunction .= "\nfunction ". $this->wtFunctionHide ."{". $effectHide->getJSHead($this->wtID ."id") . $effectHide->getJSBody($this->wtID ."id") ."}";

			// add script to hide waiting message on response
			$this->response->addScript( $this->wtFunctionHide . ";" );
		}


		// internal method to get the js show waiting message function declaration
		function _getWMFunctionShow(){

			if (isset($this->wtID)) return $this->wtFunctionShow . ";";
			else                    return '';
		}


        /**
         *	This method assigns the current autocompleter with a result array
         *
         *	@param $result		Autocompleter results array
         */		
		function addCompleterResult( $result ){
			
			// if element is not an array we must create one
			if (!is_array( $result )) $result = array( $result );
		
			// test it result is empty
			if (empty( $result )) return $this->addScript( 'autocompleterClose();' );

			// compute js variable name for results array
			$jsvar = $this->prefix . "atresulttmp";

			// assign js array values
			$js = "var ". $jsvar ." = new Array('". implode( "','", $result ) . "');";

			// send array and method to display autocompleter
			return $this->addScript( $js . 'autocompleterOpen('. $jsvar .');' );
		}
		

        /**
         *	This method assigns a server function and arguments to a form element event.
         *
         *	@param $formElementName		Form element name (string) or js function.
         *	@param $serverFunction		Array with class and method.
         *	@param $arguments			(Optional) Arguments for this function call
         *	@param $event				(Optional) Html event name (auto-detection by default when using null).
         *	@param $options				(Optional) Custom options.
         *	@param $effects				(Optional) Effect or array of effects to execute on event (before ajax call).
         */		
		 function addEvent( $formElementName, $serverFunction, $arguments = null, $event = null, $options = null, $effects = null ){ 
		 
		 	if (!is_array($options)) $options = array( $options );
		 
			// serverFunction must be an array with a class and the method (get function name)
			$functionName = $this->computeFunction( $formElementName, $serverFunction, $arguments, $options, $effects );

			if (!$this->form->isElement( $formElementName )) return;

			// get element object
			$formElement = & $this->form->getElement( $formElementName );
			
			// if event is null we must check the form element type to compute the default event
			if (is_null( $event )) $event = $this->_getDefaultEvent( $formElement->getType() );

			// parse event
			$event = strtolower( $event );

			// get previous atribute
			$previous = is_null( $formElement->getAttribute($event) ) ? '' : trim( $formElement->getAttribute($event) );

			// if previous atribute don't have ';' in the end we should add it
			if (strlen( $previous ) > 0 && $previous[ strlen( $previous ) - 1 ] != ';' ) $previous .= ';';

			if (in_array('replace', $options))
				return $formElement->setAttribute( $event, $functionName );

			if (in_array('prepend', $options))
				return $formElement->setAttribute( $event, $functionName . $previous );
			
			$formElement->setAttribute( $event, $previous . $functionName );
		}



		function computeFunction( $formElementName, $serverFunction, $arguments = null, $options = null, $effects = null ){ 

			// register function in xajax
			$this->registerFunction( array( $serverFunction[1], $serverFunction[0],  $serverFunction[1])  );

			$serverFunctionName = $serverFunction[1];

			if (!is_array( $options )) $options = array( $options );
			
			// get function name
			$functionName = $this->_getWMFunctionShow() . $this->sWrapperPrefix . $serverFunctionName .'('. $this->_computeFunctionArguments( $arguments, $options ) .');';

			// process effects
			if (!is_null( $effects )){
		
				// if is one effect, we should create an array of effects
				if (!is_array( $effects )) $effects = array( $effects );

				// cycle effects (from last to first) and create js				
				foreach( $effects as $effect ){

					// element id
					$id = $this->form->getName() .'_'. $effect->getId();
				
					if (!in_array( $effect->getVariable(), $this->effects )){
					
						// check if element is a form element
						if ( $this->form->isElement( $effect->getId() ) )	$this->effects[ $effect->getVariable() ] = $effect->getJSHead( $id );
						else												$this->effects[ $effect->getVariable() ] = $effect->getJSHead();
				
					}

					// function is now the effect js created
					$functionName .= $effect->getJSBody( $id );
				}
			}

			// if formElementName is a js function we are not assigning an event to a form element but creating a js function
			if (ereg ("^(.*)\(\)$", $formElementName, $function)){
				$this->customjs[ $function[0] ] = $functionName;
				return;
			}

			return $functionName;
		}
		

        /**
         *	This method adds an effect to a response
         *
         *	@param $effect		Effect object
         */	
		function addEffect( $effect ){

			// initialize js to send
			$js = '';

			// test if effect id is a form element
			if ( $this->form->isElement( $effect->getId() ) ){
				
				// get element
				$elem = & $this->form->getElement( $effect->getId() );		

				// element id
				$id = $elem->getAttribute( 'id' );
			}else{
			
				// if effect id is not a form element then its a html id
				$id = $effect->getId();
			}
		
			// if some html id has not already an effect applyed we must send effect header
			if (!in_array( $effect->getVariable(), $this->effects ))
				$js = $effect->getJSHead( $id );

			// send	js
			$this->response->addScript( $js . $effect->getJSBody( $id ) );
		}


		// internal method. compute js arguments string 
		function _computeFunctionArguments( $arguments, $options ){
			
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
						case 'autocompleter' :
						case 'text' :			$args[] = $this->_getValueText(           $elem, $options ); break;
						case 'password' :		$args[] = $this->_getValuePassword(       $elem, $options ); break;
						case 'textarea' :		$args[] = $this->_getValueTextarea(       $elem, $options ); break;
						case 'radio' :			$args[] = $this->_getValueRadio(          $elem, $options ); break;
						case 'checkbox' :		$args[] = $this->_getValueCheckbox(       $elem, $options ); break;
						case 'dateselect' :		$args[] = $this->_getValueDateSelect(     $elem, $options ); break;
						case 'datetimeselect' :	$args[] = $this->_getValueDateTimeSelect( $elem, $options ); break;
						case 'timeselect' :		$args[] = $this->_getValueTimeSelect(     $elem, $options ); break;
						case 'span' :			$args[] = $this->_getValueSpan(           $elem, $options ); break;
						case 'select' :			$args[] = $this->_getValueSelect(         $elem, $options ); break;
																							
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
         *	@param $message				Message to display
         *	@param $event				(Optional) Event name (auto-detection by default when using null).
         *	@param $dependence			(Optional) Element name or array of names that this element depends of
         */		
		function addConfirmation( $formElementName, $message, $event = null, $dependence = null){

			if (!is_null( $dependence )){
				$jsvariable = $this->prefix . 'change'. $formElementName .'var';
				$this->_addDependence( $dependence, $jsvariable );
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
			if (is_null($event)) $event = $this->_getDefaultEvent( $elem->getType() );

			// check if atribute exist
			$attribute = $elem->getAttribute( $event );
			if (is_null($attribute)) die( "Element ". $formElementName ." doesn't have atribute ". $event );

			// create confirmation function name
			$function = $this->prefix .'confirm'. $event . $formElementName .'()';

			// if this is a dependent element
			if (!is_null( $dependence )) $this->customjs[ $function ] = 'if ('. $jsvariable .' == false || confirm("'. addslashes( $message ) .'")) { '. $attribute .' }';
			else                         $this->customjs[ $function ] = 'if (confirm("'. addslashes( $message ) .'")) { '. $attribute .' }';

			// override element atribute
			$elem->setAttribute( strtolower($event), $function );
		}


		// internal method. This adds dependence to form elements
		function _addDependence( $dependence, $jsvariable ){
		
			// add variable to custom variables
			$this->customjsVariables[ $jsvariable ] = 'false';
		
			if (!is_array( $dependence )) $dependence = array( $dependence );
		
			// cycle form elements
			foreach ( $dependence as $formElementName ){

				if (!$this->form->isElement( $formElementName )) die( "Form element ". $formElementName ." doesn't exist");
				
				// get element
				$elem = & $this->form->getElement( $formElementName );
				
				// get previous atribute onchange
				$attribute = (is_null( $elem->getAttribute( 'onchange' ))) ? '' : $elem->getAttribute( 'onchange' );
				
				// compute new atribute 'onchange'
				$elem->setAttribute('onchange', $jsvariable .' = true;'. $attribute );
			}
		}


		// internal method. Returns the default event for a html element type
		function _getDefaultEvent( $type ){

			switch( $type ){
				case 'submit' : trigger_error('Submit buttons cannot be used in ajax because they force submition.'); break;
				case 'image' :  trigger_error('Images cannot be used in ajax because they force submition. Use "img" element instead.'); break;
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
         *	@param $event				(Optional) Event name (auto-detection by default when using null).
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
			if (is_null( $event )) $event = $this->_getDefaultEvent( $elem->getType() );
			
			// get atribute from element
			$attribute = $elem->getAttribute( $event );
			
			// check if atribute exist
			if (!$attribute) $attribute = 'false';
		
			$this->customjs[$functionName] = $attribute;
		}


        /**
         *	This method will process all events added
         */	
		function processEvents(){

			// find autocompleters
			foreach( $this->form->_elements as $formElement){
			
				if ($formElement->getType() != 'autocompleter') continue;
				
				if (empty($this->autocompleterCodeFunctions)){

					// load js code
					$this->autocompleterCode = file_get_contents( dirname( __FILE__ ) . '/js/autocompleter.js');

					// add css to template
					$this->template->addCss( file_get_contents( dirname( __FILE__ ) . '/js/autocompleter.css') );
				}
		
				// compute variable name for this autocompleter
				$variable = $this->prefix ."at".  $formElement->getName();

				// compute html text id
				$textID = $formElement->getAttribute( 'id' );
			
				// compute div id
				$divID = $textID .'_div';

				// get function call
				$serverFunctionName = $formElement->getAjaxCall();

				// get ajax arguments
				$arguments = $formElement->getAjaxArguments();

				// get ajax effects
				$effects = $formElement->getAjaxEffects();

				// add js code for autocompleter
				$this->autocompleterCodeFunctions[] = $variable . " = new AutoSuggest('" . $textID . "','" . $divID . "', null);";
				$this->autocompleterCodeFunctions[] = $variable . ".ajax = function(){currentAjaxAutocompleter=this;".  $this->computeFunction( $textID, $serverFunctionName, $arguments, $effects ) . "}";
			}

			// add js code
			$this->__assignTemplateCode();

			// process all requests and exit
			return $this->processRequests();
		}


        /**
         *	This method add a result to a form element
         *
         *	@param $formElementName		Form element name or a generic id string.
         *	@param $result				Result string or array.
         *	@param $attribute			(Optional) Optional atribute to apply result (auto-detection by default when using null).
         *	@param $options				(Optional) Aditional options.
         */	
		function addResult( $formElementName, $result, $attribute = null, $options = array() ){
		
			// if is not a form element, assign result to a html id
			if (!$this->form->isElement( $formElementName ))
				return $this->response->assignId( $formElementName, $result, $attribute, $options );

			// if is a form element, get element
			$elem = & $this->form->getElement( $formElementName );

			// assign result to element
			return $this->response->assignResult( $elem, $result, $attribute, $options);
		}


        /**
         *	This method will process all results added in a response
         */	
		function processResults(){

			// return XML to client browser
			return $this->response->getXML();
		}
		
		
		// internal method to create the needed js function to retrieve a text value
		function _getValueText( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';
		
			// add our custom js function to retrieve this text element value (it will replace possible js functions for this element because we cannot have 2 functions with the same name)
			$this->customjs[$jsfunction] = 'return document.getElementById("'. $element->getAttribute('id') .'").value;';

			// return function invocation
			return $jsfunction;
		}


		// internal method to create the needed js function to retrieve a select value
		function _getValueSelect( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';

			// if we want all values and not only the select one
			if (in_array( 'all', $options )){

				$this->customjs[$jsfunction]  = 'var __ydtmparr = new Array();' . "\n";
				$this->customjs[$jsfunction] .= 'var __ydtmpsel = document.getElementById("'. $element->getAttribute('id') .'");' . "\n";
				$this->customjs[$jsfunction] .= 'for (i = 0; i < __ydtmpsel.length; i++){' . "\n";
				$this->customjs[$jsfunction] .= '    __ydtmparr[ __ydtmpsel.options[i].value ] = __ydtmpsel.options[i].text;' . "\n";
				$this->customjs[$jsfunction] .= '}' . "\n";
				$this->customjs[$jsfunction] .= 'return __ydtmparr;';
		
				return $jsfunction;
			}
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'return document.getElementById("'. $element->getAttribute('id') .'").value;';

			// return function invocation
			return $jsfunction;
		}


		// internal method to create the needed js function to retrieve a password value
		function _getValuePassword( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'return document.getElementById("'. $element->getAttribute('id') .'").value;';

			// return function invocation
			return $jsfunction;
		}
		

		// internal method to create the needed js function to retrieve a textarea value
		function _getValueTextarea( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'return document.getElementById("'. $element->getAttribute('id') .'").value;';

			// return function invocation
			return $jsfunction;
		}
		
		
		// internal method to create the needed js function to retrieve a span value
		function _getValueSpan( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction] = 'return document.getElementById("'. $element->getAttribute('id') .'").innerHTML;';

			// return function invocation
			return $jsfunction;
		}		
		

		// internal method to create the needed js function to retrieve a dateselect value
		function _getValueDateSelect( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'var day   = document.getElementById("'. $element->getAttribute('id') .'[day]").value;' . "\n";
			$this->customjs[$jsfunction] .= 'var month = document.getElementById("'. $element->getAttribute('id') .'[month]").value - 1;' . "\n";
			$this->customjs[$jsfunction] .= 'var year  = document.getElementById("'. $element->getAttribute('id') .'[year]").value;' . "\n";
			$this->customjs[$jsfunction] .= 'var mydate = new Date( year, month, day ); ' . "\n";
			$this->customjs[$jsfunction] .= 'return mydate.getTime() / 1000;';

			// return function invocation
			return $jsfunction;
		}


		// internal method to create the needed js function to retrieve a timeselect value
		function _getValueTimeSelect( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'var hours    = document.getElementById("'. $element->getAttribute('id') .'[hours]").value;' . "\n";
			$this->customjs[$jsfunction] .= 'var minutes  = document.getElementById("'. $element->getAttribute('id') .'[minutes]").value;' . "\n";
			$this->customjs[$jsfunction] .= 'var mydate = new Date( 1970, 1, 1, hours, minutes ); ' . "\n";
			$this->customjs[$jsfunction] .= 'return mydate.getTime() / 1000;';

			// return function invocation
			return $jsfunction;
		}		


		// internal method to create the needed js function to retrieve a datetimeselect value
		function _getValueDateTimeSelect( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction]  = 'var day      = document.getElementById("'. $element->getAttribute('id') .'[day]").value;' . "\n";
			$this->customjs[$jsfunction] .= 'var month    = document.getElementById("'. $element->getAttribute('id') .'[month]").value - 1;' . "\n";
			$this->customjs[$jsfunction] .= 'var year     = document.getElementById("'. $element->getAttribute('id') .'[year]").value;' . "\n";
			$this->customjs[$jsfunction] .= 'var hours    = document.getElementById("'. $element->getAttribute('id') .'[hours]").value;' . "\n";
			$this->customjs[$jsfunction] .= 'var minutes  = document.getElementById("'. $element->getAttribute('id') .'[minutes]").value;' . "\n";
			$this->customjs[$jsfunction] .= 'var mydate = new Date( year, month, day, hours, minutes ); ' . "\n";
			$this->customjs[$jsfunction] .= 'return mydate.getTime() / 1000;';

			// return function invocation
			return $jsfunction;
		}				
		

		// internal method to create the needed js function to retrieve a checkbox value
		function _getValueCheckbox( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';
		
			// add our custom js function
			$this->customjs[$jsfunction]  =	'if (document.getElementById("'. $element->getAttribute('id') .'").checked)' . "\n";
			$this->customjs[$jsfunction] .=	'	return 1;' . "\n";
			$this->customjs[$jsfunction] .=	'return 0;';

			// return function invocation
			return $jsfunction;
		}
		

		// internal method to create the needed js function to retrieve a radio value
		function _getValueRadio( & $element, $options ){
		
			// generate function name
			$jsfunction = $this->prefix .'get'. $element->getName() .'()';
			
			// add custom js function
			$this->customjs[$jsfunction]  = 'var __ydftmp = document.getElementById("'. $element->getAttribute('id') .'");' . "\n";
			$this->customjs[$jsfunction] .= 'for (counter = 0; counter < __ydftmp.length; counter++)' . "\n";
			$this->customjs[$jsfunction] .= '	if (__ydftmp[counter].checked) return __ydftmp[counter].value;' . "\n";
			$this->customjs[$jsfunction] .= 'return false;';

			// return function invocation
			return $jsfunction;
		}		


        /**
         *	This method adds a js alert message to ajax response
         *
         *	@param $message			Message to display.
         */		
		function addAlert( $message ){
			
			$this->response->addAlert( $message );
		}


        /**
         *	This method sends a ajax response message
         *
         *	@param $message			Message to display.
         */		
		function message( $message ){
			
			$response = new YDAjaxResponse( YDConfig::get( 'YD_AJAX_ENCODING' ) );
			$response->addScript( 'try{ waitingmessagehide() }catch(e){}' );
			$response->addAlert( $message );
			
			return $response;
		}


	}



    /**
     *  Class definition for the YDAjax response.
     */
	class YDAjaxResponse extends xajaxResponse{
	
		function YDAjaxResponse( $encoding ){
			$this->xajaxResponse( $encoding );
		}
		
		
        /**
         *	This method assigns a server result to a form element in the client side.
         *
         *	@param $formElement			Form element object.
         *	@param $result				Result value.
         *	@param $attribute			Custom atribute (auto-detection by default).
         *	@param $options				Other options.
         */		
		function assignResult( & $formElement, $result, $attribute = null, $options = array()){
	
			// depending on the type we must invoke the custom function for the element
			switch( strtolower( $formElement->getType() ) ){

				case 'submit' :
				
				case 'button' :			$this->assignButton(         $formElement, $result, $attribute, $options ); break;
				case 'checkbox' :		$this->assignCheckbox(       $formElement, $result, $attribute, $options ); break;
				case 'dateselect' :		$this->assignDateSelect(     $formElement, $result, $attribute, $options ); break;
				case 'datetimeselect' :	$this->assignDateTimeSelect( $formElement, $result, $attribute, $options ); break;
				case 'timeselect' :		$this->assignTimeSelect(     $formElement, $result, $attribute, $options ); break;
				case 'bbtextarea' :		$this->assignBBtextarea(     $formElement, $result, $attribute, $options ); break;
				case 'img' :			$this->assignImg(            $formElement, $result, $attribute, $options ); break;
				case 'hidden' :			$this->assignHidden(         $formElement, $result, $attribute, $options ); break;
				case 'text' :			$this->assignText(           $formElement, $result, $attribute, $options ); break;
				case 'textarea' :		$this->assignTextarea(       $formElement, $result, $attribute, $options ); break;
				case 'radio' :			$this->assignRadio(          $formElement, $result, $attribute, $options ); break;
				case 'password' :		$this->assignPassword(       $formElement, $result, $attribute, $options ); break;
				case 'span' :			$this->assignSpan(           $formElement, $result, $attribute, $options ); break;
				case 'select' :			$this->assignSelect(         $formElement, $result, $attribute, $options ); break;
				case 'image' :			$this->addAlert('Input type image cannot be used. Please use a img instead.'); break;

				// if element doesn't exist return an js alert
				default : $this->addAlert('Element type "'. $formElement->getType() .'" is not supported in YDAjax'); break;
			}
		}


        /**
         *	This method assigns a server result to a element using just the id
         *
         *	@param $id			Html element id.
         *	@param $result		Result value.
         *	@param $attribute	Custom atribute (auto-detection by default).
         *	@param $options		Other options (not used).
         */	
		function assignId( $id, $result, $attribute, $options ){
		
			// if atribute is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'innerHTML';

			// if result is an array we should export to a valid js string
			if (is_array( $result )) $result =  str_replace( "\n", "<br>", var_export( $result, true ) ) ;
		
			// escape string
			$result = addslashes( $result );
			
			// assign result to form element using the id
			$this->addScript('document.getElementById("'. $id .'").'. $attribute .' = "'. $result .'";');
		}


		// internal method to assign a value to a select element
		function assignSelect( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
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
			$this->addScript('var __ydfselect = document.getElementById("'. $formElement->getAttribute('id') .'");'. $options );
		}


		// internal method to assign a value to a BBtextarea element
		function assignBBtextarea( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").'. $attribute .' = "'. $result .'";');
		}
		
		
		// internal method to assign a value to a img element
		function assignImg( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )){
				
				// the default atribute of an image is its source address
				$attribute = 'src';
			
				// if we want to display a new image, we must create a new src value otherwise the browser won't replace
				$result .= '?'. microtime();
			}

			// assign result image
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").'. $attribute .' = "'. $result .'";');
		}		

		
		// internal method to assign a value to a text element
		function assignText( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").'. $attribute .' = "'. $result .'";');
		}


		// internal method to assign a value to a span element
		function assignSpan( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'innerHTML';

			// if result is an array we should export to a valid js string
			if (is_array( $result )) $result =  str_replace( "\n", "<br>", var_export( $result, true ) ) ;

			// escape string
			$result = addslashes( $result );
		
			// assign result to form element using the id
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").'. $attribute .' = "'. $result .'";');
		}
		
				
		// internal method to assign a value to a textarea element
		function assignTextarea( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").'. $attribute .' = "'. $result .'";');
		}
		

		// internal method to assign a value to a radio element
		function assignRadio( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// we must cycle all radio instances of this radio element
			$this->addScript('__ydftmp = document.getElementById("'. $formElement->getAttribute('id') .'");
							 for (counter = 0; counter < __ydftmp.length; counter++)
							 	if (__ydftmp[counter].value == "'. $result .'") __ydftmp[counter].checked = true;
								else __ydftmp[counter].checked = false;');
		}		
		

		// internal method to assign a value to a password element
		function assignPassword( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").'. $attribute .' = "'. $result .'";');
		}		
		

		// internal method to assign a value to a hidden element
		function assignHidden( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
		
			// check and convert $result
			$result = htmlspecialchars( $result );

			// assign result to form element
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").'. $attribute .' = "'. $result .'";');
		}


		// internal method to assign a value to a dateselect element
		function assignDateSelect( & $formElement, $result, $attribute, $options ){

			// result must be a timestamp
			if (!is_integer( $result ))
				return $this->addAlert("DateSelect elements in YDAjax only support timestamps in assignResult");

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
		
			// get timestamp individual values
			$parsed = getdate( $result );
			$day    = $parsed['mday'];
			$month  = $parsed['mon'];
			$year   = $parsed['year'];
			
			// assign result to form element
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[day]").'.   $attribute .' = "'. $day .'";');
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[month]").'. $attribute .' = "'. $month .'";');
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[year]").'.  $attribute .' = "'. $year .'";');
		}		
		

		// internal method to assign a value to a datetimeselect element
		function assignDateTimeSelect( & $formElement, $result, $attribute, $options ){

			// result must be a timestamp
			if (!is_integer( $result ))
				return $this->addAlert("DateSelect elements in YDAjax only support timestamps in assignResult");

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
			
			// get timestamp individual values
			$parsed  = getdate( $result );
			$day     = $parsed['mday'];
			$month   = $parsed['mon'];
			$year    = $parsed['year'];
			$hours   = $parsed['hours'];
			$minutes = $parsed['minutes'];

			// assign result to form element
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[day]").'.      $attribute .' = "'. $day     .'";');
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[month]").'.    $attribute .' = "'. $month   .'";');
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[year]").'.     $attribute .' = "'. $year    .'";');
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[hours]").'.    $attribute .' = "'. $hours   .'";');
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[minutes]").'.  $attribute .' = "'. $minutes .'";');
		}		

		
		// internal method to assign a value to a timeselect element
		function assignTimeSelect( & $formElement, $result, $attribute, $options ){

			//  result must be a timestamp
			if (!is_integer( $result ))
				return $this->addAlert("TimeSelect elements in YDAjax only support timestamps in assignResult");

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
		
			// get timestamp individual values
			$parsed  = getdate( $result );
			$hours   = $parsed['hours'];
			$minutes = $parsed['minutes'];
			
			// assign result to form element
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[hours]").'.   $attribute .' = "'. $hours   .'";');
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'[minutes]").'. $attribute .' = "'. $minutes .'";');
		}	
		

		// internal method to assign a value to a button element
		function assignButton( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'value';
			
			// assign result to form element
			$this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").'. $attribute .' = "'. $result .'";');
		}
		
		
		// internal method to assign a value to a checkbox element
		function assignCheckbox( & $formElement, $result, $attribute, $options ){

			// if atribute event is not defined we must create a default one
			if (is_null( $attribute )) $attribute = 'checked';

			if ($attribute != 'checked')
				return $this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").'. $attribute .' = "'. $result .'";');

			// if atribute is 'checked' and result is true, check this checkbox
			if (is_bool($result) && $result == true)
				return $this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").checked = true;');

			// if atribute is 'checked' and result is false, clean checkbox selection
			if (is_bool($result) && $result == false)
				return $this->addScript('document.getElementById("'. $formElement->getAttribute('id') .'").checked = false;');

			// if atribute is 'checked' and result is 'toggle', checkbox will have the opposite value
			if ($result == "toggle")
				return $this->addScript('var __ydftmp = document.getElementById("'. $formElement->getAttribute('id') .'");
										 if (__ydftmp.checked == false) {__ydftmp.checked = true;} else {__ydftmp.checked = false;}');

		}
		
}


    /**
     *  Class definition for YDAjax effects.
     */
    class YDAjaxEffect extends YDBase{
	
	
        /**
         *	YDAjaxEffect constructor
         *
         *	@param $id			Html element id.
         *	@param $name		Effect name (String).
         *	@param $method		Effect method (String). For effects 'hide' and 'show' method is not needed
         *	@param $duration	(Optional) Duration in ms.
         */	
		function YDAjaxEffect( $id, $name, $method = '', $duration = 400 ){

			$this->id = $id;
			$this->name = strtolower( $name );
			$this->method = $method;
			$this->duration = $duration;
			
			// create js object variable name for this effect
			$this->js = YDConfig::get( 'YD_AJAX_PREFIX' ) . $this->name . $this->id;
		}
	

        /**
         *	This method returns the effect name
         */	
		function getName(){
		
			return $this->name;
		}


        /**
         *	This method returns the js variable name
         */	
		function getVariable(){
		
			return $this->js;
		}


        /**
         *	This method return the id
         */	
		function getID(){
		
			return $this->id;
		}
	

        /**
         *	This method return the js effect header
         */	
		function getJSHead( $id = null ){
			
			// if custom id is not defined we must use the standard id
			if (is_null( $id )) $id = $this->id;
			
			// switch effect type
			switch( $this->name ){
			
				case 'fadesize' :	$header = $this->js .' = new fx.FadeSize("'. $id .'", {duration: '. $this->duration .'});';
									break;

				case 'resize' :		$header = $this->js .' = new fx.Resize("'. $id .'", {duration: '. $this->duration .'});';
									break;

				case 'opacity' :	$header = $this->js .' = new fx.Opacity("'. $id .'", {duration: '. $this->duration .'});';
									break;
									
				case 'show' :
				case 'hide' :       $header = '';
                                    break;

				default :           die( 'Effect type '. $this->name .' is not supported by YDAjaxEffect' );
			}

			// return js header
			return $header;
		}


        /**
         *	This method returns the js effect body
         */	
		function getJSBody( $id = null ){

			// if custom id is not defined we must use the standard id
			if (is_null( $id )) $id = $this->id;

			switch( $this->name ){
			
				// return the method invocation for standard effects
				case 'fadesize' :
				case 'resize' :
				case 'opacity' : return $this->js .'.'. $this->method .';';
				case 'show' :    return 'document.getElementById("'. $id .'").style.display = "block";';
				case 'hide' :    return 'document.getElementById("'. $id .'").style.display = "none";';
			}
		}


	}

?>
