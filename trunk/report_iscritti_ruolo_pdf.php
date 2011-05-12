<?php 
define("FPDF_FONTPATH","./fpdf/font/"); 
require("./fpdf/fpdf.php"); 
require('accesso_db.inc');
require('report_library.php');

$pdf = new PDF('P','pt','A4'); 
$pdf->SetFont('Arial','',11); 
$pdf->AliasNbPages(); 
// The first Parameter is localhost again unless you are retrieving data from a different server. 
// The second parameter is your MySQL User ID. 
// The third parameter is your password for MySQL. In many cases these would be the same as your OS ID and Password. 
// The fourth parameter is the Database you'd like to run the report on. 
$pdf->connect('localhost','root','mysql','saint_martin_db'); 
// This is the title of the Report generated. 
$attr=array('tMargin'=>50,'titleFontSize'=>18,'titleText'=>"Iscritti per ruolo dell'evento ".GetNomeEventoByID($_GET['idevento'])); 
// This is your query. It should be a 'SELECT' query. 
// Reports are run over 'SELECT' querires generally

$report_sql = "SELECT 0 AS 'Num.',
	   Catechismi.Nome,
       Catechismi.Cognome,
       tblRuoliER.Ruolo,
       tblIscrizioni.AbbonamentoPranzo AS 'AP',
       tblIscrizioni.AbbonamentoCena AS 'AC',
       tblIscrizioni.Pagamento,
       tblIscrizioni.Note
  FROM    (   tblIscrizioni tblIscrizioni
           INNER JOIN
              tblRuoliER tblRuoliER
           ON (tblIscrizioni.IDRuolo = tblRuoliER.IDRuolo))
       INNER JOIN
          Catechismi Catechismi
       ON (tblIscrizioni.ID = Catechismi.ID)
 WHERE (tblIscrizioni.IDEvento = ".$_GET['idevento'].")
ORDER BY tblRuoliER.Ruolo ASC, Catechismi.Cognome ASC, Catechismi.Nome ASC";
//echo $report_sql;
//die();
$totalize = "0,0,0,0,0,0,1,0";
$groupby =  "0,0,0,4,0,0,0,0";
$pdf->mysql_report($report_sql,false,$attr, $totalize, $groupby); 
exit();
?>