<?php
class PDF extends FPDF { 

//var $tablewidths=         array(  35.0, 120.0, 120.0,   0.0, 120.0,  12.1,  12.1,  60.0,  60.0); 
//var	$tabletotalize =      array(     0,     0,     0,     0,     0,     0,     0,     0,     0); 		// colonne sulle quali totalizzare
//var	$tabletotals =        array(     0,     0,     0,     0,     0,     0,     0,     0,     0);		// totali di colonna
//var	$tablegroupby =       array(     0,     0,     0,     1,     0,     0,     0,     0,     0); 		// colonne sulle quali gestire le rotture (0: nessuna rottura; n:livello della rottura)
//var	$tablegroupbybreak =  array(    '',    '',    '',    '',    '',    '',    '',    '',    ''); 		// gestione delle rotture sulle colonne 
var $tablewidths; 
var	$tabletotalize ; 		// colonne sulle quali totalizzare
var	$tabletotals ;			// totali di colonna
var	$tablegroupby ; 		// colonne sulle quali gestire le rotture (0: nessuna rottura; n:livello della rottura)
var	$tablegroupbybreak ; 	// gestione delle rotture sulle colonne 
var $headerset; 
var $footerset; 
var $maxY; 
var $endOfPage = 777.85;

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

function ComputeReportWidth()
{
	for ($i=0; $i < count($this->tablewidths); $i++) { 
		if ($this->tablegroupby[$i] == 0) {
			$fullwidth += $this->tablewidths[$i];
		}
	} 
	return $fullwidth;
}

function ComputeActiveFields()
{
	$NoActFields = 0;
	for ($i=0; $i < count($this->tablegroupby); $i++) { 
		if ($this->tablegroupby[$i] == 0) {
			$NoActFields++;
		}
	} 
	return $NoActFields;
}

function Header() 
{ 
    global $maxY; 

    // Check if header for this page already exists 
    if(!$this->headerset[$this->page]) { 
        $fullwidth = $this->ComputeReportWidth(); 
        $this->cellFontSize = $this->FontSizePt ; 
        $this->SetFont('Arial','',( ( $this->titleFontSize) ? $this->titleFontSize : $this->FontSizePt )); 
        $titleRows = explode("\n", $this->titleText);
        // calcolo il margine superiore in modo da fare stare il titolo
        $this->tMargin = $this->FontSizePt * count($titleRows) * 1.5;
        // k è l'unità di misura in uso (1=punti, 72=pollici, ....)
        $this->SetY(($this->tMargin - ($this->FontSizePt * count($titleRows) / $this->k))/2); 
        foreach ($titleRows as $value) {
        	$this->Cell(0,$this->FontSizePt,$value,0,1,'C');
        }
        //$this->Cell(0,$this->FontSizePt,$this->titleText,0,1,'C'); 
        //$this->tMargin = $this->GetY();
        $l = ($this->lMargin); 
        $this->SetFont('Arial','',$this->cellFontSize); 
		$i = 0;
		foreach($this->colTitles as $col => $txt) { 
            if ($this->tablegroupby[$i] == 0) {
				$this->SetXY($l,($this->tMargin)); 
				$this->MultiCell($this->tablewidths[$col], $this->FontSizePt,$txt); 
				$l += $this->tablewidths[$col] ; 
				$maxY = ($maxY < $this->getY()) ? $this->getY() : $maxY ;
			}
			$i++;
        } 
        $this->SetXY($this->lMargin,$this->tMargin); 
        $this->setFillColor(180,180,180); 
        $l = ($this->lMargin); 
		$i = 0;
        foreach($this->colTitles as $col => $txt) { 
            if ($this->tablegroupby[$i] == 0) {
				$this->SetXY($l,$this->tMargin); 
				$this->cell($this->tablewidths[$col],$maxY-($this->tMargin),'',1,0,'L',1); 
				$this->SetXY($l,$this->tMargin); 
				$this->MultiCell($this->tablewidths[$col],$this->FontSizePt,$txt,0,'C'); 
				$l += $this->tablewidths[$col]; 
			}
			$i++;
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
    global $maxY; 
	
    // some things to set and 'remember' 
	$newheight = 0;
	$maxpage = 1;
    $l = $this->lMargin; 
    $startheight = $h = $this->GetY(); 
    $startpage = $this->page; 

    // calculate the whole width 
    $fullwidth = $this->ComputeReportWidth(); 

    // Now let's start to write the table 
	// scorro le righe
    $row = 0; 
    while($data=mysql_fetch_row($this->results)) { 
        // write the horizontal borders 
		//$this->SetDrawColor(0,0,255);
        $this->Line($l,$h,$fullwidth+$l,$h); 
		//$this->SetDrawColor(0,0,0);
		
		// elaboro le rotture dei gruppi
		$breakingNow = false;
        for ($g = 0; $g < count($this->tablegroupby); $g++) {
			if ($this->tablegroupby[$g] > 0) {
				if ($this->tablegroupbybreak[$this->tablegroupby[$g] - 1] <> $data[$g]) {
					// stampo la rottura
					// se la rottura causa un cambio pagina lo forzo 
					// per posizionare esattamente la rottura stesa
					//if($h + $lineheight * 1.5 > $this->endOfPage) {
					//	$this->AddPage();
					//}
					$h += $lineheight / 2;
					$this->SetXY($l,$h + ($lineheight / 4));
					$this->setFillColor(200,200,200); 
					$this->MultiCell($fullwidth, $lineheight, $this->colTitles[$g].": ".$data[$g],"TLRB","L",true);
					$this->setFillColor(255,255,255); 
					$this->tablegroupbybreak[$this->tablegroupby[$g] - 1] = $data[$g];
					// echo("data: ".$data[$g]."<br>");
					$h = $this->Gety();
					// abbiamo cambiato pagina?
					if($this->page > $maxpage) {
						$breakingNow = true;
						if($newheight < $this->GetY()) { 
							$newheight = $this->GetY(); 
						}
					} else {
						if($tmpheight < $this->GetY()) { 
							$tmpheight = $this->GetY(); 
						}
					}
									}
			}
		}
		
        // write the content and remember the height of the highest col 
		$yTopRow = $h;
		$i = 0;
		// scorro le colonne
        foreach($data as $col => $txt) { 
        	if ($this->tablegroupby[$i] == 0) {
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
				if ($txt == "Bianca") {
					$l = $l;		
				}
				//echo mysql_field_type($this->results,$i).mysql_field_flags($this->results, $i).'<br/>';
				// echo "\$txt::::::::::::::::::::::::: ".$txt."<br/>";
				$this->MultiCell($this->tablewidths[$col],$lineheight,$txt,0,$this->colAlign[$col]); 
					
				// totalizzo
				if ($this->tabletotalize[$col]) {
					$this->tabletotals[$col] += floatval($txt);
				}
				
				$l += $this->tablewidths[$col]; 
				
				// echo "\$this->GetY(): ".$this->GetY()."<br/>";
				// abbiamo cambiato pagina?
				if($this->page > $maxpage) {
					if($newheight < $this->GetY()) { 
						$newheight = $this->GetY();
					}
					// ritorno alla pagina precedente per stampare le altre celle della riga
					// a meno che il cambio pagina sia dovuto a una rottura di gruppo
					if(!$breakingNow){
						$this->page = $maxpage;
					}
				} else {
					if($tmpheight < $this->GetY()) { 
						$tmpheight = $this->GetY(); 
					}
				}
			}
            unset($data[$col]); 
			$i++;
		} 
		
		// scrivo i bordi verticali 
		// se abbiamo cambiato pagina scrivo i bordi
		// verticali amche sulla pagina nuova
		// echo "\$this->page: ".$this->page."<br/>";
		// echo "\$maxpage: ".$maxpage."<br/>";
		// echo "\$yTopRow: ".$yTopRow."<br/>";
		// echo "\$newheight: ".$newheight."<br/>";
		// echo "\$tmpheight: ".$tmpheight."<br/>";
		if($newheight > 0) {
       		if ($yTopRow < $tmpheight && !$breakingNow) {
				// se la stampa è già scivolata alla pagina successiva
				// la riporto alla precedente
				$nextpage = $this->page;
       			if ($this->page > $maxpage) {
       				$this->page = $maxpage;
				}
       			// chiudo la riga della pagina precedente
				if ($tmpheight > $yTopRow && $tmpheight < $this->endOfPage) {
					$tmpheight = $this->endOfPage;
				}
				//$this->SetDrawColor(255,0,0);
				$this->Line($this->lMargin,$tmpheight,$fullwidth+$this->lMargin,$tmpheight);
				$xvb = $this->lMargin;
				$i = 0;
				foreach($this->tablewidths as $width) { 
					if ($this->tablegroupby[$i] == 0) {
						$this->Line($xvb, $yTopRow, $xvb, $tmpheight); 
						$xvb += $width;
					}
					$i++;
				} 
				$this->Line($xvb, $yTopRow, $xvb, $tmpheight); 
				//$this->SetDrawColor(0,0,0);
				// eventualmente ripristino la posizione della pagina
				$this->page = $nextpage;
			}
			// imposto le coordinate della pagina nuova
			$maxpage += 1;
			$this->page = $maxpage;
			$tmpheight = $newheight;
			$newheight = 0;
			$yTopRow = $maxY;
			
			if($breakingNow) {
				$yTopRow += $lineheight;
			}	
			
			//$this->SetDrawColor(0,0,0);
			$xvb = $this->lMargin;
			$i = 0;
			foreach($this->tablewidths as $width) { 
				if ($this->tablegroupby[$i] == 0) {
					$this->Line($xvb, $yTopRow, $xvb, $tmpheight); 
					$xvb += $width;
				}
				$i++;
			} 
			$this->Line($xvb, $yTopRow, $xvb, $tmpheight);
			//$this->SetDrawColor(0,0,0);
		} else {	
			//$this->SetDrawColor(0,0,0);
			$xvb = $this->lMargin;
			$i = 0;
			foreach($this->tablewidths as $width) { 
				if ($this->tablegroupby[$i] == 0) {
					$this->Line($xvb, $yTopRow, $xvb, $tmpheight); 
					$xvb += $width;
				}
				$i++;
			} 
			$this->Line($xvb, $yTopRow, $xvb, $tmpheight); 
			//$this->SetDrawColor(0,0,0);
		}
		
		$h = $tmpheight; 
		// set the "pointer" to the left margin 
        $l = $this->lMargin; 
        unset($datas[$row]); 
        $row++ ; 
    } 

	// scrivo la riga dei totali
	// write the horizontal border
	$this->Line($l,$h,$fullwidth+$l,$h); 
	foreach($this->tabletotalize as $col => $tot) { 
		$this->SetXY($l,$h); 
		if ($tot) {
			$this->MultiCell($this->tablewidths[$col],$lineheight,chr(128).' '.number_format($this->tabletotals[$col], 2, ',', '.'),0,$this->colAlign[$col]); 
		}
		
		$l += $this->tablewidths[$col]; 

		if($tmpheight < $this->GetY()) { 
			$tmpheight = $this->GetY(); 
		} 
		if($this->page > $maxpage) 
			$maxpage = $this->page; 
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
		echo "Non ci sono dati per la stampa.";
   	exit(); 
    }

    $this->numFields = mysql_num_fields($this->results); 
} 

function mysql_report($query, $dump=false, $attr=array(), $totalize, $groupby){ 

    // costruisco l'array dei totali
	$temp = explode(",", $totalize);
	foreach ($temp as $n) {
		$this->tabletotalize[] = (int)$n;
		$this->tabletotals[] = 0;
	}
    // costruisco l'array dei raggruppamenti
	$temp = explode(",", $groupby);
	foreach ($temp as $n) {
		$this->tablegroupby[] = (int)$n;
		$this->tablegroupbybreak[] = "";
	}
		
	// carico i parametri della chiamata
	foreach($attr as $key=>$val){ 
        $this->$key = $val ; 
    } 

    $this->query($query); 

    // if column widths not set 
    if(!isset($this->tablewidths)){ 
		// starting col width 
		$this->sColWidth = (($this->w-$this->lMargin-$this->rMargin))/$this->ComputeActiveFields(); 
		
		// loop through results header and set initial col widths/ titles/ alignment 
		// if a col title is less than the starting col width / reduce that column size 
		for($i=0;$i<$this->numFields;$i++){ 
			if ($this->tablegroupby[$i] == 0) {
				$stringWidth = $this->getstringwidth(mysql_field_name($this->results,$i)) + 6 ; 
				if( ($stringWidth) < $this->sColWidth){ 
					$colFits[$i] = $stringWidth ; 
					// set any column titles less than the start width to the column title width 
				} 
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
			$this->colTitles[$i] = mysql_field_name($this->results,$i) ; 
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
		// calcolo lo spazio risparmiato
		foreach($colFits as $key=>$val){ 
			// set fitted columns to smallest size 
			$this->tablewidths[$key] = $val; 
			// to work out how much (if any) space has been freed up 
			$totAlreadyFitted += $val; 
		} 
		// ridistribuisco lo spazio sulle colonne da stampare
		$surplus = (sizeof($colFits)*$this->sColWidth) - ($totAlreadyFitted); 
		for($i=0;$i<$this->numFields;$i++){ 
			if ($this->tablegroupby[$i] == 0) {
				if(!in_array($i,array_keys($colFits))){ 
					$this->tablewidths[$i] = $this->sColWidth + ($surplus/(($this->ComputeActiveFields())-sizeof ($colFits))); 
				} 
			} else {
				$this->tablewidths[$i] = 0;
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
			echo "tablewidths<br>";
			print_r ($this->tablewidths);
			echo "colTitles<br>";
			print_r ($this->colTitles);
			echo "colFits<br>";
			print_r($colFits);
			echo "tablegroupby<br>";
			print_r($this->tablegroupby);
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
?>
