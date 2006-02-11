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

    // Define the default image type for the resulting graphs
    YDConfig::set( 'YD_GRAPH_TYPE', IMG_PNG, false );

    // Define the constants we are going to use
    define('HORIZONTAL', 0);
    define('VERTICAL', 1);

    // Define the different graph types
    define('SOLID', 0);
    define('DASHED', 1);
    define('DOTTED', 2);
    define('MEDIUM_SOLID', 3);
    define('MEDIUM_DASHED', 4);
    define('MEDIUM_DOTTED', 5);
    define('LARGE_SOLID', 6);
    define('LARGE_DASHED', 7);
    define('LARGE_DOTTED', 8);

    /**
     *	This class implements an addon module that is able to draw graphs using the GD library.
     */
    class YDGraph extends YDAddOnModule {

        /**
         *  This is the class constructor for the YDGraph class.
         *
         *  @param  $width              (optional) the width of the graph in pixels
         *  @param  $height             (optional) the height of the graph in pixels
         *  @param  $margin             (optional) the margin to keep around the graph
         *  @param  $backgroundColor    (optional) the background color for the graph
         */
        function YDGraph($width = 400, $height = 300, $margin = 7, $backgroundColor = '#ffffff') {

            // Initialize the parent class
            $this->YDAddOnModule();

            // Setup the module
            $this->_author = "Panacode Software; YDF addon by Francisco";
            $this->_version = "1.0";
            $this->_copyright = "Copyright (C) 2003 Eugen Fernea - eugenf@panacode.com";
            $this->_description = "This class implements a Graph Rendering Utility";

            // Setup the defaults
            $this->m_title = "";
            $this->m_width = $width;
            $this->m_height = $height;
            $this->m_image = imagecreate ($this->m_width, $this->m_height);
            $this->m_margin = $margin;
            $vBackColor = $this->_decode_color($backgroundColor);
            $this->m_backgroundColor = imagecolorallocate ($this->m_image, $vBackColor[0], $vBackColor[1], $vBackColor[2]);

            $this->m_minCount = 0;
            $this->m_maxCount = 1;
            $this->m_minValue = 0;
            $this->m_maxValue = 1;
            $this->m_style = SOLID;
            $this->m_strokeColor = $this->m_backgroundColor;
            $this->m_fillColor = $this->m_backgroundColor;

            $this->m_showHGrid = false;
            $this->m_showVGrid = false;

            $this->m_numberOfDecimals = 0;
            $this->m_thousandsSeparator = ',';
            $this->m_decimalSeparator = '.';

            $this->m_withLegend = false;

            $this->offset = false;
            $this->up_padding = 0.1;

            $this->setXAxis('#000000', SOLID, 4, "");
            $this->setYAxis('#000000', SOLID, 4, "");

            $this->setGrid("#cccccc", DASHED, "", DOTTED);

        }

        /**
         *  This function minimum and maximum for Y labels.
         *
         *  @param $min  minimum value.
         *  @param $max  max value.
         */
        function setLimits( $min = 0, $max = 0){
            $this->m_minValue = $min;
            $this->m_maxValue = $max;
        }


        /**
         *  This function sets the number of Ylabels
         *
         *  @param $offset  number of Ylabels.
         */
        function setOffset( $offset ){
            $this->offset = $offset;
        }
        
        /**
         *  This function sets the graph padding
         *
         *  @param $value  (optional) the grapdh padding. 
         *                 A float value between 0 and 1. 
         *                 Default: 0.1
         */
        function setPadding( $value = 0.1 ){
            $this->up_padding = $value;
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
         *  Set the legend for the graph.
         */
        function setLegend( $position, $borderStyle, $borderColor, $fillColor, $font ){
            $this->m_legendStyle = $style;

            $vStrokeColor = $this->_decode_color($strokeColor);
            $this->m_legendStroke = imagecolorallocate ($this->m_image, $vStrokeColor[0], $vStrokeColor[1], $vStrokeColor[2]);

            $vFillColor= $this->_decode_color($fillColor);
            $this->m_legendFill = imagecolorallocate ($this->m_image, $vFillColor[0], $vFillColor[1], $vFillColor[2]);

            $this->m_legendFont = $font;
            $this->m_withLegend = true;
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
            $vTextColor= $this->_decode_color($textColor);
            $this->m_textColor = imagecolorallocate ($this->m_image, $vTextColor[0], $vTextColor[1], $vTextColor[2]);

            $this->m_font = $font;
            $this->m_fontWidth = imagefontwidth($font);
            $this->m_fontHeight = imagefontheight($font);
        }

        /**
         *  Sets the plot area.
         */
        function setPlotArea($style, $strokeColor, $fillColor){
            $this->m_style = $style;
            if($strokeColor){
                $vStrokeColor = $this->_decode_color($strokeColor);
                $this->m_strokeColor = imagecolorallocate ($this->m_image, $vStrokeColor[0], $vStrokeColor[1], $vStrokeColor[2]);
            }
            if($fillColor){
                $vFillColor= $this->_decode_color($fillColor);
                $this->m_fillColor = imagecolorallocate ($this->m_image, $vFillColor[0], $vFillColor[1], $vFillColor[2]);
            }

        }

        /**
         *  This function sets the X axis line
         *
         *  @param $color   color.
         *  @param $style   style.
         *  @param $font    font.
         *  @param $title   title.
         */
        function setXAxis($color, $style, $font, $title){
            if(strlen($color) > 0){
                $this->m_showXAxis = true;
                $vColor = $this->_decode_color($color);
                $this->m_axisXColor= imagecolorallocate ($this->m_image, $vColor[0], $vColor[1], $vColor[2]);
                $this->m_axisXStyle = (int)$style;
                $this->m_axisXFont = (int)$font;
                $this->m_axisXFontWidth = imagefontwidth($font);
                $this->m_axisXFontHeight = imagefontheight($font);
                $this->m_axisXTitle = $title;
            }
        }

        /**
         *  This function sets the Y axis line
         *
         *  @param $color   color.
         *  @param $style   style.
         *  @param $font    font.
         *  @param $title   title.
         */
        function setYAxis($color, $style, $font, $title){
            if(strlen($color) > 0){
                $this->m_showYAxis = true;
                $vColor = $this->_decode_color($color);
                $this->m_axisYColor= imagecolorallocate ($this->m_image, $vColor[0], $vColor[1], $vColor[2]);
                $this->m_axisYStyle = (int)$style;
                $this->m_axisYFont = (int)$font;
                $this->m_axisYFontWidth = imagefontwidth($font);
                $this->m_axisYFontHeight = imagefontheight($font);
                $this->m_axisYTitle = $title;
            }
        }

        /**
         *  This function set the grid attibutes
         *
         *  @param $colorHorizontal colorHorizontal.
         *  @param $styleHorizontal styleHorizontal.
         *  @param $colorVertical   colorVertical.
         *  @param $styleVertical   styleVertical.
         */
        function setGrid($colorHorizontal, $styleHorizontal, $colorVertical, $styleVertical){
            if(strlen($colorHorizontal) > 0){
                $this->m_showHGrid = true;
                $vColor = $this->_decode_color($colorHorizontal);
                $this->m_gridHColor= imagecolorallocate ($this->m_image, $vColor[0], $vColor[1], $vColor[2]);
                $this->m_gridHStyle = $styleHorizontal;
            }
            if(strlen($colorVertical) > 0){
                $this->m_showVGrid = true;
                $vColor = $this->_decode_color($colorVertical);
                $this->m_gridVColor = imagecolorallocate ($this->m_image, $vColor[0], $vColor[1], $vColor[2]);
                $this->m_gridVStyle = $styleVertical;
            }
        }

        /**
         *  This function adds new series of data
         *
         *  @param $values      array of values.
         *  @param $plotType    plot type (eg: bar).
         *  @param $title       series title
         *  @param $style       style.
         *  @param $strokeColor strokeColor.
         *  @param $fillColor   fillColor.
         */
        function addSeries(&$values, $plotType, $title, $style, $strokeColor, $fillColor){
            $this->m_series[] = new series($this, $plotType, $values, $title, $style, $strokeColor, $fillColor);
            if($this->m_minValue===false){
                $this->m_minValue = @$values[0];
            }
            $minValue = $this->_min($values);
            $maxValue = $this->_max($values);
            if($minValue < $this->m_minValue) $this->m_minValue = $minValue;
            if($maxValue > $this->m_maxValue) $this->m_maxValue = $maxValue;

            $count = count($values);
            if($count < $this->m_minCount) $this->m_minCount = $count;
            if($count > $this->m_maxCount) $this->m_maxCount = $count;
        }

        /**
         *  This function sets X labels
         *
         *  @param  $labels      array of labels.
         *  @param  $textColor   text color.
         *  @param  $font        font
         *  @param  $direction   direction.
         */
        function setLabels(&$labels, $textColor, $font, $direction){
            $this->m_labels = &$labels;
            $vTextColor = $this->_decode_color($textColor);
            $this->m_labelsTextColor = imagecolorallocate ($this->m_image, $vTextColor[0], $vTextColor[1], $vTextColor[2]);
            $this->m_labelsFont = $font;
            $this->m_labelsFontWidth = imagefontwidth($font);
            $this->m_labelsFontHeight = imagefontheight($font);
            $this->m_labelsDirection = (int)$direction;

            $count = count($labels);
            if($count < $this->m_minCount) $this->m_minCount = $count;
            if($count > $this->m_maxCount) $this->m_maxCount = $count;

            $this->m_labelsMaxLength = $this->_maxlen($labels);
        }

        /**
         *  @internal
         */
        function _min(&$vvalues){
            if ( ! isset( $vvalues[0] ) ) {
                return 0;
            }
            $min = $vvalues[0];
            foreach($vvalues as $value){
                if ($min > $value){
                    $min = $value;
                }
            }
            return $min;
        }

        /**
         *  @internal
         */
        function _max(&$vvalues){
            if ( ! isset( $vvalues[0] ) ) {
                return 0;
            }
            $max = $vvalues[0];
            foreach($vvalues as $value){
                if ($max < $value){
                    $max = $value;
                }
            }
            return $max;
        }

        /**
         *  @internal
         */
        function _maxlen(&$vvalues){
            if ( ! isset( $vvalues[0] ) ) {
                return 0;
            }
            $max = strlen($vvalues[0]);
            foreach($vvalues as $value){
                if ($max < strlen($value)){
                    $max = strlen($value);
                }
            }
        return $max;
        }

        /**
         *  @internal
         */
        function _decode_color($scolor){
            $istart = 0;
            if($scolor[0] == '#'){
                $istart++;
            }
            $r = hexdec(@substr($scolor, $istart   , 2));
            $g = hexdec(@substr($scolor, $istart +2, 2));
            $b = hexdec(@substr($scolor, $istart +4, 2));
            $vcolor = array($r, $g, $b);
            return ( $vcolor );
        }

        /**
         *  @internal
         */
        function _set_style($img,$style,$fore,$back){
            switch($style){
                case DASHED:
                    $thickness = 1;
                    $istyle = array ($fore,$fore,$fore,$fore,$fore,
                                    $back,$back,$back,$back,$back);
                    break;
                case MEDIUM_DASHED:
                    $thickness = 2;
                    $istyle = array ($fore,$fore,$fore,$fore,$fore,$fore,$fore,$fore,
                                    $back,$back,$back,$back,$back,$back,$back,$back);
                    break;
                case LARGE_DASHED:
                    $thickness = 3;
                    $istyle = array ($fore,$fore,$fore,$fore,$fore,$fore,$fore,$fore,$fore,$fore,$fore,$fore,
                                    $back,$back,$back,$back,$back,$back,$back,$back,$back,$back,$back,$back);
                    break;
                case DOTTED:
                    $thickness = 1;
                    $istyle = array ($fore,$back,$back);
                    break;
                case MEDIUM_DOTTED:
                    $thickness = 2;
                    $istyle = array ($fore,$fore,$fore,$fore,
                                    $back,$back,$back,$back);
                    break;
                case LARGE_DOTTED:
                    $thickness = 3;
                    $istyle = array ($fore,$fore,$fore,$fore,$fore,$fore,
                                    $back,$back,$back,$back,$back,$back);
                    break;
                case SOLID:
                    $thickness=1;
                    $istyle = array ($fore,$fore);break;
                case MEDIUM_SOLID:
                    $thickness=2;
                    $istyle = array ($fore,$fore);break;
                case LARGE_SOLID:
                    $thickness=3;
                    $istyle = array ($fore,$fore);break;
                default:
                    $thickness=1;
                    $istyle = array ($fore,$fore);break;
            }
            imagesetthickness ($img, $thickness);
            imagesetstyle ($img, $istyle);
        }

        /**
         *  This function creates the graph and either sends it to the browser of saves it to a file.
         *
         *  @param  $file   (optional) If specified, the graph will be saved to the indicated file. If left as default,
         *                  the graph will be outputted to the browser.
         */
        function plot($file=''){
            $min = $this->m_minValue;
            $max = $this->m_maxValue + $this->m_maxValue * $this->up_padding;

            // margins
            $margin=$this->m_margin;
            $marginy = $margin;
            if($this->m_title){
                $marginy += $this->m_fontHeight*1.5;
            }

            $marginbottom = $margin+5;
            if($this->m_labelsDirection == HORIZONTAL){
                $marginbottom += $this->m_labelsFontWidth;
            }else{
                $marginbottom += $this->m_labelsMaxLength * $this->m_labelsFontWidth;
            }

            if(@$this->m_axisXTitle){
                $marginbottom += $this->m_axisXFontHeight*1.5;
            }

            $height = $this->m_height - $marginy - $marginbottom;
    //		if($m_withLegend){
            //
            //}
            if ($this->offset != false) $maxvalues = $this->offset;
            else 						$maxvalues = floor($height / $this->m_labelsFontHeight / 1.5);	// max displayable values

            $marginx = $margin+5;
            $marginx += strlen(number_format($this->m_maxValue, $this->m_numberOfDecimals, ',', '.')) * $this->m_labelsFontWidth;
            if(@$this->m_axisYTitle){
                $marginx += $this->m_axisYFontHeight*1.5;
            }

            $width = $this->m_width - $marginx - $margin;

            $w = $width / ($this->m_maxCount+0.2);
            $dx = $w * 0.8;
            $sx = $w - $dx;

            $width = $w * $this->m_maxCount+$sx;

            $h = ($height / $maxvalues);
            $dy = $height / ($max-$min);
            $vdy = ($max-$min) / $maxvalues;
            //plot border & background

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
            // plot values (Y)
            $this->_set_style($this->m_image,$this->m_axisYStyle, $this->m_axisYColor, $this->m_fillColor);
            for($i=0; $i<=$maxvalues; $i++){
                $yvalue = number_format($min+$vdy*$i, $this->m_numberOfDecimals, $this->m_decimalSeparator, $this->m_thousandsSeparator);
                imageline($this->m_image,
                    $marginx-3,
                    $marginy+$height - $i*$h,
                    $marginx,
                    $marginy+$height - $i*$h, IMG_COLOR_STYLED);
                imagestring ($this->m_image,
                    $this->m_labelsFont,
                    $marginx-strlen($yvalue)*$this->m_labelsFontWidth-4,
                    $marginy+$height - $i*$h - $this->m_labelsFontHeight/2,
                    $yvalue,
                    $this->m_labelsTextColor);
            }

            // plot grid
            if($this->m_showHGrid){
                for($i=0; $i<=$maxvalues; $i++){
                    $this->_set_style($this->m_image,$this->m_gridHStyle, $this->m_gridHColor, $this->m_fillColor);
                    imageline($this->m_image,
                        $marginx,
                        $marginy+$height - $i*$h,
                        $marginx+$width,
                        $marginy+$height - $i*$h,
                        IMG_COLOR_STYLED);
                }
            }
            if($this->m_showVGrid){
                for($i=0; $i<count($this->m_labels); $i++){
                    $len = strlen($this->m_labels[$i]);
                    if($len > 0){
                        $this->_set_style($this->m_image,$this->m_gridVStyle, $this->m_gridVColor, $this->m_fillColor);
                        imageline($this->m_image,
                            $marginx+$i*$w+$dx/2+$sx,
                            $height+$marginy,
                            $i*$w+$marginx+$dx/2+$sx,
                            $marginy,
                            IMG_COLOR_STYLED);
                    }
                }
            }

            $this->_set_style($this->m_image,$this->m_style, $this->m_strokeColor, $this->m_fillColor);
            imagerectangle($this->m_image, $marginx, $marginy, $marginx + $width, $marginy+$height , IMG_COLOR_STYLED);


            // plot graph
            foreach($this->m_series as $series){
                
                $cnt = count($series->m_values);
                if ($cnt == 0) continue;
                
                // LINE PLOT
                if($series->m_type == 'line'){
                    $this->_set_style($this->m_image,$series->m_style,$series->m_strokeColor, $this->m_fillColor);
                    $startx = $marginx+$dx/2+$sx ; $starty = $marginy+$height-$dy*($series->m_values[0]-$min);
                    for($i=1; $i<$cnt; $i++){
                        $x = $marginx+$i*$w+$dx/2+$sx;
                        $y = $marginy+$height-$dy*($series->m_values[$i]-$min);
                        imageline($this->m_image,$startx, $starty, $x, $y,IMG_COLOR_STYLED);
                        $startx = $x; $starty = $y;
                    }
                // AREA PLOT
                }else if($series->m_type == 'area'){
                    $this->_set_style($this->m_image,$series->m_style,$series->m_strokeColor, $this->m_fillColor);
                    $vpoints = '';
                    $startx = $marginx+$dx/2+$sx ; $starty = $marginy+$height-$dy*($series->m_values[0]-$min);
                    $vpoints[] = $startx; $vpoints[] = $marginy+$height;
                    for($i=0; $i<$cnt; $i++){
                        $x = $marginx+$i*$w+$dx/2+$sx;
                        $y = $marginy+$height-$dy*($series->m_values[$i]-$min);
                        $vpoints[]=$x; $vpoints[]=$y;
                        $startx = $x; $starty = $y;
                    }
                    $vpoints[] = $x; $vpoints[] = $marginy+$height;
                    imagefilledpolygon ( $this->m_image, $vpoints, $cnt+2, $series->m_fillColor);
                    imagepolygon ( $this->m_image, $vpoints, $cnt+2, IMG_COLOR_STYLED);
                // BAR PLOT
                }else if($series->m_type == 'bar'){
                    $this->_set_style($this->m_image,$series->m_style,$series->m_strokeColor, $this->m_fillColor);
                    $vpoints = '';
                    for($i=0; $i<$cnt; $i++){
                        imagefilledrectangle($this->m_image,
                            $sx + $marginx+$i*$w,
                            $marginy+$height-$dy*($series->m_values[$i]-$min),
                            $sx + $marginx+$i*$w+$dx,
                            $marginy+$height,
                            $series->m_fillColor);
                        imagerectangle($this->m_image,
                            $sx + $marginx+$i*$w,
                            $marginy+$height-$dy*($series->m_values[$i]-$min),
                            $sx + $marginx+$i*$w+$dx,
                            $marginy+$height,
                            IMG_COLOR_STYLED);
                    }
                // IMPULS PLOT
                }else if($series->m_type == 'impuls'){
                    $this->_set_style($this->m_image,$series->m_style,$series->m_fillColor,$this->m_fillColor);
                    for($i=0; $i<$cnt; $i++){
                        $x = $marginx+$i*$w+$dx/2+$sx;
                        $y = $marginy+$height-$dy*($series->m_values[$i]-$min);
                        imageline($this->m_image,$x, $y, $x, $marginy+$height, IMG_COLOR_STYLED);
                    }
                // STEP PLOT
                }else if($series->m_type == 'step'){
                    $this->_set_style($this->m_image,$series->m_style, $series->m_strokeColor,$this->m_fillColor);
                    $cnt = $cnt; $vpoints = '';
                    $startx = $marginx+$sx/2 ; $starty = $marginy+$height-$dy*($series->m_values[0]-$min);
                    $vpoints[] = $startx; $vpoints[] = $marginy+$height;
                    $vpoints[] = $startx; $vpoints[] = $starty;
                    for($i=1; $i<$cnt; $i++){
                        $x = $marginx+$i*$w+$sx/2;
                        $y = $marginy+$height-$dy*($series->m_values[$i]-$min);
                        $vpoints[]=$x; $vpoints[]=$starty;
                        $vpoints[]=$x; $vpoints[]=$y;
                        $startx = $x; $starty = $y;
                    }
                    $vpoints[] = $x+$w; $vpoints[] = $y;
                    $vpoints[] = $x+$w; $vpoints[] = $marginy+$height;
                    imagefilledpolygon ( $this->m_image, $vpoints, $cnt*2+2, $series->m_fillColor);
                    imagepolygon ( $this->m_image, $vpoints, $cnt*2+2, IMG_COLOR_STYLED);
                // DOT PLOT
                }else if($series->m_type == 'dot'){
                    $this->_set_style($this->m_image,$series->m_style, $series->m_strokeColor,$this->m_fillColor);
                    for($i=0; $i<$cnt; $i++){
                        $x = $marginx+$i*$w+$dx/2+$sx;
                        $y = $marginy+$height-$dy*($series->m_values[$i]-$min);
                        imagerectangle($this->m_image,$x-2, $y-2, $x+2, $y+2, IMG_COLOR_STYLED);
                        imagefilledrectangle($this->m_image,$x-1, $y-1, $x+1, $y+1, $series->m_fillColor);
                    }
                }
            }

            // plot X labels
            for($i=0; $i<count($this->m_labels); $i++){
                $len = strlen($this->m_labels[$i]);
                if($len > 0){
                    $this->_set_style($this->m_image,$this->m_axisXStyle, $this->m_axisXColor, $this->m_fillColor);
                    imageline($this->m_image,
                        $dx/2+$sx+$marginx+$i*$w,
                        $height+$marginy,
                        $dx/2+$sx+$i*$w+$marginx,
                        $height+$marginy+3,
                        IMG_COLOR_STYLED);

                    if($this->m_labelsDirection == HORIZONTAL){
                        imagestring ($this->m_image,
                            $this->m_labelsFont,
                            $dx/2+$sx+$marginx+$i*$w-$len*$this->m_labelsFontWidth/2,
                            $marginy+4+$height,
                            $this->m_labels[$i],
                            $this->m_labelsTextColor);
                    }else{
                        imagestringup ($this->m_image,
                            $this->m_labelsFont,
                            $dx/2+$sx+$marginx+$i*$w-$this->m_labelsFontHeight/2,
                            $marginy + $height + $len*$this->m_labelsFontWidth + 4,
                            $this->m_labels[$i],
                            $this->m_labelsTextColor);
                    }
                }

            }

            // plot X axis
            if($this->m_showXAxis){
                $this->_set_style($this->m_image,$this->m_axisXStyle, $this->m_axisXColor, $this->m_fillColor);
                imageline($this->m_image, $marginx, $marginy+$height, $marginx + $width, $marginy+$height, IMG_COLOR_STYLED);
                if($this->m_axisXTitle){
                    imagestring($this->m_image,
                        $this->m_axisXFont,
                        $marginx + ($width - strlen($this->m_axisXTitle) * $this->m_axisXFontWidth)/2,
                        $this->m_height - $margin - $this->m_axisXFontHeight,
                        $this->m_axisXTitle,
                        $this->m_axisXColor);
                }
            }
            // plot Y axis
            if($this->m_showYAxis){
                $this->_set_style($this->m_image,$this->m_axisYStyle, $this->m_axisYColor, $this->m_fillColor);
                imageline($this->m_image, $marginx, $marginy, $marginx, $marginy+$height, IMG_COLOR_STYLED);
                if($this->m_axisYTitle){
                    $titlewidth = strlen($this->m_axisYTitle) * $this->m_axisYFontWidth;
                    imagestringup ($this->m_image,
                        $this->m_axisYFont,
                        $margin,
                        $marginy + $titlewidth + ($height-$titlewidth)/2,
                        $this->m_axisYTitle,
                        $this->m_axisYColor);
                }
            }

            /*
            if(strlen($file) > 0){
                imagepng($this->m_image, $file);
            }else{
                header( 'Content-type: image/png' );
                imagepng($this->m_image);
            }
            */

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

    }

    /**
     *  @internal
     */
    class series{
        var $m_values, $m_seriesTitle, $m_strokeColor, $m_fillColor;
        var $m_chart, $m_type;

        /**
         *  @internal
         */
        function series(&$chart, $chartType, &$values, $title, $style, $strokeColor, $fillColor){
            $this->m_chart = &$chart;
            $this->m_type = $chartType;
            $this->m_style = (int)$style;
            $this->m_seriesTitle = $title;
            $this->m_values = &$values;
            $vStrokeColor = $this->_decode_color($strokeColor);
            $vFillColor= $this->_decode_color($fillColor);

            $this->m_strokeColor = imagecolorallocate ($this->m_chart->m_image, $vStrokeColor[0], $vStrokeColor[1], $vStrokeColor[2]);
            $this->m_fillColor = imagecolorallocate ($this->m_chart->m_image, $vFillColor[0], $vFillColor[1], $vFillColor[2]);
        }

        /**
         *  @internal
         */
        function _decode_color($scolor){
            $istart = 0;
            if($scolor[0] == '#') $istart++;

            $r = hexdec(@substr($scolor, $istart   , 2));
            $g = hexdec(@substr($scolor, $istart +2, 2));
            $b = hexdec(@substr($scolor, $istart +4, 2));
            $vcolor = array($r, $g, $b);
            return ( $vcolor );
        }
    }

?>
