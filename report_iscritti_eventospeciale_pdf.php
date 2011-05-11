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
$attr=array('titleFontSize'=>20,'titleText'=>"Iscritti per squadra dell'evento ".GetNomeEventoByID($_GET['idevento'])); 
// This is your query. It should be a 'SELECT' query. 
// Reports are run over 'SELECT' querires generally

$report_sql = "SELECT 0 AS 'Num.',
       catechismi.Cognome,
       catechismi.Nome,
       tblsquadre.NomeSquadra as 'Squadra',
       tbliscrizioni.Note,

 FROM    (tbliscrizioni tbliscrizioni
           INNER JOIN
              tblsquadre tblsquadre
           ON (tbliscrizioni.IDSquadra = tblsquadre.IDSquadra))
       INNER JOIN
          catechismi catechismi
       ON (catechismi.ID = tbliscrizioni.ID)
 WHERE tbliscrizioni.IDEvento = ".$_GET['idevento']." AND EventoSpecialeER = 1
 ORDER BY tblsquadre.NomeSquadra ASC,
          catechismi.Cognome ASC,
          catechismi.Nome ASC";
$totalize = "0,0,0,0,0";
$groupby =  "0,0,0,1,0";
$pdf->mysql_report($report_sql,false,$attr, $totalize, $groupby); 
exit();
?>