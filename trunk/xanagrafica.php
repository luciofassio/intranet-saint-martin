<?php 
session_start();

require('accesso_db.inc');   
require("funzioni_generali.inc");
require ("funzioni_anagrafica.inc");

$host  = $_SERVER['HTTP_HOST'];

// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}

$idoperatore = $_SESSION['authenticated_user_id'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Gestione Oratorio / Anagrafica</title>
   <!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  <script type="text/javascript" src="./js/f_anagrafica.js"></script>
  <script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>
</head>

<body onload="CaricamentoTab()">

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
            | sei in: <strong>anagrafica oratorio</strong> |
        </div>
        
<!-- FINE SEZIONE INTESTAZIONE -->
    
<!-- SEZIONE BARRA DI NAVIGAZIONE -->
    <?php
        $barra_di_navigazione="| <a href='homepage.php'>home page</a> | <a href='inserisci_prenotazioni.php'>iscrizioni &amp; prenotazioni Er</a> |";
        
        if ($_SESSION['access_level'] >2) {
            $barra_di_navigazione.=" <a href='xcestino.php'>visualizza cestino</a> |";
        }
        
        echo "<div id='mybarranavigazione'> \n";
        echo "$barra_di_navigazione"."\n";
        echo "</div> \n"; 
     ?>
<!-- FINE SEZIONE BARRA DI NAVIGAZIONE -->

<!-- SEZIONE OPERATORE CONNESSO -->
      <div id="myoperatore">
        <?php
            $result = GetOperatore($idoperatore); // legge nome e cognome dell'operatore in base al suo ID
            $row = mysql_fetch_object($result);
            $_POST["login"]= htmlentities($row->login);
        ?>
        | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
    
<!-- FINE SEZIONE OPERATORE CONNESSO -->

<!-- SEZIONE CAMPI DI RICERCA ISCRITTI -->   
    <div id="campi_ricerca">    
        <form id="CercaIscritti" name="CercaIscritti" method ="post" action ="xanagrafica.php">
            <input type="hidden" name="postback" value="true">
            <input type="hidden" name="tab_attivo" id="mycercaiscritto" value="0" />
            
            
            <fieldset class="cornice"><legend>Cerca iscritto... &nbsp;</legend>
                <div id="etichettacognome">
                    <input type="hidden" name="hdnID" id="hdnID" value="<?php echo ($_POST['hdnID']);?>" />
                    
                    <label for="txtCognome"><strong>Cognome</strong></label>
                    <input type="text" style="border: 1px dotted grey;" name="txtCognome" id="txtCognome" onkeyup="lookup(this.value);" onblur="fill();" onfocus="ResetCampoCognome()" onkeypress="RilevaTab(event);" autocomplete="off" />
                    &nbsp;
								
                    <div class="suggestionsBox" id="suggestions" style="display: none;">
									     <img src="./Immagini/upArrow.png" style="position: relative; top: -12px; left: 50px;" alt="" />
									     <div class="suggestionList" id="autoSuggestionsList">
										      &nbsp;
									     </div>
								    </div>
               </div>    
                
              <div id ="etichettanome">    
                    <label for="txtNome"><strong>Nome</strong></label>
                    <input type="text" style="border: 1px dotted grey;" name="txtNome" id="txtNome" onkeyup="lookup_names(this.value);" onfocus="ResetCampoNome();" onblur="fill_names();" onkeypress="RilevaTab(event);" autocomplete="off" />
                    
                    <div class="suggestionsBox" id="suggestions_names" style="display: none;">
									     <img src="./Immagini/upArrow.png" style="position: relative; top: -12px; left: 50px;" alt="" />
									     <div class="suggestionList" id="autoSuggestionsListNames">
										      &nbsp;
									     </div>
								    </div>
              </div>      
                    
              <div id ="barcode">      
                    &nbsp;
                    <label for="txtBarCode"><strong>Cod. a barre</strong></label>
                    <input type="text" style="border: 1px dotted grey;" name="txtBarCode" id="txtBarCode" autocomplete="off" onfocus="ResetCampoBarCode();" onblur="ControlloInputCodiceBarre();" onKeyPress="InvioBarCode(event);"/>
	                &nbsp;
                    <input type="button" name="caricaPersona" id="caricaPersona" value="cerca" onClick="btnCercaIscritti();" disabled />
              </div>
            
            </fieldset>
        </form>
          
        <div id="nuovoiscritto">
            <input type="button" name="newentry" id="newentry" value="nuova iscrizione" onClick="btnNuovoIscritto();" />
        </div>       

    </div>
 <!-- FINE SEZIONE CAMPI DI RICERCA ISCRITTI -->   

<!-- SEZIONE SEZIONE TABELLA FUNZIONI ANAGRAFICA -->       
    <div id="tabella_funzioni">
        <table id="funzioni_tabella">
            <tr>
                <td class="TabSelected">
                    <?php 
                        print ("<a class='cellaselezionata' href='javascript:CambiaTab(0);'>Dati Anagrafici</a>");
                    ?>   
                </td>
                
                <td class="NoTabSelected">
                    <?php 
                      if (isset($_POST["hdnID"])) { 
                        print ("<a class='cellaselezionata' href='javascript:CambiaTab(1);'>Rubrica Telefonica</a>");
                      } else {
                          print ("<a class='cellaselezionata' href='javascript:GestioneErrori();'>Rubrica Telefonica</a>");
                      }
                    ?> 
                </td>
                
                <td class="NoTabSelected">
                    <?php 
                      if (isset($_POST["hdnID"])) { 
                        print ("<a class='cellaselezionata' href='javascript:CambiaTab(2);'>Gestione Parentela</a>");
                      } else {
                          print ("<a class='cellaselezionata' href='javascript:GestioneErrori();'>Gestione Parentela</a>");
                      }
                    ?> 
                </td>
                
                <td class="NoTabSelected">
                    <?php 
                        print ("<a class='cellaselezionata' href='javascript:CambiaTab(3);'>Tesseramento</a>");
                    ?> 
                </td>
                
                <td class="NoTabSelected">
                    <?php 
                        print ("<a class='cellaselezionata' href='javascript:CambiaTab(4);'>Classi&amp;Catechismo</a>");
                    ?> 
                </td>
                <?php
                        // questa funzione è regolamentata da un ulteriore livello di privilegi: segretari e amministratori possono accedervi 
                        if ($_SESSION['access_level'] >2) {
                            if (isset($_POST["hdnID"])) { 
                                print ("<td class=\"NoTabSelected\">");
                                print ("<a class=\"cellaselezionata\" href=\"javascript:CambiaTab(5);\">Ruoli</a>");
                                print ("</td>");
                            } else {
                                print ("<td class=\"NoTabSelected\">");
                                print ("<a class='cellaselezionata' href='javascript:GestioneErrori();'>Ruoli</a>");
                                print "</td>";
                            }
                        }
                ?>
            </tr>
        </table>
    </div>
<!-- FINE SEZIONE TABELLA FUNZIONI ANAGRAFICA -->
        
<!-- SEZIONE DIV INFO -->       
    <div id="div_dati_funzioni">
        <div id="mylettera">
           <img src="./Immagini/info.png" alt="icona info" width="21" height="21" />
        </div>
    
        <div id="rigaorizzontale">

        </div>
        
        <div id="info">
            INFO SCHEDA
        </div>
        
        <div id="dati_info">
            <table id="table_info">
                <tr>
                    <th class="iscritto" colspan=2>
                        <?php print ($_POST["cognome"]." ".$_POST["nome"]."\n");?>
                    </th>
                </tr>

                <tr>
                    <th class="info_sx">
                    ID:
                    </th>
                    
                    <th class="info_dx">
                        <?php 
                            if ($_POST['hdnID']!="" ||$_POST['hdnID'] != null) {
                                echo($_POST['hdnID']."\n"); 
                            } else {
                                echo "n. d.\n";
                            }
                            
                        ?>
                    </th>
                </tr>
                
               <tr>
                    <th class="info_sx">
                   <?php
                        if ($_POST['sesso']=="M") {
                            echo "Tesserato:";
                        } elseif ($_POST['sesso']=="F"){
                            echo("Tesserata:");
                        } else {
                            echo "Tesserato:";
                        }
                   
                   ?>
                    </th>
                    
                     <th class="info_dx">
                      <?php 
                          if ((int)substr($_POST['mydataST'],0,4) ==(int)date('Y') || ((int)substr($_POST['mydataST'],0,4) ==(int)date('Y')+1)) {
                               echo "<img src='./Immagini/check.png' alt='icona' width='23' height='20' />";
                          } else {
                               echo "<img src='./Immagini/cross3.png' alt='icona' width='18' height='16' />";
                          }
                      ?>
                    </th>
                </tr>
                
                <tr>
                    <th class="info_sx">
                    Classe:
                    </th>
                    
                    <th class="info_dx">
                        <?php print (GetClasseIscritto($nome_classe)."\n"); ?>
                    </th>
                </tr>
                
                <tr>
                    <th class="info_sx">
                    Gruppo:
                    </th>
                    
                    <th class="info_dx">
                        <?php print (GetSezioneIscritto($nome_sezione)."\n"); ?>
                    </th>
                </tr>
                
                <tr>
                    <th class="info_sx">
                    Famigliari:
                    </th>
                    
                    <th class="info_dx">
                        <?php 
                            GetParentelaInfo();
                        ?>
                    </th>
                </tr>
                
                <tr>
                    <th class="info_sx">
                      Ruoli:
                    </th>
                    
                    <th class="info_dx">
                        <?php 
                            GetInfoRuolo();
                        ?>
                    </th>
                </tr>
            </table>        
        </div>
    
    </div>
  
<!-- FINE SEZIONE DIV DATI FUNZIONI -->
 
<!-- ********************** SEZIONE DATI ANAGRAFICI ******************** -->
    <form id="SalvaSchedaIscritto" name="SalvaSchedaIscritto" method="post" action="xanagrafica.php">
            <input type="hidden" name="hdnID" id="hdnIDx" value="<?php echo($_POST['hdnID']); ?>" />
            <input type="hidden" name="azione_salvataggio" id="save_scheda" value="" />
            <input type="hidden" name="tab_attivo" id="dati_anagrafici" value="<?php echo($_POST['tab_attivo']); ?>" />
            <input type="hidden" name="login" value="<?php echo ($_POST["login"]); ?>" />
            
            <div id="myDatiAnagrafici">

                <div id="imgnomecognome">
                    <img src="./Immagini/nomecognome.png" alt="icona persone" width="50" height="50" />
                </div>
                
                <div id="nomecognome">
                    
                    <label for="cognome"><strong>Cognome</strong></label>
                    <input type="text" name="cognome" id="cognome" onblur="ControlloCognome()" onfocus="FuocoCampoCognome()"  value="<?php print ($_POST["cognome"]); ?>" class="campoestratto" size="17"/>
                    &nbsp;&nbsp;
                    
                    <label for="nome"><strong>Nome</strong></label>
                    <input type="text" name="nome" id="nome" onblur="ControlloNome()" onfocus="FuocoCampoNome()" value="<?php print ($_POST["nome"]); ?>" class="campoestratto" size="17" />
                    &nbsp;
                    
                    <strong>Sesso</strong> <strong>M</strong><input type="radio" name="sesso" id="sesso" value="M" 
                    <?php 
                    if ($_POST["sesso"]=="M") {
                        echo("checked");
                    } ?> />
                    
                    <strong>F</strong><input type="radio" name="sesso" id="sessof" value="F" 
                    <?php 
                    if ($_POST["sesso"]=="F") {
                      echo("checked");
                    } ?> />
                  
                  </div>                                                        
                   
                  <div id="datanascita">
                      
                      <label for="data_nascita" id="dataNa"><strong>Data di nascita</strong></label>

                      <input type="text" name="data_nascita" id="dataN" onblur="ControlloDataNascita()" onfocus="FuocoCampoDataNascita()" value="<?php $data=$_POST["data_nascita"]; echo (ConvertiData($data)); ?>"  class="campoestratto" size="8" />
                                           
                        &nbsp;
                        <label for="luogo" id="luogo"><strong>Nato/a a</strong></label>
                        <input type="text" name="natoa" id="natoa" size="35" onblur="ControlloNato()" onfocus="FuocoCampoNatoA()" value="<?php echo ($_POST["natoa"]); ?>" class="campoestratto" />
                    </div>
                    
                    <div id="imgdatanascita">
                    	<img src="./Immagini/peluche.png" alt="icona peluche" width="50" height="50" />                 	
                 	  </div>   
                    	
                    <div id="imgindirizzo">
                    	<img src="./Immagini/indirizzo.png" alt="icona indirizzo" width="50" height="50" /> 
                    </div>
                    
                    <div id="div_indirizzo">
                        <label for="stradario" id="stradario"><strong>Indirizzo</strong></label>
                        <select name="stradario" id="miostradario" onfocus ="FuocoCampoVia()" onblur="ControlloVia()" class="campoestratto">
                            <?php 
                                $via =$_POST["stradario"];
                                //richiama funzione per popolare la lista dei tipi di via
                                PopolaListaVie($via);
                            ?>
                        </select>
                        <input type="text" name="indirizzo" id="indirizzo" size="38" onblur="ControlloIndirizzo()" onfocus="FuocoCampoIndirizzo()" value ="<?php print($_POST["indirizzo"]); ?>" class="campoestratto" />
                        &nbsp;
                        
                        <label for="numero"><strong>Nr.</strong></label>
                        <input type="text" name="numero" id="numero" size="4" onblur="ControlloNumero()" onfocus="FuocoCampoNr()" value ="<?php print($_POST["numero"]); ?>" class="campoestratto" />
                    </div>
                    
                    <div id="citta">
                        <input type="hidden" name="hdnIdcomunex" id="hdnIdcomune" value="<?php echo ($_POST["hdnIdcomune"]); ?>" />
                        <label for="comune" id="comune"><strong>Citt&agrave;/Comune</strong></label>
                       <input type="text" name="comune" id="miocomune" onkeyup="lookup_comuni(this.value);" onblur="fill_comuni();" onfocus="FuocoCampoComune()" onkeypress="RilevaTab(event);" class ="campoestratto" autocomplete="off" 
                       value= "<?php echo ($_POST["comune"]); ?>" size="25" \>
                       <div class="suggestionsBox" id="suggestions_comuni" style="display: none;">
        									<img src="./Immagini/upArrow.png" style="position: relative; top: -13px; left: 75px;" alt="" />
                          <div class="suggestionList" id="autoSuggestionsListComuni">
				          						&nbsp;
									       </div>
								       </div>
                    </div>   
                      
                    <div id="div_cap">
                        <label for="cap"><strong>Cap</strong></label>
                        <input type="text" name="cap" id="cap" size="4" onblur="ControlloCap()" onfocus="FuocoCampoCap()" value ="<?php print($_POST["cap"]); ?>"/ class="campoestratto" >
                        &nbsp;
                        <label for="prov"><strong>Prov.</strong></label>
                        <input type="text" name="prov" id="prov" size="4" onblur="ControlloProv()" onfocus="FuocoCampoProv()" value ="<?php print(strtoupper($_POST["prov"])); ?>" class="campoestratto" />
                    </div>

                    <div id="email">
                        <label for="email"><strong>E-mail</strong></label>
                        <input type="text" name="myemail" id="myemail" size="77" onblur="ControlloEmail()" onfocus="FuocoCampoEmail()" value ="<?php print($_POST["myemail"]); ?>" class="campoestratto"  />
                    </div>
                    
                    <div id="parrocchia">
                      <label for="parrocchia" id="idparrocchia"><strong>Parrocchia di provenienza</strong></label>
                      <input type="hidden" name="hdnIdParrocchia" id="hdnIdParrocchia" value="<?php echo ($_POST['hdnIdParrocchia']); ?>" />
                      <input type="text" name="parrocchia" id="miaparrocchia" onkeyup="lookup_parrocchie(this.value);" onblur="fill_parrocchie();" onfocus="FuocoCampoParrocchia()" onkeypress="RilevaTab(event);" class ="campoestratto" autocomplete="off" value= "<?php GetNomeParrocchia(); ?>" size="57" \>
                      <div class="suggestionsBoxParrocchie" id="suggestions_parrocchie" style="display: none;">
                          <img src="./Immagini/upArrow.png" style="position: relative; top: -13px; left: 50px;" alt="" />
        									<div class="suggestionList" id="autoSuggestionsListParrocchie">
				          						&nbsp;
									       </div>
								      </div>
                    </div>
                                                     
                    <div id="sped_materiale">
                      <input type="checkbox" name="chkspedizione" id="spedizione" checked="checked" />
                      <label for="chkIscrizione" id="chkIscrizione">Spedizione materiale informativo iniziative Oratorio</label>
                    </div>
            </div>  
                    
<!-- ********************** SEZIONE PULSANTIERE ******************** -->
      <!--  ************ PULSANTIERA DEFAULT (ANAGRAFICA) ************************* -->
    <div id="pulsantiera">
        <input type="button" name="privacy" id="myprivacy" value="Privacy" onclick="stampa_privacy('<?php echo($_POST["hdnID"]); ?>')" />
        <input type="button" name="salvadati" id="btnsalvadati" value="Salva i Dati" onClick="fncSalvaScheda();" />

        <?php
            if (isset($_POST["hdnID"])) {
                if ($_SESSION['access_level'] >2) {
                    echo "<div id='salva_privacy'> \n";
                    echo "<input type='button' name='cancella_scheda' id='mycancella_scheda' value ='Cancella Scheda' onClick='fncCancellaScheda();' /> \n";
                    echo "</div> \n";       
                } else {
                    echo "<style> \n";
                    echo "#pulsantiera { top: 430px; } \n";
                    echo "#div_btnchiudischeda { top: 5px; } \n";
                    echo "</style> \n";
                }
            } else {
                    echo "<style> \n";
                    echo "#pulsantiera { top: 430px; } \n";
                    echo "#div_btnchiudischeda { top: 5px; } \n";
                    echo "</style> \n";
            }
        ?>
            <div id="div_btnchiudischeda">
                <?php 
                    if (isset($_POST["hdnID"])) {
                        echo "<input type='button' name='chiudischeda' id='btnchiudischeda' value='Chiudi Scheda Iscritto' onclick='ChiudiIscritto(0);' />";
                    } else {
                        echo "<input type='button' name='chiudischeda' id='btnchiudischeda' value='Annulla Iscrizione' onclick='ChiudiIscritto(1);' />";
                    }                      
                ?>
            </div>
      </div>
      
        <!--  ************ PULSANTIERA 1 (RUBRICA) ************************* -->
      <div id="pulsantiera1">
          <input type="button" value="Salva Rubrica" onClick="fncSalvaRubrica();" name="salva_numero" id="salva_numero"  
              <?php
                  if (isset($_POST["hdnID"])) {
                      echo "enabled";
                  } else {
                      echo "disabled";
                  }
              ?>
          /> &nbsp;
          <input type="button" value="Cancella Numeri" onClick="fncCancellaNumeroTelefono()" 
              <?php
                  if (isset($_POST["hdnID"])) {
                      echo "enabled";
                  } else {
                      echo "disabled";
                  }
              ?>
          />
      
          <div id="div_btnchiudischeda1">
                <?php 
                    if (isset($_POST["hdnID"])) {
                        echo "<input type='button' name='chiudischeda' id='btnchiudischeda1' value='Chiudi Scheda Iscritto' onclick='ChiudiIscritto(0);' />";
                    } else {
                        echo "<input type='button' name='chiudischeda' id='btnchiudischeda1' value='Annulla Iscrizione' onclick='ChiudiIscritto(1);' />";
                    }                      
                ?>
            </div>
      </div>
      
        <!--  ************ PULSANTIERA 2 (PARENTELA) ************************* -->
      <div id="pulsantiera2">
          <input type="button" value="Aggiungi" onclick="fncAggiungiParente();" name="aggiungi_parente" id="aggiungi_parente"  
              <?php
                  if (isset($_POST["hdnID"])) {
                      echo "enabled";
                  } else {
                      echo "disabled";
                  }
              ?>
          /> &nbsp;
          <input type="button" value="Cancella" onclick="fncCancellaParente();" name="cancella_parente" id="cancella_parente" 
              <?php
                  if (isset($_POST["hdnID"])) {
                      echo "enabled";
                  } else {
                      echo "disabled";
                  }
              ?>
          />
          <div id="div_btnchiudischeda2">
                <?php 
                    if (isset($_POST["hdnID"])) {
                        echo "<input type='button' name='chiudischeda' id='btnchiudischeda2' value='Chiudi Scheda Iscritto' onclick='ChiudiIscritto(0);' />";
                    } else {
                        echo "<input type='button' name='chiudischeda' id='btnchiudischeda2' value='Annulla Iscrizione' onclick='ChiudiIscritto(1);' />";
                    }                      
                ?>
            </div>
      </div>
      
      <!--  ************ PULSANTIERA 3 (RUOLI) ************************* -->
      <div id="pulsantiera3">
          <input type="button" value="Aggiungi" onclick="fncAggiungiCancellaRuolo(0);" name="aggiungi_ruolo" id="aggiungi_ruolo"  
              <?php
                  if (isset($_POST["hdnID"])) {
                      echo "enabled";
                  } else {
                      echo "disabled";
                  }
              ?>
          /> &nbsp;
          
          <input type="button" value="Cancella" onclick="fncAggiungiCancellaRuolo(1);" name="cancella_ruolo" id="cancella_ruolo" 
              <?php
                  if (isset($_POST["hdnID"])) {
                      echo "enabled";
                  } else {
                      echo "disabled";
                  }
              ?>
          />
          <div id="div_btnchiudischeda3">
                <?php 
                    if (isset($_POST["hdnID"])) {
                        echo "<input type='button' name='chiudischeda' id='btnchiudischeda3' value='Chiudi Scheda Iscritto' onclick='ChiudiIscritto(0);' />";
                    } else {
                        echo "<input type='button' name='chiudischeda' id='btnchiudischeda3' value='Annulla Iscrizione' onclick='ChiudiIscritto(1);' />";
                    }                      
                ?>
            </div>
      </div>

<!-- FINE SEZIONE DIV DATI FUNZIONI E PULSANTIERE -->

<!-- ********************** SEZIONE DATI TESSERAMENTO ******************** -->
        <div id="myTesseramenti">
        
            <div id="imgTesseramenti">
                <img src="./Immagini/libro.png" alt="icona tesseramenti" width="60" height="60" />
            </div>
           
           <blockquote class="bloccoTess"> 
           
           <div id="DTesseramento">
                <label for="dataT" id="dataT" class="myfixedwidth"><strong>Data Tesseramento</strong></label>
                <?php 
                    $data=$_POST["mydataT"];  
                    print("<input type='text' name='mydataT' id='mytesseramento' class='campitesseramenti' size='12' onblur='ControlloDataTesseramento()' onfocus='FuocoCampoDataTesseramento()' value='".ConvertiData($data)."' \>");
                ?>
                <input type="button" id="bottone_data_t" value ="oggi" onClick="CalcolaDateTesseramento();" /> 
            </div>
           
           <div id="DScadenzaTesseramento">
                <label for="dataST" id="dataST" class="myfixedwidth"><strong>Data Scadenza Tessera</strong></label>
                <?php 
                    $data=$_POST["mydataST"];
                    print("<input type='text' name='mydataST' id='mydataST' class='campitesseramenti' size='12' onblur='ControlloDataScadenzaTesseramento()' onfocus='FuocoCampoDataScadenzaTesseramento()' value='".ConvertiData($data)."' disabled \>");
                ?>
            </div>
            
            <div id="opzioni_quote">
                <label for="quota" id="quota" class="myfixedwidth"><strong>Opzioni quote</strong></label>
                
                <table class ="quote" id="quote_tesseramenti">
                    <?php
                        GetQuoteOratorio();
                    ?>
                </table>
            </div>
            
             <div id="quota_versata">
                <label for="valore_quota" id="valore_quota" class="myfixedwidth"><strong>Quota versata</strong> </label>
                <input type="text" name="quotaversata" id="myquotaversata" size="18" disabled value=<?php echo ($_POST["myaltraquota"]); ?> />
                <strong>&nbsp;&euro;</strong>
            </div>
            
            <div id="sconto">
              <label for="sconti" id="sconti"><strong>Sconto</strong></label>
              <input type="text" class ="campoestratto" style="color: grey;" name="miosconto" id="miosconto" size="4" onblur="" onfocus=""  disabled="disabled" value="0"/>
              &nbsp;<strong>%</strong> &nbsp;
              <input type="checkbox" name="chkConsumazioni" id="chkConsumazioni" disabled="disabled"/>
              <label for="consumazioni" id="consumazioni"><strong>Consumazioni limitate</strong></label>
            </div>
            
            <div id="temperaneo">
                <input type="button" name="btnTemperaneo" value="Tesseramento temporaneo" disabled="disabled">
            </div>
            
            </blockquote>
            
        </div>
        
        <input type="hidden" id="dataserver" name="dataserver" value="<?php echo (date('d/m/Y'));?>" />
        
<!-- FINE SEZIONE DATI TESSERAMENTO -->   
  
<!-- ********************** SEZIONE DATI CLASSI&CATECHISMO ******************** -->     
        <div id="classecatechismo">
            
            <div id="imgTesseramenti">
                <img src="./Immagini/pergamena.png" alt="icona sezione catechismo" width="80" height="60" />
            </div>
            
            <div id="myclasse">
                <label for="classecatechismi" id="classecatechismi"><strong>Classe a scuola&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></label>
                <select class="campoestratto" name="myclassi" id="myclassi" onfocus="FuocoClasseCatechismo()" onblur="ControlloClasseCatechismo()" onclick="ControlloClasseFrequentata()">
                    <option value="0">*******
                      <?php  $classe=$_POST["myclassi"]; PopolaListaClassi($classe); ?>           
                    </option>
                </select>
            </div>  
            
            <div id="sezionecatechismo">
                <label for="sezionecatechismi" id="sezionecatechismi"><strong>Gruppo in parrocchia</strong></label>
                <select name="mysezione" id="mysezione" onfocus="FuocoSezioneCatechismo()" onblur="ControlloSezioneCatechismo()" class="campoestratto">
                    <?php  $sezione=$_POST["mysezione"];
                            PopolaListaSezioni($sezione);
                    ?>    
                </select>
            </div> 
            
            <div id="partecipazione">
                <input type="radio" name="optPartecipa" id="optPartecipa" value="1" <?php 
                if ($_POST["optPartecipa"]==1) {
                  echo("checked='checked'");
                }
                ?> />
                <label for="partecipacatechismo" id="partecipacatechismo"><strong>Partecipa regolarmente</strong></label>
            </div>
            
            <div id="imgBambiniScuola">
                <img src="./Immagini/bambini_scuola.png" alt="icona bambini scuola" width="30" height="30" />
            </div>
            
            <div id="nonpartecipazione">
                <input type="radio" name="optPartecipa" id="optPartecipa" value="2" <?php 
                if ($_POST["optPartecipa"]==2) {
                  echo("checked='checked'");
                }
                ?>/>
                <label for="nonpartecipacatechismo" id="nonpartecipacatechismo"><strong>Non partecipa</strong></label>
            </div>
            
            <div id="imgDivieto">
                <img src="./Immagini/divieto.png" alt="icona divieto d'accesso" width="30" height="30" />
            </div>
                        
            <div id="partecipazionesaltuaria">
                <input type="radio" name="optPartecipa" id="optPartecipa" value="3" <?php 
                if ($_POST["optPartecipa"]==3) {
                  echo("checked='checked'");
                }
                ?>/>
                <label for="partecipasaltuaria" id="partecipasaltuaria"><strong>Partecipa saltuariamente</strong></label>
            </div>
            
             <div id="imgBambini">
                <img src="./Immagini/bambini.png" alt="icona bambini" width="40" height="40" />
            </div>
            
            <div id="coro">
                <input type="checkbox" name="chkCoro" id="chkCoro" value="1" <?php 
                if ($_POST["chkCoro"]=="True") {
                    echo("checked='checked'");
                } ?> />
                <label for="partecipacoro" id="partecipacoro"><strong>Coro</strong></label>
            </div>

        </div>
    </form>
<!-- ************************ SEZIONE RUOLO IN ORATORIO *************************-->
     <form id="ModificaRuolo" method="post" action="xanagrafica.php">
        <input type="hidden" name="azione_salvataggio" id="modifica_ruolo" value="" />
        <input type="hidden" name="tab_attivo" id="mygestioneruoli" value="<?php echo($_POST['tab_attivo']); ?>" />
        <input type="hidden" name="hdnID" value="<?php echo ($_POST['hdnID']);?>"" />
        <input type="hidden" name="dati_check_ruoli" id="dati_check_ruoli" value="" />
        <input type="hidden" name="nrpagina_tabella_ruoli" id="nrpagina_tabella_ruoli" value="" />
        
        <div id="ruolo">
       
            <div id="imgRuoli">
                <img src="./Immagini/ruolo1.png" alt="icona tesseramenti" width="70" height="60" />
            </div>
            
            <div id="sezioneruoli">
                <label for="sezioneruolo" id="sezioneruolo"><strong>Ruolo&nbsp;&nbsp;</strong></label>
                <select name="myruolo" id="myruolo" onfocus="FuocoSezioneRuoli()" onblur="ControlloSezioneRuoli()" class="campoestratto">
                    <?php    
                            GetRuoliOratorio();
                    ?>    
                </select>
           
                <br /><br />
              
                <label for="sezioneruoloclasse" id="sezioneruoloclasse"><strong>Classe animata&nbsp;&nbsp;</strong></label>
                <select name="myruoloclasse" id="myruoloclasse" onfocus="FuocoSezioneRuoliClasse()" onblur="ControlloSezioneRuoliClasse()" class="campoestratto">
                    <?php  
                            PopolaListaClassi(17); 
                    ?>
                </select>
              
               <br /><br />
              
                <label for="sezioneruologruppo" id="sezioneruologruppo"><strong>Gruppo del&nbsp;&nbsp;</strong></label>
                <select name="myruologruppo" id="myruologruppo" onfocus="FuocoSezioneRuoliGruppo()" onblur="ControlloSezioneRuoliGruppo()" class="campoestratto">
                    <?php  
                            PopolaListaSezioni();
                    ?>    
                </select>
            </div> 
            
            <div id="tabellaruoli">
               <table id="Tabella_Ruoli" class="layout_tabella_ruoli">
                    <tr>
                        <th>SEL</th>
                        <th>Ruolo</th>
                        <th>Classe</th>
                        <th>Gruppo</th>
                    </tr>
                    
                    <?php
                        CreaTabellaRuoli();
                    ?>
                    
               </table>
        
            </div>
        </div>  
    </form>

<!-- ************************ SEZIONE RUBRICA ORATORIO *************************-->
        <form id="SalvaNumeroTel" method="post" action="xanagrafica.php">
           
            <div id="rubrica_telefono">
                <div id="imgtelefono">
                    <img src="./Immagini/telefono.png" alt="icona telefono" width="55" height="55" />
                </div>
                
                <div id="tabella_phone">
                    <table id="TabellaPhone" class="layout_tabella_rubrica">
                        <tr>
                            <th>
                                SEL
                            </th>
                            
                            <th>
                                Tipo
                            </th>
                            
                            <th>
                                Pref. Int.
                            </th>
                            
                            <th>
                                Pref. Naz.
                            </th>
                            
                            <th>
                                Numero
                            </th>
                            
                            <th>
                                SMS
                            </th>
                        </tr>
                        <?php
                            GetRubricaTelefonica($errore);
                        ?>
                    </table>
                </div>
         
                <div id="btnPhone">
                    <input type="hidden" name="hdnID" value="<?php echo ($_POST['hdnID']);?>"" />
                    <input type="hidden" name ="DatiRubrica" id ="DatiRubrica" value="<?php echo($_POST['DatiRubrica']); ?>" \>
                    <input type="hidden" name="azione_salvataggio" id="rubrica" value="<?php echo($_POST['azione_salvataggio']); ?>">
                    <input type="hidden" name="tab_attivo" id="rubrica_telefonica"value="<?php echo($_POST['tab_attivo']); ?>">
                </div>
            </div>
        </form>

<!--*********************** SEZIONE GESTIONE PARENTELA ****************************-->
        <form id="GestioneParentela" name="GestioneParentela" class="parentela" method="post" action="xanagrafica.php">
            <input type="hidden" name="azione_salvataggio" id="save_family" value="" />
            <input type="hidden" name="hdnID" value="<?php echo ($_POST['hdnID']);?>"" />
            <input type="hidden" name="hdnIDParente" id="hdnIDParente" value="<?php echo ($_POST['hdnIDParente']);?>" />
            <input type="hidden" name="id_famiglia" id="id_famiglia" value="" />
            <input type="hidden" name="tab_attivo" id="mygestioneparentela" value="<?php echo($_POST['tab_attivo']); ?>" />
            <input type="hidden" name="id_famiglia_nucleo_A" id="id_famiglia_nucleo_A" value="<?php echo($_POST['id_famiglia_nucleo_A']); ?>" />
            
            <div id="gestione_parentela">
                <div id="imgFamiglia">
                    <img src="./Immagini/famiglia.png" alt="icona telefono" width="80" height="80" />
                </div>

                <div id="cognome_parente">
                    <label class="fixedwidth"><strong>Cognome e Nome parente:</strong></label>
                    <input type="text" style="border: 1px dotted grey;" name="CognomeParente" id="CognomeParente" onkeyup="lookup_parentela(this.value);" onblur="fill_parentela();" onfocus="ResetCampoCognome()" onkeypress="RilevaTab(event);" autocomplete="off" size="41" value="<?php echo $_POST["CognomeParente"];?>" />
                    &nbsp;
								
                    <div class="suggestionsBoxParentela" id="suggestions_parentela" style="display: none;">
									     <img src="./Immagini/upArrow.png" style="position: relative; top: -13px; left: 50px;" alt="" />
									     <div class="suggestionList" id="autoSuggestionsListParentela">
										      &nbsp;
									     </div>
								    </div>
                </div>

                <div id="grado_parentela">
                    <label class="fixedwidth"><strong>Grado di parentela:</strong></label>
                    <select style="border: 1px dotted grey;" name="GradoParentela" id="GradoParentela">
                        <?php
                            GetGradoParentela();
                        ?>
                    </select>
                </div>
                
                <div id="parentela_nucleo_A">
                    <label class="fixedwidth"><strong>Nucleo famigliare: </strong></label>
                        <?php
                            GetFamigliariParentelaNucleoA();
                        ?>
                    
                </div>
                
                <div id="tabella_parentela">
                    <table id="TabellaParentela" class="layout_tabella_parentela">
                        <tr>
                            <th>
                                SEL
                            </th>
                            
                            <th>
                                ID
                            </th>
                            
                            <th>
                                Cognome&amp;Nome
                            </th>
                            
                            <th>
                                Grad. Parent.
                            </th>
                            
                            <th>
                                Tess.
                            </th>
                            
                            <th>
                                Classe
                            </th>
                            
                            <th>
                                Ruolo
                            </th>
                        </tr>
                        <?php
                            CreaTabellaFamigliari();
                        ?>
                    </table>
                </div>
            
            </div>
        </form>
    
    <?php
        StampaMessaggio($messaggio,$errore,$icona);
    ?>
    <div id ="legendaruoli">
        <?php
            GetLegendaRuoli();
        ?>
        <br /><br />
        <input type="button" id="btn_legenda_ruoli" name="btn_legenda_ruoli" value="Chiudi" onclick="LegendaRuoli(1);"/>
    </div>
</body>

</html>
