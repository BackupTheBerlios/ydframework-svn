<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

        This program is free software; you can redistribute it and/or
        modify it under the terms of the GNU General Public License
        as published by the Free Software Foundation;

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

        Based on:

            PanaChart - PHP Chart Generator -  October 2003
            Yellow Duck Framework addon by Francisco Azevedo

            Copyright (C) 2003 Eugen Fernea - eugenf@panacode.com
            Panacode Software - info@panacode.com
            http://www.panacode.com/

    */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }
    
    include_once( YD_DIR_HOME_ADD .'/YDGraph/YDGraph.php' );
    
    /**
     * This class implements an addon module that is able to draw pie graphs using the GD library.
     */
    class YDGraphPie extends YDAddOnModule {
        
        /**
         *  This is the class constructor for the YDGraphPie class.
         *
         *  @param  $width              (optional) the width of the graph in pixels
         *  @param  $height             (optional) the height of the graph in pixels
         *  @param  $margin             (optional) the margin to keep around the graph
         *  @param  $backgroundColor    (optional) the background color for the graph
         */
        function YDGraphPie( $width=400, $height=300, $margin=7, $backgroundColor='#ffffff' ) {
            
            // Initialize the parent class
            $this->YDAddOnModule();
            
            // Setup the module
            $this->_author = "David Bittencourt";
            $this->_version = "1.0";
            $this->_copyright = "(c) 2005 David Bittencourt, muitocomplicado@hotmail.com";
            $this->_description = "This class implements a pie chart rendering utility";
            
            // Setup the defaults
            $this->m_title = "";
            $this->m_width = $width;
            $this->m_height = $height;
            $this->m_image = imagecreate ($this->m_width, $this->m_height);
            $this->m_margin = $margin;
            $vBackColor = YDGraph::_decode_color($backgroundColor);
            $this->m_backgroundColor = imagecolorallocate ($this->m_image, $vBackColor[0], $vBackColor[1], $vBackColor[2]);
            
            $this->m_strokeColor = imagecolorallocate ($this->m_image, $vBackColor[0], $vBackColor[1], $vBackColor[2]);
            $this->m_fillColor = $this->m_backgroundColor;
            
            $this->m_numberOfDecimals = 0;
            $this->m_thousandsSeparator = ',';
            $this->m_decimalSeparator = '.';
            
            $this->m_showtotal   = false;
            $this->m_total       = null;
            $this->m_formattotal = true;
            $this->m_totalstring = 'Total: ';
            
        }
        
        /**
         *  This function creates the graph and either sends it to the browser
         *  of saves it to a file.
         *
         *  @param  $file   (optional) If specified, the graph will be saved to
         *                  the indicated file. If left as default, the graph
         *                  will be outputted to the browser.
         */
        function plot( $file='' ) {
            
            // margins
            $margin=$this->m_margin;
            $marginy = $margin;
            $marginx = $margin+5;
            
            if($this->m_title){
                $marginy += $this->m_fontHeight*1.5;
            }
            
            $marginbottom = $margin+5;
            $height = $this->m_height - $marginy - $marginbottom;
            $width = $this->m_width - $marginx - $margin;
            
            imagefilledrectangle($this->m_image, $marginx, $marginy, $marginx + $width, $marginy+$height , $this->m_fillColor);
            
            // plot title
            if($this->m_title){
                imagestring ($this->m_image,
                        $this->m_font,
                        ($this->m_width-strlen($this->m_title)*$this->m_fontWidth)/2,
                        $margin,
                        $this->m_title,
                        $this->m_textColor);
            }
            
            YDGraph::_set_style( $this->m_image, SOLID, $this->m_strokeColor, $this->m_fillColor );
            
            $sum = array_sum( $this->m_values );
                    
            $colors = array( '#CC3232', '#70DBDB', '#66CD00', '#FF4040', '#8A2BE2', '#5F9F9F', '#EED2EE', '#E9967A', '#6666FF', '#A2C257', '#EEB4B4', '#8F5E99', '#EEC900',  '#EE799F', '#B2DFEE', '#70DB93', '#F4A460', '#AAAAFF',     '#CC3232', '#70DBDB', '#66CD00', '#FF4040', '#8A2BE2', '#5F9F9F', '#EED2EE', '#E9967A', '#6666FF', '#A2C257', '#EEB4B4', '#8F5E99', '#EEC900',  '#EE799F', '#B2DFEE', '#70DB93', '#F4A460', '#AAAAFF' );
            
            // pieces
            $degrees = array();
            for($i=0; $i<count( $this->m_values); $i++){
                $value = ( $this->m_values[$i]/$sum ) * 360;
                if( $value > 0 && floor( ( $value/360 ) * 100 ) > 0 ) {
                    $degrees[$i] = $value;
                }
            }
            
            // sizes
            $diamx = $width/2;
            $diamy = $height;
            
            if ( $diamx > $diamy ) {
                $diam = $diamy;
            } else {
                $diam = $diamx;
            }
            $radius = $diam/2;
            
            $x = $marginx + $radius;
            $y = $marginy + $radius;
            
            imagearc( $this->m_image, $x, $y, $diam, $diam, 0, 360, $this->m_strokeColor );
            
            // lines
            $last_angle = 0;
            foreach ( $degrees as $i => $deg ) {
                
                $last_angle = $last_angle+$deg;
                $end_x = floor($x + ($radius * cos($last_angle*pi()/180)));
                $end_y = floor($y + ($radius * sin($last_angle*pi()/180)));
                
                imageline( $this->m_image, $x, $y, $end_x, $end_y, $this->m_strokeColor );
                
            }
            // colors
            $last_angle =0;
            $pointer = 0;
            $max_len = 0;
            foreach ( $degrees as $i => $deg ) {
                
                $pointer = $last_angle + $deg;
                $this_angle = ($last_angle + $pointer) / 2;
                $last_angle = $pointer;
                
                $end_x = floor($x + ($radius * cos($this_angle*pi()/180)));
                $end_y = floor($y + ($radius * sin($this_angle*pi()/180)));
                
                $mid_x = floor(($x+($end_x))/2);
                $mid_y = floor(($y+($end_y))/2);
                
                $hex_split = YDGraph::_decode_color( $colors[$i] );
                if ( isset( $this->m_colors[$i] ) ) {
                    $hex_split = YDGraph::_decode_color( $this->m_colors[$i] );
                }
                $piece_color = imagecolorallocate( $this->m_image, $hex_split[0],$hex_split[1],$hex_split[2]);
                imagefilltoborder( $this->m_image, $mid_x, $mid_y, $this->m_strokeColor, $piece_color );
                
                $percent = number_format( ( $deg/360 ) * 100, $this->m_numberOfDecimals, $this->m_decimalSeparator, $this->m_thousandsSeparator );
                $max_len = strlen( $percent ) > $max_len ? strlen( $percent ) : $max_len;
                
            }
            
            // labels
            $last_position = $marginy;
            foreach ( $degrees as $i => $deg ) {
                
                $percent = number_format( ( $deg/360 ) * 100, $this->m_numberOfDecimals, $this->m_decimalSeparator, $this->m_thousandsSeparator );
                
                $align = 0;
                switch ( $this->m_labelsFont ) {
                    case 1: $align = 1; break;
                    case 2: $align = -1; break;
                    case 3:
                    case 4: $align = -2; break;
                    case 5:
                    case 6:
                    case 7:
                    case 8: $align = -3; break;
                }
                
                if ( $this->m_labelsFontHeight + 3 >= 15 ) {
                    $last_position += $this->m_labelsFontHeight + 3;
                } else {
                    $last_position += 15;
                }
                
                $hex_split = YDGraph::_decode_color( $colors[$i] );
                if ( isset( $this->m_colors[$i] ) ) {
                    $hex_split = YDGraph::_decode_color( $this->m_colors[$i] );
                }
                $piece_color = imagecolorallocate( $this->m_image, $hex_split[0],$hex_split[1],$hex_split[2]);
                
                $w = $x+$radius;
                $h = $last_position;
                
                imagefilledrectangle( $this->m_image, $w+20, $h, $w+30, $h+10, $piece_color );
                imagestring( $this->m_image, $this->m_labelsFont, $w+40, $h+$align, str_repeat( ' ', ( $max_len - strlen( $percent ) ) ) . $percent . "% " . $this->m_labels[$i], $this->m_labelsTextColor );
                
            }

            if ( $this->m_showtotal ) {
                if ( isset( $this->m_total ) ) {
                    $sum = $this->m_total;
                } 
                if ( $this->m_formattotal ) {
                    $sum = number_format( $sum, $this->m_numberOfDecimals, $this->m_decimalSeparator, $this->m_thousandsSeparator );
                }
                imagestring( $this->m_image, $this->m_labelsFont, $w+20, $h+20, $this->m_totalstring . $sum, $this->m_labelsTextColor );
            }
            
            if( strlen($file) > 0 ){
                switch ( YDConfig::get( 'YD_GRAPH_TYPE' ) ) {
                    case IMG_GIF:
                        imagegif( $this->m_image, $file );
                        break;
                    case IMG_JPG:
                    case IMG_JPEG:
                        imagejpeg( $this->m_image, $file );
                        break;
                    default:
                        imagepng( $this->m_image, $file );
                        break;
                }
            } else{
                switch ( YDConfig::get( 'YD_GRAPH_TYPE' ) ) {
                    case IMG_GIF:
                        header( 'Content-type: image/gif' );
                        imagegif( $this->m_image );
                        break;
                    case IMG_JPG:
                    case IMG_JPEG:
                        header( 'Content-type: image/jpeg' );
                        imagejpeg( $this->m_image );
                        break;
                    default:
                        header( 'Content-type: image/png' );
                        imagepng( $this->m_image );
                        break;
                }

            }
            
        }
        
        /**
         *  This function sets the display format
         *
         *  @param $numberOfDecimals     number of decimals.
         *  @param $thousandsSeparator   thousands separator.
         *  @param $decimalSeparator     decimal separator.
         */
        function setFormat($numberOfDecimals, $thousandsSeparator, $decimalSeparator){
            $this->m_numberOfDecimals = $numberOfDecimals;
            $this->m_thousandsSeparator = $thousandsSeparator;
            $this->m_decimalSeparator = $decimalSeparator;
        }
        
        /**
         *  This function sets labels
         *
         *  @param  $labels      array of labels.
         *  @param  $textColor   text color.
         *  @param  $font        font
         */
        function setLabels(&$labels, $textColor, $font){
            $this->m_labels = &$labels;
            $vTextColor = YDGraph::_decode_color($textColor);
            $this->m_labelsTextColor = imagecolorallocate ($this->m_image, $vTextColor[0], $vTextColor[1], $vTextColor[2]);
            $this->m_labelsFont = $font;
            $this->m_labelsFontWidth = imagefontwidth($font);
            $this->m_labelsFontHeight = imagefontheight($font);
        }
        
        /**
         *  This function sets the values
         *
         *  @param $values       Array of values
         */
        function setValues( & $values ) {
            $this->m_values = & $values;
        }
        
        /**
         *  This function sets the title
         *
         *  @param $title       title.
         *  @param $textColor   textColor.
         *  @param $font        font.
         */
        function setTitle($title, $textColor, $font){
            $this->m_title = $title;
            $vTextColor= YDGraph::_decode_color($textColor);
            $this->m_textColor = imagecolorallocate ($this->m_image, $vTextColor[0], $vTextColor[1], $vTextColor[2]);

            $this->m_font = $font;
            $this->m_fontWidth = imagefontwidth($font);
            $this->m_fontHeight = imagefontheight($font);
        }
        
        /**
         *  This function sets the background colors of the pieces
         *
         *  @param $colors       An array with the colors
         */
        function setColors( & $colors ) {
            $this->m_colors = & $colors;
        }
        
        /**
         *  This function sets a custom total value
         *
         *  @param $total       (optional) The total value. If null, the sum of all values passed with setValues. Default: null.
         *  @param $format      (optional) Format the value with the definitions of setFormat. Default: false.
         */
        function setTotal( $total=null, $format=false ) {
            $this->m_total = $total;
            $this->m_showtotal = true;
            $this->m_formattotal = $format;
        }
        
        /**
         *  This function defines if the total value should be shown or not
         *
         *  @param  $string  (optional)  The string to be added before the total value. Default: "Total: ".
         *  @param  $show    (optional)  Boolean indicating if the total should be displayed or not. Default: true.
         */
        function showTotal( $string='Total: ', $show=true ) {
            $this->m_showtotal   = (boolean) $show;
            $this->m_totalstring = $string;
        }
        
    }

?>
