<?php

require('accesso_db.inc');
require('funzioni_generali.inc');
session_start();

ConnettiDB();

echo ("<h3>ELENCO ISCRITTI SUDDIVISI PER SQUADRE</h3>");
$query="SELECT tblIscrizioni.IDEvento, tblIscrizioni.ID,tblIscrizioni.IDSquadra, tblSquadre.IDSquadra, tblSquadre.NomeSquadra, Catechismi.ID, Catechismi.Cognome, Catechismi.Nome FROM tblIscrizioni INNER JOIN tblSquadre ON tblIscrizioni.IDSquadra=tblSquadre.IDSquadra INNER JOIN Catechismi ON tblIscrizioni.ID=Catechismi.ID WHERE tblIscrizioni.IDEvento=16 ORDER BY tblSquadre.NomeSquadra, Catechismi.Cognome, Catechismi.Nome";

$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);

echo ("<table border =1>");
echo ("<th>Nr.</th><th>Cognome</th><th>Nome</th><th>Squadra</th>");

while ($record=mysqli_fetch_array($result))
{
$indice++;


if ($OldSquadra != $record["NomeSquadra"] && $indice != 1) {
	$indice=1;
	
	echo ("<tr>");	
	echo("<th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th>");	
	echo ("</tr>");	
	echo ("<tr>");		
	echo("<th>Nr.</th><th>Cognome</th><th>Nome</th><th>Squadra</th>");
	echo ("</tr>");
}
echo ("<tr>");
echo ("<td>".$indice."</td>");
echo ("<td>".$record["Cognome"]."</td>");
echo ("<td>".$record["Nome"]."</td>");
echo ("<td>".$record["NomeSquadra"]."</td>");
$OldSquadra=$record["NomeSquadra"];
echo ("</tr>");

}

echo "</table>";
?>
