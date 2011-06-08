<?php
require('accesso_db.inc');
require("funzioni_generali.inc");
session_start();
//echo phpversion();
//exit();
$postback = $_POST['postback'];
$host  = $_SERVER['HTTP_HOST'];
// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}
ConnettiDB();
// controllo di avere una stringa da cercare
// il post viene fatto da Jquery
if(isset($_GET['idevcorr'])) {
	// contro sql injection
	$IDEvento = mysql_real_escape_string($_GET['idevcorr']);
} else {
	echo "<strong>ERRORE: ID evento non valido</strong>";
	exit();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <title>Estate Ragazzi / Listino Prezzi</title>
   
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

   <link href="./css/style_ff.css" rel="stylesheet" type="text/css" />
  </head>
  <body class="colorelistino">

<table class="listino">
<caption>LISTINO PREZZI <?php echo strtoupper(GetNomeEventoByID($IDEvento)) ?></caption>
<tr>
<th class="header">RUOLO</th>
<th class="header">ISCRIZIONE <br />ESTATE RAGAZZI</th>
<th class="header">SINGOLO <br />PRANZO</td>
<th class="header">SINGOLA <br />CENA</td>
<th class="header">CENA <br />FINALE</th>
<th class="header">ABBONAMENTO<br />PRANZO<sup>1</sup></th>
<th class="header">ABBONAMENTO<br />CENA<sup>1</sup></th>
<th class="header">ABBONAMENTO<br />PRANZO+CENA<sup>1</sup></th>
<th class="header">EVENTO<br />SPECIALE ER</th>
</tr>
<?php
$rstListino = GetListino($IDEvento);
if ($rstListino) {
	while ($rowListino = mysql_fetch_row($rstListino)) {
		echo "<tr>";
		echo "<th class=\"ruoli\">".strtoupper($rowListino[0])."</th>";
		for ($i = 1;$i < count($rowListino);$i++) {
			if ($rowListino[$i] > 0) {
				echo "<td>".$rowListino[$i]."&euro;</td>";
			}
			else {
				echo "<td>n.d</td>";
			}
		}
		echo "</tr>";
	}	
}
?>
</table>
</body>
</html>
