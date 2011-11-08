<?php
session_start();

$host  = $_SERVER['HTTP_HOST'];
// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}

// Identifica l'operatore che si è loggato
$idoperatore = $_SESSION['authenticated_user_id'];

require('accesso_db.inc');

ob_clear;

// Rileva il tipo di sacramento scelto dall'operatore
$sacramento=$_GET["scr"];

switch ($sacramento) {
    case 1:
        $title="Comunioni";
    break;
    
    case 2:
        $title="Cresime";
    break;
}
ConnettiDB();

// ottiene il nome dell'operatore
$result=GetOperatore($idoperatore); 
$row=mysql_fetch_object($result);
$nome_operatore=$row->Nome;
$result=null;
$row=null;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Oratorio Saint-Martin - <?php echo "Gestione ".$title." - Stampa documenti"; ?> </title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<style>

body {
  font-family: Verdana,Arial;
}

h1 {
  margin-bottom:0px;
  margin-top:-20px;
  text-align:left;
}

h3 {
  font-family:Sans Serif;
  font-weight:bold;
  text-align:center;
  margin-top:90px;
}

h5 {
  margin-top:5px;
  text-align:center;
  font-size:medium;
  font-variant:small-caps;
}

table {
  width:100%;
}

td.datitabella {
  /*border-bottom:1px dotted black;*/
  padding:0.4em 0.6em 0em 0.3em;
  background:#F2F2C2;
}

td {
  font-size:small;
}

th {
  padding-bottom:0.6em;
  padding:0.8em;
  text-align:left;
  font-size:small;
}

th.tabelladati {
  font-variant:small-caps;
  background: grey;
  color:white;
  padding:0.2em 0.2em 0.3em 0.3em;
}

em {
  color:brown;
}

#contenutopagina {
  width: 90%;
	height: 85%;
	padding:0px 0px 0px 0px;
	margin:0px 0px 30px 30px;
}

#indirizzo {
  position:relative;
  top:200px;
}

#notifica {
  position:relative;
  top: 280px;
}

#nessuno_in_elenco{
  width:500px;
  position:absolute;
  top:50px;
  left:50px;
  text-align:center;
  color: black;
  font-weight:bold;
  border:2px solid green;
  background:#CCCC99; 
  -moz-border-radius:7px;
  line-height:150%; 
}

#titolo_stampa_documenti {
  position:relative;
  top:0px;
  background:green;
  color:white;
  font-variant:small-caps;
  font-size:large;
  padding:0.2em;
  padding-bottom:0.4em
}
</style>

</head>

<body>
<?php 

// controlla se deve visualizzare la finestra stampa (scelta stampante, ecc.)
if (isset($_POST["chkVisualizzaFinestraStampa"])){
    $visualizza_finestra_stampa=true;
} else {
    $visualizza_finestra_stampa=false;
}

// controlla se deve usare la carta intestata della parrocchia
if (isset($_POST["chkCartaIntestata"])){
    $carta_intestata=true;
} else {
    $carta_intestata=false;
}

// ottiene il nome del vescovo che ha somministrato la cresima
$celebrante=$_POST["txtCelebrante"];

// ottiene la data da notificare
$data_celebrazione=$_POST["optGruppo"];
 
// controlla se deve chi ha celebrato era un vescovo supplente
if (isset($_POST["chkSupplente"])){
    $supplente=true;
} else {
    $supplente=false;
}

// controlla se deve stampare la firma del parroco
if (isset($_POST["chkFirmaParroco"])){
    $firma_parroco=true;
} else {
    $firma_parroco=false;
}

// ottiene l'azione da svolgere
if (isset($_POST["azione"])){
    $azione=$_POST["azione"];
} else {
    $azione=0;
}

switch ($azione) { 
  case 0: //visualizza le informazioni prima di stampare
    // controlla quanti gruppi ci sono nell'anno in corso e se hanno date di celebrazione differenti
    // più celebrazioni nello stesso giorno sono considerate come unico gruppo
    $query="SELECT * 
        FROM tblgruppisacramenti
        GROUP BY SCR,MONTH(GruppoSacramento),DAY(GruppoSacramento) 
        HAVING YEAR(GruppoSacramento)='".date('Y')."' 
        AND SCR=".$sacramento." 
        ORDER BY GruppoSacramento";

    // invia la query a Mysql
    $rstGruppi=mysql_query($query);
    
    // controlla se ci sono gruppi da analizzare e stampare
    $nr_gruppi=mysql_num_rows($rstGruppi);
    if ($nr_gruppi>0) {
        //Visualizza le informazioni prima di inviarle alla stampante
        VisualizzaInfo($rstGruppi);
    } else {
      NoGruppiIscritti('gruppi');
    }
  break;
  
  case 1:
      // controlla se sono stati compilati i campi obbligatori (data celebrazione e celebrante)
      if ($data_celebrazione==null || $data_celebrazione=="") {
          NoGruppiIscritti('datacelebrazione');
          exit();
      } 
      
      if ($celebrante==null || $celebrante=="") {
          NoGruppiIscritti('celebrante');
          exit();
      }
      
      // controlla se ci sono notifiche da fare per la data di celebrazione scelta
      $query="SELECT tblsacramenti.DataIscrizione,tblsacramenti.ParrocchiaBattesimo,
              tblsacramenti.SCR, COUNT(tblsacramenti.ParrocchiaBattesimo) AS Iscritti, tblgruppisacramenti.GruppoSacramento
              FROM tblsacramenti
              INNER JOIN tblgruppisacramenti
              ON tblsacramenti.IdGruppo=tblgruppisacramenti.IdGruppoSacramento
              GROUP BY tblsacramenti.ParrocchiaBattesimo
              HAVING tblsacramenti.SCR=".$sacramento." 
              AND DATE(tblgruppisacramenti.GruppoSacramento)='".ConvertiData($data_celebrazione)."'
              AND NOT (tblsacramenti.ParrocchiaBattesimo LIKE '%martin%' AND tblsacramenti.ParrocchiaBattesimo LIKE '%aosta%')
              ORDER BY tblsacramenti.ParrocchiaBattesimo";

      // invia la query a Mysql
      $result=mysql_query($query);
      
      if (mysql_num_rows($result)==0) {
          NoGruppiIscritti('iscritti');
          exit();
      } 
      
      while ($parrocchia=mysql_fetch_object($result)) {
          VisualizzaNotifica($parrocchia->ParrocchiaBattesimo);
      }
      
      // in base alle scelte dell'operatore visualizza o nasconde la finestra stampa (scelta stampante, ecc.)
      if ($visualizza_finestra_stampa) {
          echo "<script type= \"text/javascript\">";
	        echo "window.print();\n";
          echo "</script>";
      }
  break;
}

exit ();

//***************************************************************************************
function VisualizzaNotifica($parrocchia) {
global $sacramento;
global $data_celebrazione;
global $celebrante;
global $carta_intestata;
global $firma_parroco;

// ottiene i battezzati fuori dalla parrocchia passata come argomento della funzione
$query="SELECT tblsacramenti.*,Catechismi.Cognome,Catechismi.Nome,Catechismi.Data_di_nascita,Catechismi.Luogo_di_nascita   
        FROM tblsacramenti 
        INNER JOIN Catechismi 
        ON tblsacramenti.ID=Catechismi.ID
        INNER JOIN tblgruppisacramenti
        ON tblsacramenti.IdGruppo=tblgruppisacramenti.IDGruppoSacramento 
        WHERE tblsacramenti.ParrocchiaBattesimo LIKE '%".addslashes($parrocchia)."%' 
        AND tblsacramenti.SCR=".$sacramento." 
        AND DATE(tblgruppisacramenti.GruppoSacramento)='".ConvertiData($data_celebrazione)."' 
        ORDER BY Catechismi.Cognome,Catechismi.Nome";
        
      $rstBattezzati=mysql_query($query);
        
      if (mysql_num_rows($rstBattezzati) !=0) {
          ?>
          <div id="contenutopagina">
                  <?php 
                      if ($carta_intestata) {
                  ?>
                          <p>
                              <h5>&nbsp;</h5>
                              <h5>&nbsp;</h5>
                          </p>
                  <?php     
                      } else {
                  ?>    
                        <p>
                              <h5>Parrocchia di Saint-Martin de Corl&eacute;ans</h5>
                              <h5 style="margin-top:-25px;">Diocesi di Aosta</h5>
                        </p>
                  <?php    
                      }
                  ?>
                  
                  <p style="line-height:150%;text-align:right;margin-top:130px;">
                      Al Parroco
                      della Parrocchia
                      <br />
                       di <strong><?php echo $parrocchia; ?></strong>   
                  </p> 
              
              <h3>NOTIFICAZIONE DI CRESIMA</h3>
              
              <p style="line-height:150%;text-align:justify;">
                  La prego di trascrivere sui registri di Battesimo della Sua parrocchia la Cresima celebrata nella Chiesa
                  di Saint-Martin de Corl&eacute;ans in Aosta il <strong><?php echo $data_celebrazione; ?></strong> da Sua Eccellenza Monsignor <strong><?php echo $celebrante; ?></strong>
                  e conferita ai seguenti battezzati nella Sua Parrocchia:
              </p>
                     
              <p style="line-height:150%;text-align:justify;margin-top:50px;">
              <table border=0>
                  <tr>
                      <th class="tabelladati">Cresimato</th>
                      <th class="tabelladati">Luogo di nascita</th>
                      <th class="tabelladati">Data di nascita</th>
                      <th class="tabelladati">Data Battesimo</th>
                  </tr>
          <?php
              while ($row=mysql_fetch_object($rstBattezzati)){
                  $i++;
                  echo "<tr>";
                  echo "<td class='datitabella'><strong>".$row->Nome." ".$row->Cognome."</strong></td>";
                  echo "<td class='datitabella'>".$row->Luogo_di_nascita."</td>";
                  echo "<td class='datitabella'>".ConvertiData($row->Data_di_nascita)."</td>";
                  echo "<td class='datitabella'>".ConvertiData($row->DataBattesimo)."</td>";
                  echo "</tr>";
              }
              
              for ($righe=0;$righe<10-$i;$righe++) {
                  echo "<tr>";
                  echo "<td style='color:white;background:white;'>&nbsp;</td>";
                  echo "<td style='color:white;background:white;'>&nbsp;</td>";
                  echo "<td style='color:white;background:white;'>&nbsp;</td>";
                  echo "<td style='color:white;background:white;'>&nbsp;</td>";
                  echo "</tr>";
              }
          ?>    
              </table>
          </p>
          
          <p style="line-height:150%;text-align:justify;margin-top:20px;">
              Aosta, l&igrave; <?php echo date('d/m/Y');?>
          </p>
          
          <p style="line-height:150%;text-align:center;margin-top:20px;margin-left:300px;">
              Il Parroco <br />
              <?php 
                  if ($firma_parroco) {
              ?>
                      <img src="./Immagini/firma_600dpi.jpg" width="270">
              <?php 
                  }
              ?>
          </p>
          </div>
          <p style="page-break-after:always;" />
          <?php
      } 
return;
}

//***************************************************************************************
function NoGruppiIscritti($no_gruppi_iscritti) {
global $sacramento;
global $title;
global $data_celebrazione;

?>
     <form name="Iscritti" method ="post" action="xsacramenti.php?scr=<?php echo $sacramento; ?>">
         <input type="hidden" name="sezione" value="" />
         <div id='nessuno_in_elenco'>
              <div id="titolo_stampa_documenti">
                  Stampa notifiche
              </div>
              
              <p>
                  
                  <?php 
                      switch ($no_gruppi_iscritti) {
                          case 'gruppi':
                              echo "Nessun gruppo &egrave; stato configurato per l'anno in corso!";
                          break;
                          
                          case 'iscritti':
                              echo "Nessun iscritto &egrave; stato battezzato<br />fuori dalla nostra parrocchia!";
                          break;
                          
                          case 'datacelebrazione':
                              echo "Nessuna data di celebrazione &egrave; stata scelta!";
                          break;
                          
                          case 'celebrante':
                              echo "Nessun celebrante &egrave; stato indicato!";
                          break;
                      }
                  ?>
                   
              </p>
              <ul style="text-align:left;font-size:small;" type="square">
                  <table>
                      <tr>
                          <td><li>Archivio:</td><td><em><?php echo $title; ?> </em></li></td>
                      </tr>
                      <tr>
                          <td><li>Anno:</td><td><em><?php echo date('Y'); ?></em></li></td>
                      </tr> 
                     
                      <tr>
                          <td><li>Data celebrazione:</td><td><em><?php echo $data_celebrazione; ?></em></li></td>
                      </tr> 
                             
                    </table>    
               </ul>
              
              <p>&nbsp;</p>
              <p>
                  <input type="submit" 
                         style="height:40px;width:200px"
                         value="Ok"
                  />
              </p>
        </div>
    </form>
  </body>
</html>
<?php
  return;
} // chiude la funzione NoGruppiIscritti

//******************************************************************
function  VisualizzaInfo($rstGruppi) {
global $sacramento;
global $title;

?>              
<form name="StatisticheIscritti" id ="StatisticheIscritti" method ="post" action="report_notifiche.php?scr=<?php echo $sacramento; ?>">
    <input type="hidden" name="azione" id="azione" value="<?php echo $azione;?>" />
    
    <div id="nessuno_in_elenco">
        <div id="titolo_stampa_documenti">
            Stampa notifiche
        </div>
        
        <ul style="text-align:left;font-size:small;" type="square">
            <table>
                <tr>
                    <td> <li>Archivio:</td><td><em><?php echo $title; ?> </em></li></td>
                </tr>
                
                <tr>
                    <td><li>Anno:</td><td><em><?php echo date('Y'); ?></em></li></td>
                </tr>
            </table>
        </ul>
        
        <p>
            <fieldset style="text-align:left;margin-left:25px;font-size:small;width:400px;">
            <legend>&nbsp;Scegli data celebrazione...&nbsp;</legend>
            <?php 
                $nr_gruppi=mysql_num_rows($rstGruppi);
                if ($nr_gruppi==1) {
                    $checked="checked";
                } else {
                    $checked="";
                }
                
                while ($gruppi=mysql_fetch_object($rstGruppi)) {
                    echo "<input type=\"radio\" name=\"optGruppo\" value=\"".ConvertiData($gruppi->GruppoSacramento)."\"".$checked."/>&nbsp;&nbsp;".ConvertiData($gruppi->GruppoSacramento)."<br />";
                }
            ?>
            </fieldset>
        </p>
        
        <p style="text-align:left;margin-left:25px;">
            <span style="font-size:small;color:purple;font-weight:bold;">
                Celebrante: S.E. Monsignor&nbsp;
                <input type="text"
                       style="border:1px dotted grey;width:210px;" 
                       name="txtCelebrante"
                       value="Giuseppe Anfossi"
                />
            </span>
        </p>
        
      <p style="text-align:left;margin-left:25px;margin-top:-15px;">
            <span style="font-size:small;">
                <input type="checkbox"
                       name="chkSupplente"
                />
                &nbsp;Celebrante supplente
            </span>
        
        </p>
        
        <p style="text-align:left;margin-left:25px;margin-top:25px;">
            <span style="font-size:small;">
                <input type="checkbox"
                       name="chkCartaIntestata"
                       checked
                />
                &nbsp;Usa carta intestata della parrocchia
            </span>
        
        </p>
        
        <p style="text-align:left;margin-left:25px;margin-top:-15px;">
            <span style="font-size:small;">
                <input type="checkbox"
                       name="chkFirmaParroco"
                       checked
                />
                &nbsp;Inserisci firma del parroco
            </span>
        
        </p>
        
        <p style="text-align:left;margin-left:25px;margin-top:25px;">
            <span style="font-size:small;">
                <input type="checkbox"
                       name="chkVisualizzaFinestraStampa" 
                       checked
                />
                &nbsp;Visualizza finestra stampa
            </span>
        
        </p>
        
        <p>
            <input type="submit"
                   name="btnstampa" 
                   style="height:40px;width:200px"
                   value="Stampa"
                   onclick="javascript:document.getElementById('azione').value=1;"
            />
            &nbsp;
            <input type="submit" 
                   name="btnannulla"
                   style="height:40px;width:200px"
                   value="Annulla"
                   onclick="javascript:document.getElementById('StatisticheIscritti').action='xsacramenti.php?scr=<?php echo $sacramento; ?>';"
            />
        </p>
    </div>

<?php   
  return;
} // chiude la funzione VisualizzaInfo        
?>

</form>
<?php 
//***********************************************************
// Converte la data nei formati mysql-italiano e viceversa
function ConvertiData($data) {
    if ($data!=null || $data!="") {
        if (strpos($data,"/")!=2) { // italiano
            $data =substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
        } else { //mysql
            $data =substr($data,6,4)."/".substr($data,3,2)."/".substr($data,0,2);
        }
    }
     return $data;   
} 
?>
 
</body>
</html>