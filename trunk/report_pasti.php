<html>
<head>
<title>Riepilogo pasti Estate Ragazzi</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
<style>
body {
	font-family: arial;
}
h1 {
	font-size:x-large;
	font-weight: bold;
	text-align:center;
}
h2 {
	font-size:medium;
	text-align:center;
}
#pagina {
	text-align: center;
	margin-top: 75px;
}
#contenutopagina {
	width: 85%;
	height: 85%;
	text-align: left;
	padding:0px 0px 0px 0px;
	margin: 0 auto;
}
table {
	margin-top: 25px;
	width:100%;
	border-collapse: collapse;
}

table tr.d0 td {
	background-color: #E8E8E8;
}

table tr.d1 td {
	background-color: #FFFFFF;
}

th {
	background-color: #D0D0D0;
}

tr.totale {
	background-color: #D0D0D0;
	font-weight: bold;
}
</style>
</head>
<body>
	<div id="pagina">
		<div id="contenutopagina">
<?php
	require('accesso_db.inc');

	ob_clear;
	ConnettiDB();
	$oggi = getdate();
	$anno =  $oggi["year"];
	
	$tgMattina = 0;
	$tgPomeriggio = 0;
	$tgSera = 0;
	$gMerende = 0;
	$tgPranzo = 0;
	$tgPranzoGratis = 0;
	$tgCena = 0;
	$tgCenaGratis = 0;
	$tgPasti = 0;
?>
			<h2>Estate Ragazzi <?php echo $anno  ?> - Oratorio "Saint Martin"</h2>
			<h1>Riepilogo pasti - <?php echo date("d/m/Y") ?></h1>
			<hr/>
			<table border=1 align=center>
<?php
$r = 1;
$rstAbbonamentiCeneFinali = GetAbbonamentiCeneFinali();
if($rstAbbonamentiCeneFinali) {
	if(mysqli_num_rows($rstAbbonamentiCeneFinali) > 0) {
		$rowAbbonamentiCeneFinali = mysqli_fetch_object($rstAbbonamentiCeneFinali);
	}
}
$rstPasti = GetReportPasti();
if($rstPasti) {
	if(mysqli_num_rows($rstPasti) > 0) {
		while ($rowPasti = mysqli_fetch_object($rstPasti)){
			if ($r == 1) {
				echo "<tr>";	
				echo "<th style=text-align:center>Data</th>";
				echo "<th style=text-align:right>Mattina</th>";
				echo "<th style=text-align:right>Pomeriggio</th>";
				echo "<th style=text-align:right>Sera</th>";
				echo "<th style=text-align:right>Tot.merende</th>";
				echo "<th style=text-align:right>Pranzi</th>";
				echo "<th style=text-align:right>Pranzi gratis</th>";
				echo "<th style=text-align:right>Totale pranzi</th>";
				echo "<th style=text-align:right>Cene</th>";
				echo "<th style=text-align:right>Cene gratis</th>";
				echo "<th style=text-align:right>Totale cene</th>";
				echo "<th style=text-align:right>Tot.pasti</th>";
				echo "</tr>";
			}
			echo "<tr class=d".($r & 1).">";		
			echo "<td style=text-align:center>".date("d/m/Y", strtotime($rowPasti->Data))."</td>";
			echo "<td style=text-align:right>".$rowPasti->TotMattina."</td>";
			echo "<td style=text-align:right>".$rowPasti->TotPomeriggio."</td>";
			echo "<td style=text-align:right>".$rowPasti->TotSera."</td>";
			echo "<td style=text-align:right;background-color:#D0D0D0;font-weight:bold>".$rowPasti->TotMerende."</td>";
			echo "<td style=text-align:right>".($rowPasti->TotPranzo + $rowAbbonamentiCeneFinali->totAbbonamentiPranzo)."</td>";
			echo "<td style=text-align:right>".$rowPasti->TotPranzoGratis."</td>";
			echo "<td style=text-align:right;background-color:#D0D0D0;font-weight:bold>".($rowPasti->TotPranzo + $rowAbbonamentiCeneFinali->totAbbonamentiPranzo + $rowPasti->TotPranzoGratis)."</td>";
			echo "<td style=text-align:right>".($rowPasti->TotCena + $rowAbbonamentiCeneFinali->totAbbonamentiCena)."</td>";
			echo "<td style=text-align:right>".$rowPasti->TotCenaGratis."</td>";
			echo "<td style=text-align:right;background-color:#D0D0D0;font-weight:bold>".($rowPasti->TotCena + $rowAbbonamentiCeneFinali->totAbbonamentiCena + $rowPasti->TotCenaGratis)."</td>";
			echo "<td style=text-align:right>".($rowPasti->TotPasti + $rowAbbonamentiCeneFinali->totAbbonamentiPranzo + $rowAbbonamentiCeneFinali->totAbbonamentiCena)."</td>";
			echo "</tr>";

			$tgMattina += $rowPasti->TotMattina;
			$tgPomeriggio += $rowPasti->TotPomeriggio;
			$tgSera += $rowPasti->TotSera;
			$tgMerende += $rowPasti->TotMerende;
			$tgPranzo += $rowPasti->TotPranzo + $rowAbbonamentiCeneFinali->totAbbonamentiPranzo;
			$tgPranzoGratis += $rowPasti->TotPranzoGratis;
			$tgTotPranzo += $rowPasti->TotPranzo + $rowAbbonamentiCeneFinali->totAbbonamentiPranzo + $rowPasti->TotPranzoGratis;
			$tgCena += $rowPasti->TotCena + $rowAbbonamentiCeneFinali->totAbbonamentiCena;
			$tgCenaGratis += $rowPasti->TotCenaGratis;
			$tgTotCena += $rowPasti->TotCena + $rowAbbonamentiCeneFinali->totAbbonamentiCena + $rowPasti->TotCenaGratis;
			$tgPasti += $rowPasti->TotPasti + $rowAbbonamentiCeneFinali->totAbbonamentiPranzo + $rowAbbonamentiCeneFinali->totAbbonamentiCena;

			$r += 1;
		}
		// totali cena finale
		echo "<tr class=totale>";	
		echo "<td style=text-align:center>Cena finale</td>";
		echo "<td style=text-align:right>&nbsp;</td>";
		echo "<td style=text-align:right>&nbsp;</td>";
		echo "<td style=text-align:right>&nbsp;</td>";
		echo "<td style=text-align:right>&nbsp;</td>";
		echo "<td style=text-align:right>&nbsp;</td>";
		echo "<td style=text-align:right>&nbsp;</td>";
		echo "<td style=text-align:right>&nbsp;</td>";
		echo "<td style=text-align:right>".$rowAbbonamentiCeneFinali->totCenaFinale."</td>";
		echo "<td style=text-align:right>".$rowAbbonamentiCeneFinali->totCenaFinaleOspiti."</td>";
		echo "<td style=text-align:right>&nbsp;</td>";
		echo "<td style=text-align:right>".$rowAbbonamentiCeneFinali->totCeneFinali."</td>";
		echo "</tr>";
		$tgCena += $rowAbbonamentiCeneFinali->totCenaFinale;
		$tgCenaGratis += $rowAbbonamentiCeneFinali->totCenaFinaleOspiti;
		$tgPasti += $rowAbbonamentiCeneFinali->totCeneFinali;
		
		// totali finali
		echo "<tr class=totale>";	
		echo "<td style=text-align:center>Totale</td>";
		echo "<td style=text-align:right>".$tgMattina."</td>";
		echo "<td style=text-align:right>".$tgPomeriggio."</td>";
		echo "<td style=text-align:right>".$tgSera."</td>";
		echo "<td style=text-align:right>".$tgMerende."</td>";
		echo "<td style=text-align:right>".$tgPranzo."</td>";
		echo "<td style=text-align:right>".$tgPranzoGratis."</td>";
		echo "<td style=text-align:right>".$tgTotPranzo."</td>";
		echo "<td style=text-align:right>".$tgCena."</td>";
		echo "<td style=text-align:right>".$tgCenaGratis."</td>";
		echo "<td style=text-align:right>".$tgTotCena."</td>";
		echo "<td style=text-align:right>".$tgPasti."</td>";
		echo "</tr>";		
	}
}
function GetReportPasti() {
	$sql = "SELECT Data, TotMattina,TotPomeriggio,TotSera,IFNULL(TotPomeriggio,0) as TotMerende,TotPranzo,TotPranzoGratis,TotCena,TotCenaGratis,IFNULL(TotPranzo,0)+IFNULL(TotCena,0)+IFNULL(TotPranzoGratis,0)+IFNULL(TotCenaGratis,0) as TotPasti FROM (SELECT tblPrenotazioni.Data,SUM(tblPrenotazioni.Mattina) as TotMattina, SUM(tblPrenotazioni.Pomeriggio) as TotPomeriggio, SUM(tblPrenotazioni.Sera) as TotSera, SUM(tblPrenotazioni.Pranzo) as TotPranzo, SUM(tblPrenotazioni.Cena) as TotCena, SUM(tblPrenotazioni.PranzoGratis) as TotPranzoGratis, SUM(tblPrenotazioni.CenaGratis) as TotCenaGratis FROM saint_martin_db.tblPrenotazioni tblPrenotazioni GROUP BY tblPrenotazioni.Data) totali where data between (select data from tblEventi where IDEvento=(select EventoCorrente from tblParametri)) and (select DATE_ADD(data, INTERVAL durata DAY) from tblEventi where IDEvento=(select EventoCorrente from tblParametri))";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetReportPasti: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		exit();
	}  
    return $result;
}

function GetAbbonamentiCeneFinali() {
	$sql = "SELECT totAbbonamentiPranzo, totAbbonamentiCena, totCenaFinale, totCenaFinaleOspiti, totCenaFinale + totCenaFinaleOspiti AS totCeneFinali FROM (SELECT SUM(AbbonamentoPranzo) AS totAbbonamentiPranzo, SUM(AbbonamentoCena) AS totAbbonamentiCena, SUM(CenaFinale) AS totCenaFinale, SUM(CenaFinaleOspiti) AS totCenaFinaleOspiti FROM tblIscrizioni WHERE IDEvento = (SELECT EventoCorrente FROM tblParametri)) cenefinali";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetCeneFinali: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		exit();
	}  
    return $result;
}
?>
			</table>
			<div style="text-align:center;margin-top:25px">
				<input type="button" value="Stampa" onclick="window.print();">
				<input type="button" value="Chiudi" onclick="location.href='homepage.php';">
			</div>
		</div>
	</div>
</body>
</html>