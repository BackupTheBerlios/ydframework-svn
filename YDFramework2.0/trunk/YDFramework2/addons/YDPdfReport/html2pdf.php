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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    YDInclude( 'fpdf.php');

    class PDF extends FPDF{

      var $B;
      var $I;
      var $U;
      var $HREF;
      var $fontList;
      var $issetfont;
      var $issetcolor;
      
        function PDF($orientation = 'P', $unit = 'mm', $format = 'A4', $_title, $_url){

            // create fpdf object
            $this->FPDF($orientation, $unit, $format);
            $this->B = 0;
            $this->I = 0;
            $this->U = 0;
            $this->HREF = '';
            $this->PRE = false;
            $this->AddFont('Courier');
            $this->AddFont('Times');
            $this->SetFont('Times', '', 12);
            $this->fontlist = array("Times","Courier");
            $this->issetfont = true;
            $this->issetcolor = true;
            $this->articletitle = $_title;
            $this->articleurl = $_url;
            $this->AliasNbPages();
            
            $this->lastOpenedTag = '';
            $this->lastClosedTag = '';		
            $this->lastHeight = 0;
            $this->lineHeight = 6;
            
            $this->headerColor = array(255, 204, 120);
            
            $this->language = array('page'			=> 'Page ',
                                    'createdby'		=> 'Created by ' . YD_FW_NAME,
                                    'pageseparator'	=> "/",
                                    'createdlink'	=> YD_FW_HOMEPAGE);
                                    
            $this->_td = false;
            $this->_tableDefaults();
            $this->_tdDefaults();
        }
      
      
        function _tableDefaults(){

            // default table attributes
            $this->table = array(	'border'	=> 0
                                    );

            $this->tdsaved = array();
        }

        function _tdDefaults(){

            // default cell attributes
            $this->td	 = array(	'width'		=> 40,
                                    'border'	=> 0,
                                    'height'	=> 6,
                                    'align'		=> "L",
                                    'bgcolor'	=> false
                                    );
        }

      
        function setHeaderColor($r, $g, $b){
            $this->headerColor = array( $r, $g, $b);
        }
        
        
        // override language settings
        function setLanguage( $language ){
            foreach ($language as $name => $value)
                $this->language[$name] = $value;
        }
        
        // convert hexadecimal do decimal color codes
        function hex2dec($color = "#000000"){
            $tbl_color = array();
            $tbl_color['R'] = hexdec(substr($color, 1, 2));
            $tbl_color['G'] = hexdec(substr($color, 3, 2));
            $tbl_color['B'] = hexdec(substr($color, 5, 2));
            return $tbl_color;
        }

        // convert pixels to milimeters
        function px2mm($px){
            return $px * 25.4 / 72;
        }

        
        function txtentities($html){
            $trans = get_html_translation_table(HTML_ENTITIES);
            $trans = array_flip($trans);
            return strtr($html, $trans);
        }
      
      
      
        function WriteHTML($html, $bi){
        
            //remove all unsupported tags
            $this->bi = $bi;

            // current supported tags
            $mytags = array('<A>','<IMG>','<P>','<BR>','<FONT>','<TABLE>','<TR>', '<TD>', '<BLOCKQUOTE>','<H1>','<H2>','<H3>','<H4>','<H5>','<H6>','<PRE>','<RED>','<BLUE>','<UL>','<LI>','<HR>','<B>','<I>','<U>','<STRONG>','<EM>');
            
            // get supported tags from our html
            $html = strip_tags($html, implode('', $mytags));
            
            // replace carriage returns and special characters
            $html = str_replace("\n",		' ', $html); 
            $html = str_replace('&trade;',	'™', $html);
            $html = str_replace('&copy;',	'©', $html);
            $html = str_replace('&euro;',	'€', $html);
         
            // get our tags from html
            $supported_tags = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

            // cycle each tag and apply format in pdf
            foreach($supported_tags as $i => $e){
                
                // replace returns
                $e = trim( str_replace("\n", "",str_replace("\r", "", $e)) );
                
                // if empty, continue
                if ($e == '') continue;
                
                // if the first character is a slash then we need to close the tag and end format
                if ($e[0] == '/'){
                    $this->CloseTag( strtoupper( substr($e, 1) ) );
                    continue;
                }

                // extract tag and attributes
                $words	= explode(' ', $e);
                
                // tag name is always the first word
                $tag	= strtoupper(array_shift($words));

                // if it's not a html tag maybe it's a simple string OR a cell content (experimental)
                if ( !in_array("<$tag>", $mytags) ){ 
                    
                    if ($this->_td)	$this->Cell($this->td['width'], $this->td['height'], $this->txtentities($e), $this->td['border'], '', $this->td['align'], $this->td['bgcolor']);
                    else			$this->Write(5, stripslashes($this->txtentities($e)) );

                    continue;
                }

                // get all tag atributes			
                $attr = array();
                foreach($words as $v)
                    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$', $v, $at))
                        $attr[strtoupper($at[1])] = $at[2];
                
                // apply the format for this tag using all atributes
                $this->OpenTag($tag, $attr);

            }
        }
      
      
      
        function OpenTag($tag, $attr = array()){

            //Opening tag
            switch($tag){
                case 'TABLE' :	if (isset($attr['BORDER']))	$this->table['border'] = intval($attr['BORDER']);
                                break;
                
                case 'TR' :		$this->Ln($this->lineHeight + 1.5);
                                $this->lastHeight = 1.5;
                                break;
                
                case 'TD' :		// this is a table cell
                                $this->_td = true;
                
                                if (isset($attr['WIDTH']))	$this->td['width']  = $attr['WIDTH'] / 4;

                                if (isset($attr['HEIGHT']))	$this->td['height'] = $attr['HEIGHT'] / 4;

                                if (isset($attr['BORDER']))	$this->td['border'] = intval($attr['BORDER']);
                                else						$this->td['border'] = $this->table['border'];
                                
                                
                                if (isset($attr['ALIGN']))
                                    switch ($attr['ALIGN']) {
                                        case "CENTER" :		$this->td['align'] = "C"; break;
                                        case "RIGHT" :		$this->td['align'] = "R"; break;
                                    }

                                
                                if (isset($attr['BGCOLOR'])) {
                                    $color = $this->hex2dec($attr['BGCOLOR']);
                                    $this->SetFillColor($color['R'], $color['G'], $color['B']);
                                    $this->td['bgcolor'] = true;
                                }

                                break;
            
                case 'STRONG' :
                case 'B':		if ($this->bi)	$this->SetStyle('B', true);
                                else			$this->SetStyle('U', true);
                                break;

                case 'H1':		$this->Ln( 2 * $this->lineHeight );
                                $this->SetTextColor(150, 0, 0);
                                $this->SetFontSize(22);
                                break;

                case 'H2':		$this->Ln( 2 * $this->lineHeight - 1);
                                $this->SetTextColor(150, 0, 0);
                                $this->SetFontSize(16);
                                break;

                case 'H3':		$this->Ln( 2 * $this->lineHeight - 2);
                                $this->SetTextColor(150, 0, 0);
                                $this->SetFontSize(14);
                                break;

                case 'H4':		$this->Ln( 2 * $this->lineHeight - 3);
                                $this->SetTextColor(150, 0, 0);
                                $this->SetFontSize(12);
                                break;

                case 'H5':		$this->Ln( 2 * $this->lineHeight - 4);
                                $this->SetTextColor(150, 0, 0);
                                $this->SetFontSize(10);
                                break;

                case 'H6':		$this->Ln( 2 * $this->lineHeight - 5);
                                $this->SetTextColor(150, 0, 0);
                                $this->SetFontSize(8);
                                break;


                case 'PRE':		$this->SetFont('Courier', '', 11);
                                $this->SetFontSize(11);
                                $this->SetStyle('B', false);
                                $this->SetStyle('I', false);
                                $this->PRE = true;
                                break;
                
                case 'RED':		$this->SetTextColor(255, 0, 0);
                                break;

                case 'BLOCKQUOTE':	$this->mySetTextColor(100, 0, 45);
                                    $this->Ln(3);
                                    break;

                case 'BLUE':	$this->SetTextColor(0, 0, 255);
                                break;

                case 'I':		
                case 'EM':		if ($this->bi) $this->SetStyle('I', true);
                                break;

                case 'U':		$this->SetStyle('U', true);
                                break;

                case 'A':		$this->HREF = $attr['HREF'];
                                break;

                case 'IMG':		if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
                                    if(!isset($attr['WIDTH']))	$attr['WIDTH']  = 0;
                                    if(!isset($attr['HEIGHT']))	$attr['HEIGHT'] = 0;
                                
                                    if (isset($attr['SCALE'])){ 
                                        $attr['WIDTH']  *= floatval($attr['SCALE']);
                                        $attr['HEIGHT'] *= floatval($attr['SCALE']);
                                    }
                                    
                                    
                                    $this->Image($attr['SRC'], 2 + $this->GetX(), 1 + $this->GetY(), $this->px2mm($attr['WIDTH']), $this->px2mm($attr['HEIGHT']));
                                    $this->Cell($this->px2mm($attr['WIDTH']), 1);
                                    $this->lastHeight = 3 + $this->px2mm($attr['HEIGHT']) - $this->lineHeight;
                                }
                                break;

                case 'LI':		$this->Ln(2);
                                $this->SetTextColor(190, 0, 0);
                                $this->Write(5, '     » ');
                                $this->mySetTextColor(-1);
                                break;

                case 'TR':		$this->Ln(7);
                                $this->PutLine();
                                break;

                case 'BR':		$this->Ln($this->lastHeight + $this->lineHeight);
                                $this->lastHeight = 0;
                                break;
                
                case 'P':		// if last tag was a P ignore this P
                                if ($this->lastClosedTag == "/$tag")
                                    break;
                                
                                // if there was nothing define before this, return
                                if ($this->lastHeight < 0)	
                                    break;
                                
                                // create a big line: 2 normal lines + last height
                                $this->Ln($this->lastHeight + 2 * $this->lineHeight);

                                $this->lastHeight = 2 * $this->lineHeight;
                                break;
                
                case 'HR':		$this->PutLine();
                                break;

                case 'FONT':	if (isset($attr['COLOR']) and $attr['COLOR'] != '') {
                                    $coul = $this->hex2dec($attr['COLOR']);
                                    $this->mySetTextColor($coul['R'], $coul['G'], $coul['B']);
                                    $this->issetcolor=true;
                                }
                                if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
                                    $this->SetFont(strtolower($attr['FACE']));
                                    $this->issetfont = true;
                                }
                                break;

                }
                
                // save current tag
                $this->lastOpenedTag = $tag;
        }


      
        function CloseTag($tag){   

            if ($tag == 'TABLE')	$this->_tableDefaults();
            
            if ($tag == 'TD')		$this->_tdDefaults();

            //Closing tag
            if ($tag == 'P') {	$this->lastHeight = 2 * $this->lineHeight;
                                $this->Ln(2 * $this->lineHeight);}
            
            if ($tag == 'H1'){
                $this->Ln( 2 * $this->lineHeight );
                $this->normalFont();
            }

            if ($tag == 'H2'){
                $this->Ln( 2 * $this->lineHeight - 1);
                $this->normalFont();
            }

            if ($tag == 'H3'){
                $this->Ln( 2 * $this->lineHeight - 2);
                $this->normalFont();
            }
            
            if ($tag == 'H4'){
                $this->Ln( 2 * $this->lineHeight - 3);
                $this->normalFont();
            }

            if ($tag == 'H5'){
                $this->Ln( 2 * $this->lineHeight - 4);
                $this->normalFont();
            }
            if ($tag == 'H6'){
                $this->Ln( 2 * $this->lineHeight - 5);
                $this->normalFont();
            }

            
            if ($tag == 'PRE'){
                $this->SetFont('Times', '', 12);
                $this->SetFontSize(12);
                $this->PRE = false;
            }
            
            if ($tag == 'RED' || $tag == 'BLUE') $this->mySetTextColor(-1);
            
            if ($tag == 'BLOCKQUOTE'){
                $this->mySetTextColor(0, 0, 0);
                $this->Ln(3);
            }
            
            if($tag == 'STRONG')$tag = 'B';
            if($tag == 'EM')	$tag = 'I';
            
            if((!$this->bi) && $tag == 'B') $tag = 'U';
            
            if($tag == 'B' or $tag == 'I' or $tag == 'U') $this->SetStyle($tag,false);

            if($tag == 'A') $this->HREF='';
            
            if($tag == 'FONT'){
                if ($this->issetcolor == true)
                    $this->SetTextColor(0, 0, 0);
              
                if ($this->issetfont) {
                    $this->SetFont('Times', '', 12);
                    $this->issetfont = false;
                }
            }
            
            $this->lastClosedTag = "/$tag";
        }

        function normalFont(){
            $this->SetFont('Times', '', 12);
            $this->SetFontSize(12);
            $this->SetStyle('U', false);
            $this->SetStyle('B', false);
            $this->SetTextColor(0, 0, 0);			
        }

      
        function Footer(){

            //Go to 1.5 cm from bottom
            $this->SetY(-15);
            
            //Select Times italic 8
            $this->SetFont('Times', '', 8);
            
            //Print centered page number
            $this->SetTextColor(0, 0, 0);
            $this->Cell(0, 4, $this->language['page'] . $this->PageNo() . $this->language['pageseparator'] . '{nb}', 0, 1, 'C');
            $this->SetTextColor(0, 0, 180);
            $this->Cell(0, 4, $this->language['createdby'], 0, 0, 'C', 0, $this->language['createdlink']);
            $this->mySetTextColor(-1);
        }
      

     
        function SetStyle($tag, $enable){
            $this->$tag += ($enable ? 1 : -1);
            $style = '';
            foreach (array('B','I','U') as $s) 
                if($this->$s > 0) $style .= $s;
            
            $this->SetFont('',$style);
        }
      
        function PutLink($URL, $txt){   

            //Put a hyperlink
            $this->SetTextColor(0, 0, 255);
            $this->SetStyle('U', true);
            $this->Write(5, $txt, $URL);
            $this->SetStyle('U', false);
            $this->mySetTextColor(-1);
        }
      
        function PutLine(){   
            $this->Ln(2);
            $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 187, $this->GetY());
            $this->Ln(3);
        }
      
      
        function mySetTextColor($r, $g = 0, $b = 0){
            static $_r = 0, $_g = 0, $_b = 0;
            if ($r==-1)	$this->SetTextColor($_r, $_g, $_b);
            else {		$this->SetTextColor($r,  $g,  $b);
                        $_r = $r;
                        $_g = $g;
                        $_b = $b;
            }
        }
      
        function PutMainTitle($title){
            if (strlen($title) > 55) $title = substr($title, 0, 55) . "...";
            $this->SetTextColor(33, 32, 95);
            $this->SetFontSize(20);
            $this->SetFillColor($this->headerColor[0], $this->headerColor[1], $this->headerColor[2]);
            $this->Cell(0, 12, $title, 1, 1, "C", 1);
            $this->SetFillColor(255, 255, 255);
            $this->SetFontSize(12);
            $this->Ln(5);
        }
      
        function PutMinorHeading($title){
            $this->SetFontSize(12);
            $this->Cell(0, 5, $title, 0, 1, "C");
            $this->SetFontSize(12);
        }
      
        function PutMinorTitle($title, $url=''){
            $title = str_replace('http://', '', $title);
            if (strlen($title) > 70) 
                if (!(strrpos($title, '/') == false)) 
                        $title = substr($title, strrpos($title, '/') + 1);
            $title = substr($title, 0, 70);
            $this->SetFontSize(16);
            if ($url!='') {
                    $this->SetStyle('U', false);
                    $this->SetTextColor(0, 0, 180);
                    $this->Cell(0, 6, $title, 0, 1, "C", 0, $url);
                    $this->SetTextColor(0, 0, 0);
                    $this->SetStyle('U', false);
            } else  $this->Cell(0, 6, $title, 0, 1, "C", 0);

            $this->SetFontSize(12);
            $this->Ln(4);
        }


        function create( $format, $name ){
        
            switch( strtolower( $format )){
                case 'inline' :		$this->output( '', '');
                                    while (@ob_end_flush());
                                    die();
                
                case 'file' :		return $this->output( $name, 'F');
                
                case 'download' :	$this->output( $name, 'D');
                                    while (@ob_end_flush());
                                    die();
            
                case 'buffer' :		return $this->buffer;
            }

        }

    }

?>
