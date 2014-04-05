<?php
require("accesso_db.inc");
require("funzioni_generali.inc");
session_start();	// set session on
ob_start (); 		// set buffer on
$postback = $_POST['postback'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Estate Ragazzi / Squadre</title>
   
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>
	<script type="text/javascript">
		var lsBrowser= navigator.appName;
        switch (lsBrowser) {
          case "Microsoft Internet Explorer":
            document.write("<link href=\"./css/stylesquadre.css\" rel=\"stylesheet\" type=\"text/css\" />");
          break;
          
          case "Netscape":
            document.write("<link href=\"./css/stylesquadre_ff.css\" rel=\"stylesheet\" type=\"text/css\" />");
          break;
        }
		
		function inizializza() {
			// prelevo i dati dell'iscrivendo dalla form principale
			document.getElementById("hdnID").value = window.opener.document.getElementById("hdnID").value;
			document.getElementById("txtCognome").value = window.opener.document.getElementById("pers_cognome").textContent;
			document.getElementById("txtNome").value = window.opener.document.getElementById("pers_nome").textContent;
			document.getElementById("hdnEventoCorrente").value = window.opener.document.getElementById("Evento").value;
			
			nomesquadre = document.getElementById("nomesquadre");
			// seleziono la prima squadra
			if (nomesquadre.length > 0 && nomesquadre.selectedIndex == -1) {
				nomesquadre.selectedIndex = 0;
			}
			// se c'è una squadra selezionata compilo l'elenco iscritti
			if (nomesquadre.selectedIndex != -1) {
				GetIscrittiSquadra();
			}
			// dò eventuali messaggi di errore
			if (document.getElementById("error_message").value != "") {
				alert (document.getElementById("error_message").value);
			}
		}
		// elenco iscritti in ajax
		function GetIscrittiSquadra() {
			nomesquadre = document.getElementById("nomesquadre");
			if (nomesquadre.selectedIndex == -1) {
			  alert("Attenzione! Non hai selezionato nessuna squadra!");
			  return;
			} 
			//alert(squadraselezionata[0].value);			
			squadracorrente = document.getElementById("hdnSquadraCorrente");
			squadracorrente.value = nomesquadre[nomesquadre.selectedIndex].value;
			idEvento = window.opener.document.getElementById("Evento").value;
			$.post("rpc_iscritti_squadra.php", {IDSquadra: ""+nomesquadre[nomesquadre.selectedIndex].value+"", IDEvento: ""+idEvento+""}, function(data){
				squadreiscritti = document.getElementById("squadreiscritti");
				// inietto i dati nel controlllo
				if (data.length > 0) {
					resp = data.split("|");
					$('#num_iscritti').html(resp[0]);
					$('#squadreiscritti').html(resp[1]);
					$('#ruoli').html(resp[2]);
					$('#iscritti_x_ruolo').html(resp[3]);
					squadreiscritti.selectedIndex = -1;
				} else {
					squadreiscritti.options.length = 0;
				}
			});
		} // lookup
		
   </script>
<style>
select {
	font-family: Courier New;
}
</style>  
</head>

<body onload="inizializza()">

<div id="gufo">
	<img src="./Immagini/gufo.png" alt="logo pagina squadre" width="120" />
</div>
	
<form name="miesquadre"  method ="post" action="squadra.php">
    <input type="hidden" name="postback" value="true">
	<div id="iscritto">
	<fieldset class="posizioneiscritto">
		<legend>Iscritto da inserire nella squadra &nbsp;</legend>	
		<div id="nomeiscritto">
    	    <label for="txtCognome" class="posizione">Cognome&nbsp;</label>
    	    <input class="cognomenome" type="text" name="txtCognome" id="txtCognome" size="30" />
    	    <p>
    	    <label for="txtNome" class="posizione">Nome&nbsp;</label>
    	    <input class="cognomenome" type="text" name="txtNome" id="txtNome" size="30" />
		      </p>
    </div>
    </fieldset>
  	</div>
  	
  	<div id="inseriscisquadre">
        <fieldset>
            <legend>Squadre disponibili...&nbsp;</legend>  
				<input type="hidden" id="hdnSquadraCorrente" name="hdnSquadraCorrente" value="<?php $_POST['hdnSquadraCorrente']?>">
                <select id="nomesquadre" name="nomesquadre" size="6" onclick="GetIscrittiSquadra()" style="width:50%"><?php
ConnettiDB();
// l'id della persona è in querystring la prima volta, poi viene conservato in hdnID
$sqCorrente = $_POST['hdnSquadraCorrente'];
if ($_GET['id'] != "") {
	$ID = $_GET['id'];
	$IDEventoCorrente = $_GET['idevcorr'];
}
else {
	$ID = $_POST["hdnID"];
	$IDEventoCorrente = $_POST['hdnEventoCorrente'];
}
$rstIscritto = GetIscrizioneER($ID, $IDEventoCorrente);
if (mysqli_num_rows($rstIscritto) > 0) {
	$rowIscritto = mysqli_fetch_object($rstIscritto);
	$sqCorrente = $rowIscritto->IDSquadra;
}
switch ($_REQUEST['btnAzione']) {
	case "aggiungi alla squadra":
		try {
			if (mysqli_num_rows($rstIscritto) > 0) {
				UpdateIscrizioneSquadra($rowIscritto->IDIscrizione, $_POST["hdnSquadraCorrente"]);
			}
			else {
				InsertIscrizioneSquadra($_POST["hdnID"], $_POST["hdnSquadraCorrente"], $IDEventoCorrente);
			}
			// imposto la nuova squadra corrente
			$sqCorrente = $_POST["hdnSquadraCorrente"];
		}
		catch (Exception $e) {
			switch (getErrorCode($e->getMessage())) {
				// non si verifica più perchè controllo l'esistenza della iscrizione 
				case "1062":
					$rstSquadra = getSquadraByID($_POST['hdnID'], $IDEventoCorrente);
					$rowSquadra = mysqli_fetch_object($rstSquadra);
					$error_message = "La persona è già presente nella squadra: ".$rowSquadra->NomeSquadra;
					break;
				default:
					ob_clean();
					echo $e;
					exit();
			}
		}
		break;
	case "togli dalla squadra":
		if ($rowIscritto->IDSquadra == null) {
			$error_message = "La persona non è assegnata ad alcuna squadra";
		}
		elseif ($rowIscritto->IDSquadra != $_POST["nomesquadre"]) {
			$error_message = "La persona non è assegnata alla squadra selezionata";		
		}
		else {
			try {
				UpdateIscrizioneSquadra($rowIscritto->IDIscrizione, "null");
			}
			catch (Exception $e) {
				switch (getErrorCode($e->getMessage())) {
					default:
						ob_clean();
						echo $e->getMessage();
						exit();
				}
			}
		}
		break;
}
$result = GetSquadre($IDEventoCorrente);
while ($row = mysqli_fetch_array($result)) {
	if ($row['IDSquadra'] == $sqCorrente) {
		$selez = "selected=\"selected\"";
	} 
	else {
		$selez = "";
	}
?>
                    <option value="<?php echo htmlentities($row['IDSquadra']); ?>" <?php echo $selez ?>><?php echo htmlentities($row['NomeSquadra']); ?></option>
<?php
}
?>
                </select>
     	       
            <div id="info">
                <div style="float:left">
					N° iscritti squadra:<br/> 
					<span id="ruoli"></span>
				</div>
                <div style="float:right;margin-left:10px"> 
					<span id="num_iscritti"></span><br/>
					<span id="iscritti_x_ruolo"></span>
				</div>
            </div>
            
            <!-- div id="bottoneiscritti">
                <input type="button" name="btniscritti" value="mostra iscritti" onclick="GetIscrittiSquadra()" />
            </div -->
            
        </fieldset>
   
  </div>

  <div id="operazioni">
      <fieldset>
          <legend>Operazioni...&nbsp;</legend>
              <div id="btnAggiungi">
                    <input type="submit" name="btnAzione" value="aggiungi alla squadra" onclick="" />
              </div>
              <div id="btnModifica">
                    <input type="submit" name="btnAzione" value="togli dalla squadra" onclick="" />
              </div>
			  <br/>
              <div id="btnChiudi">
                    <input type="button" name="btnChiudi" value="chiudi" onclick="window.close()" />
              </div>
      </fieldset>
  </div>
	<input type="hidden" id="error_message" name="error_message" value="<?php echo $error_message ?>">
	<input type="hidden" name="hdnEventoCorrente" id="hdnEventoCorrente" value="<?php echo $IDEventoCorrente ?>">
	<input type="hidden" name="hdnID" id="hdnID" value="<?php echo $ID ?>">
</form>
  
  <div id="posizionelista" style="width:55%">
	<fieldset>
		<legend>Iscritti alla squadra selezionata</legend>
		<select id="squadreiscritti" name="squadreiscritti" size="12" style="width:100%">
			<option value="1" class="listaiscritti"></option>
		</select>
	</fieldset> 
  </div>
</body>
</html>
