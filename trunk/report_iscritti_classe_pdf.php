<html>
<head>
<title>Elenco iscritti per classe - Estate Ragazzi</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
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
	width: 100%;
	height: 100%;
	text-align: left;
	padding:0px 0px 0px 0px;
	margin: 0 auto;
}
table {
	margin-top: 0px;
	width:90%;
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

tr.break {
	background-color: #FFFFFF;
	height: 3em;
	vertical-align: bottom;
	font-weight: bold;
}

tr.totale {
	background-color: #D0D0D0;
	font-weight: bold;
}
</style>
<script type="text/javascript" src="./js/jquery-1.11.0.min.js"></script>
<script type="text/javascript">
	function PrintPage() {
		$("input[type='button']").hide();
		window.print();
		$("input[type='button']").show();
	}
</script> 
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

$CostoComplessivo = 0.0;
$Pagamento = 0.0;

$classe = "";

$rstEvento = GetEventoByID($_GET['idevento']);
$rowEvento = mysqli_fetch_object($rstEvento);
$rstIscritti = GetIscrittiClasse();
if($rstIscritti) {
	if(mysqli_num_rows($rstIscritti) > 0) {
		echo "<table border=1 align=center>";
		echo "<thead>";
		echo "<tr><td colspan=6 align=center><br/><h2>".$rowEvento->NomeEvento." - Oratorio Saint Martin</h2><h1>Elenco iscritti per classe - ".date("d/m/Y")."</h1></td></tr>";
		echo "<tr>";	
		echo "<th style=text-align:center>Num.</th>";
		echo "<th style=text-align:right>Cognome</th>";
		echo "<th style=text-align:right>Nome</th>";
		echo "<th style=text-align:right>Cifra&nbsp;da<br/>pagare</th>";
		echo "<th style=text-align:right>Cifra<br/>pagata</th>";
		echo "<th style=text-align:left>Note</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		while ($rowIscritti = mysqli_fetch_object($rstIscritti)){
			if($classe <> $rowIscritti->Classe) {
				echo "<tr class=break ><td colspan=6>Classe: ".$rowIscritti->Classe."</td></tr>";
				$classe = $rowIscritti->Classe;
			}
			if($rowIscritti->CostoComplessivo == 0.0){
				$cc = "&nbsp;";
			} else {
				$cc = number_format($rowIscritti->CostoComplessivo, 2, ',', '.');
			}
			if($rowIscritti->Pagamento == 0.0){
				$pa = "&nbsp;";
			} else {
				$pa = number_format($rowIscritti->Pagamento, 2, ',', '.');
			}
			echo "<tr class=d".($rowIscritti->rownum % 2).">";		
			echo "<td style=text-align:right>".$rowIscritti->rownum."</td>";
			echo "<td style=text-align:right>".$rowIscritti->Cognome."</td>";
			echo "<td style=text-align:right>".$rowIscritti->Nome."</td>";
			echo "<td style=text-align:right>".$cc."</td>";
			echo "<td style=text-align:right>".$pa."</td>";
			echo "<td style=text-align:left>".stripslashes($rowIscritti->Note)."</td>";
			echo "</tr>";
			
			$CostoComplessivo += $rowIscritti->CostoComplessivo;
			$Pagamento += $rowIscritti->Pagamento;
			$r++;
		}
		// totali finali
		echo "<tr class=totale>";	
		echo "<td style=text-align:left colspan=3>Totale</td>";
		echo "<td style=text-align:right>&euro;&nbsp;".number_format($CostoComplessivo, 2, ',', '.')."</td>";
		echo "<td style=text-align:right>&euro;&nbsp;".number_format($Pagamento, 2, ',', '.')."</td>";
		echo "<td style=text-align:right>&nbsp;</td>";
		echo "</tr>";		
	}
	echo "</tbody>";
	echo "</table>";
}

function GetIscrittiClasse() {
	$sql = "SELECT @rownum:=@rownum+1 AS rownum, t.* FROM ( 
	       SELECT 
		   Catechismi.Cognome,
	       Catechismi.Nome,
	       tblClassi.Classe,
	       tblIscrizioni.CostoComplessivo,
	       tblIscrizioni.Pagamento,
		   tblIscrizioni.Note
		   FROM    (   saint_martin_db.Catechismi Catechismi
			           INNER JOIN
			              saint_martin_db.tblClassi tblClassi
			           ON (Catechismi.Classe = tblClassi.IDClasse))
			       INNER JOIN
			          saint_martin_db.tblIscrizioni tblIscrizioni
			       ON (tblIscrizioni.ID = Catechismi.ID)
	 	   WHERE (tblIscrizioni.IDEvento = ".$_GET['idevento'].")
	       ORDER BY Catechismi.Classe, Catechismi.Cognome ASC, Catechismi.Nome ASC) t, (SELECT @rownum:=0) r";
	echo "<!--".$sql."-->";
	//exit();
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
		throw new Exception("GetIscritti: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		exit();
	}
	return $result;
}
?>
<div style="text-align:center;margin:25px 0px 25px 0px">
<input type="button" value="Stampa" onclick="PrintPage();" />
<input type="button" value="Chiudi" onclick="location.href='report.php';" />
</div>
</div>
</div>
</body>
</html>