<?php 
define("FPDF_FONTPATH","./fpdf/font/"); 
require("./fpdf/fpdf.php"); 
require('accesso_db.inc');

class PDF extends FPDF { 

var $tablewidths; 
var	$tabletotals = array(0, 0, 0, 0, 0, 0, 0, 0);
var	$tabletotalize = array(false, false, false, true, true, false, false, false); 
var $headerset; 
var $footerset; 

function _beginpage($orientation) { 
    $this->page++; 
    if(!$this->pages[$this->page]) // solved the problem of overwriting a page, if it already exists 
        $this->pages[$this->page]=''; 
    $this->state=2; 
    $this->x=$this->lMargin; 
    $this->y=$this->tMargin; 
    $this->lasth=0; 
    $this->FontFamily=''; 
    //Page orientation 
    if(!$orientation) 
        $orientation=$this->DefOrientation; 
    else 
    { 
        $orientation=strtoupper($orientation{0}); 
        if($orientation!=$this->DefOrientation) 
            $this->OrientationChanges[$this->page]=true; 
    } 
    if($orientation!=$this->CurOrientation) 
    { 
        //Change orientation 
        if($orientation=='P') 
        { 
            $this->wPt=$this->fwPt; 
            $this->hPt=$this->fhPt; 
            $this->w=$this->fw; 
            $this->h=$this->fh; 
        } 
        else 
        { 
            $this->wPt=$this->fhPt; 
            $this->hPt=$this->fwPt; 
            $this->w=$this->fh; 
            $this->h=$this->fw; 
        } 
        $this->PageBreakTrigger=$this->h-$this->bMargin; 
        $this->CurOrientation=$orientation; 
    } 
} 

function Header() 
{ 
    global $maxY; 

    // Check if header for this page already exists 
    if(!$this->headerset[$this->page]) { 

        foreach($this->tablewidths as $width) { 
            $fullwidth += $width; 
        } 
        $this->SetY(($this->tMargin) - ($this->FontSizePt/$this->k)*2); 
        $this->cellFontSize = $this->FontSizePt ; 
        $this->SetFont('Arial','',( ( $this->titleFontSize) ? $this->titleFontSize : $this->FontSizePt )); 
        $this->Cell(0,$this->FontSizePt,$this->titleText,0,1,'C'); 
        $l = ($this->lMargin); 
        $this->SetFont('Arial','',$this->cellFontSize); 
        foreach($this->colTitles as $col => $txt) { 
            $this->SetXY($l,($this->tMargin)); 
            $this->MultiCell($this->tablewidths[$col], $this->FontSizePt,$txt); 
            $l += $this->tablewidths[$col] ; 
            $maxY = ($maxY < $this->getY()) ? $this->getY() : $maxY ; 
        } 
        $this->SetXY($this->lMargin,$this->tMargin); 
        $this->setFillColor(200,200,200); 
        $l = ($this->lMargin); 
        foreach($this->colTitles as $col => $txt) { 
            $this->SetXY($l,$this->tMargin); 
            $this->cell($this->tablewidths[$col],$maxY-($this->tMargin),'',1,0,'L',1); 
            $this->SetXY($l,$this->tMargin); 
            $this->MultiCell($this->tablewidths[$col],$this->FontSizePt,$txt,0,'C'); 
            $l += $this->tablewidths[$col]; 
        } 
        $this->setFillColor(255,255,255); 
        // set headerset 
        $this->headerset[$this->page] = 1; 
    } 

    $this->SetY($maxY); 
} 

function Footer() { 
    // Check if footer for this page already exists 
    if(!$this->footerset[$this->page]) { 
        $this->SetY(-15); 
        //Page number 
        $this->Cell(0,10,'Pag. '.$this->PageNo().'/{nb}',0,0,'C'); 
        // set footerset 
        $this->footerset[$this->page] = 1; 
    } 
} 

function morepagestable($lineheight=8) { 
    // some things to set and 'remember' 
    $l = $this->lMargin; 
    $startheight = $h = $this->GetY(); 
    $startpage = $currpage = $this->page; 

    // calculate the whole width 
    foreach($this->tablewidths as $width) { 
        $fullwidth += $width; 
    } 

    // Now let's start to write the table 
    $row = 0; 
    while($data=mysql_fetch_row($this->results)) { 
        $this->page = $currpage; 
        // write the horizontal borders 
        $this->Line($l,$h,$fullwidth+$l,$h); 
        // write the content and remember the height of the highest col 
		$i = 0;
        foreach($data as $col => $txt) { 

            $this->page = $currpage; 
            $this->SetXY($l,$h); 
			
			// formatto i numeri in modo europeo
			switch (mysql_field_type($this->results,$i)){ 
                case "int":
                case "tinyint":
                    if(mysql_field_name($this->results,$i) == 'Num.') {
						$txt = number_format($row + 1, 0);
					} else {
						$txt = number_format($txt, 0);
					}
                    break; 
				case "float":
				case "real":
                    $txt = number_format($txt, 2, ',', '.'); 
                    break; 
 				case "string":
					if (mysql_field_len($this->results,$i) == 4000) {
						$txt = utf8_decode($txt);
					}
					break;
			} 
			//echo mysql_field_type($this->results,$i).mysql_field_flags($this->results, $i).'<br/>';
            $this->MultiCell($this->tablewidths[$col],$lineheight,$txt,0,$this->colAlign[$col]); 

			// totalizzo
			if ($this->tabletotalize[$col]) {
				$this->tabletotals[$col] += floatval($txt);
			}
			
            $l += $this->tablewidths[$col]; 

            if($tmpheight[$row.'-'.$this->page] < $this->GetY()) { 
                $tmpheight[$row.'-'.$this->page] = $this->GetY(); 
            } 
            if($this->page > $maxpage) 
                $maxpage = $this->page; 
            unset($data[$col]); 
			$i++;
		} 
        // get the height we were in the last used page 
        $h = $tmpheight[$row.'-'.$maxpage]; 
        // set the "pointer" to the left margin 
        $l = $this->lMargin; 
        // set the $currpage to the last page 
        $currpage = $maxpage; 
        unset($datas[$row]); 
        $row++ ; 
    } 

	// scrivo la riga dei totali
	// write the horizontal borders 
	$this->Line($l,$h,$fullwidth+$l,$h); 
	// write the content and remember the height of the highest col 
	foreach($this->tabletotalize as $col => $tot) { 

		$this->page = $currpage; 
		$this->SetXY($l,$h); 
		if ($tot) {
			$this->MultiCell($this->tablewidths[$col],$lineheight,chr(128).' '.number_format($this->tabletotals[$col], 2, ',', '.'),0,$this->colAlign[$col]); 
		}
		
		$l += $this->tablewidths[$col]; 

		if($tmpheight[$row.'-'.$this->page] < $this->GetY()) { 
			$tmpheight[$row.'-'.$this->page] = $this->GetY(); 
		} 
		if($this->page > $maxpage) 
			$maxpage = $this->page; 
	} 

    // draw the borders 
    // we start adding a horizontal line on the last page 
    $this->page = $maxpage; 
    $this->Line($l,$h,$fullwidth+$l,$h); 
    // now we start at the top of the document and walk down 
    for($i = $startpage; $i <= $maxpage; $i++) { 
        $this->page = $i; 
        $l = $this->lMargin; 
        $t = ($i == $startpage) ? $startheight : $this->tMargin; 
        $lh = ($i == $maxpage) ? $h : $this->h-$this->bMargin; 
        $this->Line($l,$t,$l,$lh); 
        foreach($this->tablewidths as $width) { 
            $l += $width; 
            $this->Line($l,$t,$l,$lh); 
        } 
    } 
    // set it to the last page, if not it'll cause some problems 
    $this->page = $maxpage; 
} 

// Leave this as it is unless you are sure what changes you are making. 
// $host is generally localhost unless you are trying to interact with Database 
// on another server. 
function connect($host='localhost',$username='',$passwd='',$db='') 
{ 
    $this->conn = mysql_connect($host,$username,$passwd) or die( mysql_error() ); 
    mysql_select_db($db,$this->conn) or die( mysql_error() ); 
    return true; 
} 

function query($query){ 
    $this->results = mysql_query($query,$this->conn); 
    if(mysql_num_rows($this->results) == 0)  {
		echo "Non ci sono iscritti in questo evento.";
   	exit(); 
    }

    $this->numFields = mysql_num_fields($this->results); 
} 

function mysql_report($query,$dump=false,$attr=array()){ 

    foreach($attr as $key=>$val){ 
        $this->$key = $val ; 
    } 

    $this->query($query); 

    // if column widths not set 
    if(!isset($this->tablewidths)){ 

        // starting col width 
        $this->sColWidth = (($this->w-$this->lMargin-$this->rMargin))/$this->numFields; 

        // loop through results header and set initial col widths/ titles/ alignment 
        // if a col title is less than the starting col width / reduce that column size 
        for($i=0;$i<$this->numFields;$i++){ 
            $stringWidth = $this->getstringwidth(mysql_field_name($this->results,$i)) + 6 ; 
            if( ($stringWidth) < $this->sColWidth){ 
                $colFits[$i] = $stringWidth ; 
                // set any column titles less than the start width to the column title width 
            } 
            $this->colTitles[$i] = mysql_field_name($this->results,$i) ; 
			//echo mysql_field_type($this->results,$i)."<br/>";
            switch (mysql_field_type($this->results,$i)){ 
                case "int":
				case "float":
				case "real":
                    $this->colAlign[$i] = 'R'; 
                    break; 
                default: 
                    $this->colAlign[$i] = 'L'; 
            } 
        } 

        // loop through the data, any column whose contents is bigger that the col size is 
        // resized 
        while($row=mysql_fetch_row($this->results)){ 
            foreach($colFits as $key=>$val){ 
                $stringWidth = $this->getstringwidth($row[$key]) + 6 ; 
                if( ($stringWidth) > $this->sColWidth ){ 
                    // any col where row is bigger than the start width is now discarded 
                    unset($colFits[$key]); 
                }else{ 
                    // if text is not bigger than the current column width setting enlarge the column 
                    if( ($stringWidth) > $val ){ 
                        $colFits[$key] = ($stringWidth) ; 
                    } 
                } 
            } 
        } 

        foreach($colFits as $key=>$val){ 
            // set fitted columns to smallest size 
            $this->tablewidths[$key] = $val; 
            // to work out how much (if any) space has been freed up 
            $totAlreadyFitted += $val; 
        } 

        $surplus = (sizeof($colFits)*$this->sColWidth) - ($totAlreadyFitted); 
        for($i=0;$i<$this->numFields;$i++){ 
            if(!in_array($i,array_keys($colFits))){ 
                $this->tablewidths[$i] = $this->sColWidth + ($surplus/(($this->numFields)-sizeof ($colFits))); 
            } 
        } 

        ksort($this->tablewidths); 

        if($dump){ 
            Header('Content-type: text/plain'); 
            for($i=0;$i<$this->numFields;$i++){ 
                if(strlen(mysql_field_name($this->results,$i))>$flength){ 
                    $flength = strlen(mysql_field_name($this->results,$i)); 
                } 
            } 
            switch($this->k){ 
                case 72/25.4: 
                    $unit = 'millimeters'; 
                    break; 
                case 72/2.54: 
                    $unit = 'centimeters'; 
                    break; 
                case 72: 
                    $unit = 'inches'; 
                    break; 
                default: 
                    $unit = 'points'; 
            } 
            print "All measurements in $unit\n\n"; 
            for($i=0;$i<$this->numFields;$i++){ 
                printf("%-{$flength}s : %-10s : %10f\n", 
                    mysql_field_name($this->results,$i), 
                    mysql_field_type($this->results,$i), 
                    $this->tablewidths[$i] ); 
            } 
            print "\n\n"; 
            print "\$pdf->tablewidths=\n\tarray(\n\t\t"; 
            for($i=0;$i<$this->numFields;$i++){ 
                ($i<($this->numFields-1)) ? 
                print $this->tablewidths[$i].", /* ".mysql_field_name($this->results,$i)." */ \n\t\t": 
                print $this->tablewidths[$i]." /* ".mysql_field_name($this->results,$i)." */\n\t\t"; 
            } 
            print "\n\t);\n"; 
            exit; 
        } 

    } else { // end of if tablewidths not defined 

        for($i=0;$i<$this->numFields;$i++){ 
            $this->colTitles[$i] = mysql_field_name($this->results,$i) ; 
            switch (mysql_field_type($this->results,$i)){ 
                case "int":
				case "float":
				case "real":
                    $this->colAlign[$i] = 'R'; 
                    break; 
                default: 
                    $this->colAlign[$i] = 'L'; 
            } 
        } 
    } 

    mysql_data_seek($this->results,0); 
    $this->Open(); 
    $this->setY($this->tMargin); 
    $this->AddPage(); 
    //$this->morepagestable($this->FontSizePt); 
    $this->morepagestable($this->FontSizePt + 2); 
    $this->Output(); 
} 

} 

$pdf = new PDF('P','pt','A4'); 
$pdf->SetFont('Arial','',11); 
$pdf->AliasNbPages(); 
// The first Parameter is localhost again unless you are retrieving data from a different server. 
// The second parameter is your MySQL User ID. 
// The third parameter is your password for MySQL. In many cases these would be the same as your OS ID and Password. 
// The fourth parameter is the Database you'd like to run the report on. 
$pdf->connect('localhost','root','mysql','saint_martin_db'); 
// This is the title of the Report generated. 
$attr=array('titleFontSize'=>20,'titleText'=>"Bilancio dell'evento ".GetNomeEventoByID($_GET['idevento'])); 
// This is your query. It should be a 'SELECT' query. 
// Reports are run over 'SELECT' querires generally
$report_sql =  "SELECT 0 AS 'Num.', Cognome, Nome, CostoComplessivo AS 'Cifra da pagare', Pagamento AS 'Cifra pagata', AbbonamentoCena AS AC, CenaFinale AS CF,tblIscrizioni.Note ";
$report_sql .= "FROM tblIscrizioni tblIscrizioni INNER JOIN Catechismi Catechismi ";
$report_sql .= "ON (tblIscrizioni.ID = Catechismi.ID) WHERE tblIscrizioni.IDEvento = ".$_GET['idevento']." ORDER BY Catechismi.Cognome ASC, Catechismi.Nome ASC"; 
$pdf->mysql_report($report_sql,false,$attr); 
exit();
?>