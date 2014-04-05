<?php
//echo phpinfo();
//exit();
require('accesso_db.inc');
require('business_layer.inc');
require("funzioni_generali.inc");
require("get_data.inc");
session_start();

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
ConnettiDB();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
	<title>Gestione Oratorio / Stampa Privacy</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>
	<script type="text/javascript" src="./js/jsDate.js"></script>
	<script type="text/javascript" src="./js/date.format.js"></script>
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
	<div>   
    <form name="StampaPrivacy" id="StampaPrivacy" method="post" action="stampa_privacy_totale.php" >
	  <input type="hidden" name="postback" id="postback" value="1">
 	  <input type="hidden" name="data_loaded" id="data_loaded" value="<?php echo $data_loaded ?>">
      <!-- ********************** sezione nome della pagina, operatore e data ********************-->
<?php
$result = GetOperatore($idoperatore);
if (IsResultSet($result)) {
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_object($result);
		$operatore = htmlentities($row->Nome).' '.htmlentities($row->Cognome);
	}
}
if(!$postback) {
	$gruppi .= CaricaGruppi("0");
	$oggi = getdate();
	$aaaa =  $oggi["year"];
	$anno .= "<option value=\"".($aaaa - 1)."\">".($aaaa - 1)."</option>";
	$anno .= "<option value=\"".$aaaa."\" selected=\"selected\">".$aaaa."</option>";
	$anno .= "<option value=\"".($aaaa + 1)."\">".($aaaa + 1)."</option>";
}
?>
      <!-- ******************** sezione barra di navigazione ***************************************-->
    <div id="barranavigazione">
        | <a href="homepage.php">home page</a> |
    </div> 
   </div> <!-- fine sezione intestazione -->
     <div id="myoperatore">
          | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
		<!--*********************** corpo pagina **************************************************-->
      <div style="width:100%;text-align:center;margin-top:80px;">
		<div style="width:30%;height:10em;float:left;margin-left:14em">
			<fieldset>
				<legend>Stampa moduli compilati</legend>		
				<div style="margin-top:1.5em;">
					Gruppi: <select name="gruppi" id="gruppi" >
						<?php echo $gruppi ?>
					</select>
				</div>
				<div style="margin:1.5em 0 0.5em 0;">
					Anno: <select name="anno" id="anno" >
						<?php echo $anno ?>
					</select>
				</div>
			</fieldset>
		</div>
		<div style="width:30%;height:10em;float:right;margin-right:14em">
			<fieldset>
				<legend>Stampa moduli in bianco</legend>		
				<div style="margin:2.5em 0 2.2em 0;">
					N. copie: <input type="text" name="copie" id="copie" value="" size="1" maxlength="3"/>
				</div>
			</fieldset>
		</div>
		<div style="clear:both;text-align:center;margin-top:2em"><input name="stampa" type="submit" value ="Stampa"/></div>
      </div> <!-- fine corpo pagina -->
     
    </form>  
	</div>
</body>
</html>