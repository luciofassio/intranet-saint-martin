<?php
//echo phpinfo();
//exit();
ob_start();
require('accesso_db.inc');
require('business_layer.inc');
require("funzioni_generali.inc");
require("get_data.inc");
session_start();

//variabili di pagina
$postback = (bool)$_POST['postback'];
$host  = $_SERVER['HTTP_HOST'];

// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}
$idoperatore = $_SESSION['authenticated_user_id'];
ConnettiDB();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
	<title>Estate Ragazzi / Nuovo evento</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>
	<script type="text/javascript" src="./js/jsDate.js"></script>
	<script type="text/javascript" src="./js/date.format.js"></script>
	<script type="text/javascript" src="./js/funzioni.js"></script>
	<script type="text/javascript" src="./js/f_anagrafica.js"></script>
	<script type="text/javascript">
		function ValidaForm() {
			if(!ControlloObbligatorio(document.getElementById("evento"))) return false;
			if(!ControlloObbligatorio(document.getElementById("data_evento"))) {
				return false;
			} else {
				if(!ControlloDataInserita(document.getElementById("data_evento"),'dt')) return false;		
			}
			if(!ControlloObbligatorio(document.getElementById("durata"))) {
				return false;
			} else {
				if(!ControlloNumeroPositivo(document.getElementById("durata"))) return false;		
			}
			return true;			
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
    <form name="SezioneEventi" id="SezioneEventi" method="post" action="nuovo_evento.php" onsubmit="return ValidaForm()" >
	  <input type="hidden" name="postback" id="postback" value="1">
      <!-- ********************** sezione nome della pagina, operatore e data ********************-->
<?php
$result = GetOperatore($idoperatore);
if (IsResultSet($result)) {
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_object($result);
		$operatore = htmlentities($row->Nome).' '.htmlentities($row->Cognome);
	}
}
?>
      <!-- ******************** sezione barra di navigazione ***************************************-->
    <div id="barranavigazione">
        | <a href="homepage.php">home page</a> | <a href="inserisci_prenotazioni.php">iscrizioni &amp; prenotazioni Er</a> | <a href="logout.php">esci</a> |
    </div> 
   </div> <!-- fine sezione intestazione -->
     <div id="myoperatore">
          | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
    <div style="clear:both;margin-top:4em">
      <div style="width:80%;margin:0 auto">
		<label for="evento" style="display:inline-block;width:100px">Evento</label><input type="text" name="evento" id="evento" maxlength="50" size="50" value="<?php echo $_POST['evento']?>" /><br/>
		<label for="data_evento" style="display:inline-block;width:100px">Data</label><input type="text" name="data_evento" id="data_evento" maxlength="10" size="10" value="<?php echo $_POST['data_evento']?>" /><br/>
		<label for="durata" style="display:inline-block;width:100px">Durata</label><input type="text" name="durata" id="durata" maxlength="2" size="2" value="<?php echo $_POST['durata']?>" /><br/>
      </div>
    </div> 
<?php
$evento = $_POST['evento'];
$data_evento = $_POST['data_evento'];
$durata = $_POST['durata'];

// aggiorno i dati se è stato richiesto
if (strtolower($_REQUEST["salva"]) == "salva") {
	if ($_POST["postback"]) {
		mysqli_query($GLOBALS["___mysqli_ston"], "START TRANSACTION");
		InsertEvento($evento, $data_evento, $durata);
		mysqli_query($GLOBALS["___mysqli_ston"], "COMMIT");
	}
}
?>
  
		<div id="salva">
			 <div style="text-align:center">
			 	<input name="salva" type="submit" value ="salva" />
			 	<input name="annulla" type="button" value ="Annulla" onclick="location.href='/html/eventi.php'"/>
			 	<input name="elenco_eventi" type="button" value ="Elenco eventi" onclick="location.href='/html/eventi.php'"/>
			 </div>
		</div>
    </form>  
	</div>
</body>			
</html>
