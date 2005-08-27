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

    // Includes
    include_once( dirname( __FILE__ ) . '/../YDForm.php');
    include_once( dirname( __FILE__ ) . '/../YDXml.php');

    /**
     *	This is the class that is able to render/import a form to/from XML.
     */
    class YDFormRenderer_xml extends YDFormRenderer {

        /**
         *	This is the class constructor for the YDFormRenderer_xml class.
         *
         *	@param $form		The form that needs to be rendered.
         */
        function YDFormRenderer_xml( $form ) {

            // Initialize the parent
            $this->YDFormRenderer( $form );

        }

        /**
         *	This function will render the form.
         *
         *	@returns	The rendered form.
         */
        function render() {

            // Form
            $xml['form'][0]['#'] = array();
            $xml['form'][0]['@']['name']   = $this->_form->_name;
            $xml['form'][0]['@']['method'] = $this->_form->_method;
            $xml['form'][0]['@']['action'] = $this->_form->_action;
            $xml['form'][0]['@']['target'] = $this->_form->_target;
            
            $form = & $xml['form'][0]['#'];
            $attr = & $form['attributes'][0]['#'];
            $elem = & $form['elements'][0]['#'];
            $rule = & $form['rules'][0]['#'];
            $comp = & $form['comparerules'][0]['#'];
            $frul = & $form['formrules'][0]['#'];
            $filt = & $form['filters'][0]['#'];
            
            $r_elem = & $form['registered'][0]['#']['elements'][0]['#'];
            $r_filt = & $form['registered'][0]['#']['filters'][0]['#'];
            $r_rule = & $form['registered'][0]['#']['rules'][0]['#'];
            $r_rend = & $form['registered'][0]['#']['renderers'][0]['#'];
            
            // Attributes
            if ( sizeof( $this->_form->_attributes ) ) {
                
                $attr = & $attr['attribute'];
                $i = 0;
            
                foreach ( $this->_form->_attributes as $name => $value ) {
                    $attr[$i]['@']['name']  = $name;
                    $attr[$i]['@']['value'] = $value;
                    $i++;
                }
            }
            
            // Elements
            if ( sizeof( $this->_form->_elements ) ) {
                $i = 0;
                $elem = & $elem['element'];
                foreach ( $this->_form->_elements as $name => $obj ) {
                    
                    $arr = $obj->toArray();
                    
                    // Name and Type
                    $elem[$i]['@']['name'] = $arr['name'];
                    $elem[$i]['@']['type'] = $arr['type'];
                    
                    // Labels (support only default values)
                    $e_lab = & $elem[$i]['#']['labels'][0]['#']['label'];
                    $e_lab[0]['@']['id']    = 'default';
                    $e_lab[0]['@']['value'] = $arr['labelname'];
                    
                    // Values (support only default values)
                    $e_val = & $elem[$i]['#']['values'][0]['#']['value'];
                    $e_val[0]['@']['id']    = 'default';
                    $e_val[0]['@']['value'] = $arr['value'];
                    
                    // Attributes
                    $e_attr = & $elem[$i]['#']['attributes'][0]['#'];
                    
                    if ( sizeof( $arr['attributes'] ) ) {
                        $e_attr = & $e_attr['attribute'];
                        $j = 0;
                        foreach ( $arr['attributes'] as $name => $value ) {
                            $e_attr[$j]['@']['name']  = $name;
                            $e_attr[$j]['@']['value'] = $value;
                            $j++;
                        }
                    }
                    
                    // Options
                    $e_opts = & $elem[$i]['#']['options'][0]['#'];
                    
                    if ( sizeof( $arr['options'] ) ) {
                        $e_opts = & $e_opts['option'];
                        $j = 0;
                        foreach ( $arr['options'] as $name => $value ) {
                            $e_opts[$j]['@']['name']  = $name;
                            $e_opts[$j]['@']['value'] = $value;
                            $j++;
                        }
                    }
                    
                    $i++;
                }
            }

            // Rules
            if ( sizeof( $this->_form->_rules ) ) {
                $i = 0;
                $rule = & $rule['rule'];
                foreach ( $this->_form->_rules as $e => $rules ) {
                    
                    foreach ( $rules as $r ) {
                        
                        // Name and Type
                        $rule[$i]['@']['element'] = $e;
                        $rule[$i]['@']['type'] = $r['rule'];
                        
                        // Errors (support only default values)
                        $r_err = & $rule[$i]['#']['errors'][0]['#']['error'];
                        $r_err[0]['@']['id']    = 'default';
                        $r_err[0]['@']['value'] = $r['error'];
                        
                        // Options
                        $r_opt = & $rule[$i]['#']['options'][0]['#'];
                        
                        if ( ! is_null( $r['options'] ) ) {
                            
                            // If Integer, Float, String create just one option with id = ""
                            if ( is_scalar( $r['options'] ) ) {
                                $r_opt = & $r_opt['option'];
                                $r_opt[0]['@']['id'] = "";
                                $r_opt[0]['@']['value'] = $r['options'];
                                $r_opt[0]['@']['serialized'] = 'false'; 
                            
                            // If Array, create all options
                            } else if ( is_array( $r['options'] ) && ! empty( $r['options'] ) ) {
                                
                                $r_opt = & $r_opt['option'];
                                $j = 0;
                                foreach ( $r['options'] as $id => $value ) {
                                    
                                    $r_opt[$j]['@']['id'] = $id;
                                    $r_opt[$j]['@']['serialized'] = 'false'; 
                                    
                                    if ( is_object( $value ) || is_array( $value ) ) {
                                        $value = YDObjectUtil::serialize( $value );
                                        $r_opt[$j]['@']['serialized'] = 'true'; 
                                    }
                                    
                                    $r_opt[$j]['@']['value'] = $value;
                                    $j++;
                                }
                                
                            // If Object, serialize it
                            } else if ( is_object( $r['options'] ) ) {
                                $r_opt = & $r_opt['option'];
                                $r_opt[0]['@']['id'] = "";
                                $r_opt[0]['@']['value'] = YDObjectUtil::serialize( $r['options'] );
                                $r_opt[0]['@']['serialized'] = 'true'; 
                            }
                        }
                        
                        $i++;
                    }
                }
            }

            // Compare Rules
            if ( sizeof( $this->_form->_comparerules ) ) {
                $i = 0;
                $comp = & $comp['rule'];
                foreach ( $this->_form->_comparerules as $r ) {
                    
                    // Type
                    $comp[$i]['@']['type'] = $r['rule'];
                    
                    // Elements
                    $c_elem = & $comp[$i]['#']['elements'][0]['#']['element'];
                    
                    $j = 0;
                    foreach ( $r['elements'] as $e ) {
                        $c_elem[$j]['@']['name'] = $e;
                        $j++;
                    }
                    
                    // Errors (support only default values)
                    $c_err = & $comp[$i]['#']['errors'][0]['#']['error'];
                    $c_err[0]['@']['id']    = 'default';
                    $c_err[0]['@']['value'] = $r['error'];
                    
                    $i++;
                }
            }

            // Form Rules
            if ( sizeof( $this->_form->_formrules ) ) {
                $i = 0;
                $frul = & $frul['rule'];
                foreach ( $this->_form->_formrules as $r ) {
                    
                    if ( is_array( $r ) ) {
                        $r = $r[0] . '::' . $r[1];
                    }
                    
                    // Type
                    $frul[$i]['@']['callback'] = $r;
                    
                    $i++;
                }
            }

            // Filters
            if ( sizeof( $this->_form->_filters ) ) {
                $i = 0;
                $filt = & $filt['element'];
                foreach ( $this->_form->_filters as $e => $filters ) {
                    
                    foreach ( $filters as $f ) {
                        
                        // Name and Filter
                        $filt[$i]['@']['name']   = $e;
                        $filt[$i]['@']['filter'] = $f;
                        
                        $i++;
                        
                    }
                }
            }

            // Registered filters
            if ( sizeof( $this->_form->_regFilters ) ) {
                $i = 0;
                $r_filt = & $r_filt['filter'];
                foreach ( $this->_form->_regFilters as $name => $r ) {
                    
                    if ( is_array( $r['callback'] ) ) {
                        $r['callback'] = $r['callback'][0] . '::' . $r['callback'][1];
                    }
                    
                    $r_filt[$i]['@']['name']     = $name;
                    $r_filt[$i]['@']['callback'] = $r['callback'];
                    $r_filt[$i]['@']['file']     = $r['file'];
                    
                    $i++;
                }
            }

            // Registered rules
            if ( sizeof( $this->_form->_regRules ) ) {
                $i = 0;
                $r_rule = & $r_rule['rule'];
                foreach ( $this->_form->_regRules as $name => $r ) {
                    
                    if ( is_array( $r['callback'] ) ) {
                        $r['callback'] = $r['callback'][0] . '::' . $r['callback'][1];
                    }
                    
                    $r_rule[$i]['@']['name']     = $name;
                    $r_rule[$i]['@']['callback'] = $r['callback'];
                    $r_rule[$i]['@']['file']     = $r['file'];
                    
                    $i++;
                }
            }

            // Registered renderers
            if ( sizeof( $this->_form->_regRenderers ) ) {
                $i = 0;
                $r_rend = & $r_rend['renderer'];
                foreach ( $this->_form->_regRenderers as $name => $r ) {
                    
                    $r_rend[$i]['@']['name']  = $name;
                    $r_rend[$i]['@']['class'] = $r['class'];
                    $r_rend[$i]['@']['file']  = $r['file'];
                    
                    $i++;
                }
            }

            // Registered elements
            if ( sizeof( $this->_form->_regElements ) ) {
                $i = 0;
                $r_elem = & $r_elem['element'];
                foreach ( $this->_form->_regElements as $name => $r ) {
                    
                    $r_elem[$i]['@']['name']  = $name;
                    $r_elem[$i]['@']['class'] = $r['class'];
                    $r_elem[$i]['@']['file']  = $r['file'];
                    
                    $i++;
                }
            }

            $xml = new YDXml( $xml, YD_XML_ARRAY );
            return $xml->toString();

        }
        
        /**
         *  This function will parse the contents of a render and return
         *  a new YDForm object.
         *
         *  @returns    A YDForm object.
         */
        function import( $content, $options=array() ) {
            
            $class = isset( $options['class'] ) ? $options['class'] : 'YDForm';
            $type  = isset( $options['type']  ) ? $options['type']  : YD_XML_STRING;
            
            $xml = new YDXml( $content, $type );
            $arr = $xml->toArray();
            
            // Form Name, Method, Action, Target
            $f_name = $arr['form'][0]['@']['name'];
            $f_method = $arr['form'][0]['@']['method'];
            $f_action = $arr['form'][0]['@']['action'];
            $f_target = $arr['form'][0]['@']['target'];
            
            // References
            $form = & $arr['form'][0]['#'];
            $attr = & $form['attributes'][0]['#'];
            $elem = & $form['elements'][0]['#'];
            $rule = & $form['rules'][0]['#'];
            $comp = & $form['comparerules'][0]['#'];
            $frul = & $form['formrules'][0]['#'];
            $filt = & $form['filters'][0]['#'];
            
            $r_elem = & $form['registered'][0]['#']['elements'][0]['#'];
            $r_filt = & $form['registered'][0]['#']['filters'][0]['#'];
            $r_rule = & $form['registered'][0]['#']['rules'][0]['#'];
            $r_rend = & $form['registered'][0]['#']['renderers'][0]['#'];
            
            // Attributes
            $f_attr = array();
            if ( is_array( $attr ) ) {
                $attr = & $attr['attribute'];
                for ( $i=0; $i < count( $attr ); $i++ ) {
                    $f_attr[ $attr[ $i ]['@']['name'] ] = $attr[ $i ]['@']['value'];
                }
            }
            
            // YDForm object
            $f = new $class( $f_name, $f_method, $f_action, $f_target, $f_attr );
            
           
            // Elements
            if ( is_array( $elem ) ) {
                $elem = & $elem['element'];
                for ( $i=0; $i < count( $elem ); $i++ ) {
                    
                    // Name, Type, Label, Value
                    $name  = $elem[ $i ]['@']['name'];
                    $type  = $elem[ $i ]['@']['type'];
                    $label = $elem[ $i ]['#']['labels'][0]['#']['label'][0]['@']['value'];
                    $value = $elem[ $i ]['#']['values'][0]['#']['value'][0]['@']['value'];
                    
                    // Attributes
                    $attributes = array();
                    $attr = & $elem[$i]['#']['attributes'][0]['#'];
                    if ( is_array( $attr ) ) {
                        $attr = & $attr['attribute'];
                        for ( $j=0; $j < count( $attr ); $j++ ) {
                            $attributes[ $attr[ $j ]['@']['name'] ] = $attr[ $j ]['@']['value'];
                        }
                    }
                    
                    // Options
                    $options = array();
                    $opts = & $elem[$i]['#']['options'][0]['#'];
                    if ( is_array( $opts ) ) {
                        $opts = & $opts['option'];
                        for ( $j=0; $j < count( $opts ); $j++ ) {
                            $options[ $opts[ $j ]['@']['name'] ] = $opts[ $j ]['@']['value'];
                        }
                    }
                    
                    // Add
                    $e = & $f->addElement( $type, $name, $label, $attributes, $options );
                    $e->setValue( $value );
                    
                }
            }
            
            // Rules
            if ( is_array( $rule ) ) {
                $rule = & $rule['rule'];
                for ( $i=0; $i < count( $rule ); $i++ ) {
                    
                    // Element, Type, Error
                    $element = $rule[ $i ]['@']['element'];
                    $type    = $rule[ $i ]['@']['type'];
                    $error   = $rule[ $i ]['#']['errors'][0]['#']['error'][0]['@']['value'];
                    
                    // Options
                    $options = array();
                    $opts = & $rule[$i]['#']['options'][0]['#'];
                    if ( is_array( $opts ) ) {
                        $opts = & $opts['option'];
                        for ( $j=0; $j < count( $opts ); $j++ ) {
                            if ( $opts[ $j ]['@']['serialized'] == 'true' ) {
                                $opts[ $j ]['@']['value'] = YDObjectUtil::unserialize( $opts[ $j ]['@']['value'] );
                            }
                            if ( $opts[ $j ]['@']['id'] == '' ) {
                                $options = $opts[ $j ]['@']['value'];
                            } else {
                                $options[ $opts[ $j ]['@']['id'] ] = $opts[ $j ]['@']['value'];
                            }
                        }
                    }
                    
                    // Add
                    $f->addRule( $element, $type, $error, $options );
                }
            }
            
            // Compare Rules
            if ( is_array( $comp ) ) {
                $comp = & $comp['rule'];
                for ( $i=0; $i < count( $comp ); $i++ ) {
                    
                    // Type, Error
                    $type    = $comp[ $i ]['@']['type'];
                    $error   = $comp[ $i ]['#']['errors'][0]['#']['error'][0]['@']['value'];
                    
                    // Elements
                    $elements = array();
                    $elem = & $comp[$i]['#']['elements'][0]['#'];
                    if ( is_array( $elem ) ) {
                        $elem = & $elem['element'];
                        for ( $j=0; $j < count( $elem ); $j++ ) {
                            $elements[] = $elem[ $j ]['@']['name'];
                        }
                    }
                    
                    // Add
                    $f->addCompareRule( $elements, $type, $error );
                }
            }
            
            // Form Rules
            if ( is_array( $frul ) ) {
                $frul = & $frul['rule'];
                for ( $i=0; $i < count( $frul ); $i++ ) {
                    
                    // Callback
                    $callback = $frul[ $i ]['@']['callback'];
                    if ( strpos( $callback, '::' ) !== false ) {
                        $c = explode( '::', $callback );
                        $callback = array( $c[0], $c[1] );
                    }
                    
                    // Add
                    $f->addFormRule( $callback );
                }
            }
            
            // Filters
            if ( is_array( $filt ) ) {
                $filt = & $filt['element'];
                for ( $i=0; $i < count( $filt ); $i++ ) {
                    
                    // Element, Type
                    $element = $filt[ $i ]['@']['name'];
                    $type    = $filt[ $i ]['@']['filter'];
                    
                    // Add
                    $f->addFilter( $element, $type );
                }
            }
            
            // Registered Elements
            if ( is_array( $r_elem ) ) {
                $r_elem = & $r_elem['element'];
                for ( $i=0; $i < count( $r_elem ); $i++ ) {
                    
                    // Name, Class, File
                    $name  = $r_elem[ $i ]['@']['name'];
                    $class = $r_elem[ $i ]['@']['class'];
                    $file  = $r_elem[ $i ]['@']['file'];
                    
                    // Add
                    $f->registerElement( $name, $class, $file );
                }
            }
            
            // Registered Renderers
            if ( is_array( $r_rend ) ) {
                $r_rend = & $r_rend['renderer'];
                for ( $i=0; $i < count( $r_rend ); $i++ ) {
                    
                    // Name, Class, File
                    $name  = $r_rend[ $i ]['@']['name'];
                    $class = $r_rend[ $i ]['@']['class'];
                    $file  = $r_rend[ $i ]['@']['file'];
                    
                    // Add
                    $f->registerRenderer( $name, $class, $file );
                }
            }
            
            // Registered Filters
            if ( is_array( $r_filt ) ) {
                $r_filt = & $r_filt['filter'];
                for ( $i=0; $i < count( $r_filt ); $i++ ) {
                    
                    // Name, File
                    $name     = $r_filt[ $i ]['@']['name'];
                    $file     = $r_filt[ $i ]['@']['file'];
                    
                    // Callback
                    $callback = $r_filt[ $i ]['@']['callback'];
                    if ( strpos( $callback, '::' ) !== false ) {
                        $c = explode( '::', $callback );
                        $callback = array( $c[0], $c[1] );
                    }
                    
                    // Add
                    $f->registerFilter( $name, $callback, $file );
                }
            }
            
            // Registered Rules
            if ( is_array( $r_rule ) ) {
                $r_rule = & $r_rule['rule'];
                for ( $i=0; $i < count( $r_rule ); $i++ ) {
                    
                    // Name, File
                    $name     = $r_rule[ $i ]['@']['name'];
                    $file     = $r_rule[ $i ]['@']['file'];
                    
                    // Callback
                    $callback = $r_rule[ $i ]['@']['callback'];
                    if ( strpos( $callback, '::' ) !== false ) {
                        $c = explode( '::', $callback );
                        $callback = array( $c[0], $c[1] );
                    }
                    
                    // Add
                    $f->registerFilter( $name, $callback, $file );
                }
            }
            
            return $f;
            
        }

    }

?>