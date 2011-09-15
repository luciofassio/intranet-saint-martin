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
//require ('bar128.php');							// Our Library of Barcode Functions


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
@media screen,print {

body {
  font-family:Verdana;
}

h1 {
  margin-bottom:0px;
  margin-top:-20px;
  text-align:left;
}

h5 {
  margin-top:5px;
  background:#C0C0C0;
  /*color:white;*/
  padding:0.3em 0.4em 0.3em 0.3em;
  text-align:left;
  font-size:medium;
  font-variant:small-caps;
}

table {
  width:100%;
}

td {
  font-size:small;
}

td.datielencosx {
  padding:2px;
  border-bottom:1px dotted black;
  width:40%;
  font-weight:bold;
  color:#2F4F4F;
}

td.datielencodx {
  padding:2px;
  border-bottom:1px dotted black;
  width:80%;
  text-align:justify;
}

th{
  padding-bottom:0.6em;
  padding-top:0.8em;
  text-align:left;
}

em {
  color:brown;
}
#contenutopagina {
	width: 100%;
	height: 85%;
	/*text-align: left;*/
	padding:0px 0px 0px 0px;
	margin: 0 auto;
}

#intestazione {
  position:relative;
  width:100%;
}

#titolo {
  position:relative;
  width:40%;
  float:left;
}

#statistiche {
  position:relative;
  border:1px solid black;
  padding:0.5em;
  float:right;
}

#dati{
  position:relative;
  float:left;
  width:100%;
  top: 30px;
}

#nessuno_in_elenco{
  width:40%;
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

} /* fine dichiarazioni @media*/

</style>

</head>

<body>
<?php 
 // variabili di servizio
$id_gruppo=$_GET["gp"]; // ottiene il gruppo da stampare
$azione=$_POST["azione_stampa"]; // ottiene l'azione da svolgere
$nr_elementi_pagina=2; // stabilisce quante schede può stampare per pagina

// controlla se deve evidenziare i documenti mancanti
if (isset($_POST["chkEvidenzia"])){
    $evidenzia=true;
} else {
    $evidenzia=false;
}
    
// prepara la query da inviare a Mysql
  switch ($id_gruppo) {
      case 0: // per gli iscritti senza gruppo
          $query="SELECT Catechismi.Nome,Catechismi.Cognome,Catechismi.Data_di_nascita,Catechismi.Luogo_di_nascita,Catechismi.Sesso, tblsacramenti.* 
                  FROM tblsacramenti
                  INNER JOIN Catechismi
                  ON tblsacramenti.ID=Catechismi.ID
                  WHERE YEAR(tblsacramenti.DataIscrizione)=".date('Y')." 
                  AND tblsacramenti.IdGruppo=".$id_gruppo." 
                  AND tblsacramenti.SCR=".$sacramento."
                  ORDER BY tblsacramenti.IdGruppo,Catechismi.Cognome,Catechismi.Nome ASC 
                  ";
      break;
      
      default: // per tutti gli altri
          $query="SELECT Catechismi.Nome,Catechismi.Cognome,Catechismi.Data_di_nascita,Catechismi.Luogo_di_nascita,Catechismi.Sesso, tblsacramenti.* 
                  FROM tblsacramenti
                  INNER JOIN Catechismi
                  ON tblsacramenti.ID=Catechismi.ID
                  WHERE tblsacramenti.IdGruppo=".$id_gruppo." 
                  AND tblsacramenti.SCR=".$sacramento."
                  ORDER BY tblsacramenti.IdGruppo,Catechismi.Cognome,Catechismi.Nome ASC 
                  ";    
      break;
  }
  
  // invia la query a Mysql
  $result=mysql_query($query);
  
  // trova il numero di iscritti al turno di cresima
  if ($result) {
    $nr_iscritti=mysql_num_rows($result);
  } else {
    $nr_iscritti=0;
  }

  if ($nr_iscritti==0) {
?>
     <form name="Iscritti" method ="post" action="xsacramenti.php?scr=<?php echo $sacramento; ?>">
         <input type="hidden" name="sezione" value="elenco" />
         <div id='nessuno_in_elenco'>
              <div id="titolo_stampa_documenti">
                  Stampa documenti
              </div>
              
              <p>
                  Nessun iscritto &egrave; presente nel gruppo che hai scelto! 
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
                              <?php 
                                  if ($id_gruppo==0) {
                                  ?>
                                      <td><li>Gruppo:</td><td><em>**********</em></li></td></tr>
                                      <tr><td><li>Ora:</td><td><em>*****</em></li></td></tr>
                              <?php
                                  } else {
                              ?>
                                    <td><li>Gruppo:</td><td><em><?php echo GetGruppo($id_gruppo,$sacramento,'data');?></em></li></td></tr>
                                    <tr><td><li>Ora:</td><td><em><?php echo GetGruppo($id_gruppo,$sacramento,'ora');?></em></li></td></tr>
                              <?php 
                                  }
                              ?>
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
    exit();
    }
    
    // calcola il numero di pagine da stampare 
    $nr_pagine=($nr_iscritti/$nr_elementi_pagina);
    if ($nr_pagine-(int)$nr_pagine>0) {
        $nr_pagine++;
    }

    $nr_pagine=(int)$nr_pagine;

    switch ($azione) {
        case 0: //visualizza statistiche risultati
?>              
<form name="StatisticheIscritti" id ="StatisticheIscritti" method ="post" action="report_iscritti_sacramenti.php?scr=<?php echo $sacramento."&gp=".$id_gruppo;?>">
    <input type="hidden" name="azione_stampa" id="azione_stampa" value="<?php echo $azione;?>" />
    
    <div id="nessuno_in_elenco">
        <div id="titolo_stampa_documenti">
            Stampa documenti
        </div>
        
        <ul style="text-align:left;font-size:small;" type="square">
            <table>
                <tr>
                    <td> <li>Archivio:</td><td><em><?php echo $title; ?> </em></li></td>
                </tr>
                
                <tr>
                    <td><li>Anno:</td><td><em><?php echo date('Y'); ?></em></li></td>
                </tr>
                
                <tr>
                    <td><li>Gruppo del:</td><td>  <em><?php echo GetGruppo($id_gruppo,$sacramento,'data');?></em></li></td>
                </tr>
            
                <tr>
                    <td><li>Ora celebrazione:</td><td><em><?php echo GetGruppo($id_gruppo,$sacramento,'ora');?></em></li></td>
                </tr>
            
                <tr>
                    <td><li>Iscritti al gruppo:</td><td><em><?php echo $nr_iscritti;?></em></li></td>
                </tr>
                
                <tr>
                    <td><li>Pagine elaborate:</td><td><em><?php echo $nr_pagine;?></em></li></td>
                </tr>
            </table>
        </ul>
        
        <p>
            <span style="font-size:small;text-align:left;">
                <input type="checkbox" name="chkEvidenzia" />
                Evidenzia i documenti mancanti
            </span>
        </p>
        
        <p>
            <input type="submit"
                   name="btnstampa" 
                   style="height:40px;width:200px"
                   value="Stampa"
                   onclick="javascript:document.getElementById('azione_stampa').value=1;"
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
        break;
  
        case 1: // visualizza le pagine con i dati trovati
            for ($nr_pagina=1;$nr_pagina<=$nr_pagine;$nr_pagina++) {
                StampaIntestazione();
                StampaDati($result);
                if ($nr_pagine>1){
                    echo "<p style='page-break-after:always;' />\n";
                }
            }
            // dà il via libera alla stampante
            echo "<script type= \"text/javascript\">";
	          echo "window.print();\n";
            echo "</script>";
        break;
    } // chiude lo switch per l'azione ($azione)
?>
</form>
<?php
//***********************************************************  
function StampaIntestazione() {
  global $nr_pagina;
  global $nr_pagine;
  global $nome_operatore;
  global $sacramento;
  global $nr_iscritti;
  global $id_gruppo;
  
?>
    <div id="intestazione">
        <div id="titolo">
            <h5 style="background:white;color:black;font-size:9.5pt;padding:0;">Parrocchia di Saint-Martin de Corl&eacute;ans</h5>
            <h1>Cresime <?php echo date('Y');?></h1>
            <h5>Verifica documenti</h5>
            <span style="font-size:x-small;color:black;"><?php echo "|".$nr_pagina."|"; ?></span>
        </div> 
        
        <div id="statistiche">
            <table>
                <tr>
                    <td colspan="2" style="text-align:center;background:#C0C0C0;color:black;font-weight:bold;padding-left:0.2em;padding-right:0.2em;">STATISTICHE</td>
                </tr>
                <tr>
                    <td>Elenco stampato da: </td>
                    <td><strong><?php echo $nome_operatore;?></strong></td>
                </tr>
                
                <tr>
                    <td>Data stampa: </td>
                    <td><strong><?php echo date('d/m/Y');?><strong></td>
                </tr>
                
                <tr>
                    <td>Orario stampa: </td>
                    <td><strong><?php echo date('G:i');?><strong></td>
                </tr>
                
                <tr>
                    <td>Pagine elaborate: </td>
                    <td><strong><?php echo $nr_pagine; ?></strong></td>
                </tr>
                  
                <tr>
                    <td>Gruppo:</td>
                    <td><strong><?php GetGruppo($id_gruppo,$sacramento,'data');?></strong>
                    </td>
                </tr>
                
                 <tr>
                    <td>Orario celebrazione:</td>
                    <td>
                        <strong><?php GetGruppo($id_gruppo,$sacramento,'ora');?></strong>
                    </td>
                </tr>
                
                <tr>
                    <td>Totale iscritti al gruppo: </td>
                    <td><strong><?php echo $nr_iscritti; ?></strong></td>
                </tr>
                
             </table>             
        </div> <!-- statistiche -->   
    </div> <!-- intestazione -->
<?php
return;
}
//***********************************************************************
function StampaDati($result) {
      global $nr_elementi_pagina;
      global $i;
      global $evidenzia;
      $elementi=0;
      
      echo "<div id='dati'>\n";
      echo "<table width='100%'>\n";
      
      while($row=mysql_fetch_object($result)) {
      $i++;
      
      echo "<tr>\n";
      echo "<th valign='top' width='5%' rowspan=13><span style='padding:0.2em;background:#C0C0C0;'>".$i."</span></th>\n";
      if ($row->Sesso=='M') {
          $natoa="Nato a ";
      } else {
          $natoa="Nata a ";
      }
      echo "<th colspan='2'><span style='font-size:x-large;line-height:100%'>".$row->Cognome." ".$row->Nome.
      "</span><br /><span style='font-variant:small-caps;font-size:small;line-height:165%;'>".$natoa.$row->Luogo_di_nascita." il ".ConvertiData($row->Data_di_nascita)."</span></th>\n";
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Data battesimo:</td>";
      if ($row->DataBattesimo!="" || $row->DataBattesimo!=null) {
          echo "<td class=\"datielencodx\">".ConvertiData($row->DataBattesimo)."</td>\n";
      } else {
          if ($evidenzia) { 
              echo "<td class=\"datielencodx\"\n>
                    <span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>
                        Manca la data del battesimo
                    </span>
                    </td>\n";
          } else {
              echo "<td class=\"datielencodx\"\n>&nbsp;</td>";
          }
      }
      echo "</tr>\n";
      
      echo "<tr>";
      echo "<td class=\"datielencosx\">Parrocchia battesimo:</td>\n";
      if ($row->ParrocchiaBattesimo!="" || $row->ParrocchiaBattesimo!=null) {
          echo "<td class=\"datielencodx\">".$row->ParrocchiaBattesimo."</td>\n";
      } else {
          if ($evidenzia) {
              echo "<td class=\"datielencodx\"\n>
                    <span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>
                        Manca la parrocchia del battesimo
                    </span></td>\n";
          } else {
                echo "<td class=\"datielencodx\"\n>&nbsp;</td>";
          }
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Indirizzo parrocchia battesimo:</td>\n";
      if ($row->IndirizzoParrocchiaBattesimo!="" || $row->IndirizzoParrocchiaBattesimo!=null) {
          echo "<td class=\"datielencodx\">".$row->IndirizzoParrocchiaBattesimo."</td>\n";
      } else {
           if ($evidenzia) {
              echo "<td class=\"datielencodx\"\n>
                    <span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>
                        Manca indirizzo parrocchia battesimo
                   </span></td>\n";
            } else {
                  echo "<td class=\"datielencodx\"\n>&nbsp;</td>";
          }
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Certificato battesimo:</td>\n";
      switch ($row->CertificatoBattesimo) {
          case 0:
               if ($evidenzia) {
                  echo "<td class=\"datielencodx\">
                      <span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>
                          Dato non disponibile
                        </span></td>\n";
                } else {
                    echo "<td class=\"datielencodx\"\n>&nbsp;</td>";
                }
          break;
          
          case 1:
              echo "<td class=\"datielencodx\">Consegnato</td>\n";
          break;
          
           case 2:
              if ($evidenzia) {
                  echo "<td class=\"datielencodx\">
                        <span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>Non consegnato</span></td>\n";
              } else {
                  echo "<td class=\"datielencodx\"\n>&nbsp;</td>";
          }
          break;
      
           case 3:
              echo "<td class=\"datielencodx\">Gi&agrave in parrocchia</td>\n";
          break;
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Padrino/Madrina:</td>\n";
      if ($row->NominativoPadrinoMadrina!="" || $row->NominativoPadrinoMadrina!=null) {
          echo "<td class=\"datielencodx\">".$row->NominativoPadrinoMadrina."</td>\n";
      } else {
          if ($evidenzia) {
              echo "<td class=\"datielencodx\"\n>
                    <span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>
                    Manca il nominativo del padrino/madrina
                    </span></td>\n";
          } else {
              echo "<td class=\"datielencodx\"\n>&nbsp;</td>";
          }
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Parrocchia padrino/madrina:</td>\n";
      if ($row->ParrocchiaPadrinoMadrina!="" || $row->ParrocchiaPadrinoMadrina!=null) {
          echo "<td class=\"datielencodx\">".$row->ParrocchiaPadrinoMadrina."</td>\n";
      } else {
          if ($evidenzia) {
              echo "<td class=\"datielencodx\"\n>
                    <span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>
                    Manca la parrocchia del padrino/madrina
                    </span></td>\n";
          } else {
              echo "<td class=\"datielencodx\"\n>&nbsp;</td>";
          }
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Certificato di idoneit&agrave;:</td>\n";
      if ($row->CertificatoIdoneita) {
          echo "<td class=\"datielencodx\">Consegnato</td>\n";
      } else {
          if ($evidenzia) {
              echo "<td class=\"datielencodx\">
              <span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>Non consegnato</span></td>\n";
          } else {
              echo "<td class=\"datielencodx\"\n>&nbsp;</td>";
          }
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Contributo:</td>\n";
      if ($row->ContributoVersato) {
          echo "<td class=\"datielencodx\">Versato</td>\n";
      } else {
          if ($evidenzia) {
              echo "<td class=\"datielencodx\"><span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>Non versato</span></td>\n";
          } else {
              echo "<td class=\"datielencodx\"\n>Non versato</td>";
          }
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Iscrizione gratuita:</td>\n";
      if ($row->IscrizioneGratuita) {
          echo "<td class=\"datielencodx\"><span style='background:#2F4F4F;color:white;padding:0em 0.4em 0.2em 0.2em;'>S&igrave;</span></td>\n";
      } else {
          echo "<td class=\"datielencodx\">No</td>\n";
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Data iscrizione:</td>\n";
      if ($row->DataIscrizione!="" || $row->DataIscrizione!=null) {
          echo "<td class=\"datielencodx\">".ConvertiData($row->DataIscrizione)."</td>\n";
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td class=\"datielencosx\">Note:</td>\n";
      if ($row->Note!="" || $row->Note!=null) {
          echo "<td class=\"datielencodx\">".$row->Note."</td>\n";
      } else {
          echo "<td class=\"datielencodx\">&nbsp;</td>\n";
      }
      echo "</tr>\n";
      
      echo "<tr>\n";
      echo "<td style='height:40px;' colspan='3'>&nbsp;</td>";
      echo "</tr>\n";
      
      $elementi++;
      if ($elementi==$nr_elementi_pagina) {
          $elementi=0;
          echo "</table>\n"; 
          echo "</div> <!-- div dati tabella -->\n";
          break;
      }
  }
      return;
}
//***********************************************************
// Recupera dati sul gruppo
function GetGruppo($id,$sacramento,$tempo) {
    if($id==0 || $id==null) {
       echo "Non disponibile";
       return;
    }
    
    $query="SELECT * FROM tblgruppisacramenti WHERE IDGruppoSacramento=".$id." AND SCR=".$sacramento;
    $result=mysql_query($query);
    
    $row=mysql_fetch_object($result);
    
    $stringa=$row->GruppoSacramento;
    
    switch ($tempo) {
        case 'data':
            if ($stringa !="" || $stringa!=null){
                echo ConvertiData($stringa);
            } else {
                $stringa="**********";
                return $stringa;
            }
        break;
        
        case 'ora':
            if ($stringa !="" || $stringa!=null){
                echo substr($stringa,11,2).":".substr($stringa,14,2);
            } else {
                $stringa="*****";
                return $stringa;
            }
        break;
    }
  return;
}
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