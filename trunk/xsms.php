<?php
session_start();

require('accesso_db.inc');
require("funzioni_generali.inc");
require ("get_data.inc");           // funzione per il calcolo della data!
require ('accesso_gtw_sms.inc');    // setup per l'accesso al gateway
require ("lib-mobytsms.inc.php");   // libreria per l'invio degli sms a Mobyt
require ("f_sms.inc");              // funzioni php

$host  = $_SERVER['HTTP_HOST'];

// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}

// Identifica l'operatore che si è loggato
$idoperatore = $_SESSION['authenticated_user_id'];

// Definisce il titolo della finestra del browser
$title="Gestione SMS";

ConnettiDB();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Oratorio Saint-Martin / <?php echo $title; ?> </title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    
  <?php SelectCSS("struttura_pagina"); ?>   

<script type="text/javascript" src="./js/f_sms.js"></script>
<script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>

<style>

table.scegli_tipo_invio_SMS th.bordo{
  background:green;
  color:white;
  text-align:left;
  -moz-border-radius:7px;
  font-variant:small-caps;
}

table.scegli_tipo_invio_SMS {
  position:absolute;
  left:200px;
}

table.scegli_tipo_invio_SMS td.bordino{
  border-bottom:1px dotted black;
}

#esito_spedizione {
  position:absolute;
  margin-top:5px;
  background:#CCCC99;
  -moz-border-radius:7px;
  padding:2px;
}

td.esito {
  vertical-align:middle;
  border-bottom:1px dotted black;
}

#tblRiepilogo {
  position:relative;
  top:5px;
  left:60px;
}

th.riepilogo {
  text-align:left;
 /* border-bottom:2px dotted red;*/
}

td.riepilogo2 {
  vertical-align:middle;
  /*border-bottom:2px dotted red;*/
}
table.scegli_tipo_invio_SMS td.bordino_credito{
  background:#CCCC99;
  -moz-border-radius:7px;
  font-weight:bold;
  text-align:left;
}

td {
  vertical-align:top;
}

legend {
  font-weight:bold;
  background:#CCFF99;
  -moz-border-radius:7px;
  padding-left:5px;
  padding-bottom:3px;
}

#btnEsitoTornaMenu {
    height:80px;
    width: 200px;
}

#fldClassi {
  -moz-border-radius:7px;
  border: #CCCC99;
  background: #CCCC99;
}

#fldRuoli {
  -moz-border-radius:7px;
  border: 1px dotted  #CCCC99;
  background: #CCCC99;
}

#fldTesseramenti {
  -moz-border-radius:7px;
  border: 1px dotted  #CCCC99;
  background: #CCCC99;
}

#fldPartecipazione {
  -moz-border-radius:7px;
  border: 1px dotted  #CCCC99;
  background: #CCCC99;
}

#fldGruppo {
  -moz-border-radius:7px;
  border: 1px dotted  #CCCC99;
  background: #CCCC99;
}

#fldAltro {
  -moz-border-radius:7px;
  border: 1px dotted  #CCCC99;
  background: #CCCC99;
}

#tblClassi {
  height: 250px;
} 

#btnElabora {
  width: 150px;
  height: 100px;
  color: green;
  font-weight:bold;
}

#btnMailingList {
  width: 150px;
  height: 100px;
  font-weight:bold;
  color: red;
}

#btnTornaMenu {
  width: 150px;
  height: 100px;
  font-weight:bold;
  color: black;
}

#criteri_scelta {
  visibility:hidden;
  position:absolute;
  top:75px;
  left:50px;
  width:920px;
  height:410px;
  background: white;
}
/******************* REGOLE PER MENU SMS    *******************/
#menu_sms {
  visibility:hidden;
  position:absolute;
  top:90px;
  left:50px;
  width:800px;
  height:20px;
  background: white;
  text-align:center;
}

#btnInviaSms{
  height:100px;
  width:250px;
  font-weight:bold;
}

#btnStatistiche{
  height:100px;
  width:250px;
  font-weight:bold;
}

.OkCredit {
  background:green;
  color:white;
  text-align:center;
  padding:3px;
  -moz-border-radius:7px;
}

.EndingCredit {
  background:orange;
  color:white;
  text-align:center;
  padding:3px;
   -moz-border-radius:7px;
}

.NoCredit {
  background:red;
  color:white;
  text-align:center;
  padding:3px;
   -moz-border-radius:7px;
}
/******************* REGOLE PER RIEPILOGO SMS        *******************/
#riepilogo_sms {
  visibility: hidden;
  position: absolute;
  top:75px;
  left:50px;
  width:900px;
  height:410px;
  -moz-border-radius:7px;
  background: #CCCC99;
}

#sms_text2 {
    height:100px;
    width: 280px;
    border:1px dotted grey;
    color:green;
    font-weight:bold;
    padding-left:0.2em;
    background:white;
}

#btnInviaGateway {
  height:40px;
  width:150px;
}

#btnModificaTesto {
  height:40px;
  width:150px;
}

#btnAnnullaSpedizioneRiepilogo {
  height:40px;
  width:150px;
}

/******************* REGOLE PER SEZIONE RISPOSTA GATEWAY DOPO INVIO SMS *******************/
#risposta_gtw {
  visibility: hidden;
  position: absolute;
  top:75px;
  left:50px;
  width:900px;
  height:410px;
}

/******************* REGOLE PER SEZIONE INVIA SMS    *******************/
#invia_sms {
  visibility: hidden;
  position: absolute;
  top:75px;
  left:50px;
  width:900px;
  height:410px;
  -moz-border-radius:7px;
  background: #CCCC99;
}

#lstListaTelefoni {
  position:relative;
  top: 5px;
  left: 5px;
  font-family:courier new;
  border:1px dotted orange;
  width:450px;
}

option {
  border-bottom:1px dashed green;
}

#tbl_invia_sms {
  width:100%;
}

#sms_text {
    height:100px;
    width: 400px;
    border:1px dotted green;
    color:green;
    font-weight:bold;
    padding-left:0.2em;
}

#btnSubmit {
    width:200px;
    background:#CCFF99;
    -moz-border-radius:7px;
}

#btnAnnulla {
    width:200px;
    background:#FF6347; 
     -moz-border-radius:7px;
}

.nrStatistiche {
    background:green; 
    color:white; 
    font-weight:bold;
    padding-right:0.8em;
    -moz-border-radius: 7px;
    text-align:right;
    border:0px;
}

.nrStatistiche2 {
    background:#808000; 
    color:white; 
    font-weight:bold;
    padding-right:0.8em;
    -moz-border-radius: 7px;
    text-align:right;
    border:0px;
}
.RisultatiTrovati {
    background:purple; 
    color:white; 
    font-weight:bold;
    padding:0.3em;
    -moz-border-radius: 7px;
}

#btnDeseleziona {
    -moz-border-radius: 7px;
    background:#DDA0DD;
}

 /****************** REGOLE PER LA FINESTRA AJAX *******************/ 
 .suggestionsBox {
		font-family: Helvetica;
		font-size: 12px;
		color: #000000;
		position: relative;
		left: 55%;
		margin: 10px 0px 0px 0px;
		width: 200px;
		background-color: #FFFFFF;
		-moz-border-radius: 7px;
		/*-webkit-border-radius: 7px;*/
		border: 2px solid #FF8C00;	
		color: #000000;
		z-index:10;
		text-align:left;
	}
	
.suggestionsBoxNames {
		font-family: Helvetica;
		font-size: 12px;
		color: #000000;
		position: relative;
		left: 55%;
		margin: 10px 0px 0px 0px;
		width: 200px;
		background-color: #FFFFFF;
		-moz-border-radius: 7px;
		/*-webkit-border-radius: 7px;*/
		border: 2px solid #FF8C00;	
		color: #000000;
		z-index:10;
		text-align:left;
	}
	
.suggestionList {
		margin: 0px;
		padding: 0px;
	}
	
.suggestionList li {
		margin: 0px 0px 3px 0px;
		padding: 3px;
		cursor: pointer;
		list-style-type: none;
	}
	
	.suggestionList li:hover {
		background-color: #659CD8;
	}
 

/* ****************************************************************************************/
</style>

</head>

<body>
<!-- SEZIONE INTESTAZIONE PAGINA. CONTIENE STRUTTURA PAGINA -->
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
        
        <div id="scritta_location">
            | sei in: <strong><?php echo $title; ?></strong> |
        </div>
    
<!-- FINE SEZIONE INTESTAZIONE -->

<!-- SEZIONE BARRA DI NAVIGAZIONE -->
    <?php
        $barra_di_navigazione="| <a href='homepage.php'>men&ugrave; principale</a> | <a href='homepage.php?menu_padre=8'>utility</a> | 
                              <a href='xanagrafica.php'>anagrafica</a> |";
        
        /*if ($_SESSION['access_level'] >2) {
            $barra_di_navigazione.=" <a href='xcestino.php'>visualizza cestino</a> |";
        }*/
        
        echo "<div id='mybarranavigazione'> \n";
        echo "$barra_di_navigazione"."\n";
        echo "</div> \n"; 
     ?>
<!-- FINE SEZIONE BARRA DI NAVIGAZIONE -->
 
<!-- SEZIONE OPERATORE CONNESSO -->
     <?php
        $result = GetOperatore($idoperatore); // legge nome e cognome dell'operatore in base al suo ID
        $row = mysql_fetch_object($result);
        $operatore=htmlentities($row->Nome).' '.htmlentities($row->Cognome);
     ?>
     <div id="myoperatore">
          | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
    
<!-- FINE SEZIONE OPERATORE CONNESSO -->

<!-- COSTRUZIONE ELEMENTI PAGINA -->
<form id ="frmCriteri" name="frmCriteri" method="post">
  <input type="hidden" name="hdnClassi" id="hdnClassi" value="" />
  <input type="hidden" name="hdnGruppi" id="hdnGruppi" value="" />
  <input type="hidden" name="hdnRuoli" id="hdnRuoli" value="" />
  <input type="hidden" name="hdnTesseramenti" id="hdnTesseramenti" value="" />
  <input type="hidden" name="hdnPartecipazione" id="hdnPartecipazione" value="" />
  <input type="hidden" name="hdnAltro" id="hdnAltro" value="" />
  <input type="hidden" name="azione" id="azione" value="<?php echo $_POST['azione'];?>" />
  <input type="hidden" name="hdnListaDestinatari" id="hdnListaDestinatari" value="<?php echo $_POST['hdnListaDestinatari'];?>" />
  <input type="hidden" name="hdnCreditoMessaggi" id="hdnCreditoMessaggi" value="<?php echo $_POST['hdnCreditoMessaggi'];?>" />
 <?php 
    switch ($_GET['s']) {
        case 0: // menu principale dell'utility sms
            ?>
            <div id="menu_sms">
                <table class="scegli_tipo_invio_SMS" cellpadding="10">
                    <tr>
                        <th class="bordo" colspan="2">
                            Men&ugrave; SMS
                        </th>
                        
                        <th class="bordo" colspan="2">
                            Crediti
                        </th>
                    </tr>
                    <tr>
                        <td class="bordino">
                            <img src="./Immagini/sms1.jpg" 
                                width="120"
                                title="Invia SMS" />
                        </td>
                        <td class="bordino"> 
                            <input 
                                type="button"
                                id="btnInviaSms"
                                value="Invia SMS" 
                                onclick="Spedizione('menu_invia_sms')" />
                        </td>
                    
                        <!-- CALCOLA IL CREDITO RESIDUO -->
                        <td class="bordino_credito">
                            <table width="100%" cellpadding="10">
                                <tr>
                                    <td>
                                        &euro;:
                                    </td>
                                    
                                    <td>
                                        <?php 
                                            // istanzia una classe per reperire il credito, i messaggi e le notifiche disponibili
                                            $sms=new mobytSms($account, $pw_gtw);
                                            
                                            // ottiene il credito (espresso in euro) ancora disponibile
                                            $residuo_credito=($sms->getCredit()/10000);

                                            if ($residuo_credito<1 || $residuo_credito==null) {
                                                $classe="NoCredit";
                                                $residuo_credito=0;
                                            } elseif ($residuo_credito>0 && $residuo_credito<50) {
                                                $classe="EndingCredit";
                                            } else {
                                                $classe="OkCredit";
                                            }
                                            
                                            //sostituisce il punto per mettere la virgola italiana per i decimali
                                            $residuo_credito=str_replace(".",",",$residuo_credito);
                                            
                                            //stampa il credito residuo
                                            echo "<span class='".$classe."'>".$residuo_credito."</span>";
                                        ?>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>
                                        Messaggi:
                                    </td>
                                    <td id="tdMessaggi">
                                        <?php 
                                            // ottiene il numero (approssimativo) degli sms disponibili
                                            $residuo_messaggi=$sms->getAvailableSms();
                                            //$residuo_messaggi=400; //DA CANCELLARE SERVIVA PER PROVA NON IN RETE
                                            $_POST['hdnCreditoMessaggi']=$residuo_messaggi;
                                            if ($residuo_messaggi <1 || $residuo_messaggi==null) {
                                                $classe="NoCredit";
                                                $residuo_messaggi=0;
                                            } elseif ($residuo_messaggi>0 && $residuo_messaggi<50) {
                                                $classe="EndingCredit";
                                            } else {
                                                $classe="OkCredit";
                                            }
                                
                                            echo "<span class='".$classe."'>".$residuo_messaggi."</span>";
                                        ?>
                                    </td>
                                    
                                </tr>
                                <tr>
                                    <td>
                                        Notifiche:
                                    </td>
                                    <td>
                                        <?php 
                                            // ottiene il numero di notifiche ancora disponibili
                                            $residuo_notifiche=$sms->getAvailableNotifies();
                                            if ($residuo_notifiche<1 || $residuo_notifiche==null) {
                                                $classe="NoCredit";
                                                $residuo_notifiche=0;
                                            } elseif ($residuo_notifiche>0 && $residuo_notifiche<50) {
                                                $classe="EndingCredit";
                                            } else {
                                                $classe="OkCredit";
                                            }
                                
                                            echo "<span class='".$classe."'>".$residuo_notifiche."</span>";
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="bordino">
                            <img src="./Immagini/sms2.jpg" 
                                  width="100" 
                                  title="Mailing List" />
                        </td>
                        
                        <td class="bordino">
                            <input 
                              type="button" 
                              id="btnStatistiche"
                              value="Mailing List"
                              disabled />
                        </td>
                        
                        <td colspan="2" class="bordino_credito">
                          <table cellpadding="10">
                              <tr>
                                  <td>
                                      <span style="background:green;padding:5px;">&nbsp;</span>
                                  </td>
                                  <td>
                                      disponibile
                                  </td>
                              </tr>
                              <tr>
                                  <td>
                                      <span style="background:orange;padding:5px;">&nbsp;</span>
                                  </td>
                                  <td>
                                      in riserva
                                  </td>
                              </tr>
                              <tr>
                                  <td>
                                      <span style="background:red;padding:5px;">&nbsp;</span>
                                  </td>
                                  <td>
                                      esaurito
                                  </td>
                              </tr>
                          </table>
                        </td>
                        
                    </tr>
                </table>                
            </div>
            <?php
                echo "<script>";
                echo "document.getElementById('menu_sms').setAttribute('style','visibility:visible');\n";
                echo "</script>";
        break;
        
        case 1: // form per filtrare i dati e costruire una lista di distribuzione
          ?>

  <div id="criteri_scelta"> 
  <!-- criteri di scelta -->
  <table id="tblScelta" border=0 cellpadding="3">
      <tr>
          <!-- CLASSI -->
          <td>
              <fieldset id="fldClassi"><legend>Classi... </legend>
                  <?php PopolaClassi() ?>
              </fieldset>
          </td>
          <td>
              <!-- RUOLI -->
              <fieldset id="fldRuoli"><legend>Ruoli... </legend>
                  <?php PopolaRuoli() ?>
              </fieldset>
          </td>
          
          <td>
              <!-- PARTECIPAZIONE -->
              <fieldset id="fldPartecipazione"><legend>Partecipazione... </legend>
                  <p>
                    <input  type="checkbox" 
                            name ="chkPartecipazione" 
                            value="1" checked 
                            />&nbsp;Regolare
                  </p> 
                  
                  <p>
                    <input  type="checkbox" 
                            name ="chkPartecipazione" 
                            value="3" 
                            />&nbsp;Saltuaria
                  </p>
                  
                  <p>
                    <input  type="checkbox" 
                            name ="chkPartecipazione" 
                            value="2" 
                            />&nbsp;Nessuna
                  </p>
                  <p>&nbsp;</p>
                  <p>&nbsp;</p>
                  <p>&nbsp;</p>
                  <p>&nbsp;</p>
              </fieldset>
          </td>
          
          <td rowspan=2 style="vertical-align:middle;">
              <!-- PULSANTI -->
              <p><input type="submit" id="btnElabora" value="Elabora" onclick="Elabora();"/></p>
              <p><input type="button" id="btnMailingList" value="Mailing List" disabled /></p>
              <p><input type="button" id="btnTornaMenu" value="Men&ugrave; SMS" onclick="Spedizione('menu');" /></p>
              
          </td>
      </tr>
     
     <tr>
          <td>
              <!-- GRUPPI -->
              <fieldset id="fldGruppo"><legend>Gruppo del... </legend>
                  <table cellpadding="5">
                      <tr>
                          <td><input type="checkbox" name ="chkGruppo" value="2">&nbsp;LUN</td>
                          <td><input type="checkbox" name ="chkGruppo" value="3">&nbsp;MAR</td>
                          <td><input type="checkbox" name ="chkGruppo" value="4">&nbsp;MER</td>
                      </tr> 
                      <tr>
                          <td><input type="checkbox" name ="chkGruppo" value="5">&nbsp;GIO</td>
                          <td><input type="checkbox" name ="chkGruppo" value="6">&nbsp;VEN</td>
                          <td><input type="checkbox" name ="chkGruppo" value="7">&nbsp;SAB</td>
                          <td><input type="checkbox" name ="chkGruppo" value="8">&nbsp;DOM</td>
                      </tr>  
                  </table>
                  
              </fieldset>
          </td>
          <td>
              <!-- TESSERAMENTI -->
              <fieldset id="fldTesseramenti"><legend>Tesseramenti... </legend>
                    <input  type="checkbox"
                            name ="chkTesseramento"
                            value ="1" 
                            checked />&nbsp;Tesserati
                  <p>
                    <input  type="checkbox" 
                            name ="chkTesseramento"
                            value ="0"  
                            />&nbsp;Non tesserati
                  </p>
              </fieldset>
          </td>
          
          <td>
              <!-- ALTRO -->
              <fieldset id="fldAltro"><legend> Altro... </legend>
                  <p>
                    <input type="checkbox" name ="chkAltro" value="coro" />&nbsp;Coro
                  </p> 
              </fieldset>
          </td>
     </tr>
  </table>
  </div>
  <?php
                      echo "<script>";
                      echo "document.getElementById('criteri_scelta').style.visibility='visible';";
                      echo "</script>";
    
    break;
  
    case 3:  // visualizza la lista di distribuzione filtrata dal db e permette di scrivere il testo dell'sms
  ?>
  
  <div id="invia_sms">
    <legend>Ricerca con:&nbsp; <?php $ricerca=ParametriRicerca($classi,$gruppi,$ruoli,$tesseramenti,$partecipazione,$coro); echo $ricerca; ?></legend>
    <table id="tbl_invia_sms">
    <tr>
      <td rowspan=7> <!-- costruzione lista trovati dalla ricerca parametrica -->
          <select name="lstListaTelefoni" 
                  id="lstListaTelefoni" 
                  size=19 
                  multiple 
                  onMouseUp="ContaSelezionati();"
                  onKeyUp="ContaSelezionati();" 
                  title="seleziona/deseleziona con CTRL+click">           
              <?php 
                      FiltraDati($classi,$gruppi,$ruoli,$tesseramenti,$partecipazione,$coro);
              ?>
          </select>
          
          <p>
            &nbsp;
            <span class="RisultatiTrovati">
                <?php 
                      $lista_totale==1 ? $trovati="&nbsp;Trovato 1 contatto&nbsp;" : $trovati="&nbsp;Trovati&nbsp;".$lista_totale."&nbsp;contatti&nbsp;";
                      echo $trovati; 
                ?>
            </span>
            &nbsp;&nbsp;
            <input  type="button"
                    id="btnDeseleziona" 
                    value="deseleziona tutto" 
                    onclick="DeselezionaLista();"
                    disabled 
            />            
          </p>
      </td>
    
      <td>
         <p>In lista per spedizione:</p>
      </td>            
       
      <td>      
            <p id="in_lista" class="nrStatistiche2"><?php  echo $lista_totale; ?></p>
      </td> 
      </tr>
      
      <tr>  
        <td>   
          <p>Status:&nbsp;</p>
        </td>
        <td>
          <p id="status_invio" style="background:red; color:white; font-weight:bold;padding-right:0.8em;-moz-border-radius:7px;text-align:right;">
              <?php 
                  if ($lista_totale==0) {
                      echo "Lista vuota";
                  } else {
                      echo "Invia a tutta la lista";
                  }
              ?>
          
          
          </p>
      </td>
    </tr>
    
    <tr>
        <td>
          <p>Max lunghezza messaggio:</p>
        </td>
        <td>
          <p class="nrStatistiche">
              <input type="textbox" 
                     id="max_caratteri" 
                     value="160 caratteri" 
                     class ="nrStatistiche"
                     disabled 
                     />
          </p>
        </td>
    </tr>
    
    <tr>
        <td>
          <p>Caratteri rimanenti:</p>
        </td>
        <td>
          <p>
              <input type="textbox" 
                     id="nr_caratteri_rimanenti"
                     value="160" 
                     class ="nrStatistiche"
                     disabled 
                     />
          </p>
        </td>
    </tr>
    
    <tr>
        <td>
          <p>Caratteri messaggio:</p>
        </td>
        <td>
          <p>
              <input type="textbox" 
                     id="nr_caratteri_sms" 
                     value="0" 
                     class="nrStatistiche"
                     disabled/>
          </p>
        </td>
    </tr>
    
    <tr>
        <td colspan="2">
          <textarea rows="4" 
                    cols="27" 
                    id="sms_text" 
                    name="sms_text" 
                    onKeyUp="ControllaNrCaratteri(this.value,'sms_text');"
                    title="testo colore rosso=testo troppo lungo o carattere non supportato dallo standard GSM   testo colore verde=testo ok per l'invio" 
                    <?php if ($lista_totale==0) echo "disabled" ?>
                    ></textarea>
        </td>
    </tr>
    
    <tr>
        <td colspan="2">
          <input type="submit" id ="btnSubmit" name="btnSubmit" value="Invia messaggio" onClick="Spedizione('riepilogo_sms')" disabled />
          <input type="button" id ="btnAnnulla" name="btnSubmit" value="Annulla spedizione" onClick="Spedizione('annulla_spedizione');" />
          
        </td>
    </tr>
    </table>
  </div>
  <?php 
                      echo "<script>";
                      echo "document.getElementById('invia_sms').style.visibility='visible';";
                      echo "var p=setInterval(\"CambiaColore()\",800)";
                      echo "</script>";
  break;
  
  case 4: // visualizza il riepilogo dei dati scelti e permette di inviare l'sms
  ?>
      <div id="riepilogo_sms">
          <?php  
              $_SESSION['access_level']>4 ? $rowspan="" : $rowspan="rowspan=2";
          ?>
          <legend>Riepilogo spedizione</legend>
          
          <table id="tblRiepilogo" cellpadding=15 border=0>
              <tr>
                  <th class="riepilogo">
                      Credito:                  
                  </th>
                  <td class="riepilogo2">
                      <?php 
                          if ($_POST['hdnCreditoMessaggi'] < sizeof($ListaDestinatari)) {
                              echo "<span style='background:red;color:white;padding-bottom:2px;'>&nbsp;insufficiente per completare la spedizione&nbsp;</span>";
                              
                          } else {
                              echo "<span style='background:green;color:white;padding:2px;'>&nbsp;sufficiente per completare la spedizione&nbsp;</span>";
                          }
                      ?>
                  </td>
                  
                  <th class="riepilogo" <?php echo $rowspan; ?>">
                      Mittente:
                  </th>
                  <td class="riepilogo2" <?php echo $rowspan; ?>">
                      <select id="from" name="from"> 
                            <?php 
                                // carica i mittenti in base all'associazione definita nell'array $mittente
                                $kGruppo=array_keys($mittente);
                                for ($i=0;$i<sizeof($kGruppo);$i++){
                                    if (!stristr($operatore,$kGruppo[$i])){
                                        $associazione='oratorio';
                                    } else {
                                        $associazione=$kGruppo[$i];
                                         break;
                                    }
                                }

                                while($cella=each($mittente[$associazione])) {
                                    // controlla che la lunghezza del mittente 
                                    // e i caratteri da utilizzare siano coerenti con le specifiche indicate da Mobyt
                                    if (ereg("^[\+0-9]+$",$cella['key']) && strlen($cella['key'])<17) { //numeri in formato internazionale
                                        $ok=true;
                                    } elseif (ereg("^[a-zA-Z0-9]+$",$cella['key']) && strlen($cella['key'])<12) { //alfanumerico
                                        $ok=true;
                                    } else {
                                        $ok=false;
                                    }

                                    if ($ok && $cella['value']){
                                        echo "<option name='optFrom' value='".$cella['key']."'>".$cella['key']."</option>\n";
                                    }
                                }
                                      
                            ?> 
                        </select>
                  </td>
              </tr>
              
              <tr> 
                  <th class="riepilogo">
                      In lista per spedizione:
                  </th>
                  
                  <td class="riepilogo2">
                      <span style="font-size:large;font-weight:bold;color:purple;">
                          <?php 
                                sizeof($ListaDestinatari)==1 ? $contatti=" contatto" : $contatti =" contatti";
                                echo sizeof($ListaDestinatari).$contatti;
                          ?>        
                      </span>
                  </td>
                  
                  <?php 
                          if ($_SESSION['access_level'] >4) {
                  ?>
                  <td colspan=2> <!-- INFO ACCOUNT, QUALITA' SMS, METODO TRASMISSIVO, NOTIFICHE-->
                      
                      <table cellpadding=5>
                          <tr>
                              <th class="riepilogo">
                                  Account:
                              </th>
                              <td class="riepilogo2">
                                  <?php echo $account; ?>
                              </td>
                          </tr>
                          
                          <tr>
                              <th class="riepilogo">
                                  Qualit&agrave;:
                              </th>
                              <td class="riepilogo2">
                                  <?php echo $quality; ?>
                              </td>
                          </tr>
                          
                          <tr>
                              <th class="riepilogo">
                                  Metodo Tx:
                              </th>
                              <td class="riepilogo2">
                                 <?php echo $metodo_tx; ?>
                              </td>
                          </tr>
                          
                          <tr>
                              <th class="riepilogo">
                                  Rx Notifiche:
                              </th>
                              <td class="riepilogo2">
                                 <?php $abilita_notifiche==true ? $a="S&igrave;" : $a="No";
                                        echo $a;
                                 ?>  
                              </td>
                          </tr>
                      </table>
                  <?php 
                      } 
                  ?>
                  </td>
              </tr>
              
              <tr>
                  <th class="riepilogo">
                      Testo da inviare:
                  </th>
                  
                  <td colspan=0 class="riepilogo2">
                      <textarea id="sms_text2" 
                                name="sms_text2"
                                onKeyUp="ControllaNrCaratteri(this.value,'sms_text2');"
                                title="testo colore rosso=testo troppo lungo o carattere non supportato dallo standard GSM   testo colore verde=testo ok per l'invio" 
                                disabled
                      ><?php echo $_POST['sms_text']; ?></textarea>
                      
                  </td>
                  <th colspan=2>
                      <input type="button"
                             id="btnModificaTesto" 
                             value="Modifica testo" 
                             onClick="ModificaTesto();"
                             <?php 
                                if ($_POST['hdnCreditoMessaggi'] < sizeof($ListaDestinatari)) {
                                    echo "disabled";
                                }
                             ?>
                             />
                      
                      <p><input type="button" 
                                id="btnInviaGateway" 
                                value="Invia"
                                onClick="Spedizione('togateway');"
                                <?php 
                                    if ($_POST['hdnCreditoMessaggi'] < sizeof($ListaDestinatari)) {
                                        echo "disabled";
                                    }
                                ?> 
                                />
                      </p>
                      
                      <p><input type="button" 
                                id="btnAnnullaSpedizioneRiepilogo" 
                                value="Annulla spedizione" 
                                onClick="Spedizione();"
                                />
                      </p>
                  </th>
              </tr>
          </table>
      </div>  
  <?php
                      echo "<script>";
                      echo "document.getElementById('riepilogo_sms').style.visibility='visible';";
                      echo "</script>";
    
    break;
    
    case 5:
   
  ?>
  <div id="risposta_gtw">
      <table id="esito_spedizione" width=100% height=400px border=0 cellpadding=3>
          <tr>
              <td class="esito">         
                  <b>Esito spedizione:</b>
              </td>
              <td class="esito"> 
                  <?php
                      // verifica se ci sono stati errori nel prendere in carico la spedizione
                      if (substr($result,0,2)=='OK') {
                          echo "<span style='background:green;color:white;padding:2px;-moz-border-radius:7px;'>&nbsp;Presa in carico correttamente&nbsp;</span>";
                      } else {
                          echo "<span style='background:red;color:white;padding:2px;-moz-border-radius:7px;'>&nbsp;Non presa in carico per un errore durante il trasferimento dei dati&nbsp;</span>";
                    }
                ?>
              </td>
              
              <td rowspan=6 style="text-align:center;">
                  
                  <legend>Notifiche ricezione</legend><br>
                  <?php
                      if (!$abilita_notifiche) {
                          echo "<b>&nbsp;Non abilitate</b>";
                      } else {
                  ?>
                  <select size=20>
                      <?php 
                      if ($abilita_notifiche) {
                          foreach (explode("\n", $result) as $key => $value) {
	                           if (substr($value, 0, 2) == 'OK') {
		                              echo '<option><span style="color:green;">'./*$ListaDestinatari[$key]*/$value.' OK</span></option>';
	                            }
	                                else {
		                                  echo '<option><span style="color:red;">'./*$ListaDestinatari[$key]*/$value.' KO</span></option>';
	                               }
                              } 
                     }
                     }
                     ?>
                  </select>
              </td>
              
          </tr>
          
          <tr>
              <td class="esito">
                  <b>Messaggi in lista:</b>
              </td>
              <td class="esito">    
                  <span style="font-size:large;background:purple;color:white;padding:2px;-moz-border-radius:7px;"><?php echo sizeof($ListaDestinatari);?></span>
              </td>
          </tr>
          
          <tr>
              <td class="esito">
                  <b>Credito residuo:</b>
              </td>
             <td class="esito">
                  <?php 
                                            // ottiene il credito (espresso in euro) ancora disponibile
                                            $residuo_credito=($sms->getCredit()/10000);

                                            if ($residuo_credito<1 || $residuo_credito==null) {
                                                $classe="NoCredit";
                                                $residuo_credito==null ? $residuo_credito="Non disponibile" : $residuo_credito=0;
                                            } elseif ($residuo_credito>0 && $residuo_credito<50) {
                                                $classe="EndingCredit";
                                            } else {
                                                $classe="OkCredit";
                                            }
                                            
                                            //sostituisce il punto per mettere la virgola italiana per i decimali
                                            $residuo_credito=str_replace(".",",",$residuo_credito);
                                            
                                            //stampa il credito residuo
                                            echo "<span class='".$classe."'>".$residuo_credito."</span>";
                ?>
              </td>
          </tr>
          <tr>
              <td class="esito">
                  <b>Messaggi disponibili:</b>
              </td>
               <td class="esito">                 
                                        <?php 
                                            // ottiene il numero (approssimativo) degli sms disponibili
                                            $residuo_messaggi=$sms->getAvailableSms();
                                            $_POST['hdnCreditoMessaggi']=$residuo_messaggi;
                                            if ($residuo_messaggi <1 || $residuo_messaggi==null) {
                                                $classe="NoCredit";
                                                $residuo_messaggi==null ? $residuo_messaggi="Non disponibile" : $residuo_messaggi=0;
                                            } elseif ($residuo_messaggi>0 && $residuo_messaggi<50) {
                                                $classe="EndingCredit";
                                            } else {
                                                $classe="OkCredit";
                                            }
                                
                                            echo "<span class='".$classe."'>".$residuo_messaggi."</span>";
                                        ?>
              </td>
          </tr>
          <tr>
              <td class="esito">
                  <b>Notifiche:</b>
              </td>
               <td class="esito">
                                        <?php 
                                            // ottiene il numero di notifiche ancora disponibili
                                            $residuo_notifiche=$sms->getAvailableNotifies();
                                            if ($residuo_notifiche<1 || $residuo_notifiche==null) {
                                                $classe="NoCredit";
                                                $residuo_notifiche==null ? $residuo_notifiche="Non disponibile" : $residuo_notifiche=0;
                                            } elseif ($residuo_notifiche>0 && $residuo_notifiche<50) {
                                                $classe="EndingCredit";
                                            } else {
                                                $classe="OkCredit";
                                            }
                                
                                            echo "<span class='".$classe."'>".$residuo_notifiche."</span>";
                                        ?>
                </td>
          </tr>
          <tr>
              <td style="vertical-align:middle;text-align:center" colspan=2>
                  <input type="button" id="btnEsitoTornaMenu" value="Torna al men&ugrave;" onClick="Spedizione('menu');">
              </td>
          </tr>
      </table>
  </div>
      
      <?php
                      echo "<script>";
                      echo "document.getElementById('risposta_gtw').style.visibility='visible';";
                      echo "</script>";
    break;
    }
  ?>
</form>

<!-- FINE COSTRUZIONE ELEMENTI PAGINA -->
  
</body>
</html>
