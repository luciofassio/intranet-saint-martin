<?php
// smiste le azioni
switch ($_POST["azione"]) {
    case "chiudi_scheda":
        ResetCampi();
    break;
    
    case "salva_scheda":
        SalvaScheda();
        $_POST["trovati"]=RicercaIscritti($_POST["hdnID"],$sacramento);
    break;
    
    case "elimina_scheda":
        EliminaScheda($_POST["hdnID"],$sacramento);
        ResetCampi();
    break;

    case "aggiungi_gruppo":
        ElaboraGruppi($_POST["azione"],$sacramento);
    break;
    
    case "aggiorna_gruppo":
        ElaboraGruppi($_POST["azione"],$sacramento);
    break;
    
    case "rimuovi_gruppo":
        ElaboraGruppi($_POST["azione"],$sacramento);
    break;
    
    case "chiudi_gruppi":
        unset($_POST["sezione"]);
    break;
    
    case "elenco":
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		    
        if (isset($_POST["chkUnisciGruppi"])) {
            $_POST["gruppo"]=-1;
            $stampa_elenco_alfabetico_gruppi="&sg=1";
        } else {
            $stampa_elenco_alfabetico_gruppi="&sg=0";
        }
        
        header("Location: http://$host$uri/report_iscritti_sacramenti.php?scr=".$sacramento."&gp=".$_POST["gruppo"].$stampa_elenco_alfabetico_gruppi);
		    exit();
    break;
    
    default:
        //controlla che l'operatore abbia scelto un candidato (presente in anagrafica...)
        if (!isset($_POST["hdnID"])) {
            $_POST["hdnID"]=0;
        }
        $_POST["trovati"]=RicercaIscritti($_POST["hdnID"],$sacramento);    
    break;
}


//************************ SEZIONE FUNZIONI ********************************
// Ricerca Iscritti
function RicercaIscritti($ID,$sacramento) {
    // va a prendere nella tabella catechismo il nominativo dell'ID passato
    $query="SELECT Cognome,Nome 
            FROM Catechismi 
            WHERE ID=".$ID;
     
            
    $result=mysql_query($query);
    
    $row=mysql_fetch_object($result);
    $_POST["txtCognome"]=htmlentities($row->Cognome);
    $_POST["txtNome"]=htmlentities($row->Nome);
    
    // legge i dati dalla scheda in base all'ID passato
    $query="SELECT * 
            FROM tblsacramenti 
            WHERE tblsacramenti.ID=".$ID." 
            AND tblsacramenti.SCR=".$sacramento;
    
    // il risultato
    $result=mysql_query($query);
    
    if ($result) {
        $row=mysql_fetch_object($result);
        $_POST["data_battesimo"]=ConvertiData($row->DataBattesimo);
        $_POST["parrocchia_battesimo"]=htmlentities($row->ParrocchiaBattesimo);
        //$_POST["hdnIdParrocchia"]=$row->IDParrocchiaBattesimo;
        $_POST["indirizzo_parrocchia_battesimo"]=htmlentities($row->IndirizzoParrocchiaBattesimo);
        $_POST["optBattesimo"]=$row->CertificatoBattesimo;
        $_POST["nome_padrino"]=htmlentities($row->NominativoPadrinoMadrina);
        $_POST["parrocchia_padrino"]=htmlentities($row->ParrocchiaPadrinoMadrina);
        $_POST["chkCI"]=$row->CertificatoIdoneita;
        if ($row->IdGruppo==null) {
            $_POST["gruppo"]=0;
        } else {
            $_POST["gruppo"]=$row->IdGruppo;
        }
        
        $_POST["chkContributo"]=$row->ContributoVersato;
        $_POST["chkIscrizione"]=$row->IscrizioneGratuita;
        $_POST["data_iscrizione"]=ConvertiData($row->DataIscrizione);
        $_POST["note"]=htmlentities($row->Note);
        
        $trovati=mysql_num_rows($result);
    } else {
        $trovati=0;
    }
    
    //ritorna il numero delle ricorrenze trovate
    return $trovati;
}

//**************************************
// Get data e ora di celebrazione del sacramento
function GetDataSacramento($sezione){
  global $sacramento;
  $query="SELECT * FROM tblgruppisacramenti WHERE YEAR(GruppoSacramento)=".date("Y")." AND SCR=".$sacramento; //.$_GET["scr"];
  
  $result=mysql_query($query);
  if ($sezione=="scheda") {
      echo "<option value='0'>******************</option>";
  }

  while ($row=mysql_fetch_object($result)){
      $data=(substr($row->GruppoSacramento,0,10));
      $ora=(substr($row->GruppoSacramento,11,5));
      if ($row->IDGruppoSacramento==$_POST["gruppo"]) {
          echo "<option value='".$row->IDGruppoSacramento."' selected>".ConvertiData($data)."&nbsp;(".$ora.")</option>";
      } else {
          echo "<option value='".$row->IDGruppoSacramento."'>".ConvertiData($data)."&nbsp;(".$ora.")</option>";
      }
  }
}

//**************************************
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

//**************************************
function SalvaScheda() {
    //costruisce la query da inviare a Mysql in base ai campi compilati
    switch ($_POST["trovati"]){
        case 0: // insert
            $fields="ID";
            $values=$_POST['hdnID'];
            
            // parrocchia battesimo
            if ($_POST["parrocchia_battesimo"]!="" || $_POST["parrocchia_battesimo"]!=null) {
                $fields.=",ParrocchiaBattesimo";
                $values.=",'".$_POST['parrocchia_battesimo']."'";
            }
            
            //data battesimo
            if ($_POST["data_battesimo"]!="" || $_POST["data_battesimo"]!=null) {
                $fields.=",DataBattesimo";
                $values.=",'".ConvertiData($_POST['data_battesimo'])."'";
            }
            
            //indirizzo parrocchia battesimo
            if ($_POST["indirizzo_parrocchia_battesimo"]!="" || $_POST["indirizzo_parrocchia_battesimo"]!=null) {
                $fields.=",IndirizzoParrocchiaBattesimo";
                $values.=",'".addslashes($_POST['indirizzo_parrocchia_battesimo'])."'";
            }
            
            //consegna certificato battesimo
            if ($_POST["optBattesimo"]) {
                $fields.=",CertificatoBattesimo";
                $values.=",".$_POST["optBattesimo"];
            }
            
            //nominativo padrino
            if ($_POST["nome_padrino"]!="" || $_POST["nome_padrino"]!=null) {
                $fields.=",NominativoPadrinoMadrina";
                $values.=",'".$_POST['nome_padrino']."'";
            }
            
            // parrocchia padrino madrina
            if ($_POST["parrocchia_padrino"]!="" || $_POST["parrocchia_padrino"]!=null) {
                $fields.=",ParrocchiaPadrinoMadrina";
                $values.=",'".addslashes($_POST['parrocchia_padrino'])."'";
            }
            
            //consegna certificato idoneità
            if ($_POST["chkCI"]) {
                $fields.=",CertificatoIdoneita";
                $values.=",true";
            } else {
                $fields.=",CertificatoIdoneita";
                $values.=",false";
            }
            
            //contributo versato
            if ($_POST["chkContributo"]) {
                $fields.=",ContributoVersato";
                $values.=",true";
            } else {
                $fields.=",ContributoVersato";
                $values.=",false";
            }
            
            //iscrizione gratuita
            if ($_POST["chkIscrizione"]) {
                $fields.=",IscrizioneGratuita";
                $values.=",true";
            } else {
                $fields.=",IscrizioneGratuita";
                $values.=",false";
            }

            //data iscrizione
            if ($_POST["data_iscrizione"]!="" || $_POST["data_iscrizione"]!=null) {
                $fields.=",DataIscrizione";
                $values.=",'".ConvertiData($_POST['data_iscrizione'])."'";
            }
            
            // SCR (tipo di sacramento)
                $fields.=",SCR";
                $values.=",".$_GET["scr"];
            
            // gruppo 
                $fields.=",IdGruppo";
                $values.=",".$_POST['gruppo'];

              // note
            if ($_POST["note"]!="" || $_POST["note"]!=null) {
                $fields.=",Note";
                $values.=",'".addslashes($_POST['note'])."'";
            }
              
            // costruisce la query da inviare
            $query="INSERT INTO tblsacramenti ($fields) VALUES ($values)";
        break;
        
        case 1:
            $query="UPDATE tblsacramenti SET ";
            
            //data battesimo
            if ($_POST["data_battesimo"]!="" || $_POST["data_battesimo"]!=null) {
                $fields.="DataBattesimo='".ConvertiData($_POST['data_battesimo'])."'";
            } else {
                $fields.="DataBattesimo=null";
            }
            
            // parrocchia battesimo
            if ($_POST["parrocchia_battesimo"]!="" || $_POST["parrocchia_battesimo"]!=null) {
                  $fields.=",ParrocchiaBattesimo='".addslashes($_POST['parrocchia_battesimo'])."'";
            } else {
                  $fields.=",ParrocchiaBattesimo=null";
            }
            
             //indirizzo parrocchia battesimo
            if ($_POST["indirizzo_parrocchia_battesimo"]!="" || $_POST["indirizzo_parrocchia_battesimo"]!=null) {
                $fields.=",IndirizzoParrocchiaBattesimo='".addslashes($_POST['indirizzo_parrocchia_battesimo'])."'";
            } else {
                  $fields.=",IndirizzoParrocchiaBattesimo=null";
            }
            
            //consegna certificato battesimo
            if ($_POST["optBattesimo"]) {
                $fields.=",CertificatoBattesimo=".$_POST["optBattesimo"];
            } else {
                $fields.=",CertificatoBattesimo=2";
            }
            
            //nominativo padrino
            if ($_POST["nome_padrino"]!="" || $_POST["nome_padrino"]!=null) {
                $fields.=",NominativoPadrinoMadrina='".addslashes($_POST['nome_padrino'])."'";
            } else {
                $fields.=",NominativoPadrinoMadrina=null";          
            }
            
            // parrocchia padrino madrina
            if ($_POST["parrocchia_padrino"]!="" || $_POST["parrocchia_padrino"]!=null) {
                $fields.=",ParrocchiaPadrinoMadrina='".addslashes($_POST['parrocchia_padrino'])."'";
            } else {
                $fields.=",ParrocchiaPadrinoMadrina=null";
            }
            
            //consegna certificato idoneità
            if ($_POST["chkCI"]) {
                $fields.=",CertificatoIdoneita=true";
            } else {
                $fields.=",CertificatoIdoneita=false";
            }
            
            // gruppo 
            $fields.=",IdGruppo=".$_POST['gruppo'];

            //contributo versato
            if ($_POST["chkContributo"]) {
                $fields.=",ContributoVersato=true";
            } else {
                $fields.=",ContributoVersato=false";
            }
            
            //iscrizione gratuita
            if ($_POST["chkIscrizione"]) {
                $fields.=",IscrizioneGratuita=true";
            } else {
                $fields.=",IscrizioneGratuita=false";
            }

            //data iscrizione
            if ($_POST["data_iscrizione"]!="" || $_POST["data_iscrizione"]!=null) {
                $fields.=",DataIscrizione='".ConvertiData($_POST['data_iscrizione'])."'";
            } else {
                $fields.=",DataIscrizione=null";
            }
            
            // non serve aggiornare il campo SCR (tipo di sacramento) perché rimane fisso sulla scheda
            
            // note
            if ($_POST["note"]!="" || $_POST["note"]!=null) {
                $fields.=",Note='".addslashes($_POST['note'])."'";
            } else {
                $fields.=",Note=null";
            }
            
            //completa la query da inviare a Mysql
            $query.=$fields." WHERE ID=".$_POST['hdnID']." AND SCR=".$_GET["scr"];

        break;
    } 
    
    // manda la query costruita a Mysql (della serie io speriamo che me la cavo...)
    mysql_query("START TRANSACTION");
    $result=mysql_query($query) || die("Ops! Ho avuto un problema con questa query -> ".$query);
    mysql_query("COMMIT");

    return;
}
//**************************************
// Reset campi
function ResetCampi(){
  unset($_POST["hdnID"]);
  unset($_POST["data_battesimo"]);
  unset($_POST["parrocchia_battesimo"]);
  unset($_POST['indirizzo_parrocchia_battesimo']);
  unset($_POST['chkCB']); // certificato battesimo
  unset($_POST["nome_padrino"]);
  unset($_POST["parrocchia_padrino"]);
  unset($_POST["chkCI"]); //certificato di idoneità
  unset($_POST['gruppo']);
  unset($_POST["chkContributo"]);
  unset($_POST["chkIscrizione"]);
  unset($_POST["data_iscrizione"]);
  unset($_POST["note"]);
  unset($_POST["sezione"]);
  
  return;
}
//**************************************
function EliminaScheda($id,$scr) {
  
  $query="DELETE FROM tblsacramenti WHERE ID=".$id." AND SCR=".$scr;
  
  mysql_query("START TRANSACTION");
  $result=mysql_query($query);
  mysql_query("COMMIT");
  
  return;
}

//**************************************
function ElaboraGruppi($azione,$sacramento) {
    $gruppo_sacramento=ConvertiData($_POST["data_gruppo"])." ".$_POST["ora_gruppo"].":00";
    $id_gruppo=$_POST["lista_gruppi"];
    
    switch ($azione) {
        case 'aggiungi_gruppo':
            //cerca omonimi
            $query="SELECT * FROM tblgruppisacramenti
                    WHERE GruppoSacramento='".$gruppo_sacramento."' 
                    AND SCR=".$sacramento;
            
            $result=mysql_query($query);

            if (mysql_num_rows($result)>0){
                unset ($_POST["ora_gruppo"]);
                unset ($_POST["data_gruppo"]);
                return;
            }
            
            // non ho trovato nulla, procedo con la costruzione della nuova query di inserimento
            $query ="INSERT INTO tblgruppisacramenti 
                    (SCR,GruppoSacramento)
                    VALUES 
                    ($sacramento,'$gruppo_sacramento')";
        break;
        
        case 'aggiorna_gruppo':
            $query="UPDATE tblgruppisacramenti 
                    SET GruppoSacramento='".$gruppo_sacramento."' 
                    WHERE IDGruppoSacramento=".$id_gruppo." 
                    AND SCR=".$sacramento;
        break;
        
        case 'rimuovi_gruppo':
            $query_tblsacramenti="UPDATE tblsacramenti SET IdGruppo=0
                                  WHERE IdGruppo=".$id_gruppo."
                                  AND SCR=".$sacramento;
        
            $query="DELETE FROM tblgruppisacramenti
                    WHERE IDGruppoSacramento=".$id_gruppo;
        break;
    }
    
    mysql_query("START TRANSACTION");
    
    // aggiorna il campo gruppo a chi appartiene al gruppo che viene cancellato
    if ($azione=="rimuovi_gruppo") {
        $result=mysql_query($query_tblsacramenti);
    }
    
    $result=mysql_query($query);
        
     mysql_query("COMMIT");
    return; 
}
?>

