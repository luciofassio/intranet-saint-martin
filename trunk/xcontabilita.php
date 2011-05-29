<?php 
session_start();

require('accesso_db.inc');   
require("funzioni_generali.inc");
require ("funzioni_contabilita.inc");



$host  = $_SERVER['HTTP_HOST'];

// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}

$idoperatore = $_SESSION['authenticated_user_id'];

/**********************>>>>>>>>>> FUNZIONE PER IL CALCOLO DELLA DATA <<<<<<<<***************************/ 
function GetData($Data) {
    // Crea l'array $giorni per il calcolo della data
      $giorni=array(
          0=>"domenica",
          1=>"luned&igrave;",
		      2=>"marted&igrave;",
		      3=>"mercoled&igrave;",
		      4=>"gioved&igrave;",
		      5=>"venerd&igrave;",
		      6=>"sabato");
		
    // Crea l'array $mesi per il calcolo della data
		 $mesi=array(
          1=>"gennaio",
		      2=>"febbraio",
		      3=>"marzo",
		      4=>"aprile",
		      5=>"maggio",
		      6=>"giugno",
          7=>"luglio",
          8=>"agosto",
          9=>"settembre",
          10=>"ottobre",
          11=>"novembre",
          12=>"dicembre");
        
    // prende dal sistema (server) la data corrente
    $myData=getdate(time());
    
    // costruisce la stringa contenente la data formattata
    $Data=$giorni[$myData["wday"]]." ".$myData["mday"]." ".$mesi[$myData["mon"]]." ".$myData["year"];
    
    // ritorna il valore della data
    return $Data;
}    

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Gestione Oratorio / Contabilit&agrave;</title>
   <!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
  <script type="text/javascript" src="./js/f_contabilita.js"></script>
  <script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>
  
</head>

<style>
  
  td {
    text-align:right;
  }
  
  td.pulsantiera {
      text-align:center;
  }
  
  table.layoutcomandi {
    width: 300px;
    height:420px;
  }
  
  table.layoutcomandi td.laycmdsx {
    color:black;
    padding:0.6em;
    text-align: left;
    background:#CCFF99;
  }
  
  table.layoutcomandi td.laycmddx {
    color:black;
    padding:0.6em;
    text-align: left;
    background:#CCCC99;
  }
  
  table.layoutcomandi th.laycmd {
    color:black;
    padding:0.6em;
    text-align: center;
    font-variant: small-caps;
    border-top:1px dotted grey;
    height:25px;
  }
  
  table.layoutvocicapitoli {
    position: relative;
    top:2%;
    left:1%;
    width: 610px;
    height:380px;
  }
  
  table.layoutvocicapitoli td.radiovoci{
      height:25px;
      width:150px;
      text-align:left;
      color:black;
      padding-right:0.3em;
      padding-top:0.3em;
  }
  
  #comando_dati {
    visibility:visible;
    position: absolute;
    top:12%;
    left:3%;
  }
  
  #voci {
    width:170px;
    border: 1px dotted grey;
  }
  
  #filtra_capitolo {
    width:170px;
    border: 1px dotted grey;
  }
  
  #tipo_contabilita {
    width:170px;
    border: 1px dotted grey;
  }
  
  #txtImporto {
    width:170px;
    font-weight:bold;
    border: 1px dotted grey;
    text-align:right;
  }
  
  #data_operazione {
    width:170px;
    font-weight:bold;
    border: 1px dotted grey;
    text-align: right;
    color: green;
  }
  
/********************************************************************************/
/* SEZIONE ESTRATTO CONTO */
/********************************************************************************/
table.ec {
    width:100%;
    color: black;
}

table.ec td.layoutEC {
    border-top:1px dotted grey;
    border-bottom:1px dotted grey;
    padding:0.3em;
    color:black;
    text-align:left;
}

table.ec td.layoutECdx {
    border-top:1px dotted grey;
    border-bottom:1px dotted grey;
    padding:0.3em;
    color:black;
    text-align:right;
}

table.ec td.layoutECdispari {
    padding:0.3em;
    color:black;
    text-align:left;
    background:#CCCC99;
     -moz-border-radius: 4px;
}

table.ec td.layoutECpari {
    padding:0.3em;
    color:black;
    text-align:left;
    background:#CCFF99;
     -moz-border-radius: 4px;
}

table.ec td.layoutECent {
    padding:0.3em;
    color:blue;
    text-align:right;
    background:#CCCC99;
     -moz-border-radius: 4px;
}

table.ec td.layoutECusc {
    padding:0.3em;
    color:red;
    text-align:right;
    background:#CCFF99;
     -moz-border-radius: 4px;
}

#EstrattoConto {
      visibility: visible;
      position:absolute;
      left: 28%;
      top: 12%;
      width:625px;
      height:420px;
      background:white;
  }
 
 /********************************************************************************/
 /* SEZIONE VOCI & CAPITOLI */
 /********************************************************************************/
  #voci_capitoli{
      position:absolute;
      left: 28%;
      top: 12%;
      width:625px;
      height:420px;
      background:#CCFF99;
      -moz-border-radius: 7px;
  }
  #txtCapitolo{
    width:280px;
    font-weight:bold;
    border:1px dotted grey;
  }
  
  #txtCapitolo{
    width:280px;
    font-weight:bold;
    border:1px dotted grey;
  }
  
  #txtSigla{
    width:280px;
    font-weight:bold;
    border:1px dotted grey;
  }
  
  #txtVoce{
    width:280px;
    font-weight:bold;
    border:1px dotted grey;
  }
  
  #addCapitoli{
    width:290px;
    border:1px dotted grey;
    -moz-border-radius: 7px;
  }
  
  #addVoci{
    width:290px;
    border:1px dotted grey;
    -moz-border-radius: 7px;
  }

   .intestazionevoci{
      background:green;
      color:white;
      font-variant:small-caps;
      padding-left:0.3em;
      padding-right:0.3em;
      padding-bottom:0.3em;
      -moz-border-radius: 7px;
  }
/********************************************************************************/
/* SEZIONE STAMPA ERRORI */
/********************************************************************************/
  #stampamessaggio{
  visibility:hidden;
  position: absolute;
  background: white;
  border:3px solid green;
  -moz-border-radius: 7px;
  /* top: 200px;
  left: 270px; */
  z-index: 1;
  padding: 5px;
}

  .intestazioneerrori{
      position:relative;
      width:100%;
      top:-18px;
      left:-4px;
      background:green;
      color:white;
      font-variant:small-caps;
      padding-left:0.3em;
      padding-right:0.3em;
      padding-bottom:0.3em;
      -moz-border-radius: 7px;
  }
  
  .iconaerrori {
      position:relative;
      top:-20px;
  }

  .testomessaggio {
    position:relative;
    top:-60px;
    left:35px;
    text-align:left;
  }

  #ChiudiMessaggio {
      position:relative;
      top:-40px;
      width:80px;
  }
  
  /********************************************************************************/
/* SEZIONE FINESTRA BILANCIO */
/********************************************************************************/
#div_bilancio {
  visibility:hidden;
  position: absolute;
  background: white;
  border:3px solid green;
  -moz-border-radius: 7px;
  z-index: 1;
  padding: 5px;
  top:12.5%;
  left:3%;
  }

#annoBilancio {
  border:1px dotted grey;
}

.Budget {
  border:1px dotted orange;
  padding:0.6em;
  
}

.ChiudiDivBilancio {
  text-align:center;
}

</style>

<body onLoad="DivAperto('<?php echo $_POST["divAperto"];?>');">

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
            | sei in: <strong>contabilit&agrave; oratorio</strong> |
        </div>
        
<!-- FINE SEZIONE INTESTAZIONE -->
    
<!-- SEZIONE BARRA DI NAVIGAZIONE -->
    <?php
        $barra_di_navigazione="| <a href='homepage.php'>home page</a> |";
        
        /*if ($_SESSION['access_level'] >2) {
            $barra_di_navigazione.=" <a href='xcestino.php'>visualizza cestino</a> |";
        }*/
        
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

<!-- SEZIONE BARRA COMANDI -->
   <form id="frmFiltraVoci" method="post" action="xcontabilita.php">
        <input type="hidden" name ="myfiltro" id="xfiltro" value="" />
        <input type="hidden" name="azione" id="myazione" value="" />
        <input type="hidden" name="divAperto" id="mydivAperto" value="<?php //echo $_POST["divAperto"]; ?>" />
        <input type="hidden" name="mymodificaoperazione" id="mymodificaoperazione" value="<?php echo $_POST["mymodificaoperazione"]; ?>" />
        <input type ="hidden" name="nrpagina" id="nrpagina" value="<?php echo ($_POST['nrpagina']);?>" />
    
    <div id="comando_dati">
        <table id="tBarra_Comandi" class="layoutcomandi">
        <tr>
            <th class="laycmd" colspan="2">
                barra comandi
            </th>
        </tr>
        <tr>        
            <td class="laycmdsx">
                Contabilit&agrave;
            </td>
            
            <td class="laycmddx">
                <select name="tipo_contabilita" id="tipo_contabilita">
                    <option name="myselezione_contabilita" onclick="FiltraContabilita();" value="OR" 
                        <?php 
                            if ($_SESSION['access_level'] >3) {
                              echo ("enabled");
                              $TipoContabilita="OR";
                            } else {
                              echo ("disabled");
                              $TipoContabilita="ER";
                            }
                            
                            if ($_POST["tipo_contabilita"]=="OR") {
                                $TipoContabilita="OR";
                                echo "selected";
                            }
                            
                        ?> >Oratorio</option>
                    <option value="ER" name="myselezione_contabilita" onclick="FiltraContabilita();"
                        <?php 
                            if ($_POST["tipo_contabilita"]=="ER") {
                                $TipoContabilita="ER";
                                echo "selected";
                            }
                        ?> 
                    >Estate Ragazzi</option>
                </select>
            </td>
          
        </tr>
        
        <tr>     
            <td class="laycmdsx">
                Operazione
            </td>
            
            <td class="laycmddx">
                      <?php 
                          switch ($_POST["optOperazione"]) {
                              case "E":
                                  $selected_entrata="checked";
                                  $selected_uscita="";
                              break;
                              
                              case "U":
                                  $selected_entrata="";
                                  $selected_uscita="checked";
                              break;
                              
                              default:
                                  $selected_entrata="";
                                  $selected_uscita="";
                              break;
                          }
                      ?>
                      
                      Entrata&nbsp;&nbsp;
                      <input type="radio" id="optOperazione" name="optOperazione" value="E" onclick="xFiltraVoci(0);" <?php  echo ($selected_entrata); ?> />
                      &nbsp;Uscita&nbsp;&nbsp;
                      <input type="radio" name="optOperazione" value="U" onclick="xFiltraVoci(1);" <?php  echo ($selected_uscita); ?> />
            </td>
           
        </tr>
        <tr>
          <td class="laycmdsx">
              Capitolo
          </td>
          
          <td class="laycmddx">
              <select name="filtra_capitolo" id="filtra_capitolo">
                              <option name="optCapitoliEC" id="optCapitoliEC" value="0" onclick="FiltraVociEC();">**********</option>
                              <?php
                                  PopolaCapitoli("EC");
                              ?>
                          </select>
          </td>
        </tr>
        <tr>    
            <td class="laycmdsx">
                Voce
            </td>
            
            <td class="laycmddx">
                <select name="voci" id="voci">
                    <option value="0">**********</option>
                    <?php
                        if (isset($_POST["myfiltro"])){
                            PopolaVoci($_POST["myfiltro"]);
                        }
                        
                    ?>
                </select>
            </td>    
            
        </tr>
        
        <tr>     
            <td class="laycmdsx">
                Importo &euro;
            </td>
            
            <td class="laycmddx">
                <input type="text" name="txtImporto" id="txtImporto" onblur="ControlloImporto('blur');" onfocus="ControlloImporto('focus')"
                       value="<?php echo $_POST["txtImporto"]; ?>"/> 
            </td>
        </tr>
        
        <tr>     
            <td class="laycmdsx">
                Data
            </td>
            
            <td class="laycmddx">
                <input type="text" name="data_operazione" id="data_operazione" onblur="ControlloData('blur');" onfocus="ControlloData('focus');" value=
                            "<?php 
                                    If (isset($_POST["data_operazione"])) {
                                        echo $_POST["data_operazione"];    
                                    } else {    
                                        $mydata=getdate(time());
                                        if (strlen($mydata["mday"])==1) {
                                            $giorno="0".$mydata["mday"];
                                        } else {
                                            $giorno=$mydata["mday"];
                                        }
                                    
                                        if (strlen($mydata["mon"])==1) {
                                            $mese="0".$mydata["mon"];
                                        }
                                    
                                        echo ($giorno."/".$mese."/".$mydata["year"]);
                                    }
                
                ?>"/>
            </td>
        </tr>
        <tr>     
            <td colspan="2" class="pulsantiera">
                            <?php 
                                if ($_POST["azione"]=="ModificaOperazioneEC" || $_POST["azione"]=="ModificaOperazioneECEU") {
                                    $btnValue="Salva";
                                } else {
                                    $btnValue="Inserisci";
                                }
                            ?>
                            
                            <input type="button" id="btnOperazione" name="btnOperazione" value="<?php echo $btnValue;?>" onclick="InserisciOperazione('salva');" />
                &nbsp;&nbsp;<input type="button" id="btnModifica" name="btnModifica" value="Modifica" onclick="InserisciOperazione('modifica');"  />
                &nbsp;&nbsp;<input type="button" id="btnCancella" name="btnCancella" value="Elimina" onclick="InserisciOperazione('elimina');" />
                <br /><br />
                &nbsp;&nbsp;<input type="button" id="btnVoci" name="btnVoci" value="Voci &amp; Capitoli" onClick="VisualizzaDivVoci('apri')" />
            </td>
        </tr>
        </table>    
    </div>
</form>

 <!-- FINE SEZIONE BARRA COMANDI -->  

<!-- SEZIONE ESTRATTO CONTO -->    
    <div id="EstrattoConto">
        <table id="tbEstrattoConto" class="ec">
            <?php 
                if (isset($_POST["tipo_contabilita"])) {
                    $TipoContabilita=$_POST["tipo_contabilita"];
                }
                
                CostruisciEC($TipoContabilita); ?>
        </table>    
    </div>

<!-- SEZIONE VOCI & CAPITOLI -->    
<form name="frmVociCapitoli" id="frmVociCapitoli" method ="post" action ="xcontabilita.php">    
    <input type="hidden" name ="azione" id="azione" value="" />
    <input type="hidden" name ="chkOperazione" id="chkOperazione" value="" />
    <input type="hidden" name ="FiltroVoce" id="filtrovoce" value="" />
    <input type="hidden" name="divAperto" id="divAperto" value="<?php echo $_POST['divAperto'];?>" />
    
    <div id="voci_capitoli">
        <span class="intestazionevoci">Voci &amp; Capitoli</span>
        
        <table id="tableVociCapitoli" class="layoutvocicapitoli">         
            <tr>
                <td class="radiovoci">
                    <select name="addCapitoli" id="addCapitoli" size="13" multiple>
                        <?php
                            PopolaCapitoli();
                        ?>
                       
                    </select>
                    
                     <br /><br />
                    
                    Capitolo <br />
                     <input type="text" name="txtCapitolo" id="txtCapitolo" maxlength="30" onfocus="ControlloCapitolo('focus');" onblur="ControlloCapitolo('blur');" 
                     value="<?php 
                                //if ($_POST["FiltroVoce"]!="F") {
                                    $result= RecuperaCapitolo();
                                    if ($result) {
                                        $row=mysql_fetch_object($result);
                                        echo (htmlentities($row->Capitolo));
                                    }
                                //}
                            ?>" />
                     
                     <br /><br />
                    
                    Sigla <br />
                    <input type="text" name="txtSigla" id ="txtSigla" maxlength="3" onfocus="ControlloSigla('focus');" onblur="ControlloSigla('blur');" 
                    value="<?php echo (htmlentities($row->SiglaCapitolo)); ?>" />
                </td>
                
                <td class="radiovoci">
                    Voce <br />
                    <input type="text" name="txtVoce" id="txtVoce" maxlength="30" onfocus="ControlloVoce('focus');" onblur="ControlloVoce('blur');"
                    value="<?php 
                                $result= RecuperaVoce();
                                if ($result) {
                                    $row=mysql_fetch_object($result);
                                    echo (htmlentities($row->Voce));
                                    
                                    switch ($row->Movimentazione) {
                                        case "E":
                                            $checked_E="checked";
                                        break;
                                        
                                        case "U":
                                            $checked_U="checked";
                                        break;
                                    
                                        case "EU":
                                            $checked_E="checked";
                                            $checked_U="checked";
                                        break;
                                        
                                        default:
                                            $checked_E="";
                                            $checked_U="";
                                        break;
                                    }
                                }
                                
                            ?>" />
                     
                    <br /><br />
                     
                    <input type ="checkbox" name="selEntrataUscita" value="E" <?php echo $checked_E; ?> />&nbsp;&nbsp;Entrata
                    &nbsp;&nbsp;
                    <input type ="checkbox" name="selEntrataUscita" value="U" <?php echo $checked_U; ?> />&nbsp;&nbsp;Uscita
                   
                    <br /><br /><br />
                    
                    <select name="addVoci" id="addVoci" size="13" multiple>
                        <?php
                            PopolaVoci($_POST["FiltroVoce"]);
                        ?>
                    </select>
                </td>
                
          </tr>
          <tr>
              <td colspan="2">
                  <br />
                  <input type="button" name="btnAggiungi" id="btnAggiungi"
                  <?php 
                      if ((isset($_POST["addCapitoli"]) || isset($_POST["addVoci"])) && isset($_POST["FiltroVoce"])) {
                          print ("value=\"Modifica\" onClick=\"SalvaVociCapitoli('modifica');\"");
                      } else {
                          print ("value=\"Aggiungi\" onClick=\"SalvaVociCapitoli('aggiungi');\"");
                      }
                  ?> />
<?php 
// visualizza in base ai privilegi il bottone per rimuovere voci/capitoli
$bottone=<<<EOD
&nbsp;&nbsp;<input type="button" name="btnRimuovi" id="btnRimuovi" value="Rimuovi" onClick="RimuoviVoceCapitolo();"/>
EOD;

if ($_SESSION['access_level'] >3) {
    echo $bottone;
} 
?>
                  &nbsp;&nbsp;<input type="button" name="btnChiudi" value="Chiudi" onClick="VisualizzaDivVoci('chiudi')"/>
              </td>
          </tr>
        </table>
    </div>
</form>

<!-- SEZIONE DIV BILANCIO-->
<form name="frmBilancio" id="frmBilancio" method ="post" action ="xstampa_bilancio.php" target="_blank">    
    <div id="div_bilancio">
        <p class="intestazioneerrori">Elabora Bilancio</p>
        
        <span style="font-weight:bold;">Anno&nbsp;</span>
        
        <select name="annoBilancio" id="annoBilancio">
            <option name="yearBilancio" id="yearBilancio" value="2011">2011</option>
        </select>
        
        <p>
            <fieldset class="Budget"><legend>Tipo... &nbsp;</legend>
                <input type="radio" value="1" name="tipoBilancio" checked />&nbsp;&nbsp;Sintetico<br /><br />
                <input type="radio" value="2" name="tipoBilancio" />&nbsp;&nbsp;Analitico<br /><br />
                <input type="radio" value="3" name="tipoBilancio" />&nbsp;&nbsp;Movimenti di Cassa
            </fieldset>
        </p> 
        
        <br />
        
        <p>
            <fieldset class="Budget"><legend>Contabilit&agrave;... &nbsp;</legend>
                    <?php 
                        if ($TipoContabilita=="OR") {
                            $checkedOR="checked";
                            $checkedER=null;
                        }
                        
                        if ($TipoContabilita=="ER") {
                            $checkedOR=null;
                            $checkedER="checked";
                        }
                        
                        if ($_SESSION['access_level'] >3) {
                              $enabled="enabled";
                            } else {
                              $enabled="disabled";
                            }
                    ?>
                    <input type="radio" value="1" name="typeContabilita" <?php echo $checkedOR;?> <?php echo $enabled;?>/>&nbsp;&nbsp;Oratorio<br /><br />
                    <input type="radio" value="2" name="typeContabilita" <?php echo $checkedER; ?> />&nbsp;&nbsp;Estate Ragazzi
            </fieldset>
            
        </p>
        
        <p class="ChiudiDivBilancio">
            <input type="button" id="ElaboraBilancio" value="Elabora" onClick="DivBilancio('elabora');" /> 
            <input type="button" id="ChiudiDivBilancio" value="Chiudi" onClick="DivBilancio('chiudi');" /> 
        </p>

    </div>  
</form>

<!-- SEZIONE DIV MESSAGGI ERRORE-->   
    <div id="stampamessaggio">
        <p class="intestazioneerrori">Errore</p>
        
        <p class="iconaerrori">
            <img src="./Immagini/cross.png" alt="" width="35" height="30" />
        </p>
        
        <p id="messaggio_errore" class="testomessaggio"></p>
        
        <p class="chiudimessaggio">
            <input type="button" id="ChiudiMessaggio" value="ok" onClick="StampaMessaggioErrore('',false);" /> 
        </p>
        
    </div>
     
    

</body>

</html>
