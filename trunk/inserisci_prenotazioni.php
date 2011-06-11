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

$host = $_SERVER['HTTP_HOST'];

if ($postback) {
	$hdnID = $_POST['hdnID'];
	$hdnIDCLasse = $_POST['hdnIDClasse'];
} else {
	$hdnID = "";
	$hdnIDCLasse = "";
}
$tesseramento = "";
$note = "";
$iscrizione = "";
$btnsquadra = "";
$squadra = "";
$ruoli = "";
$eventi = "";
$AbbonamentoPranzo = 0;
$AbbonamentoCena = 0;
$CenaFinale = 0;
$NrOspiti = 0;
$EventoSpecialeER = 0;
$CostoTotaleEuro = "";
$Pagamento= "";
$dateEvento = null;
$ClassePagamento = "";
$prezzoIscrizione = "";
$prezzoPranzo = "";
$prezzoCena = "";
$prezzoCenaFinale = "";
$prezzoAbbPranzo = "";
$prezzoAbbCena = "";
$prezzoAbbPranzoCena = "";
$prezzoEventoSpecialeER = "";

$rstIscrizioneER = null;
$rowIscrizioneER = null;

// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}
$idoperatore = $_SESSION['authenticated_user_id'];
ConnettiDB();
if ($postback){
	// carico la persona in base al bar code
	if (strtolower($_REQUEST["caricaPersona"]) == "carica") {
		$hdnID = GetIDPersonaByBarCode($_POST['txtBarCode']);
	}
	// se c'è una persona impostata carico l'evento selezionato dalla combo box
	if (strtolower($_REQUEST["salvaprenotazioni"]) == "salva iscr./prenot." || $hdnID != '') {
		$rstEventoCorrente = GetEventoByID($_POST['Evento']);
	} 
	// se nessuno è selezionato carico l'evento corrente
	if ($hdnID == '') {
		$rstEventoCorrente = GetEventoCorrente();
	}
	if (mysql_num_rows($rstEventoCorrente) > 0) {
		$rowEventoCorrente = mysql_fetch_object($rstEventoCorrente);
	}
	if ($hdnID == '') {
		// nessuna persona selezionata, imposto l'evento corrente predefinito
		$eventi = CaricaEventi($rowEventoCorrente->IDEvento, null);
		$oggi = getdate(strtotime($rowEventoCorrente->Data));
		$iniziogiorno = $oggi["mday"];
		$giornosettimana = $oggi["wday"];
		$iniziomese = $oggi["mon"];
		$durata = $rowEventoCorrente->Durata;
		//echo "<br/><br/>".$rowEventoCorrente->Data.",".$iniziogiorno.",".$giornosettimana.",".$iniziomese.",".$durata;
		//exit();
	} else {
		// persona selezionata, evidenzio gli eventi per i quali esiste una prenotazione
		$arrEventiPersona = ResultSet2Array(GetEventiPartecipatiByID($hdnID));
		$eventi = CaricaEventi($rowEventoCorrente->IDEvento, $arrEventiPersona);
		$oggi = getdate(strtotime($rowEventoCorrente->Data));
		$iniziogiorno = $oggi["mday"];
		$giornosettimana = $oggi["wday"];
		$iniziomese = $oggi["mon"];
		$durata = $rowEventoCorrente->Durata;
		// imposto il bottone squadre in base alla selezione della persona e della presenza dell'iscrizione		
		//if (postCheck("chkIscrizione", "on") != '') {
		//	$btnSquadre = "";
		//}
	}
} else {
	// leggo i dati dell'evento in corso per posizionare la griglia sul calendario
	$rstEventoCorrente = GetEventoCorrente();
	if (mysql_num_rows($rstEventoCorrente) > 0) {
		$rowEventoCorrente = mysql_fetch_object($rstEventoCorrente);
	}
	if ($hdnID == '') {
		// nessuna persona selezionata, imposto l'evento corrente predefinito
		$eventi = CaricaEventi($rowEventoCorrente->IDEvento, null);
		$oggi = getdate(strtotime($rowEventoCorrente->Data));
		$iniziogiorno = $oggi["mday"];
		$giornosettimana = $oggi["wday"];
		$iniziomese = $oggi["mon"];
		$durata = $rowEventoCorrente->Durata;
		//echo "<br/><br/>".$rowEventoCorrente->Data.",".$iniziogiorno.",".$giornosettimana.",".$iniziomese.",".$durata;
		//exit();
	} else {
		// persona selezionata, evidenzio gli eventi per i quali esiste una prenotazione
		$arrEventiPersona = ResultSet2Array(GetEventiPartecipatiByID($hdnID));
		$eventi = CaricaEventi($rowEventoCorrente->IDEvento, $arrEventiPersona);
		$oggi = getdate(strtotime($rowEventoCorrente->Data));
		$iniziogiorno = $oggi["mday"];
		$giornosettimana = $oggi["wday"];
		$iniziomese = $oggi["mon"];
		$durata = $rowEventoCorrente->Durata;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
	<title>Estate Ragazzi / Iscrizioni &amp; Prenotazioni</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>
	<script type="text/javascript" src="./js/jsDate.js"></script>
	<script type="text/javascript" src="./js/date.format.js"></script>
	<?php SelectCSS(""); ?>   
	<script type="text/javascript" src="./js/funzioni.js" charset="ISO-8859-1"></script>
</head>

<body onLoad="CaricamentoIscrizioni()">
	<div class="suggestionsBox" id="suggestions">
		<img src="./Immagini/upArrow.png" class="suggestionPointer" alt="" />
		<div class="suggestionList" id="autoSuggestionsList">
			&nbsp;
		</div>
	</div>
  
   <!-- inizio sezione intestazione. Questa sezione contiene il logo, i campi di ricerca, la barra di navigazione e la data -->
        <div id="barratop">
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
    <form name="SezioneIscrizioni" id="SezioneIscrizioni" method="post" action="inserisci_prenotazioni.php" onsubmit="return ValidaPrenotazioni()">
	  <input type="hidden" name="postback" id="postback" value="1">
      <div id="campiricerca">
         <fieldset>
            <legend>Cerca iscritti &nbsp;</legend>
               <div id="etichette"">
                    <input type="hidden" name="hdnID" id="hdnID" value="<?php echo $hdnID ?>" />
                    <label for="txtCognome">Cognome</label>
                    <input type="text" name="txtCognome" id="txtCognome" onkeyup="lookup(this.value,'cognome');" onblur="fill();" autocomplete="off" />
                    &nbsp;
                    <label for="txtNome">Nome</label>
                    <input type="text" name="txtNome" id="txtNome" onkeyup="lookup(this.value,'nome');" onblur="fill();" autocomplete="off" />
                    &nbsp;
                    <label for="txtBarCode">Cod. a barre</label>
                    <input type="text" name="txtBarCode" id="txtBarCode" />
	                &nbsp;
                    <input type="submit" name="caricaPersona" id="caricaPersona" value="carica" onclick="submitKey = this.id" />
                </div> <!-- fine etichette -->
         </fieldset>
      </div> <!-- fine campi di ricerca -->
      <!-- ********************** sezione nome della pagina, operatore e data ********************-->
<?php
$result = GetOperatore($idoperatore);
$row = mysql_fetch_object($result);
// preparo il link alla stampa della privacy
if ($hdnID != null) {
	$stampa_privacy = "<a href=stampa_privacy_totale.php?id=".$hdnID." target=_blank >stampa privacy</a>";
} else {
	$stampa_privacy = "stampa privacy";
}
?>
      <div id="nomepagina">
          | operatore: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
      </div> <!-- fine sezione nome della pagina-->
      
      <!-- ******************** sezione barra di navigazione ***************************************-->
      <div id="barranavigazione">
        | <a href="homepage.php">home page</a> | <a href="xanagrafica.php">anagrafica oratorio</a> | <?php echo $stampa_privacy ?> |
      </div> <!-- fine sezione barra di navigazione -->
   </div> <!-- fine sezione intestazione -->
     		
		<!--*********************** sezione scelta prenotazioni (tabella e cena finale) **************************************************-->
      <div id="sceltaprenotazioni">
         <fieldset>
            <legend>Si prenota per...&nbsp;</legend>
               <div id="checkscelte"> <!-- inizio scelte -->
<?php
//
// aggiorno i dati se è stato richiesto
switch (strtolower($_REQUEST["salvaprenotazioni"])) {
	case "carica":		
	case "salva iscr./prenot.":		
		$dateEvento = CalcolaDateEvento(strtotime($rowEventoCorrente->Data), $durata);
		mysql_query("START TRANSACTION");
		AggiornaPrenotazioni();
		AggiornaIscrizione();
		mysql_query("COMMIT");
		break;
	default:
}
// compilo la griglia delle prenotazioni
CreaForm($iniziogiorno, $giornosettimana, $iniziomese, $durata, strtotime($rowEventoCorrente->Data) );
?>
              </div> <!-- fine scelte -->
         </fieldset>
      </div> <!-- fine scelta prenotazioni -->
     
     <!-- ********************************  CENA FINALE ESTATE RAGAZZI *********************************-->
     <div id="cenafinaleER"> 
        <fieldset id="cenafinalefs">
            <legend>Cena finale e eventi&nbsp;</legend>
                  <div style="margin-top:10px">
                    <input type="checkbox" name="CenaFinale" id="CenaFinale" onclick="ControlloCenaFinale();CalcolaCenaFinale()" value="CenaFinale" <?php if($CenaFinale) echo " checked=checked " ?>/>
                    <label for="CenaFinale"><strong>Cena finale&nbsp;</strong></label>
                  </div>
                  <div style="margin-top:10px">  
                    <input type="text" name="NrOspiti" id="NrOspiti" size="3" onblur="CalcolaCenaFinale()" value="<?php echo number_format($NrOspiti, 0) ?>"/>
                    <label for="nrospiti">Ospiti</label>
                  </div> 
                   <div style="margin-top:15px">
                    <input type="checkbox" name="EventoSpecialeER" id="EventoSpecialeER" onclick="ControlloEventoSpecialeER();" value="EventoSpecialeER" <?php if($EventoSpecialeER) echo " checked=checked " ?>/>
                    <label for="CenaFinale"><strong>Evento speciale&nbsp;</strong></label>
                  </div>
       </fieldset>     
      </div>
     <div id="conteggiocomplessivo"> 
        <fieldset id="conteggiocomplessivofs">
             <legend>Conteggio complessivo&nbsp;</legend>
                 <div id="TotaleCosto">
                  	<input type="text" name="CostoTotaleEuroCalcolato" id="CostoTotaleEuroCalcolato" size="3" style="margin-bottom:5px" readonly="readonly" value="" onclick="CopiaInCostoTotaleEuro(this)" />                	
                  	<label for="CostoTotaleEuroCalcolato"><strong>&euro; calcolati</strong></label>
                  	<input type="text" name="CostoTotaleEuro" id="CostoTotaleEuro" size="3" value="<?php echo number_format($CostoTotaleEuro, 2, ',', '.') ?>" />                	
                  	<label for="CostoTotaleEuro"><strong>&euro; da pagare</strong></label>
                  </div>
                  
                  <div id="CenaFinalePagamento">
                  	<input type="text" name="Pagamento" id="Pagamento" size="3" class="<?php echo $ClassePagamento ?>" onkeyup="PagamentoCompleto('CostoTotaleEuro',this)" onblur="PagamentoCompleto('CostoTotaleEuro',this)" value="<?php echo number_format($Pagamento, 2, ',', '.') ?>" />                	
                    <label for="Pagamento">&euro; versati</label>
                  </div>
        </fieldset>       
      </div>
      
      <!-- *******************  sezione iscrizioni *********************************** -->
      <div id="iscrizioni">
        <fieldset>
            <legend>Iscrizione&nbsp;</legend>
              <div id="iscrizionedati">
                Cognome:
                &nbsp;&nbsp;<span id="pers_cognome"><strong><?php echo $cognome ?></strong></span>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                Nome:
                &nbsp;<span id="pers_nome"><strong><?php echo $nome ?></strong></span>
              
				<div style="width:65%;margin-top:10px">
					<div style="float:right">
		                Tesserato:
		                &nbsp;<strong><span id="tesserato"><?php echo $tesseramento ?></span></strong>
		                &nbsp;
		                <input type="checkbox" name="chkIscrizione" id="Iscrizione" onclick="chkIscrizioneClick()" <?php echo $iscrizione ?> />
		                <label for="chkIscrizione">Iscritto Er</label>
					</div>
					<div style="float:left">
						&nbsp;
						Ruolo Er:&nbsp;
						<select name="RuoloIscritto" id="RuoloIscritto" onchange="AggiornaListino(this);" >
							<?php echo $ruoli ?>
						</select>
					</div>
				</div>
				<div style="width:65%;margin-top:40px">
					<div style="float:left"><?php echo "Squadra:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>".$squadra."</strong>" ?>&nbsp;</div>
					<div style="float:right">
						A:&nbsp;
						<select name="Evento" id="Evento" onchange="EventoChange(this)" onmousedown="EventoMouseDown(this)" onmousedown="EventoMouseUp(this)" onclick="EventoClick(this)">
							<?php echo $eventi ?>
						</select>
					</div>
				</div>

              <div id="TestoNote">
                <label for="note">Note</label> <br />
                <textarea name="note" rows="5" cols="27" id="note" class="nero"><?php echo stripslashes($note) ?></textarea>  
			  </div>
              <div style="clear:both;width:65%;margin-top:65px">
                  <div style="float:left">
					  <input type="button" name="squadra" id= "squadra" value ="squadra" <?php echo $btnsquadra ?> onclick="FinestraSquadra()" />
					  <input type="button" name="listino" id= "listino" value = "listino" onclick="aprilistino()" />
				  </div>
                  <div style="float:right">
				      <input type="button" name="carica_iscrizione" id= "carica_iscrizione" value = "Carica evento" onclick="CaricaIscrizione()" />
				  </div>
			  </div>
        	</div> <!-- fine della sezione iscrizionedati -->
        </fieldset>
      </div> <!-- fine sezione iscrizioni -->
     
     <!-- ***************** sezione conteggi ********************************** --> 
      <div id="conteggi">
        <fieldset id="conteggifs">
                <div id="etichetteconteggi" style="float:left">
					Iscrizione:<br/>
					Pranzo:<br/>
					Cena:<br/>
					Cena finale:<br/>
                    Abb.pranzo:<br/>
                    Abb.cena:<br/>
                    Abb.pr+cen.:<br/>
                    Ev.speciale:<br/>
				</div>  
				<div style="float:left; margin-left:15px; margin-right:0px">  
					<strong>&euro;</strong><br/>
                    <strong>&euro;</strong><br/>
					<strong>&euro;</strong><br/>
                    <strong>&euro;</strong><br/>
                    <strong>&euro;</strong><br/>
                    <strong>&euro;</strong><br/>
                    <strong>&euro;</strong><br/>
                    <strong>&euro;</strong><br/>
                </div>
				<div id="listino_prezzi" style="float:right">  
					<span id="prezzoIscrizione" style="float:right"><?php echo number_format($prezzoIscrizione, 2, ',', '.') ?></span><br/>
                    <span id="prezzoPranzo" style="float:right"><?php echo number_format($prezzoPranzo, 2, ',', '.') ?></span><br/>
					<span id="prezzoCena" style="float:right"><?php echo number_format($prezzoCena, 2, ',', '.') ?></span><br/>
                    <span id="prezzoCenaFinale" style="float:right"><?php echo number_format($prezzoCenaFinale, 2, ',', '.') ?></span><br/>
                    <span id="prezzoAbbPranzo" style="float:right"><?php echo number_format($prezzoAbbPranzo, 2, ',', '.') ?></span><br/>
                    <span id="prezzoAbbCena" style="float:right"><?php echo number_format($prezzoAbbCena, 2, ',', '.') ?></span><br/>
                    <span id="prezzoAbbPranzoCena" style="float:right"><?php echo number_format($prezzoAbbPranzoCena, 2, ',', '.') ?></span><br/>
                    <span id="prezzoEventoSpecialeER" style="float:right"><?php echo number_format($prezzoEventoSpecialeER, 2, ',', '.') ?></span><br/>
                </div>
        </fieldset>
      </div>
		
		<!-- *********** bottone per salvare le prenotazioni ******************* -->
		<div id="salvaprenotazioni" >
			 <input name="salvaprenotazioni" id="btnsalvaprenotazioni" type="submit" value ="salva iscr./prenot." onclick="submitKey = this.id;AbilitaCampiPerSubmit()" />
		</div>
		<input type="hidden" name="data_loaded" id="data_loaded" value="<?php echo $data_loaded ?>">
      <input type="hidden" name="hdnIDClasse" id="hdnIDClasse" value="<?php echo $hdnIDClasse ?>" />
    </form>  
	 <script type="text/javascript" >
			document.getElementById("CostoTotaleEuroCalcolato").value = CalcolaCostoTotale().toFixed(2).replace(".", ",");
	 </script>
</body>
</html>

<?php
// calcola le date dei giorni dell'evento
function CalcolaDateEvento($datagiorno, $durata) {
	$giorno = $datagiorno;
	// creo un array con le date dell'evento
	for ($g=0;$g<$durata;$g++) {
		$dateEvento[] = date("d/m/Y", $giorno);
		$giorno = DateAdd("d", 1, $giorno); // incremento la data
		$ggSettim = getdate($giorno);
		// la domenica viene saltata
		if ($ggSettim["wday"] == "0") {
			$giorno = DateAdd("d", 1, $giorno);
		}
	}
	return $dateEvento;
}

//********************************************************************************** 
// funzione per creare dinamicamente la tabella dei giorni nella pagina "iscrizioni e prenotazioni"
function CreaForm($iniziogiorno, $giornosettimana, $iniziomese, $durata, $dataGiorno) {
	// definizione delle variabili della funzione
	// iniziogiorno          giorno di inizio dell'Er
	// giornosettimana    numero del giorno della settimana 0 domenica, 1 lunedì, 2 martedì, ecc. 
	// iniziomese             mese in cui inizia l'Er
	// durata                   durata dell'evento
	// dataGiorno            data di inizio dell'evento
	
	global $data_loaded;			// rende viisibile una variabile definita a livello globale in questa function
	global $hdnID;
	global $hdnIDClasse;
	global $postback;				
	global $rstIscrizioneER;	
	global $rowIscrizioneER;
	global $note;
	global $iscrizione; 
	global $ruoli;
	global $AbbonamentoPranzo;
	global $AbbonamentoCena;
	global $CenaFinale;	
	global $NrOspiti;
	global $EventoSpecialeER;
	global $CostoTotaleEuro;
	global $Pagamento;
	global $ClassePagamento;
	global $prezzoIscrizione;
	global $prezzoPranzo;
	global $prezzoCena;
	global $prezzoCenaFinale;
	global $prezzoAbbPranzo;
	global $prezzoAbbCena;
	global $prezzoAbbPranzoCena;
	global $prezzoEventoSpecialeER;
	
	$numerogiorno; 					// variabile che serve per rappresentare correttamente il numero dei giorni del mese nella tabella
	$contagiorno=0; 				// serve per trovare il giorno domenica perché non si fa attività
	$finegiorno=($oggi + $durata); 	// data della fine dell'iniziativa
	$nrighe=6; 						// numero righe della tabella
	$ncolonne=8; 					// numero di colonne della tabella
	
	$giorni[1]="luned&igrave;";
	$giorni[2]="marted&igrave;";
	$giorni[3]="mercoled&igrave;";
	$giorni[4]="gioved&igrave;";
	$giorni[5]="venerd&igrave;";
	$giorni[6]="sabato";
	$giorni[0]="domenica";
	
	$nrgiorni[1]=31;  // gennaio
	$nrgiorni[2]=28;  // in un momento di voglia fargli calcolare anche i giorni di febbraio nell'anno bisestile...!
	$nrgiorni[3]=31;  // marzo
	$nrgiorni[4]=30;  // aprile
	$nrgiorni[5]=31;  // maggio
	$nrgiorni[6]=30;  // giugno
	$nrgiorni[7]=31;  // luglio
	$nrgiorni[8]=31;  // agosto
	$nrgiorni[9]=30;  // settembre
	$nrgiorni[10]=31; // ottobre
	$nrgiorni[11]=30; // novembre
	$nrgiorni[12]=31; // dicembre
	
	$chkNomeAttivita[0]=""; //non utilizzato
	$chkNomeAttivita[1]=""; //non utilizzato
	$chkNomeAttivita[2]="AttivitaMattino[]";
	$chkNomeAttivita[3]="AttivitaPomeriggio[]";
	$chkNomeAttivita[4]="AttivitaSera[]";
	$chkNomeAttivita[5]="Pranzo[]";
	$chkNomeAttivita[6]="Cena[]";

	$chkCampoAttivita[0]=""; //non utilizzato
	$chkCampoAttivita[1]=""; //non utilizzato
	$chkCampoAttivita[2]="Mattina";
	$chkCampoAttivita[3]="Pomeriggio";
	$chkCampoAttivita[4]="Sera";
	$chkCampoAttivita[5]="Pranzo";
	$chkCampoAttivita[6]="Cena";

	$intestazione[0]="Attivit&agrave;/GG";
	$intestazione[1]="&nbsp";
	$intestazione[2]="Mattino";
	$intestazione[3]="Pomeriggio";
	$intestazione[4]="Sera";
	$intestazione[5]="Pranzo";
	$intestazione[6]="Cena"; 

	$conteggio=0; // variabile che contiene il numero dei giorni dall'inizio dell'anno
	$mioconteggio=0; // variabile che contiene il numero dei giorni dall'inizio dell'anno, ma riferito alla data di inizio dell'Er
	
	$oggi = getdate();
	$mese =  $oggi["mon"];
	
	for ($indice = 1; $indice < $mese; $indice++) { // conta i giorni dall'inizio dell'anno al mese precedente del mese corrente
		$conteggio += $nrgiorni[$indice];
	}
	$conteggio += $oggi["mday"];   // aggiunge a alla variabile conteggio i giorni del mese corrente che mancano

	for ($indice=1; $indice < $iniziomese; $indice++) { // conta i giorni dall'inizio dell'anno al mese precedente di quello di inizio dell'Er
		$mioconteggio += $nrgiorni[$indice];
	}  
	$mioconteggio += $iniziogiorno; // aggiunge a alla variabile mioconteggio i giorni del mese corrente che mancano
	$oldmioconteggio = $mioconteggio;
	
	// leggo i dati dal db (se ho un utente selezionato)
	if ($hdnID != '') {
		LeggiIscritto();	//dati in read only, li leggo tutte le volte
		if ($data_loaded){
			// carico i dati dal post
			$note = $_POST["note"];
			$iscrizione = postCheck("chkIscrizione", "on"); 
			$ruoli = CaricaRuoli($_POST["RuoloIscritto"]);
			// carico il listino del ruolo dell'utente
			$rstListino = GetListinoByRuoloER($_POST["RuoloIscritto"], $_POST['Evento']);
			$rowListino = mysql_fetch_object($rstListino);
			$prezzoIscrizione = $rowListino->Iscrizione;
			$prezzoPranzo = $rowListino->Pranzo;
			$prezzoCena = $rowListino->Cena;
			$prezzoCenaFinale = $rowListino->CenaFinale;
			$prezzoAbbPranzo = $rowListino->AbbonamentoPranzo;
			$prezzoAbbCena = $rowListino->AbbonamentoCena;
			$prezzoAbbPranzoCena = $rowListino->AbbonamentoPranzoCena;
			$prezzoEventoSpecialeER = $rowListino->EventoSpecialeER;
			$AbbonamentoPranzo = $_POST["AbbonamentoPranzo"];
			$AbbonamentoCena = $_POST["AbbonamentoCena"];
			$CenaFinale = postCheckValue("CenaFinale", "CenaFinale");
			$NrOspiti = $_POST["NrOspiti"];
			$EventoSpecialeER = $_POST["EventoSpecialeER"];
			$CostoTotaleEuro = $_POST["CostoTotaleEuro"];
			$Pagamento = $_POST["Pagamento"];
		} else {
			// carico i dati dal db
			LeggiIscrizione();
			// carico i dati delle prenotazioni
			$rstPrenotazione = GetPrenotazione($hdnID, $_POST['Evento']);
			if (mysql_num_rows($rstPrenotazione) == 0) {		
				mysql_free_result($rstPrenotazione);
			}
			$data_loaded = true;
		}
	}
	//echo "<hr/>data loaded:".sprintf("%d", $data_loaded)."-".gettype($data_loaded)."<hr/>";
	
	// inizia a scrivere la tabella  
	echo ("<table>");
	for ($contarighe=0; $contarighe <= $nrighe; $contarighe++) {
		echo ("<tr>");
		switch ($contarighe) { // incolonna le voci di intestazione.
			case 0: 
				echo ("<th rowspan=\"2\" colspan=\"2\">".$intestazione[$contarighe]."</th>");
				break;
			case 1:
				break;
			default:
				if ($contarighe<5) {
					echo ("<th colspan=\"2\">".$intestazione[$contarighe]."</th>");
				} else {
					echo ("<th>".$intestazione[$contarighe]."</th>");
					// tolgo le parentesi quadre dal nome
					$NomeAttivita = substr($chkNomeAttivita[$contarighe], 0, strlen($chkNomeAttivita[$contarighe]) - 2);
					$Abbon = "";
					if (IsResultSet($rstIscrizioneER)) {
						switch ($NomeAttivita) {
							case "Pranzo":
								if ($AbbonamentoPranzo) {
									$Abbon = " checked=checked ";
								}
								break;
							case "Cena":
								if ($AbbonamentoCena) {
									$Abbon = " checked=checked ";
								}
								break;
						}
					} else {
						$Abbon = postCheck("Abbonamento".$NomeAttivita, $NomeAttivita);
					}
					echo ("<th class=\"abbonamento\">Abb.<input type=\"checkbox\" name=\"Abbonamento".$NomeAttivita."\" id=\"Abbonamento".$NomeAttivita."\" onclick=\"ControlloAbbonamento".$NomeAttivita."()\" value=\"".$NomeAttivita."\"".$Abbon."  /></th>");
				}
 
		} // finisce istruzione switch
		  
		$contagiorno = $giornosettimana;
		$numerogiorno = $iniziogiorno;
		$val = 0;
		$giorno = $dataGiorno;
		for ($contacolonne=0; $contacolonne <= $ncolonne; $contacolonne++) {
			if ($contagiorno==0) { // se è domenica non crea la colonna nella tabella
				$contagiorno++;
				$mioconteggio++;
				$numerogiorno++;
			} else {           
				switch ($contarighe) {
					case 0: // riga del nome del giorno
						$inizialigiorno=substr($giorni[$contagiorno],0,3); // estrae le prime tre lettere del nome del giorno
						if ($mioconteggio < $conteggio) {
							echo ("<td class=\"giornotrascorso\">".strtoupper($inizialigiorno)."</td>");
						}
						if ($mioconteggio == $conteggio) {
							echo ("<td class=\"giornoattivo\"><strong>".strtoupper($inizialigiorno)."</strong></td>");
						  } 
						if ($mioconteggio > $conteggio) {
							echo ("<td class=\"giornopassivo\">".strtoupper($inizialigiorno)."</td>");
						}
						$mioconteggio++;	//incrementa di uno il giorno
						break;
					
					case 1: // riga del numero del giorno
						if ($mioconteggio < $conteggio){
							$tipogiorno = "giornotrascorso"; 
						} else if ($mioconteggio == $conteggio) {
							$tipogiorno = "giornoattivo"; 
						} else {
							$tipogiorno = "giornopassivo"; 
						} 
						echo ("<td class=\"".$tipogiorno."\"><span title=\"".date("d/m/Y", DateAdd("d", $contacolonne, $dataGiorno))."\"><strong>".$numerogiorno."</strong></span></td>");
						$numerogiorno++;	  
						if ($numerogiorno > $nrgiorni[$iniziomese]) {// controlla se si supera la fine del mese
							$numerogiorno=1;
						}
						$mioconteggio++;	
						//if ((contagiorno.1)>6) {$numerogiorno++;} //controlla che il giorno successivo sia un giorno valido
						break;
						
					default: // righe che contengono i checkbox
						if ($intestazione[$contarighe]!="Pranzo" && $intestazione[$contarighe]!="Cena"){ // compone le righe dei checkbox delle attività
							// la routine deve avvisare l'utente che ha scelto di selezionare un'attività di un giorno già trascorso
							if (IsResultSet($rstPrenotazione)) {
								$rowPrenotazione = mysql_fetch_array($rstPrenotazione);
								if ($rowPrenotazione[$chkCampoAttivita[$contarighe]] == 1) {
									$prenotaz = "checked";
								} else {
									$prenotaz = "";
								}
							} else {
								$prenotaz = postCheck($chkNomeAttivita[$contarighe], date("d/m/Y", $giorno));
							}
							if ($mioconteggio < $conteggio){   
								echo ("<td class=\"giornotrascorso\"><input type=\"checkbox\" name=\"".$chkNomeAttivita[$contarighe]."\" onclick=\"ControlloAttivita()\" value=\"".date("d/m/Y", $giorno)."\"".$prenotaz." /></td>");
							} else if ($mioconteggio==$conteggio) { 
								echo ("<td class=\"giornoattivo\"><input type=\"checkbox\" name=\"".$chkNomeAttivita[$contarighe]."\" value=\"".date("d/m/Y", $giorno)."\"".$prenotaz." /></td>");   
							} else {
								echo ("<td class=\"giornopassivo\"><input type=\"checkbox\" name=\"".$chkNomeAttivita[$contarighe]. "\"  value=\"".date("d/m/Y", $giorno)."\"".$prenotaz." /></td>");
							}    
						} 
						// checkbox e input per pranzi e cene  
						if ($intestazione[$contarighe]=="Pranzo" || $intestazione[$contarighe]=="Cena") { 
							if ($mioconteggio < $conteggio) {
								$classeTd = "giornotrascorso";
								$classeInput = "rosso";
							} else if ($mioconteggio == $conteggio) {
								$classeTd = "giornoattivo";
								$classeInput = "violagrassetto";
							} else {
								$classeTd = "giornopassivo";
								$classeInput = "rosso";
							} 
							
							if (IsResultSet($rstPrenotazione)) {
								// sto caricando i dati dal db
								$rowPrenotazione = mysql_fetch_array($rstPrenotazione);
								if ($rowPrenotazione[$chkCampoAttivita[$contarighe]."Gratis"] == 0) {
									$statoCheckGratis = "";
								} else {
									$statoCheckGratis = " checked=checked ";
								}
								if ($rowPrenotazione[$chkCampoAttivita[$contarighe]."Quota"] == 0) {
									$statoCheckPrezzo = "";
								} else {
									$statoCheckPrezzo = " checked=checked ";
								}
							} else {
								// sto leggendo il post  della form
								$statoCheckPrezzo = postCheck($chkNomeAttivita[$contarighe], date("d/m/Y", $giorno));
								$statoCheckGratis = postCheck("Gratis".$chkNomeAttivita[$contarighe], date("d/m/Y", $giorno));
							}
							if ($statoCheckPrezzo == "") {
								$statoPrezzo = "disabled=\"disabled\"";
								$valorePrezzo = "";
							}
							else {
								$statoPrezzo = "";
								if (IsResultSet($rstPrenotazione)) {
									$valorePrezzo = $rowPrenotazione[$chkCampoAttivita[$contarighe]."Quota"];
								} else {
									$valorePrezzo = $_POST["Costo".$NomeAttivita][$val];
								}
								$val++;
							}
							echo ("<td class=\"".$classeTd."\"><input type=\"checkbox\" onclick=\"ControlliPagamento(this)\" name=\"".$chkNomeAttivita[$contarighe]."\" value=\"".date("d/m/Y", $giorno)."\"".$statoCheckPrezzo." />");
							echo ("&euro; <input type=\"text\" size=\"2\" name=\"Costo".$NomeAttivita."[]\" id=\"Costo".$NomeAttivita."[]\" ".$statoPrezzo." class=".$classeInput."\" value=\"".$valorePrezzo."\" onblur=\"ControlloNumerico(this)\" />");
							echo ("<br />gratis<input type=\"checkbox\" name=\"Gratis".$chkNomeAttivita[$contarighe]."\" onclick=\"ControlliPagamento(this)\" value=\"".date("d/m/Y", $giorno)."\"".$statoCheckGratis." /></td>");
						}                          	      	  
						$mioconteggio++;	
						$giorno = DateAdd("d", 1, $giorno); // incremento la data
						$ggSettim = getdate($giorno);
						// la domenica viene saltata
						if ($ggSettim["wday"] == "0") {
							$giorno = DateAdd("d", 1, $giorno);
						}
				} // chiude istruzione switch
				$contagiorno++;
				if ($contagiorno > 6) { //controlla se si supera la fine della settimana
					$contagiorno = 0;
				} 				
			} //fine else controllo contagiorno 
		} // fine secondo ciclo for 
		echo ("</tr>");
		$mioconteggio = $oldmioconteggio;
		$contagiorno = $giornosettimana;	
		// se sto leggendo dal db riporto il reesultset all'inizio
		if (IsResultSet($rstPrenotazione)) {
			mysql_data_seek($rstPrenotazione, 0);
		}
	} // fine primo ciclo for 
	echo ("</table>");

	// imposto il colore della cifra versata in relazione al prezzo calcolato
	if ($CostoTotaleEuro <= $Pagamento) {
		$ClassePagamento ="nero";
	} else {
		$ClassePagamento ="rosso";
	}
	return;
} //************* fine funzione crea tabella ****************************

// carica i  dati di intestazione dell'iscritto
function LeggiIscritto() {
	global $tesseramento;			// rende viisibile una variabile definita a livello globale in questa function
	global $cognome;
	global $nome;
	global $hdnID;
	global $hdnIDClasse;
	
	$rstPersona = GetPersona($hdnID);
	if($rstPersona) {
		if(mysql_num_rows($rstPersona) > 0) {
			$row = mysql_fetch_object($rstPersona);
			$cognome = htmlentities($row->Cognome);
			$nome = htmlentities($row->Nome);
			$hdnIDClasse = $row->Classe;
			$oggi = getdate();
			$anno = $oggi["year"] - 1;
			$data_tesseramento_valido = date_create("10/01/".$anno); //data in formato mm/gg/aaaa
			//echo "Tessera:".date_format(date_create($row->DataTesseramento), "d/m/Y")."<br/>";
			//echo "Valida:".date_format($data_tesseramento_valido, "d/m/Y")."<br/>";
			//echo "tipo:".gettype($row->DataTesseramento)."<br/>";
			if($row->DataTesseramento == null or date_create($row->DataTesseramento) < $data_tesseramento_valido) {
				$tesseramento = "NO";
			}
			else {	
				$tesseramento = date("d/m/Y", strtotime($row->DataTesseramento));
			}
		}
	}
}

// carica i  dati di iscrizione all'ER
function LeggiIscrizione() {
	global $note;			// rende viisibile una variabile definita a livello globale in questa function
	global $hdnID;
	global $hdnIDClasse;
	global $iscrizione;
	global $btnsquadra;
	global $squadra;
	global $ruoli;
	global $rstIscrizioneER;	
	global $rowIscrizioneER;
	global $AbbonamentoPranzo;
	global $AbbonamentoCena;
	global $CenaFinale;
	global $NrOspiti;
	global $EventoSpecialeER;
	global $CostoTotaleEuro;
	global $Pagamento;
	global $prezzoIscrizione;
	global $prezzoPranzo;
	global $prezzoCena;
	global $prezzoCenaFinale;
	global $prezzoAbbPranzo;
	global $prezzoAbbCena;
	global $prezzoAbbPranzoCena;
	global $prezzoEventoSpecialeER;
	
	$rstIscrizioneER = GetIscrizioneER($hdnID, $_POST['Evento']);
	$rowIscrizioneER = null;
	$iscrizione = "";
	$btnsquadra = "disabled=\"disabled\"";
	if($rstIscrizioneER) {
		$rowIscrizioneER = mysql_fetch_object($rstIscrizioneER);
		if (mysql_num_rows($rstIscrizioneER) > 0) {
			// carico i dati della iscrizione
			$iscrizione = "checked";
			$btnsquadra = "";				// abilito il bottone Squadra
			$rstSquadra = getSquadraByID($hdnID, $_POST['Evento']);
			$rowSquadra = mysql_fetch_object($rstSquadra);
			$squadra = $rowSquadra->NomeSquadra;
			$note = $rowIscrizioneER->Note;
			$ruoli = CaricaRuoli($rowIscrizioneER->IDRuolo);
			$AbbonamentoPranzo = $rowIscrizioneER->AbbonamentoPranzo;
			$AbbonamentoCena = $rowIscrizioneER->AbbonamentoCena;
			$CenaFinale = $rowIscrizioneER->CenaFinale;
			$NrOspiti = $rowIscrizioneER->CenaFinaleOspiti;
			$EventoSpecialeER = $rowIscrizioneER->EventoSpecialeER;
			$CostoTotaleEuro = $rowIscrizioneER->CostoComplessivo;
			$Pagamento = $rowIscrizioneER->Pagamento;
			// carico il listino del ruolo dell'utente
			// LF 12/06/2010 
			// se l'iscrizione è stata fatta senza impostare il ruolo non carico il listino
			if ($rowIscrizioneER->IDRuolo != "") {
				$rstListino = GetListinoByRuoloER($rowIscrizioneER->IDRuolo, $_POST['Evento']);
				$rowListino = mysql_fetch_object($rstListino);
				$prezzoIscrizione = $rowListino->Iscrizione;
				$prezzoPranzo = $rowListino->Pranzo;
				$prezzoCena = $rowListino->Cena;
				$prezzoCenaFinale = $rowListino->CenaFinale;
				$prezzoAbbPranzo = $rowListino->AbbonamentoPranzo;
				$prezzoAbbCena = $rowListino->AbbonamentoCena;
				$prezzoAbbPranzoCena = $rowListino->AbbonamentoPranzoCena;
				$prezzoEventoSpecialeER = $rowListino->EventoSpecialeER;
				$ruoli = CaricaRuoli($rowIscrizioneER->IDRuolo);
			}
		} else {
			$ruoli = CaricaRuoli("0");
		}
	}
}
	
function AggiornaPrenotazioni() {
	global $dateEvento;
	global $hdnID;
	global $hdnIDClasse;
	
	$iPranzo = 0;
	$iCena = 0;
	foreach ($dateEvento as $dataEvento) {
		$aggiornare = false;
		$Mattina = 0;
		$Pomeriggio = 0;
		$Sera = 0;
		$Pranzo = 0;
		$PranzoQuota = 0;
		$Cena = 0;
		$CenaQuota = 0;
		$PranzoGratis = 0;
		$CenaGratis = 0;
		if (postCheck("AttivitaMattino", $dataEvento)) {
			$Mattina = 1;
			$aggiornare = true;
		}
		if (postCheck("AttivitaPomeriggio", $dataEvento)) {
			$Pomeriggio = 1;
			$aggiornare = true;
		}
		if (postCheck("AttivitaSera", $dataEvento)) {
			$Sera = 1;
			$aggiornare = true;
		}
		if (postCheck("Pranzo", $dataEvento)) {
			$Pranzo = 1;
			$aggiornare = true;
			$PranzoQuota = $_POST["CostoPranzo"][$iPranzo];
			if ($PranzoQuota == "") $PranzoQuota = 0;
			$iPranzo++;
		}
		if (postCheck("Cena", $dataEvento)) {
			$Cena = 1;
			$aggiornare = true;
			$CenaQuota = $_POST["CostoCena"][$iCena];
			if ($CenaQuota == "") $CenaQuota = 0;
			$iCena++;
		}
		if (postCheck("GratisPranzo", $dataEvento)) {
			$PranzoGratis = 1;
			$aggiornare = true;
		}
		if (postCheck("GratisCena", $dataEvento)) {
			$CenaGratis = 1;
			$aggiornare = true;
		}
		InsertPrenotazione($hdnID, $_POST['Evento'], $dataEvento, $Mattina, $Pomeriggio, $Sera, $Pranzo, $Cena, $PranzoQuota, $CenaQuota, $PranzoGratis, $CenaGratis);
	}
}

function AggiornaIscrizione() {
	global $hdnID;
	global $hdnIDClasse;
	global $rstIscrizioneER;
	global $rowIscrizioneER;
	global $data_loaded;
	
	// carico il recordset se non esiste
	if (!isset($rstIscrizioneER)) {
		$rstIscrizioneER = GetIscrizioneER($hdnID, $_POST['Evento']);
		$rowIscrizioneER = null;
		if($rstIscrizioneER) {
			$rowIscrizioneER = mysql_fetch_object($rstIscrizioneER);
		}
	}
	// c'è l'iscrizione sulla form?
	if ($_POST["chkIscrizione"]) {
		// c'è l'iscrizione nel db?
		if (mysql_num_rows($rstIscrizioneER) > 0) {
			// sì, aggiorno
			try {
				UpdateIscrizioneER($rowIscrizioneER->IDIscrizione, $_POST["RuoloIscritto"], $_POST["note"], postCheckValue("AbbonamentoPranzo", "Pranzo"), postCheckValue("AbbonamentoCena", "Cena"), $_POST["CostoTotaleEuro"], postCheckValue("CenaFinale", "CenaFinale"), $_POST["NrOspiti"], postCheckValue("EventoSpecialeER", "EventoSpecialeER"), $_POST["Pagamento"]);
			}
			catch (Exception $e) {
				switch (getErrorCode($e->getMessage())) {
					default:
						ob_clean();
						echo $e->getMessage();
						mysql_query("ROLLBACK");
						exit();
				}
			}
		}
		else {
			// no, inserisco
			try {
				// $NrOspiti = $rowIscrizioneER->CenaFinaleOspiti;
				// $CostoTotaleEuro = $rowIscrizioneER->CenaFinale;
				// $Pagamento = $rowIscrizioneER->Pagamento;
				InsertIscrizioneER($_POST["hdnID"], $_POST['Evento'], $_POST["RuoloIscritto"], $_POST["note"], postCheckValue("AbbonamentoPranzo", "Pranzo"), postCheckValue("AbbonamentoCena", "Cena"), $_POST["CostoTotaleEuro"], postCheckValue("CenaFinale", "CenaFinale"), $_POST["NrOspiti"], postCheckValue("EventoSpecialeER", "EventoSpecialeER"), $_POST["Pagamento"]);
			}
			catch (Exception $e) {
				switch (getErrorCode($e->getMessage())) {
					default:
						ob_clean();
						echo $e->getMessage();
						mysql_query("ROLLBACK");
						exit();
				}
			}
		}
		$note = $_POST["note"];
	}
	else {
		// c'è l'iscrizione nel db?
		if (mysql_num_rows($rstIscrizioneER) > 0) {
			// sì, elimino
			try {
				DeleteIscrizioneER($rowIscrizioneER->IDIscrizione);
				DeletePrenotazioni($_POST["hdnID"], $_POST["Evento"]);
				// azzero i dati di post in seguito alla eliminazione della prenotazione
				postClear("AttivitaMattino[]");
				postClear("AttivitaPomeriggio[]");
				postClear("AttivitaSera[]");
				postClear("Pranzo[]");
				postClear("Cena[]");
				postClear("CostoPranzo[]");
				postClear("CostoCena[]");
				postClear("GratisPranzo[]");
				postClear("GratisCena[]");
				// faccio ricaricare la pagina dal db
				$data_loaded = false;
			}
			catch (Exception $e) {
				switch (getErrorCode($e->getMessage())) {
					default:
						ob_clean();
						echo $e->getMessage();
						mysql_query("ROLLBACK");
						exit();
				}
			}
		}
	}
}
?>