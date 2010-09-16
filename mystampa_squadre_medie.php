<?php

require('accesso_db.inc');
require('funzioni_generali.inc');
session_start();

ConnettiDB();
echo ("<h2>ESTATE RAGAZZI 2009 - MEDIE</h2>");
echo ("<h4>ISCRITTI SUDDIVISI PER SQUADRE</h4>");
$query="SELECT tblIscrizioni.IDEvento, tblIscrizioni.ID,tblIscrizioni.IDSquadra, tblSquadre.IDSquadra, tblSquadre.NomeSquadra, Catechismi.ID, Catechismi.Cognome, Catechismi.Nome FROM tblIscrizioni INNER JOIN tblSquadre ON tblIscrizioni.IDSquadra=tblSquadre.IDSquadra INNER JOIN Catechismi ON tblIscrizioni.ID=Catechismi.ID WHERE tblIscrizioni.IDEvento=17 and tblIscrizioni.IDRuolo =2 ORDER BY tblSquadre.NomeSquadra, Catechismi.Cognome, Catechismi.Nome";

$result=mysql_query($query);

echo ("<table border =1>");
echo ("<th>Nr.</th><th>Cognome</th><th>Nome</th><th>Squadra</th>");

while ($record=mysql_fetch_array($result))
{
$indice++;


if ($OldSquadra != $record["NomeSquadra"] && $indice != 1) {
	$indice=1;
	echo "</table>";
	echo ("<br>");
	echo ("<table border =1>");
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

//cerca gli iscritti senza squadra
echo ("<h4>ISCRITTI CHE NON HANNO SQUADRA</h4>");

$query="SELECT tblIscrizioni.IDEvento, tblIscrizioni.ID,tblIscrizioni.IDSquadra, Catechismi.ID, Catechismi.Cognome, Catechismi.Nome FROM tblIscrizioni INNER JOIN Catechismi ON tblIscrizioni.ID=Catechismi.ID WHERE tblIscrizioni.IDEvento=17 AND tblIscrizioni.IDRuolo =2 ORDER BY Catechismi.Cognome, Catechismi.Nome";

$result=mysql_query($query);

echo ("<table border =1>");
echo ("<th>Nr.</th><th>Cognome</th><th>Nome</th><th>Squadra</th>");
$indice=0;

while ($record=mysql_fetch_array($result))
{
	if (IS_NULL($record["IDSquadra"])) {
		
		$indice++;

		echo ("<tr>");
		echo ("<td>".$indice."</td>");
		echo ("<td>".$record["Cognome"]."</td>");
		echo ("<td>".$record["Nome"]."</td>");
		echo ("<td>".$record["NomeSquadra"]."</td>");
		echo ("</tr>");
	}
}

echo "</table>";

// animatori già iscritti 
echo ("<h4>ANIMATORI GIA' ISCRITTI DIVISI PER SQUADRE</h4>");
$query="SELECT tblIscrizioni.IDEvento, tblIscrizioni.ID,tblIscrizioni.IDSquadra, tblSquadre.IDSquadra, tblSquadre.NomeSquadra, Catechismi.ID, Catechismi.Cognome, Catechismi.Nome FROM tblIscrizioni INNER JOIN tblSquadre ON tblIscrizioni.IDSquadra=tblSquadre.IDSquadra INNER JOIN Catechismi ON tblIscrizioni.ID=Catechismi.ID WHERE tblIscrizioni.IDEvento=17 and tblIscrizioni.IDRuolo =7 ORDER BY tblSquadre.NomeSquadra, Catechismi.Cognome, Catechismi.Nome";

$result=mysql_query($query);

echo ("<table border =1>");
echo ("<th>Nr.</th><th>Cognome</th><th>Nome</th><th>Squadra</th>");

$indice=0;

while ($record=mysql_fetch_array($result))
{
$indice++;


if ($OldSquadra != $record["NomeSquadra"] && $indice != 1) {
	$indice=1;
	echo ("</table>");
	echo ("<br>");
	echo ("<table border=1>");	
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
