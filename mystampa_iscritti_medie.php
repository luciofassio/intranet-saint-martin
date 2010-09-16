<?php

require('accesso_db.inc');
require('funzioni_generali.inc');
session_start();

ConnettiDB();
echo ("<h2>ESTATE RAGAZZI 2009 - MEDIE</h2>");
echo ("<h4>DATI ISCRITTI</h4>");


// estrae tutti gli iscritti delle medie che hanno una squadra
$query="SELECT tblIscrizioni.IDRuolo, tblIscrizioni.IDIscrizione,tblIscrizioni.IDEvento, tblIscrizioni.ID,tblIscrizioni.IDSquadra,
		         tblSquadre.IDSquadra, tblSquadre.NomeSquadra, Catechismi.ID, Catechismi.Cognome,
		         Catechismi.Nome, tblIscrizioni.AbbonamentoPranzo,tblIscrizioni.AbbonamentoCena,
		         tblIscrizioni.Pagamento,tblIscrizioni.Note
		         FROM tblIscrizioni		         
		         INNER JOIN Catechismi ON tblIscrizioni.ID=Catechismi.ID
		         INNER JOIN tblSquadre ON tblIscrizioni.IDSquadra=tblSquadre.IDSquadra
		         WHERE tblIscrizioni.IDEvento=17 
		         ORDER BY Catechismi.Cognome, Catechismi.Nome";


$result=mysql_query($query);

echo ("<table border =1>");
echo ("<th>Nr.</th><th>Codice iscrizione</th><th>Cognome</th><th>Nome</th><th>Squadra</th><th>AbbPr</th><th>AbbCen</th><th>Pagamento</th><th>Note</th>");

while ($record=mysql_fetch_array($result))
{
$indice++;

echo ("<tr>");

if ($record["IDRuolo"]==7) {
	echo ("<td><strong>".$indice."</strong></td>");
}
else {
	echo ("<td>".$indice."</td>");
}

if ($record["IDRuolo"]==7) {
	echo ("<td><strong>".$record["IDIscrizione"]."</strong></td>");
}
else {
	echo ("<td>".$record["IDIscrizione"]."</td>");
}


if ($record["IDRuolo"]==7) {
	echo ("<td><strong>".$record["Cognome"]."</strong></td>");
}
else {
	echo ("<td>".$record["Cognome"]."</td>");
}

if ($record["IDRuolo"]==7) {
	echo ("<td><strong>".$record["Nome"]."</strong></td>");
}
else {
	echo ("<td>".$record["Nome"]."</td>");
}


if ($record["IDRuolo"]==7) {
	echo ("<td><strong>".$record["NomeSquadra"]."</strong></td>");
}
else {
	echo ("<td>".$record["NomeSquadra"]."</td>");
}


if ($record["AbbonamentoPranzo"]!=0) {
	echo ("<td align=center>sì</td>");
}
else {
	echo ("<td align=center>no</td>");
}

if ($record["AbbonamentoCena"]!=0) {
	echo ("<td align=center>sì</td>");
}
else {
	echo ("<td align=center>no</td>");
}

echo ("<td>".$record["Pagamento"]."</td>");

if ($record["Note"]!="") {
	echo ("<td>".$record["Note"]."</td>");
}
else {
	echo ("<td>&nbsp;</td>");
}

echo ("</tr>");

}

echo "</table>";

echo "<br><br><br>";

// estrae tutti gli iscritti delle medie che non hanno una squadra
$query="SELECT tblIscrizioni.IDIscrizione,tblIscrizioni.IDEvento, tblIscrizioni.ID,tblIscrizioni.IDSquadra,
		         tblIscrizioni.IDRuolo,Catechismi.ID, Catechismi.Cognome,
		         Catechismi.Nome, tblIscrizioni.AbbonamentoPranzo,tblIscrizioni.AbbonamentoCena,
		         tblIscrizioni.Pagamento,tblIscrizioni.Note
		         FROM tblIscrizioni
		         INNER JOIN Catechismi ON tblIscrizioni.ID=Catechismi.ID
		         WHERE tblIscrizioni.IDEvento=17 
		         ORDER BY Catechismi.Cognome, Catechismi.Nome";


$result=mysql_query($query);

echo ("<h4>ISCRITTI SENZA SQUADRA</h4>");
echo ("<table border =1>");
echo ("<th>Nr.</th><th>Codice iscrizione</th><th>Cognome</th><th>Nome</th><th>Squadra</th><th>AbbPr</th><th>AbbCen</th><th>Pagamento</th><th>Note</th>");

while ($record=mysql_fetch_array($result))
{
	if ($record["IDSquadra"]==null || $record["IDSquadra"]=="") {
		$indice++;

		echo ("<tr>");
		if ($record["IDRuolo"]==7) {
	echo ("<td><strong>".$indice."</strong></td>");
}
else {
	echo ("<td>".$indice."</td>");
}

if ($record["IDRuolo"]==7) {
	echo ("<td><strong>".$record["IDIscrizione"]."</strong></td>");
}
else {
	echo ("<td>".$record["IDIscrizione"]."</td>");
}


if ($record["IDRuolo"]==7) {
	echo ("<td><strong>".$record["Cognome"]."</strong></td>");
}
else {
	echo ("<td>".$record["Cognome"]."</td>");
}

if ($record["IDRuolo"]==7) {
	echo ("<td><strong>".$record["Nome"]."</strong></td>");
}
else {
	echo ("<td>".$record["Nome"]."</td>");
}


		echo ("<td align=center>-</td>");
	

		if ($record["AbbonamentoPranzo"]!=0) {
			echo ("<td align=center>sì</td>");
		}
		else {
			echo ("<td align=center>no</td>");
		}

		if ($record["AbbonamentoCena"]!=0) {
			echo ("<td align=center>sì</td>");
		}
		else {
			echo ("<td align=center>no</td>");
		}

		echo ("<td>".$record["Pagamento"]."</td>");

		if ($record["Note"]!="") {
			echo ("<td>".$record["Note"]."</td>");
		}
		else {
			echo ("<td>&nbsp;</td>");
		}

		echo ("</tr>");
	}
}

echo "</table>";

?>
