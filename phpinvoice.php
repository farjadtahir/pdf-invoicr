<?php
/*******************************************************************************
* PHP Invoice                                                                  *
*                                                                              *
* Version: 1.5	                                                               *
* Author:  Farjad Tahir	                                    				   *
* http://www.splashpk.com                                                      *
*******************************************************************************/
require_once('inc/__autoload.php');

class phpinvoice extends FPDF_rotation  {

    public $lang;	 		/* Font Name : See inc/fpdf/font for all supported fonts */
    public $document;		/* Items table background color opacity. Range (0.00 - 1) */
    public $type;			/* Spacing between Item Tables */
    public $reference;	  	/* Currency formater */
    public $logo;   		/* l: Left Side , t: Top Side , r: Right Side */
    public $footerImage;
    public $color;
    public $date;
    public $time;
    public $due;
    public $from;
    public $to;
    public $items;
    public $totals;
    public $badge;
    public $addText;
    public $footernote;
    public $dimensions;
    public $footerDimensions;
    public $display_tofrom = true;
    public $curreny_direction = "left";
    public $items_total	= 0;
    public $grand_total = 0;
    public $hide_discount = 0;
    public $hide_vat = 0;
    public $title = "";
    protected $B = 0;
    protected $I = 0;
    protected $U = 0;
    protected $HREF = '';
	private $font 			 = 'helvetica';
	private $columnOpacity   = 0.06;
	private $columnSpacing   = 0.3;
	private $referenceformat = array('.',',');
	private $margins 		 = array('l' => 15, 't' => 15, 'r' => 15);

    /******************************************
     * Class Constructor               		 *
     * param : Page Size , Currency, Language *
     ******************************************/
    public function __construct($size='A4',$currency='$',$language='en') {
        $this->columns  		  	= 4;
        $this->items 			  = array();
        $this->totals 			 = array();
        $this->addText 			= array();
        $this->firstColumnWidth   = 70;
        $this->currency 		   = html_entity_decode($currency, ENT_NOQUOTES, 'UTF-8');
        $this->maxImageDimensions 	= array(230,130);
        $this->setLanguage($language);
        $this->setDocumentSize($size);
        $this->setColor("#222222");

        parent::__CONSTRUCT('P','mm',array($this->document['w'],$this->document['h']));
        $this->AliasNbPages();
        $this->SetMargins($this->margins['l'],$this->margins['t'],$this->margins['r']);
    }

    private function setLanguage($language) {
        $lang = array();
        $this->language = $language;
        include('inc/languages/'.$language.'.inc');
        $this->lang = $lang;
    }

    private function setDocumentSize($dsize) {
        switch ($dsize) {
            case 'A4':
                $document['w'] = 210;
                $document['h'] = 297;
                break;
            case 'letter':
                $document['w'] = 215.9;
                $document['h'] = 279.4;
                break;
            case 'legal':
                $document['w'] = 215.9;
                $document['h'] = 355.6;
                break;
            default:
                $document['w'] = 210;
                $document['h'] = 297;
                break;
        }
        $this->document = $document;
    }

    public function setColor($rgbcolor) {
        $this->color = $this->hex2rgb($rgbcolor);
    }

    private function hex2rgb($hex) {
        $hex = str_replace("#", "", $hex);
        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = array($r, $g, $b);
        return $rgb;
    }

    public function setTimeZone($zone = "") {
        if(!empty($zone) and $this->isValidTimezoneId($zone) === TRUE) {
            date_default_timezone_set($zone);
        }
    }

    public function isValidTimezoneId($zone) {
        try{ new DateTimeZone($zone); }
        catch(Exception $e){ return FALSE; }
        return TRUE;
    }

    public function setCurrenyDirection($direction = "left") {
        $this->curreny_direction = $direction;
    }

    public function setType($title) {
        $this->title = $title;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function setDue($date) {
        $this->due = $date;
    }

    public function setLogo($logo = 0,$maxWidth = 0,$maxHeight = 0) {
        if($maxWidth and $maxHeight) {
            $this->maxImageDimensions = array($maxWidth,$maxHeight);
        }
        $this->logo = $logo;
        $this->dimensions = $this->resizeToFit($logo);
    }

    private function resizeToFit($image) {
        list($width, $height) = getimagesize($image);
        $newWidth 	= $this->maxImageDimensions[0]/$width;
        $newHeight 	= $this->maxImageDimensions[1]/$height;
        $scale 		= min($newWidth, $newHeight);
        return array(
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height))
        );
    }

    private function pixelsToMM($val){
        $mm_inch = 25.4;
        $dpi = 96;
        return ($val * $mm_inch)/$dpi;
    }

    public function setFooterImage($image = 0,$maxWidth = 0,$maxHeight = 0) {
        if($maxWidth and $maxHeight) {
            $this->maxImageDimensions = array($maxWidth,$maxHeight);
        }
        $this->footerImage = $image;
        $this->footerDimensions = $this->resizeToFit($image);
    }

    public function hide_tofrom() {
        $this->display_tofrom = false;
    }

    public function setFrom($data) {
        $this->from = $data;
    }

    public function setTo($data) {
        $this->to = $data;
    }

    public function setReference($reference) {
        $this->reference = $reference;
    }

    public function setNumberFormat($decimals,$thousands_sep) {
        $this->referenceformat = array($decimals,$thousands_sep);
    }

    public function flipflop() {
        $this->flipflop = true;
    }

    public function hideDiscountCol() {
        $this->hide_discount = 1;
        $this->columns -= 1;
    }

    public function hideVATCol() {
        $this->hide_vat = 1;
        $this->columns -= 1;
    }

    public function addItem($item , $description = "" , $quantity = 1 , $vat = 0 , $price = 0 , $discount = 0) {

        $p['item'] 			= $item;
        $p['description'] 	= $this->br2nl($description);
        $total_amount		= 0;
        $total_amount 		= (((float)$quantity)*((float)$price));

        if($vat !== false) {
            $p['vat']			= $vat;
            if(is_numeric($vat)) {
                if($this->curreny_direction == "left") {
                    $p['vat']		= $this->currency.' '.number_format($vat,2,$this->referenceformat[0],$this->referenceformat[1]);
                }else {
                    $p['vat']		= number_format($vat,2,$this->referenceformat[0],$this->referenceformat[1]).' '.$this->currency;
                }
                $total_amount	= $total_amount + $vat;
            }else if (strpos($vat, '%') !== false) {
                $vat			= (float) str_replace("%","",$vat);
                $vat_amount		= ($vat / 100);
                $vat_amount		= $total_amount * $vat_amount;
                $total_amount	= $total_amount + $vat_amount;
            }
            $this->vatField = true;
            $this->columns = 5;
        }

        if($discount !== false) {
            $this->firstColumnWidth = 58;
            $p['discount'] = $discount;
            if(is_numeric($discount)) {
                if($this->curreny_direction == "left") {
                    $p['discount']	= $this->currency.' '.number_format($discount,2,$this->referenceformat[0],$this->referenceformat[1]);
                }else{
                    $p['discount']	= number_format($discount,2,$this->referenceformat[0],$this->referenceformat[1]).' '.$this->currency;
                }
                $total_amount	= $total_amount - $discount;
            }else if (strpos($discount, '%') !== false) {
                $discount			= (float) str_replace("%","",$discount);
                $discount_amount	= ($discount / 100);
                $discount_amount	= $total_amount * $discount_amount;
                $total_amount		= $total_amount - $discount_amount;
            }
            $this->discountField = true;
            $this->columns = 6;
        }

        $p['quantity'] 		= $quantity;
        $p['price']			= $price;
        $p['total']			= $total_amount;

        $this->items[]		= $p;
        $this->items_total  += $total_amount;

    }

    private function br2nl($string) {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    }

    public function GetPercentage($percentage) {
        if(!empty($this->grand_total) and !empty($percentage)) {
            $total	= $this->grand_total;
            $percentage = ($percentage / 100);
            $vat = $total * $percentage;
            return $vat;
        }
    }

    public function addTotal($name,$value,$colored = FALSE,$subtract = FALSE) {

        $t['name']			= $name;
        $t['value']			= $value;
        if(is_numeric($value)) {
            if($this->curreny_direction == "left") {
                $t['value']			= $this->currency.' '.number_format($value,2,$this->referenceformat[0],$this->referenceformat[1]);
            }else{
                $t['value']			= number_format($value,2,$this->referenceformat[0],$this->referenceformat[1]).' '.$this->currency;
            }
            if($subtract === TRUE){
                $this->grand_total -= $value;
            }else{
                $this->grand_total += $value;
            }
        }
        $t['colored']		= $colored;
        $this->totals[]		= $t;
    }

    public function GetGrandTotal() {
        return $this->grand_total;
    }

    public function addTitle($title) {
        $title = mb_convert_encoding($title, "HTML-ENTITIES", 'UTF-8');
        $this->addText[] = array('title',$title);
    }

    public function addParagraph($paragraph) {
        $paragraph = $this->br2nl($paragraph);
        $paragraph = mb_convert_encoding($paragraph, "HTML-ENTITIES", 'UTF-8');
        $this->addText[] = array('paragraph',$paragraph);
    }

    public function addBadge($badge) {
        $this->badge = $badge;
    }

    public function setFooternote($note) {
        $this->footernote = $note;
    }

    public function render($name='',$destination='') {
        $this->AddPage();
        $this->Body();
        $this->AliasNbPages();
        return $this->Output($name,$destination);
    }

    public function Body() {
        $width_other = ($this->document['w']-$this->margins['l']-$this->margins['r']-$this->firstColumnWidth-($this->columns*$this->columnSpacing))/($this->columns-1);
        $cellHeight = 8;
        $bgcolor = (1-$this->columnOpacity)*255;
        if($this->items) {
            foreach($this->items as $item)
            {
                if($item['description'])
                {
                    //Precalculate height
                    $calculateHeight = new phpinvoice;
                    $calculateHeight->addPage();
                    $calculateHeight->setXY(0,0);
                    $calculateHeight->SetFont($this->font,'',7);
                    $calculateHeight->MultiCell($this->firstColumnWidth,3,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",$item['description']),0,'L',1);
                    $descriptionHeight = $calculateHeight->getY()+$cellHeight+2;
                    $pageHeight = $this->document['h']-$this->GetY()-$this->margins['t']-$this->margins['t'];
                    if($pageHeight < 35)
                    {
                        $this->AddPage();
                    }
                }
                $cHeight = $cellHeight;
                $this->SetFont($this->font,'b',8);
                $this->SetTextColor(50,50,50);
                $this->SetFillColor($bgcolor,$bgcolor,$bgcolor);
                $this->Cell(1,$cHeight,'',0,0,'L',1);
                $x = $this->GetX();
                $this->Cell($this->firstColumnWidth,$cHeight,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",$item['item']),0,0,'L',1);
                if($item['description']) {
                    $resetX = $this->GetX();
                    $resetY = $this->GetY();
                    $this->SetTextColor(120,120,120);
                    $this->SetXY($x,$this->GetY()+8);
                    $this->SetFont($this->font,'',7);
                    $this->MultiCell($this->firstColumnWidth,3,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",$item['description']),0,'L',1);
                    //Calculate Height
                    $newY = $this->GetY();
                    $cHeight = $newY-$resetY+2;
                    //Make our spacer cell the same height
                    $this->SetXY($x-1,$resetY);
                    $this->Cell(1,$cHeight,'',0,0,'L',1);
                    //Draw empty cell
                    $this->SetXY($x,$newY);
                    $this->Cell($this->firstColumnWidth,2,'',0,0,'L',1);
                    $this->SetXY($resetX,$resetY);
                }
                $this->SetTextColor(50,50,50);
                $this->SetFont($this->font,'',8);
                $this->Cell($this->columnSpacing,$cHeight,'',0,0,'L',0);
                $this->Cell($width_other,$cHeight,$item['quantity'],0,0,'C',1);
                $this->Cell($this->columnSpacing,$cHeight,'',0,0,'L',0);
                if(isset($this->vatField) and $this->hide_vat == 0)
                {
                    $this->Cell($this->columnSpacing,$cHeight,'',0,0,'L',0);
                    if(isset($item['vat'])) {
                        $this->Cell($width_other,$cHeight,iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $item['vat']),0,0,'C',1);
                    }
                    else
                    {
                        $this->Cell($width_other,$cHeight,'',0,0,'C',1);
                    }

                }
                $this->Cell($this->columnSpacing,$cHeight,'',0,0,'L',0);

                if($this->curreny_direction == "left") {
                    $this->Cell($width_other,$cHeight,iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $this->currency.' '.number_format($item['price'],2,$this->referenceformat[0],$this->referenceformat[1])),0,0,'C',1);
                }else{
                    $this->Cell($width_other,$cHeight,iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', number_format($item['price'],2,$this->referenceformat[0],$this->referenceformat[1]).' '.$this->currency),0,0,'C',1);
                }

                if(isset($this->discountField) and $this->hide_discount == 0)
                {
                    $this->Cell($this->columnSpacing,$cHeight,'',0,0,'L',0);
                    if(isset($item['discount']))
                    {
                        $this->Cell($width_other,$cHeight,iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE',$item['discount']),0,0,'C',1);
                    }
                    else
                    {
                        $this->Cell($width_other,$cHeight,'',0,0,'C',1);
                    }
                }
                $this->Cell($this->columnSpacing,$cHeight,'',0,0,'L',0);

                if($this->curreny_direction == "left") {
                    $this->Cell($width_other,$cHeight,iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $this->currency.' '.number_format($item['total'],2,$this->referenceformat[0],$this->referenceformat[1])),0,0,'C',1);
                }else{
                    $this->Cell($width_other,$cHeight,iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', number_format($item['total'],2,$this->referenceformat[0],$this->referenceformat[1]).' '.$this->currency),0,0,'C',1);
                }

                $this->Ln();
                $this->Ln($this->columnSpacing);
            }
        }
        $badgeX = $this->getX();
        $badgeY = $this->getY();

        //Add totals
        if($this->totals)
        {
            foreach($this->totals as $total)
            {
                $this->SetTextColor(50,50,50);
                $this->SetFillColor($bgcolor,$bgcolor,$bgcolor);
                $this->Cell(1+$this->firstColumnWidth,$cellHeight,'',0,0,'L',0);
                for($i=0;$i<$this->columns-3;$i++)
                {
                    $this->Cell($width_other,$cellHeight,'',0,0,'L',0);
                    $this->Cell($this->columnSpacing,$cellHeight,'',0,0,'L',0);
                }
                $this->Cell($this->columnSpacing,$cellHeight,'',0,0,'L',0);
                if(is_bool($total['colored']) and $total['colored'] == true){
                    $this->SetTextColor(255,255,255);
                    $this->SetFillColor($this->color[0],$this->color[1],$this->color[2]);
                }else if(!empty($total['colored']) and is_array($total['colored'])) {
                    $this->SetTextColor(255,255,255);
                    $this->SetFillColor($total['colored'][0],$total['colored'][1],$total['colored'][2]);
                }
                $this->SetFont($this->font,'b',8);
                $this->Cell(1,$cellHeight,'',0,0,'L',1);
                $this->Cell($width_other-1,$cellHeight,iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE',$total['name']),0,0,'L',1);
                $this->Cell($this->columnSpacing,$cellHeight,'',0,0,'L',0);
                $this->SetFont($this->font,'b',8);
                $this->SetFillColor($bgcolor,$bgcolor,$bgcolor);
                if(is_bool($total['colored']) and $total['colored'] == true){
                    $this->SetTextColor(255,255,255);
                    $this->SetFillColor($this->color[0],$this->color[1],$this->color[2]);
                }else if(!empty($total['colored']) and is_array($total['colored'])) {
                    $this->SetTextColor(255,255,255);
                    $this->SetFillColor($total['colored'][0],$total['colored'][1],$total['colored'][2]);
                }
                $this->Cell($width_other,$cellHeight,iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE',$total['value']),0,0,'C',1);
                $this->Ln();
                $this->Ln($this->columnSpacing);
            }
        }
        $this->productsEnded = true;
        $this->Ln();
        $this->Ln(3);


        //Badge
        if($this->badge) {
            $badge = ' '.strtoupper($this->badge).' ';
            $resetX = $this->getX();
            $resetY = $this->getY();
            $this->setXY($badgeX,$badgeY+15);
            $this->SetLineWidth(0.4);
            $this->SetDrawColor($this->color[0],$this->color[1],$this->color[2]);
            $this->setTextColor($this->color[0],$this->color[1],$this->color[2]);
            $this->SetFont($this->font,'b',15);
            $this->Rotate(10,$this->getX(),$this->getY());
            $this->Rect($this->GetX(),$this->GetY(),$this->GetStringWidth($badge)+2,10);
            $this->Write(10,$badge);
            $this->Rotate(0);
            if($resetY>$this->getY()+20)
            {
                $this->setXY($resetX,$resetY);
            }
            else
            {
                $this->Ln(18);
            }
        }

        if(isset($this->footerImage) and !empty($this->footerImage)) {
            $this->Image($this->footerImage,$this->margins['l'],$this->GetY(),$this->footerDimensions[0],$this->footerDimensions[1]);
            $this->Ln(40);
        }

        //Add information
        foreach($this->addText as $text)  {
            if($text[0] == 'title')
            {
                $this->SetFont($this->font,'b',9);
                $this->SetTextColor(50,50,50);
                $this->Cell(0,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($text[1])),0,0,'L',0);
                $this->Ln();
                $this->SetLineWidth(0.3);
                $this->SetDrawColor($this->color[0],$this->color[1],$this->color[2]);
                $this->Line($this->margins['l'], $this->GetY(),$this->document['w']-$this->margins['r'], $this->GetY());
                $this->Ln(4);
            }
            if($text[0] == 'paragraph')
            {
                $this->SetTextColor(80,80,80);
                $this->SetFont($this->font,'',8);
                $this->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",$this->WriteHTML($text[1])),0,'L',0);
                $this->Ln(4);
            }
        }

    }

    /******************************************
     * Function WriteHTML               		 *
     * param : html 							 *
     ******************************************/
    public function WriteHTML($html) {
        // HTML parser

        $html = strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr>");
        $html = str_replace("\n",' ',$html); //replace carriage returns by spaces

        $html = str_replace('&trade;','™',$html);
        $html = str_replace('&copy;','©',$html);
        $html = str_replace('&euro;','€',$html);
        $html = str_replace('&rdquo;','"',$html);


        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e) {
            if($i%2==0) {
                // Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(5,$e);
            }
            else {
                // Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else {
                    // Extract attributes
                    $a2 = explode(' ',$e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v) {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    public function PutLink($URL, $txt) {
        // Put a hyperlink
        $this->SetTextColor($this->color[0],$this->color[1],$this->color[2]);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

    public function SetStyle($tag, $enable) {
        // Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach(array('B', 'I', 'U') as $s) {
            if($this->$s>0)
                $style .= $s;
        }
        $this->SetFont('',$style);
    }

    public function CloseTag($tag) {
        // Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF = '';
    }

    public function OpenTag($tag, $attr) {
        // Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF = $attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }

    public function Header() {

        if(isset($this->logo) and !empty($this->logo)) {
            $this->Image($this->logo,$this->margins['l'],$this->margins['t'],$this->dimensions[0],$this->dimensions[1]);
        }

        //Title
        $this->SetTextColor(0,0,0);
        $this->SetFont($this->font,'B',20);
        $this->Cell(0,5,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->title)),0,1,'R');
        $this->SetFont($this->font,'',9);
        $this->Ln(5);

        $lineheight = 5;
        //Calculate position of strings
        $this->SetFont($this->font,'B',9);
        $positionX = $this->document['w']-$this->margins['l']-$this->margins['r']-max(strtoupper($this->GetStringWidth($this->lang['number'])),
                strtoupper($this->GetStringWidth($this->lang['date'])),
                strtoupper($this->GetStringWidth($this->lang['due'])))-35;

        //Number
        if(!empty($this->reference)) {
            $this->Cell($positionX,$lineheight);
            $this->SetTextColor($this->color[0],$this->color[1],$this->color[2]);
            $this->Cell(32,$lineheight,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['number']).':'),0,0,'L');
            $this->SetTextColor(50,50,50);
            $this->SetFont($this->font,'',9);
            $this->Cell(0,$lineheight,$this->reference,0,1,'R');
        }
        //Date
        $this->Cell($positionX,$lineheight);
        $this->SetFont($this->font,'B',9);
        $this->SetTextColor($this->color[0],$this->color[1],$this->color[2]);
        $this->Cell(32,$lineheight,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['date'])).':',0,0,'L');
        $this->SetTextColor(50,50,50);
        $this->SetFont($this->font,'',9);
        $this->Cell(0,$lineheight,$this->date,0,1,'R');

        //Time
        if(!empty($this->time)){
            $this->Cell($positionX,$lineheight);
            $this->SetFont($this->font,'B',9);
            $this->SetTextColor($this->color[0],$this->color[1],$this->color[2]);
            $this->Cell(32,$lineheight,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['time'])).':',0,0,'L');
            $this->SetTextColor(50,50,50);
            $this->SetFont($this->font,'',9);
            $this->Cell(0,$lineheight,$this->time,0,1,'R');
        }
        //Due date
        if(!empty($this->due)){
            $this->Cell($positionX,$lineheight);
            $this->SetFont($this->font,'B',9);
            $this->SetTextColor($this->color[0],$this->color[1],$this->color[2]);
            $this->Cell(32,$lineheight,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['due'])).':',0,0,'L');
            $this->SetTextColor(50,50,50);
            $this->SetFont($this->font,'',9);
            $this->Cell(0,$lineheight,$this->due,0,1,'R');
        }

        //First page
        if($this->PageNo()== 1) {
            if(($this->margins['t']+$this->dimensions[1]) > $this->GetY()) {
                $this->SetY($this->margins['t']+$this->dimensions[1]+5);
            }
            else  {
                $this->SetY($this->GetY()+10);
            }
            $this->Ln(5);
            $this->SetFillColor($this->color[0],$this->color[1],$this->color[2]);
            $this->SetTextColor($this->color[0],$this->color[1],$this->color[2]);

            $this->SetDrawColor($this->color[0],$this->color[1],$this->color[2]);
            $this->SetFont($this->font,'B',10);
            $width = ($this->document['w']-$this->margins['l']-$this->margins['r'])/2;
            if(isset($this->flipflop)) {
                $to   				= $this->lang['to'];
                $from 				= $this->lang['from'];
                $this->lang['to'] 	= $from;
                $this->lang['from'] = $to;
                $to 				= $this->to;
                $from 				= $this->from;
                $this->to 			= $from;
                $this->from 		= $to;
            }

            if($this->display_tofrom === true) {
                $this->Cell($width,$lineheight,strtoupper($this->lang['from']),0,0,'L');
                $this->Cell(0,$lineheight,strtoupper($this->lang['to']),0,0,'L');
                $this->Ln(7);
                $this->SetLineWidth(0.4);
                $this->Line($this->margins['l'], $this->GetY(),$this->margins['l']+$width-10, $this->GetY());
                $this->Line($this->margins['l']+$width, $this->GetY(),$this->margins['l']+$width+$width, $this->GetY());

                //Information
                $this->Ln(5);
                $this->SetTextColor(50,50,50);
                $this->SetFont($this->font,'B',10);
                $this->Cell($width,$lineheight,$this->from[0],0,0,'L');
                $this->Cell(0,$lineheight,$this->to[0],0,0,'L');
                $this->SetFont($this->font,'',8);
                $this->SetTextColor(100,100,100);
                $this->Ln(7);
                for($i=1; $i<max(count($this->from),count($this->to)); $i++) {
                    $this->Cell($width,$lineheight,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",$this->from[$i]),0,0,'L');
                    $this->Cell(0,$lineheight,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",$this->to[$i]),0,0,'L');
                    $this->Ln(5);
                }
                $this->Ln(-6);
                $this->Ln(5);
            }else{
                $this->Ln(-10);
            }
        }
        //Table header
        if(!isset($this->productsEnded))  {
            $width_other = ($this->document['w']-$this->margins['l']-$this->margins['r']-$this->firstColumnWidth-($this->columns*$this->columnSpacing))/($this->columns-1);
            $this->SetTextColor(50,50,50);
            $this->Ln(12);
            $this->SetFont($this->font,'B',9);
            $this->Cell(1,10,'',0,0,'L',0);
            $this->Cell($this->firstColumnWidth,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['product'])),0,0,'L',0);
            $this->Cell($this->columnSpacing,10,'',0,0,'L',0);
            $this->Cell($width_other,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['qty'])),0,0,'C',0);
            if(isset($this->vatField) and $this->hide_vat == 0) {
                $this->Cell($this->columnSpacing,10,'',0,0,'L',0);
                $this->Cell($width_other,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['vat'])),0,0,'C',0);
            }
            $this->Cell($this->columnSpacing,10,'',0,0,'L',0);
            $this->Cell($width_other,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['price'])),0,0,'C',0);
            if(isset($this->discountField) and $this->hide_discount == 0) {
                $this->Cell($this->columnSpacing,10,'',0,0,'L',0);
                $this->Cell($width_other,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['discount'])),0,0,'C',0);
            }
            $this->Cell($this->columnSpacing,10,'',0,0,'L',0);
            $this->Cell($width_other,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",strtoupper($this->lang['total'])),0,0,'C',0);
            $this->Ln();
            $this->SetLineWidth(0.3);
            $this->SetDrawColor($this->color[0],$this->color[1],$this->color[2]);
            $this->Line($this->margins['l'], $this->GetY(),$this->document['w']-$this->margins['r'], $this->GetY());
            $this->Ln(2);
        } else {
            $this->Ln(12);
        }

    }

    public function Footer() {
        $this->SetY(-$this->margins['t']);
        $this->SetFont($this->font,'',8);
        $this->SetTextColor(50,50,50);

        $this->Cell(0,10,iconv("UTF-8", "ISO-8859-1//TRANSLIT//IGNORE",$this->WriteHTML($this->footernote)),0,0,'L');
        $this->Cell(0,10,$this->lang['page'].' '.$this->PageNo().' '.$this->lang['page_of'].' {nb}',0,0,'R');
    }

}
?>
