<?php
require('./fpdf/fpdf.php');
require('accesso_db.inc');

class PDF extends FPDF
{
function Header()
{
	global $title;

	//Arial bold 15
	$this->SetFont('Arial','B',15);
	//Calculate width of title and position
	$w=$this->GetStringWidth($title)+6;
	$this->SetX((210-$w)/2);
	//Colors of frame, background and text
	$this->SetDrawColor(255,255,255);
	$this->SetFillColor(255,255,255);
	$this->SetTextColor(0,0,0);
	//Thickness of frame (1 mm)
	$this->SetLineWidth(1);
	//Title
	$this->Cell($w,9,$title,1,1,'C',true);
	//Line break
	$this->Ln(10);
}

function Footer()
{
	//Position at 1.5 cm from bottom
	$this->SetY(-15);
	//Arial italic 8
	$this->SetFont('Arial','I',8);
	//Text color in gray
	$this->SetTextColor(128);
	//Page number
	$this->Cell(0,10,'Pag. '.$this->PageNo(),0,0,'C');
}

function ChapterTitle($num,$label)
{
	//Arial 12
	$this->SetFont('Arial','',12);
	//Background color
	$this->SetFillColor(200,220,255);
	//Title
	//$this->Cell(0,6,"Capitolo $num : $label",0,1,'L',true);
	$this->Cell(0,6,"$label",0,1,'L',true);
	//Line break
	$this->Ln(4);
}

function ChapterBody($file, $nome, $cognome)
{
	//Read text file
	$f=fopen($file,'r');
	$txt=fread($f,filesize($file));
	fclose($f);
	$txt = str_replace("#nome#",$nome,$txt);
	$txt = str_replace("#cognome#",$cognome,$txt);
	
	//Times 12
	$this->SetFont('Times','',12);
	//Output justified text
	$this->MultiCell(0,5,$txt);
	//Line break
	$this->Ln();
	//Mention in italics
	$this->SetFont('','I');
	$this->Cell(0,5,'(fine)');
}

function PrintChapter($num,$title,$file, $nome, $cognome)
{
	$this->AddPage();
	$this->ChapterTitle($num,$title);
	$this->ChapterBody($file, $nome, $cognome);
}
}

ob_clear;
if ($_GET["id"] != "") {
	ConnettiDB();
	$rstPersona = GetPersona($_GET["id"]);
	if($rstPersona) {
		if(mysqli_num_rows($rstPersona) > 0) {
			$row = mysqli_fetch_object($rstPersona);
			$nome = htmlentities($row->Nome);
			$cognome = htmlentities($row->Cognome);
		}
	}
	$pdf=new PDF();
	$title='Trattamento dei dati personali';
	$pdf->SetTitle($title);
	$pdf->SetAuthor('Parrocchia di Saint-Martin de Corleans');
	$pdf->PrintChapter(1,'INFORMATIVA AI SENSI DEL D. LGS. 196 DEL 30 GIUGNO 2003','privacy.txt', $nome, $cognome);
	$pdf->Output();
} else {
	echo "Errore: codice persona mancante";
}
?>
