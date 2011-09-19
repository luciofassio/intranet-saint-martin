<?php
session_start();

require('accesso_db.inc');
require("funzioni_generali.inc");
require ("get_data.inc");

$host  = $_SERVER['HTTP_HOST'];
// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}

// Identifica l'operatore che si è loggato
$idoperatore = $_SESSION['authenticated_user_id'];

// Rileva il tipo di sacramento scelto dall'operatore
$sacramento=$_GET["scr"];

switch ($sacramento) {
    case 1:
        $title="Gestione Comunioni";
    break;
    
    case 2:
        $title="Gestione Cresime";
    break;
}
ConnettiDB();

require ("f_sacramenti.inc");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Oratorio Saint-Martin / <?php echo $title; ?> </title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    
  <?php SelectCSS("struttura_pagina"); ?>   

<script type="text/javascript" src="./js/f_sacramenti.js"></script>
<script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>

<script type="text/javascript" >
  

</script>

<style>
p {
   line-height:180%;
}

p.ricerca {
  line-height:290%;
}

blockquote.myscheda label.schedadati{
  position:relative;
  width:165px;
  left:-30px;
  display: block;
  float:left;
}

blockquote.ricerca_iscritti label.RicercaIscritti{
  position:relative;
  width:70px;
  left:-40px;
  display: block;
  float:left;
}

blockquote.divgruppi label.div_gruppi{
  position:relative;
  width:70px;
  left:-40px;
  display: block;
  float:left;
}

#scheda{
  visibility: hidden;
  position:absolute; 
  width:950px;
  height:400px;
  /*height:230px;
  top:200px;*/
  top:80px;
  left:25px;
  background:#CCCC99;
  padding:0.4em;
  -moz-border-radius:4px;
  border-right:1px solid black;
  border-bottom:1px solid black;
}

#titolo_scheda{
  position:absolute;
  /*top:163px;*/
  /*top:80px;*/
  top:0px;
  left:0px;
  background:#CCFF99;
  padding:0.6em;
  font-weight:bold;
  font-variant:small-caps;
  font-size:12pt;
  -moz-border-radius:4px;
}

#dati_ragazzo{
  width:54%;
  position:relative;
  top:40px;
}

#dati_padrino{
  width:54%;
  position:relative;
  top:50px;
}

#altri_dati{
  position:absolute;
  width:44.5%;
  top:5px;
  left:528px;
}

#pulsantiera{
  position:absolute;
  top:360px;
  left:600px;
}

#data_battesimo {
  width:80px;
}

#parrocchia_battesimo{
  width:235px;
}

#indirizzo_parrocchia_battesimo{
  width:235px;
}

#parrocchia_padrino{
  width:235px;
}

#nome_padrino{
  width:235px;
}

#gruppo{
  font-weight:bold;
  width:150px;
}

#data_iscrizione{
  width:80px;
}

.candidato {
  border:1px dotted orange;
  font-weight:bold;
  color: green;
 }
 
.padrino {
  border:1px dotted orange;
  font-weight:bold;
  color: purple;
 }
 
.altridati {
  border:1px dotted orange;
  font-weight:bold;
  color: brown;
}

#note {
  width:98%;
  height:130px;
  border:1px dotted orange;
  padding:5px;
  font-family:Verdana;
  font-size:small;
}
/*********************** REGOLE IMPAGINAZIONE CAMPI DI RICERCA ISCRITTI ******************/
#campi_ricerca {
  visibility: visible;
  position: absolute;
  width: 70%;
  height: 420px;
  top: 12%;
  left:160px;
  /*left: 120px;*/
}

fieldset.cornice {
  height: 190px;
  width: 300px;
  border: 1px dotted #FF8C00;
  -moz-border-radius: 7px;
  padding-right:2.5em;
 }

#fldGruppi{
  border:1px dotted #FF8C00;
}

#fldStampe{
  border:1px dotted #FF8C00;
  /*position:absolute;
  top:0%;
  left:46%;*/
  height:397px;
}

#etichettacognome {
  /*position: relative; */
 /* top:50%;*/
  height:25px;
  width:100%;
  /*text-align:right;*/
}

#etichettanome {
  /*position: absolute;
  /*top:130%;/* -20px;*/
  width:100%;
  /*left: 250px;*/
  height: 25px;
  /*text-align:right;*/
}
 
 #barcode {
  /*position: absolute;
  /*top:215%;*/
  /*left: 450px;*/
  height: 25px;
  width:100%;
  /*text-align:right;*/
}
 
 #caricaPersona {
  height: 25px;
  width:60%;
  visibility:hidden;
 }
 
 #nuovoiscritto{
  position: relative;
  top: 20%;
  left:15%;
  /*left: 690px;*/
 }
 
 #newentry {
  height: 30px;
  width:70%;
  /*width: 110px;*/
 }
 
 .buttonCerca{
    height:40px;
    width:105%;
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
	
.suggestionsBoxParrocchiePadrino {
		font-family: Helvetica;
		font-size: 12px;
		color: #000000;
		position: absolute;
    top:-250px;
    left: 390px;
		margin: 10px 0px 0px 0px;
		width: 200px;
		height:400px;
    background-color: #FFFFFF;
		-moz-border-radius: 7px;
		/*-webkit-border-radius: 7px;*/
		border: 2px solid purple;	
		color: #000000;
		z-index:10;
		text-align:left;
	}
  	
.suggestionsBoxParrocchie {
		font-family: Helvetica;
		font-size: 12px;
		color: #000000;
		position: absolute;
    left: 190px;
		margin: 10px 0px 0px 0px;
		width: 200px;
		background-color: #FFFFFF;
		-moz-border-radius: 7px;
		/*-webkit-border-radius: 7px;*/
		border: 2px solid green;	
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
 
/*****************************************************************************************/
/* REGOLE PER IMPAGINAZIONE GRUPPI                                                       */
#gruppi {
  visibility:hidden;
  position: absolute;
  width: 50%;
  height: 350px;
  top: 13%;
  left:200px;
  border:2px solid green;
  background:#CCCC99; 
  -moz-border-radius:7px;
}

#titolo_gruppi {
  position:relative;
  background:green;
  color:white;
  font-variant:small-caps;
  font-size:large;
  padding:0.2em;
  padding-bottom:0.4em;
}

#dati_gruppi{
  position:relative;
  top:0%;
  left:1%;
  width:50%;
}

#archivio_gruppi{
  position:absolute;
  top:0%;
  left:102%;
  width:280px;
}

#lista_gruppi {
  width:275px;
  height:280px;
}

#pulsantiera_gruppi{
  position:relative;
  top:15px;
  left:15px;
}

#data_gruppo {
  width:60%;
  font-weight:bold;
}

#ora_gruppo {
  width:60%;
  font-weight:bold;
}
/* REGOLE PER IMPAGINARE LA FINESTRA SCEGLI IL GRUPPO DA STAMPARE*/
#stampa_documenti {
  visibility:hidden;
  position: absolute;
  width: 40%;
  height: 100px;
  top: 20%;
  left:280px;
  border:2px solid green;
  background:#CCCC99; 
  -moz-border-radius:7px;
}

#titolo_stampa_documenti {
  position:relative;
  background:green;
  color:white;
  font-variant:small-caps;
  font-size:large;
  padding:0.2em;
  padding-bottom:0.4em;
}

#campi_stampa_documenti{
  position:relative;
  top:10%;
  left:2%
  
}
/* ****************************************************************************************/
</style>

</head>

<body onload="CaricamentoPagina('<?php echo $_POST["sezione"]; ?>')">
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
     ?>
     <div id="myoperatore">
          | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
    
<!-- FINE SEZIONE OPERATORE CONNESSO -->

<!-- SEZIONE CAMPI DI RICERCA ISCRITTI -->   
    <div id="campi_ricerca">    
        <form id="CercaIscritti" name="CercaIscritti" method ="post" action ="xsacramenti.php?scr=<?php echo $sacramento; ?>">
            <input type="hidden" name="postback" value="true">
            <input type="hidden" name="azione" id="azione" value="" />
            <input type="hidden" name="hdnID" id="hdnID" value="<?php echo ($_POST['hdnID']);?>" />
            <input type="hidden" name="hdnIdParrocchia" id="hdnIdParrocchia" value="<?php echo ($_POST['hdnIdParrocchia']);?>" /> 
            <input type="hidden" name="trovati" id="trovati" value="<?php echo ($_POST['trovati']);?>" />
            <input type="hidden" name="sezione" id="sezione" value="<?php echo ($_POST['sezione']);?>" />
            
            <table>
            <tr>
                <td>
            <fieldset class="cornice"><legend>Cerca iscritto... &nbsp;</legend>
                <blockquote class="ricerca_iscritti">
                <div id="etichettacognome">
                    <p class="ricerca">
                    <label for="txtCognome" class="RicercaIscritti"><strong>Cognome</strong></label>
                    <input type="text"
                           style="border: 1px dotted grey;"
                           name="txtCognome" id="txtCognome"
                           onkeyup="lookup(this.value);"
                           onblur="fill();"
                           onfocus="ResetCampo('txtCognome','#FAF176')"
                           onkeypress="RilevaTab(event,'');"
                           autocomplete="off" 
                           size ="15"
                    />

                    <div class="suggestionsBox" id="suggestions" style="display: none;">
                       <img src="./Immagini/upArrow.png" style="position: relative; top: -15px; left: 50px;" alt="" />
									     <div class="suggestionList" id="autoSuggestionsList">
										      &nbsp;
									     </div>
								    </div>
                    </p>
                </div>
              
                <div id ="etichettanome">
                <p class="ricerca">
                   <label for="txtNome" class="RicercaIscritti"><strong>Nome</strong></label>
                    <input type="text"
                           style="border: 1px dotted grey;"
                           name="txtNome" id="txtNome"
                           onkeyup="lookup_names(this.value);"
                           onfocus="ResetCampo('txtNome','#FAF176');"
                           onblur="fill_names();"
                           onkeypress="RilevaTab(event,'');"
                           autocomplete="off"
                           size ="15"
                    />
                    
                    <div class="suggestionsBoxNames" id="suggestions_names" style="display: none;">
									     <img src="./Immagini/upArrow.png" style="position: relative; top: -15px; left: 50px;" alt="" />
									     <div class="suggestionList" id="autoSuggestionsListNames">
										      &nbsp;
									     </div>
								    </div>
                </p>
                </div>       
                    
            <div id ="barcode">  
                <p class="ricerca">
                    <label for="txtBarCode" class="RicercaIscritti"><strong>C. Barre</strong></label>
                    <input type="text"
                           style="border: 1px dotted grey;"
                           name="txtBarCode"
                           id="txtBarCode"
                           autocomplete="off"
                           onfocus="ResetCampo('txtBarCode','#FAF176');"
                           onblur="ControlloInput('txtBarCode',false);"
                           onkeypress="RilevaTab(event,'txtBarCode');"
                           size ="15"
                    />
	               </p> 
                
                <p class="ricerca" style="text-align:center">
                    <input type="button" name="caricaPersona" id="caricaPersona" value="cerca" onClick="btnCercaIscritti();" disabled />
                </p>
            </div>
            </blockquote>
            </fieldset>
        
          <fieldset class="cornice" id="fldGruppi"><legend>Gestione gruppi... &nbsp;</legend>
              <div id="gestione_gruppi">
                  <p class="ricerca">
                      <input type="button"
                             class="buttonCerca" 
                             value="Crea/Modifica"
                             onclick="CaricamentoPagina('gruppi');" />
                  </p>
              </div>
          </fieldset>
        </td> 
        
        <td>
        <fieldset class="cornice" id="fldStampe">
            <legend>Stampa... &nbsp;</legend>
            
            <p class="ricerca">
                <input type="button"
                       class="buttonCerca"
                       value ="Modulo d'iscrizione precompilato"
                />
            </p>
            
            <p class="ricerca">
                <input type="button" 
                       class="buttonCerca"
                       value ="Modulo d'iscrizione vuoto"
                />
            </p>
            
             <p class="ricerca">
                <strong>Data Restituzione</strong>
                <input type="text" 
                       style="border:1px dotted grey;"
                       name="txtDataRestituzione"
                       id="txtDataRestituzione"
                       onfocus="ResetCampo('txtDataRestituzione','#FAF176');"
                       onblur="ControlloInput('txtDataRestituzione',false);"
                       size ="10"
                />
             </p>
             
             <p class="ricerca">
                <hr>
             </p>
             
             <p class="ricerca"> 
                <input type="button" 
                        class="buttonCerca" 
                        value ="Elenco candidati/documenti mancanti"
                        onclick="CaricamentoPagina('elenco');"
                />
            </p>
             
            <p class="ricerca">
                <input type="button"
                       class="buttonCerca"
                       value ="Notifiche alle parrocchie"
                />
            </p>
            
            <p class="ricerca">
                <hr>
             </p>
           
           <p class="ricerca">
                <input type="button" 
                       class="buttonCerca"
                       value ="Tabulati archivio"
                />
            </p>
        </fieldset>
       </td>
       </tr>
       </table>
    </div>
 <!-- FINE SEZIONE CAMPI DI RICERCA ISCRITTI -->   

<!-- SEZIONE SCHEDA DATI -->
<div id="scheda">
    <div id="titolo_scheda">
        Scheda di 
        
        <?php 
              // stampa il nome e il cognome del candidato
              echo $_POST["txtNome"]."&nbsp;".$_POST["txtCognome"];
              
              // mette una linguetta rossa se è una scheda nuova, una blu se è già stata registrata
              if ($_POST["trovati"]==0) {
                  echo "<script>";
                  echo "document.getElementById('titolo_scheda').style.borderTop='6px solid red'";
                  echo "</script>";
              } else {
                  echo "<script>";
                  echo "document.getElementById('titolo_scheda').style.borderTop='6px solid blue'";
                  echo "</script>";
              }
        ?>
    </div>

    <div id="dati_ragazzo">
        <fieldset><legend>Dati cresimando... &nbsp;</legend>
          <blockquote class="myscheda">
          <p>
              <label class="schedadati"><strong>Data Battesimo </strong></label>
              <input type="text" 
                      name="data_battesimo"
                      id="data_battesimo"
                      class="candidato"
                      autocomplete="off"
                      onfocus="ResetCampo('data_battesimo','#CCFF99');"
                      onblur="ControlloDataInserita(this.value,'data_battesimo','dn','green','white');"
                      onkeypress="RilevaTab(event);"
                      value="<?php echo $_POST["data_battesimo"]?>" />
          </p>
          <p">
              <label class="schedadati"><strong>Parrocchia Battesimo </strong></label>
              <input type="text"
                      name="parrocchia_battesimo" 
                      id="parrocchia_battesimo"
                      class="candidato"
                      autocomplete="off"
                      onfocus="ResetCampo('parrocchia_battesimo','#CCFF99');"
                      onkeyup="lookup_parrocchie(this.value);"
                      onblur="FiltroStringa(this.value,'parrocchia_battesimo','green');" 
                      onkeypress="RilevaTab(event);"
                      value="<?php echo $_POST["parrocchia_battesimo"]?>" />
              
              <div class="suggestionsBoxParrocchie" id="suggestions_parrocchie" style="display: none;">
                <img src="./Immagini/upArrow.png" style="position: relative; top: -15px; left: 50px;" alt="" />
									<div class="suggestionList" id="autoSuggestionsListParrocchie">
									   &nbsp;
									</div>
							</div>
          </p>
          <p>
              <label class="schedadati"><strong>Indirizzo Parrocchia</strong></label>
              <input type="text"
                     name="indirizzo_parrocchia_battesimo"
                     id="indirizzo_parrocchia_battesimo"
                     class="candidato" autocomplete="off"
                     onfocus="ResetCampo('indirizzo_parrocchia_battesimo','#CCFF99');"
                     onblur="FiltroStringa(this.value,'indirizzo_parrocchia_battesimo','green');"
                     value="<?php echo $_POST["indirizzo_parrocchia_battesimo"]?>" />
          </p>
          
          <p>
              <label id="checkBattesimo" class="schedadati" title="da spuntare se &egrave; stato consegnato il certificato di battesimo">
              <strong>Certificato Battesimo</strong></label>
              <input type="radio"
                     name="optBattesimo"
                     value="1" 
              <?php 
                  if ($_POST["optBattesimo"]==1){
                      echo "checked";
                  }
              ?> />CONS
              
              &nbsp;
              <input type="radio"
                     name="optBattesimo"
                     value="2" 
              <?php 
                  if ($_POST["optBattesimo"]==2|| $_POST["optBattesimo"]==0) {
                      echo "checked";
                  }
              ?> />NON CONS
              
              &nbsp;
              <input type="radio"
                     name="optBattesimo"
                     value="3"
                     onclick="AssegnaBattesimo('opt');"
              <?php 
                  if ($_POST["optBattesimo"]==3){
                      echo "checked";
                  }
              ?> />PARR
          </p>

         </blockquote>
        </fieldset>
    </div>
    
    <div id="dati_padrino">
        <fieldset><legend>Dati padrino/madrina... &nbsp;</legend>
          <blockquote class="myscheda">
          <p>
              <label class="schedadati"><strong>Nome & Cognome </strong></label>
              <input type="text"
                     name="nome_padrino"
                     id="nome_padrino"
                     class="padrino"
                     autocomplete="off"
                     onfocus="ResetCampo('nome_padrino','#CCFF99');"
                     onblur="FiltroStringa(this.value,'nome_padrino','purple');"
                     value="<?php echo $_POST["nome_padrino"]?>"
                     onBlur="FiltroStringa(this.value,'nome_padrino');"
                     onkeypress="RilevaTab(event);"  
              />
          </p>
          <p>
              <label class="schedadati"><strong>Parrocchia </strong></label>
              <input type="text"
                      name="parrocchia_padrino"
                      id="parrocchia_padrino"
                      class="padrino" 
                      autocomplete="off"
                      onfocus="ResetCampo('parrocchia_padrino','#CCFF99');" 
                      onblur="fill_parrocchia_padrino();FiltroStringa(this.value,'parrocchia_padrino','purple');"
                      onkeyup="lookup_parrocchie(this.value,'parrocchia_padrino');"
                      onkeypress="RilevaTab(event);" 
                      value="<?php echo $_POST["parrocchia_padrino"]?>" />
          
              <div class="suggestionsBoxParrocchiePadrino" id="suggestions_parrocchie_padrino" style="display: none;">
               <img src="./Immagini/leftArrow.png" style="position: relative; top: 310px; left:-12px;" alt="" /> 
									<div class="suggestionList" id="autoSuggestionsListParrocchiePadrino">
									   &nbsp;
									</div>
							</div>
           </p>   
          <p>
              <label class="schedadati" title="da spuntare se il certificato di idoneit&agrave; &egrave; stato consegnato"><strong>Certificato di idoneit&agrave; </strong></label>
              <input type="checkbox" name="chkCI" id="chkCI"
              <?php 
                    if ($_POST["chkCI"]){
                        echo "checked='checked'";
                    }
              ?> />
          </p>
          </blockquote>
        </fieldset>
    </div>
    
    <div id="altri_dati">
        <fieldset><legend>Altri dati... &nbsp;</legend>
          <blockquote class="myscheda">
          <p>
              <label class="schedadati"><strong>Gruppo del </strong></label>
              <select class="altridati"
                      name="gruppo"
                      id="gruppo"
                      onfocus="ResetCampo('gruppo','#CCFF99');"
                      onblur="FiltroStringa(this.value,'gruppo','brown');">
                      <?php GetDataSacramento('scheda');?>
              </select>
          </p>
          <p>
              <label class="schedadati"><strong>Contributo versato</strong></label>
              <input type="checkbox" name="chkContributo" id="chkContributo"
              <?php 
                    if ($_POST["chkContributo"]){
                        echo "checked='checked'";
                    }
              ?> />
          </p>
          <p>
              <label class="schedadati"><strong>Iscrizione gratuita</strong></label>
              <input type="checkbox" name="chkIscrizione" id="chkIscrizione"
              <?php 
                    if ($_POST["chkIscrizione"]){
                        echo "checked='checked'";
                    }
              ?> />
          </p>
          
          <p>
              <label class="schedadati"><strong>Data iscrizione</strong></label>
              <input type="text" name="data_iscrizione" id="data_iscrizione" class="altridati" value="<?php 
                                  if (isset($_POST["data_iscrizione"])) {
                                      echo $_POST["data_iscrizione"];
                                  } else {
                                      echo date('d/m/Y');
                                  }
                            ?>" 
                            autocomplete="off"
                            onfocus="ResetCampo('data_iscrizione','#CCFF99');" 
                            onBlur="ControlloDataInserita(this.value,'data_iscrizione','dt','brown','white');" />
          </p>
         </blockquote>
          
          <strong>&nbsp;&nbsp;&nbsp;Note</strong>
          <textarea name="note" id="note" onkeyup="ContaCaratteri('note',this.value,200);"><?php echo $_POST["note"];?></textarea>
        
        </fieldset>
    </div>
     
    <div id="pulsantiera">
        <input type="button" style="height:40px;width:110px" value="Salva" onClick="AzioniPulsanti('salva');" />
        <?php  // costruisce la pulsantiera in base alle diverse situazioni (scheda già registrata, scheda nuova...) 
            if ($_POST["trovati"]>0) { //impedisce di eliminare una scheda... non ancora esistente!
                echo "&nbsp;";
                echo "<input type=\"button\" style=\"height:40px;width:110px\" value=\"Elimina\" onClick=\"AzioniPulsanti('elimina');\" />";
                echo "&nbsp;";
                echo "<input type=\"button\" style=\"height:40px;width:110px\" value=\"Chiudi\" onClick=\"AzioniPulsanti('chiudi');\" />";
            } else {
                echo "&nbsp;";
                echo "<input type=\"button\" style=\"height:40px;width:110px;\" value=\"Annulla\" onClick=\"AzioniPulsanti('chiudi');\" />";
                echo "<script>";
                echo "document.getElementById('pulsantiera').style.left='720px'";
                echo "</script>";
            }
        ?>
    </div>
</div>

<!-- FINE SEZIONE SCHEDA DATI -->

<!-- SEZIONE GRUPPI -->
<div id="gruppi">
    <div id="titolo_gruppi">
        Gruppi
    </div>
    
    <div id="dati_gruppi">
         <fieldset>
              <legend>Data & ora gruppo... &nbsp;</legend>
              <blockquote class="divgruppi">
                  <p>
                      <label class="div_gruppi"><strong>Data</strong></label>
                      <input type="text"
                             name="data_gruppo"
                             id="data_gruppo"
                             style="border:1px dotted orange;" 
                             onfocus="ResetCampo('data_gruppo','#CCFF99');"
                             onblur="ControlloDataInserita(this.value,'data_gruppo','dt','purple','white');"
                             autocomplete="off"
                             maxlength="10"
                      />
                  </p>
                  
                  <p>
                      <label class="div_gruppi"><strong>Ora</strong></label>
                      <input type="text"
                             name="ora_gruppo"
                             id="ora_gruppo"
                             style="border:1px dotted orange;" 
                             onfocus="ResetCampo('ora_gruppo','#CCFF99');"
                             onblur="ControlloOraInserita(this.value,'ora_gruppo','purple','white');"
                             autocomplete="off"
                             maxlength="5"
                      />
                  </p>
                  
                  <p>
                      &nbsp;
                  </p>
                  
                  <p>
                      &nbsp;
                  </p>
              </blockquote>
         </fieldset>
        
        <fieldset id="archivio_gruppi">
            <legend>Gruppi in archivio...&nbsp; </legend>
            <select id="lista_gruppi"
                    name="lista_gruppi" 
                    size="30"
                    style="color:brown;font-weight:bold;"
            >
                <?php 
                    GetDataSacramento('gruppi');
                ?>
            </select>
        </fieldset>
        
        <div id="pulsantiera_gruppi">
            <p>
            <input type="button"
                   id="agruppo"
                   style="height:40px;width:140px"
                   value="Aggiungi"
                   onclick="ElaboraGruppi(this.value);" 
            />
            
            &nbsp;       
            <input type="button"
                   id="mgruppo"
                   style="height:40px;width:140px"
                   value="Modifica"
                   onclick="ElaboraGruppi(this.value);" 
            />
            </p>        
            <p>            
            <input type="button"
                    id="rgruppo"
                   style="height:40px;width:140px"
                   value="Rimuovi"
                   onclick="ElaboraGruppi(this.value);"
            />
                   
            &nbsp;
            <input type="button"
                   style="height:40px;width:140px"
                   value="Chiudi" 
                   onclick="AzioniPulsanti('chiudi_gruppi');"
            />
            </p>
        </div>

    </div>
</div>
<!-- FINE GRUPPI -->
<!-- FINSTRA SCEGLI GRUPPO PER STAMPE -->
<div id="stampa_documenti">
    <div id="titolo_stampa_documenti">
        Scegli il gruppo...
    </div>
    
    <div id="campi_stampa_documenti">
        <label class="schedadati"><strong>Gruppo del </strong></label>
            <select class="altridati"
                    name="stampa_gruppo"
                    id="gruppo"
                    onfocus="ResetCampo('gruppo','#CCFF99');"
                    onblur="FiltroStringa(this.value,'gruppo','brown');">
                    <?php GetDataSacramento('scheda'); ?>
            </select>
            &nbsp;
            <input type="button" 
                    value="Ok"
                    style="height:40px;width:110px"
                    onclick="AzioniPulsanti('elenco')";
            />
            &nbsp;
            <input type="button" 
                    value="Annulla"
                    style="height:40px;width:110px"
                    onclick="AzioniPulsanti('chiudi_gruppi')";
            />
    </div>
</div>

 </form>
</body>
</html>
