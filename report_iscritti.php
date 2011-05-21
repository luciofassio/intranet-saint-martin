<?php
//echo phpinfo();
//exit();
require('accesso_db.inc');
require('business_layer.inc');
require("funzioni_generali.inc");
require("get_data.inc");
session_start();
ob_start(); 
//variabili di pagina
$postback = (bool)$_POST['postback'];
//echo "<hr/>postback:".sprintf("%d", $postback)."-".gettype($postback)."<hr/>";
$data_loaded = (bool)$_POST['data_loaded'];
//echo "<hr/>data loaded:".sprintf("%d", $data_loaded)."-".gettype($data_loaded)."<hr/>";
//echo "<hr/>chkiscrizione:".sprintf("%s", $_POST["chkIscrizione"])."-".gettype($_POST["chkIscrizione"])."<hr/>";

$host  = $_SERVER['HTTP_HOST'];

// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}
$idoperatore = $_SESSION['authenticated_user_id'];
if (!$postback) {
	 $_SESSION['required_report']	= $_GET["r"];	
	 $_SESSION['required_report_title']	= $_GET["t"];	
}

ConnettiDB();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
	<title>Estate Ragazzi / Eventi</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>
	<script type="text/javascript" src="./js/jsDate.js"></script>
	<script type="text/javascript" src="./js/date.format.js"></script>
	<script type="text/javascript">
	function ControllaCheck(chkCliccato) {
		chkLista = document.getElementsByName("cod_evento[]");
		for(i=0;i<chkLista.length;i++) {
			if(chkLista[i] != chkCliccato) {
				chkLista[i].checked = false;
			}
		}
	}
	</script>
	<?php SelectCSS(""); ?>   
</head>

<body>
   <!-- inizio sezione intestazione. Questa sezione contiene il logo, i campi di ricerca, la barra di navigazione e la data -->
        <div id="barratop">
            &nbsp;
        </div>
  
        <div id="barrabottom">
            &nbsp;
        </div>
  
        <div id="intestazione">   
            <img src ="./Immagini/logoratorio.png" width="40" height="40" alt="Logo Oratorio Saint-Martin" />
        </div>
    
        <div id="scritta_barra">
            ORATORIO SAINT MARTIN
        </div>
        
        <div id="mydata">
            <?php echo(GetData($Data)."\n");?>
        </div>
        
      <!-- sezione campi di ricerca -->
	<div  style="clear:both">   
    <form name="ReportIscritti" id="ReportIscritti" method="post" action="report_iscritti.php" >
	  <input type="hidden" name="postback" id="postback" value="1">
 	  <input type="hidden" name="data_loaded" id="data_loaded" value="<?php echo $data_loaded ?>">
      <!-- ********************** sezione nome della pagina, operatore e data ********************-->
<?php
$result = GetOperatore($idoperatore);
if (IsResultSet($result)) {
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_object($result);
		$operatore = htmlentities($row->Nome).' '.htmlentities($row->Cognome);
	}
}
?>
      <!-- ******************** sezione barra di navigazione ***************************************-->
    <div id="barranavigazione">
        | <a href="homepage.php">home page</a> | <a href="report.php">elenco report</a> |<a href="inserisci_prenotazioni.php">iscrizioni &amp; prenotazioni Er</a> |
    </div> 
   </div> <!-- fine sezione intestazione -->
     <div id="myoperatore">
          | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
    
  		
		<!--*********************** corpo pagina **************************************************-->
      <div style="clear:both">
<?php
$rstEventoCorrente = GetIDEventoCorrente();
if (IsResultSet($rstEventoCorrente)) {
	if (mysql_num_rows($rstEventoCorrente) > 0) {
		$rowEventoCorrente = mysql_fetch_object($rstEventoCorrente);
		$EventoCorrente = $rowEventoCorrente->EventoCorrente;
	}
}
$rstEventi = GetEventi();
if (IsResultSet($rstEventi)) {
	if (mysql_num_rows($rstEventi) > 0) {
		echo "<table style=\"margin:60px auto 10px ;width:80%\" border=0>";
			echo "<tr>";
			echo "<th colspan=4>".$_SESSION['required_report_title']."</th>";
			echo "</tr>";
			echo "<tr>";
			echo "<th style=\"text-align:left;width:90%\">Evento</th>";
			echo "<th style=\"text-align:center;width:5%\">Data</th>";
			echo "<th style=\"text-align:right;width:2%\">Durata</th>";
			echo "<th style=\"text-align:center;width:10%\">Corrente</th>";
			echo "</tr>";
		while ($rowEventi = mysql_fetch_object($rstEventi)){
			echo "<tr>";
			echo "<td style=\"text-align:left\"><input type=\"text\" name=\"NomeEvento[]\" value=\"".htmlentities($rowEventi->NomeEvento)."\" maxlength=\"40\" size=\"86\" /></td>";
			echo "<td style=\"text-align:center\"><input type=\"text\" name=\"DataEvento[]\" value=\"".date("d/m/Y", strtotime($rowEventi->Data))."\" maxlength=\"10\" size=\"10\" /></td>";
			echo "<td style=\"text-align:right\"><input type=\"text\" name=\"DurataEvento[]\" value=\"".htmlentities($rowEventi->Durata)."\" maxlength=\"2\" size=\"2\" style=\"text-align:right\" /></td>";
			if($_POST["postback"]) {
				$ev = postCheck("cod_evento", $rowEventi->IDEvento);
			} else {
				$ev = "";
				if($EventoCorrente == $rowEventi->IDEvento) {
					$ev = " checked=checked ";
				}
			}
			echo "<td><input name=\"cod_evento[]\" type=\"checkbox\" value=\"".$rowEventi->IDEvento."\" ".$ev." onclick=\"ControllaCheck(this);\"/></td>";
			echo "</tr>";
		}
		echo "</table>";
	}
}
//print_r($_POST);
//var_dump($_POST);
// aggiorno i dati se è stato richiesto
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
switch (strtolower($_REQUEST["stampa"])) {
	case "stampa":		
		if ($_POST["postback"]) {
			if (isset($_POST["cod_evento"])) {
				if (count($_POST["cod_evento"]) > 0 && count($_POST["cod_evento"]) <= 1) {
					//echo("Location: http://$host$uri/".$_SESSION['required_report']."?idevento=".$_POST["cod_evento"][0]);
					//die();					
					header("Location: http://$host$uri/".$_SESSION['required_report']."?idevento=".$_POST["cod_evento"][0]);
					//unset($_SESSION['required_report']);					
					ob_end_flush();
				} elseif (count($_POST["cod_evento"]) > 1) {
					echo "<p style=\"font-weight:bold\">Deve essere selezionato un solo evento</p>";
				}
			}
		}
		break;
	case "elenco report":		
		header("Location: http://$host$uri/report.php");
		break;
	default:
}
?>
      </div> <!-- fine corpo pagina -->    
		<!-- *********** bottone per salvare gli eventi ******************* -->
		<div id="stampa">
			 <div style="text-align:center">
			 	<input name="stampa" type="submit" value ="Stampa"/>&nbsp;&nbsp;
			 	<input name="stampa" type="submit" value ="Elenco report"/>
			 </div>
		</div>
    </form>  
	</div>
</body>
</html>