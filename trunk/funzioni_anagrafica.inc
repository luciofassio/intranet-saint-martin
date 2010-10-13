<?php
      //connessione al db
      ConnettiDb();
     
     // variabili globali di servizio
     global $errore;
     global $messaggio;
     global $icona;
     
     // smista le funzioni in base alle azioni richiamate dall'utente
     switch ($_POST["azione_salvataggio"]) {
        case "anagrafica":
            SalvaScheda();
        break;
        
        case "chiudi_iscritto":
            // serve per controllare se i dati sono stati variati rispetto al db. In caso affermativo li salva
            if (ControllaScheda($variazione)) { 
                $errore=0;
                SalvaScheda();
                if ($errore == 0) {
                    $messaggio="Le modifiche a $variazione sono state salvate con successo";
                    $errore=1;
                    $icona=1;
                }
            }
            unset($_POST["hdnID"]);
            unset($_POST["hdnIDParente"]);
            unset($_POST["txtCognome"]);
        break;
        
        case "delete_parente":
            CancellaDatiParentela();
        break;
        
        case "delete_rubrica":
            DeleteDatiRubrica(); 
        break;
        
        case "delete_scheda":
            DeleteDatiScheda();
            
            unset($_POST["azione_salvataggio"]);
            unset($_POST["hdnID"]);
            unset($_POST["hdnIDParente"]);
            unset($_POST["txtCognome"]);
        break;
        
        case "parentela":
            $errore =SalvaDatiParentela($errore);
            
            switch ($errore) {
                case 1:
                    $messaggio="Il familiare selezionato &egrave; gi&agrave; associato";
                    $icona=2; 
                break;
                
                case 2:
                    $messaggio="Salvataggio annullato! Incongruenza dati trasmessi al server";
                    $icona=2; 
                break;
                
                case 3:
                    $messaggio="Salvataggio annullato! Problemi con la query inviata al server";
                    $icona=2; 
                break;
            }

        break;
        
        case "salva_rubrica":
            SalvaDatiRubrica(); 
        break;

        default:
            $errore=0;
        break;
     } 
    
    RicercaIscritti();
    
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


/*********************************************************************************
* FUNZIONE PER IL SALVATAGGIO DEI DATI NEL DATABASE           
* TABELLA 'CATECHISMI'                             
* versione 1.3 - 20/07/09: risolto problema di chiusura scheda nel salvataggio dati
* versione 1.4 - 15/09/09: trasformata routine in una function  
* versione 1.5 - 11/10/09: modificata procedura e controllo delle date di tesseramento
* versione 1.6 - 30/08/10: aggiunto comandi per evitare l'sql injection (addslashes)                       
**********************************************************************************/
 function SalvaScheda() {
       // variabili di servizio
       global $errore,$messaggio,$icona;
       
       // imposta le variabili con i valori dei campi 
        $scheda_id=$_POST["hdnID"];
        $scheda_cognome=addslashes($_POST["cognome"]);
        $scheda_nome=addslashes($_POST["nome"]);
        $scheda_sesso=$_POST["sesso"];
        $scheda_dataN=$_POST["data_nascita"];
        $scheda_luogoN=addslashes($_POST["natoa"]);
        $scheda_tipo_via=$_POST["stradario"];
        $scheda_via=addslashes($_POST["indirizzo"]);
        $scheda_numero_civico=addslashes($_POST["numero"]);
        $scheda_comune=addslashes($_POST["comune"]);
        $scheda_cap=$_POST["cap"];
        $scheda_provincia=addslashes($_POST["prov"]);
        $scheda_email=addslashes($_POST["myemail"]);
        $scheda_parrocchia=addslashes($_POST["hdnIdParrocchia"]);
        $scheda_spedizione=$_POST["chkspedizione"];
        $scheda_tipo_quota=$_POST["myquota"];
        $scheda_myaltraquota=$_POST["myaltraquota"];
        $scheda_dataT=$_POST["mydataT"];
        $scheda_dataST=$_POST["mydataST"]; // data di scadenza della tessera
        $scheda_classe=$_POST["myclassi"];
        $scheda_sezione=$_POST["mysezione"];
        $scheda_partecipazione=$_POST["optPartecipa"];
        $scheda_coro=$_POST["chkCoro"];
        $scheda_ruolo=$_POST["myruolo"];
        
        // controlla i dati provenienti dai checkbox
       if ($scheda_coro) {
            $scheda_coro="True";
        } else {
            $scheda_coro="False";
        }
        
        if ($scheda_partecipazione=="") {
            $scheda_partecipazione=2; //non partecipa alle attività dell'oratorio
        }
        
        //trasforma la data in formato inglese per Mysql
        // data di nascita
        if ($scheda_dataN != "" || $scheda_dataN != null) { 
            $scheda_dataN=substr($scheda_dataN,6,4)."-".substr($scheda_dataN,3,2)."-".substr($scheda_dataN,0,2);
        }
        
        //Controlla e calcola la DATA DI TESSERAMENTO e la DATA DI SCADENZA della tessera
        $timestamp_primo_settembre=mktime(0,0,0,8,31,date('Y')); 
       
        if ($scheda_dataT != "" || $scheda_dataT != null) {
            if ($scheda_dataT=="Oggi") {
                $scheda_dataT=date('Y-m-d');
                $timestamp_data_tesseramento=mktime(0,0,0,date('m'),date('d'),date('Y'));
            } else {
                
                if ((int)substr($scheda_dataT,6,4) != (int)date('Y')) { //blocca il tentativo di tesserare fuori dall'anno corrente 
                    $errore=1;
                    $messaggio="Attenzione! Controlla la data di tesseramento: l'anno che hai inserito potrebbe non essere valido";
                    $icona=2;
                    return;
                }
                    $timestamp_data_tesseramento=mktime(0,0,0,(int)substr($scheda_dataT,3,2),(int)substr($scheda_dataT,0,2),(int)substr($scheda_dataT,6,4));
                    $scheda_dataT=substr($scheda_dataT,6,4)."-".substr($scheda_dataT,3,2)."-".substr($scheda_dataT,0,2);
            }
            
            //controlla che il tesseramento non sia stato fatto dopo il primo settembre (la tessera scadrebbe a dicembre dell'anno successivo)
            if ($timestamp_data_tesseramento > $timestamp_primo_settembre) { //siamo dopo il primo settembre aumenta di 1 anno
                $scheda_dataST=(date('Y')+1)."-12-31";
            } else { // siamo prima del primo settembre
                  $scheda_dataST=date('Y')."-12-31";  
            }
        }
        
        // controlla se i dati sono di un nuovo iscritto o da aggiornare e costruisce le query
        if ($scheda_id != 0 || $scheda_id !=null){
            $query = "UPDATE catechismi SET Cognome='".$scheda_cognome."', Nome='".$scheda_nome."', Sesso='".$scheda_sesso."'";
                 
                if ($scheda_dataN != "" || $scheda_dataN != null) { 
                    $query.=", Data_di_nascita ='".$scheda_dataN."'";
                } else {
                     $query.=", Data_di_nascita =null";
                }
                         
                if ($scheda_luogoN != "" || $scheda_luogoN != null) {
                    $query.=", Luogo_di_nascita='".$scheda_luogoN."'";
                } else {
                    $query.=", Luogo_di_nascita=null";
                }
                        
                if ($scheda_tipo_via != "" || $scheda_tipo_via != null) {
                    $query.=", Tipo_via='".$scheda_tipo_via."'";
                } else {
                    $query.=", Tipo_via=null";
                }
                            
                if ($scheda_via != "" || $scheda_via != null) {
                    $query.=", Via='".$scheda_via."'";
                } else {
                    $query.=", Via=null";
                }

                if ($scheda_numero_civico != "" || $scheda_numero_civico != null) {
                    $query.=", Numero_civico='".$scheda_numero_civico."'";
                } else {
                    $query.=", Numero_civico=null";
                }
                
                if ($scheda_comune != "" || $scheda_comune != null) {
                    $query.=", Citt='".$scheda_comune."'";
                } else {
                    $query.=", Citt=null";
                }
                          
                if ($scheda_cap != "" || $scheda_cap != null) {
                    $query.=", Cap='".$scheda_cap."'";
                } else {
                    $query.=", Cap=null";
                }

                if ($scheda_provincia != "" || $scheda_provincia != null) {
                    $query.=", Provincia='".$scheda_provincia."'";
                } else {
                    $query.=", Provincia=null";
                }
 
                if ($scheda_email != "" || $scheda_email != null) {
                    $query.=", Email='".$scheda_email."'";
                } else {
                    $query.=", Email=null";
                }
                       
                if ($scheda_parrocchia != "" || $scheda_parrocchia != null) {
                    $query.=", Parrocchia_Provenienza=".$scheda_parrocchia;
                } else {
                    $query.=", Parrocchia_Provenienza=null";
                }

                // calcola la quota versata
                if ($scheda_tipo_quota != 0 && $scheda_dataT != null) {
                    switch ($scheda_tipo_quota) {
                        case 3:
                            $query.=", TipoQuota=3";
                            $query.=", QuotaOratorio=0.00";
                        break;
                        
                        case 4:
                            $query.=", TipoQuota=4";
                            if ($scheda_myaltraquota!=null) {
                                $query.=", QuotaOratorio=".str_replace(",",".",$scheda_myaltraquota);
                            } else {
                                $query.=", TipoQuota=5";
                                $query.=", QuotaOratorio=0.00";
                            }
                        break;
                        
                        case 5:
                            $query.=", TipoQuota=5";
                            $query.=", QuotaOratorio=0.00";
                        break;
                        
                        default:
                            $query.=", TipoQuota=".$scheda_tipo_quota;
                            $query.=", QuotaOratorio=".GetQuota($scheda_tipo_quota);
                        break;
                    }
                } else {
                    $query.=", TipoQuota=5";
                    $query.=", QuotaOratorio='0.00'";
                }
                
                if ($scheda_dataT != "" || $scheda_dataT != null) {
                    $query.=", DataTesseramento='".$scheda_dataT."'";
                } else {
                    $query.=", DataTesseramento=null";
                }
                
                if ($scheda_dataST != "" || $scheda_dataST != null) {
                    $query.=", DataScadenzaTessera='".$scheda_dataST."'";
                } else {
                    $query.=", DataScadenzaTessera=null";
                }
 
               $query.=", Classe=".$scheda_classe.", Sezione=".$scheda_sezione.", Presenza=".$scheda_partecipazione.", Coro='".$scheda_coro."'".", IdRuolo=".$scheda_ruolo;
               $query.=" WHERE id=".$scheda_id;
        } else {
            // prima di salvare controlla se c'è una omonimia sui campi cognome, nome, sesso e data di nascita
            $query="SELECT Cognome,Nome,Sesso,Data_di_nascita FROM catechismi WHERE Cognome='".$scheda_cognome."' and Nome='".$scheda_nome."' and Sesso='".$scheda_sesso."'";
            $result=mysql_query($query);
            if (mysql_num_rows($result)>0) { // se trova un'omonimia la confronta con le date di nascita
                while ($row=mysql_fetch_array($result))
                {
                  if (substr($row["Data_di_nascita"],0,10)==$scheda_dataN) {
                      echo ("<script type=\"text/javascript\">\n");
                      echo ("alert(\"Attenzione! I dati di $scheda_nome $scheda_cognome sono gia' presenti in archivio. Inserimento fallito!\");\n");
                      echo ("</script>\n");
                      $omonimia=true;
                  } else {
                      echo ("<script type=\"text/javascript\">\n");
                      if ($scheda_dataN=="") { // avvisa che ha trovato delle omonimie ma che non è riuscito a validarle perché la data di nascita non è stata inserita
                          echo ("alert(\"Attenzione! E' stato rilevato un problema di omonimia e non e' stato possibile fare un confronto con la data di nascita poiche' il campo non e' stato compilato. I dati inseriti non sono stati salvati!\");\n");
                          $omonimia=true;
                      } else {
                          $omonimia=false;
                      }
                      echo ("</script>\n");
                  }
                }
            } else {
                  $omonimia=false;
              }
            
            if (!$omonimia) {
               // costruisce la query in base alla presenza dei dati
               $query="INSERT INTO catechismi (Cognome,Nome,Sesso";
               $value="VALUES ('$scheda_cognome','$scheda_nome','$scheda_sesso'";
            
                if ($scheda_dataN !="") {
                  $insert.=",Data_di_nascita";
                  $value.=",'$scheda_dataN'";
                }
                    
                if ($scheda_luogoN !="") {
                  $insert.=",Luogo_di_nascita";
                  $value.=",'$scheda_luogoN'";
                }
            
                if ($scheda_tipo_via !="") {
                  $insert.=",Tipo_via";
                  $value.=",'$scheda_tipo_via'";
                }
            
                if ($scheda_via !="") {
                  $insert.=",Via";
                  $value.=",'$scheda_via'";
                }
        
                if ($scheda_numero_civico !="") {
                  $insert.=",Numero_civico";
                  $value.=",'$scheda_numero_civico'";
                }        
        
                if ($scheda_comune !="") {
                  $insert.=",Citt";
                  $value.=",'$scheda_comune'";
                }
        
                if ($scheda_cap !="") {
                  $insert.=",Cap";
                  $value.=",'$scheda_cap'";
                }
            
                if ($scheda_provincia !="") {
                  $insert.=",Provincia";
                  $value.=",'$scheda_provincia'";
                }
            
                if ($scheda_email !="") {
                  $insert.=",Email";
                  $value.=",'$scheda_email'";
                }
            
                if ($scheda_parrocchia !="") {
                  $insert.=",Parrocchia_Provenienza";
                  $value.=",$scheda_parrocchia";
                }
        
                if ($scheda_quota !="") {
                  $insert.=",QuotaOratorio";
                  $value.=",".str_replace(",",".",$scheda_quota);
                }
            
                if ($scheda_tipo_quota !="") {
                  $insert.=",TipoQuota";
                  $value.=",'$scheda_tipo_quota'";
                }
                
                if ($scheda_dataT !="") {
                  $insert.=",DataTesseramento";
                  $value.=",'$scheda_dataT'";
                }
                
                if ($scheda_dataST !="") {
                  $insert.=",DataScadenzaTessera";
                  $value.=",'$scheda_dataST'";
                }
                
                
                if ($scheda_classe >0) {
                  $insert.=",Classe";
                  $value.=",$scheda_classe";
                }
        
                if ($scheda_sezione >0) {
                  $insert.=",Sezione";
                  $value.=",$scheda_sezione";
                }
            
                if ($scheda_partecipazione !="") {
                  $insert.=",Presenza";
                  $value.=",$scheda_partecipazione";
                }
            
                if ($scheda_coro !="") {
                  $insert.=",Coro";
                  $value.=",'$scheda_coro'";
                }  
                
                if ($scheda_ruolo > 0) {
                  $insert.=",IdRuolo";
                  $value.=", $scheda_ruolo";
                }
                
                $query.=$insert.") ".$value.")";
          }
      }
   
    // va a salvare (finalmente) nel database  
    $result=mysql_query($query) || die($query);
    if (mysql_affected_rows() < 0) {
        echo '<script type="text/javascript">';
        echo 'alert("Acc..! Qualcosa è andato storto durante le operazioni di salvataggio dei dati. Riprovare, se il problema persiste contattare gli amministratori del sistema.");';
        echo '</script>';
        
        // serve per ricostruire la pagina dopo l'errore: se si tratta di aggiornamento riapre i dati dell'iscritto selezionato
        // al contrario apre la pagina pulita (ma si può anche modificare...)
        if ($scheda_id!=0 || $scheda_id !=null) {
            $_POST["txtCognome"]=$scheda_cognome;
            $_POST["hdnId"]=$scheda_id;
        } else {
            unset($_POST["txtCognome"]);
            unset($_POST["hdnID"]);
        } 
    }   
  // controllo inserito per non chiudere la scheda dell'iscritto quando vengono salvati i suoi dati
    if ($_POST["txtCognome"]=="" || $_POST["hdnID"]=="") {
        $_POST["txtCognome"]=$scheda_cognome;
        $_POST["txtNome"]=$scheda_nome;
        $_POST["hdnID"]=$scheda_id;
      
  } else {
        unset($_POST["txtCognome"]);
        unset($_POST["hdnID"]);
  }
  
  unset($_POST["azione_salvataggio"]);
  return;
}  
/**********************************************************************************
 * CANCELLA DATI SCHEDA
 * (pone allo stato di cancellato i dati dell'iscritto
 * e segna il giorno di cancellazione e chi ha cancellato) */

function DeleteDatiScheda() {
    global $errore,$messaggio,$icona;
    
    $operatore=$_POST["login"]; // legge l'user id dell'operatore

    $data_cancellazione=date('Y-m-d'); // setta la data di cancellazione
    
    $query="UPDATE catechismi SET cancellato='True',DataCancellazione='$data_cancellazione',OperatoreCancellazione='$operatore' WHERE ID=".$_POST["hdnID"];
    $result=mysql_query($query);

    $errore=1;
    $messaggio="I dati di ".$_POST["cognome"]." ". $_POST["nome"]." sono stati cestinati!";
    $icona=1;  
    return;
}

//**********************************************************************************
// RICERCA DEGLI ISCRITTI NELL'ANAGRAFICA DELL'ORATORIO        
// TABELLA 'CATECHISMI'                             
//**********************************************************************************
function RicercaIscritti() {
    global $errore,$messaggio,$icona;
    
    // costruisce il primo pezzo di query da inviare a MySql
    $query ="SELECT ID,cognome,nome,Data_di_nascita,luogo_di_nascita,sesso,tipo_via,via,numero_civico,citt as citta, cap,provincia,citt,email,datatesseramento,quotaoratorio,TipoQuota,classe,sezione,presenza,coro,parrocchia_provenienza,DataScadenzaTessera,IdRuolo FROM Catechismi ";

    // controlla il tipo di ricerca scelto dall'utente: se con ajax o barcode (quindi con ID)
    // oppure con il classico nome e cognome in modo da completare la query
    if ($_POST["hdnID"]!="") { 
        // controlla se l'id inviato è formato solo da numeri
        if (!ereg("^[0-9]+$",$_POST["hdnID"])) {
            $errore=1;
            $messaggio="Il codice a barre inserito non &egrave; compatibile!";
            $icona=2;
            unset ($_POST["hdnID"]);
            return;
        } else {
            $query.="WHERE ID=".$_POST["hdnID"]." AND cancellato='False'";
            $parola_chiave=$_POST["hdnID"];
        }
    } elseif ($_POST["txtCognome"]!=""){
        $query.="WHERE cognome LIKE'".$_POST["txtCognome"]."%' AND nome LIKE'".$_POST["txtNome"]."%' AND cancellato='False'";
        $parola_chiave=$_POST["txtNome"]." ".$_POST["txtCognome"];
    } else {
        PulisciCampi();
        return;
    }
    
    // invia la query a MySql
    $rstRicerca=mysql_query($query);
  
    // verifica quante corrispondenze ha trovato
    if (mysql_num_rows($rstRicerca)<1) { // nessuna corrispondenza trovata
        $errore=1;
        $icona=2;
        $messaggio="Nessuna corrispondenza &egrave; stata trovata con la parola chiave '".$parola_chiave."'";
        unset ($_POST["hdnID"]);
    } elseif (mysql_num_rows($rstRicerca)>1) { // più di una corrispondenza trovata
          $errore=1;
          $icona=2;
          $messaggio="Sono state trovate ".mysql_num_rows($rstRicerca)." corrispondenze con la parola chiave '".$parola_chiave."'";
          unset ($_POST["hdnID"]);
    } else {
          $record=mysql_fetch_object($rstRicerca);
          
          $_POST["hdnID"]=($record->ID);
          $_POST["cognome"]=htmlentities($record->cognome);
          $_POST["nome"]=htmlentities($record->nome);
          $_POST["sesso"]=($record->sesso);
          $_POST["data_nascita"]=($record->Data_di_nascita);
          $_POST["natoa"]=htmlentities($record->luogo_di_nascita);
          $_POST["stradario"]=htmlentities($record->tipo_via);
          $_POST["indirizzo"]=htmlentities($record->via);
          $_POST["numero"]=($record->numero_civico);
          $_POST["comune"]=htmlentities($record->citta);
          $_POST["cap"]=($record->cap);
          $_POST["prov"]=($record->provincia);
          $_POST["myemail"]=htmlentities($record->email);
          $_POST["myquota"]=($record->TipoQuota);
          $_POST["myaltraquota"]=($record->quotaoratorio);        
          $_POST["mydataT"]=($record->datatesseramento);
          $_POST["mydataST"]=($record->DataScadenzaTessera);
          $_POST["myclassi"]=($record->classe);
          $_POST["mysezione"]=($record->sezione);
          $_POST["optPartecipa"]=($record->presenza);
          $_POST["chkCoro"]=($record->coro);
          $_POST['hdnIdParrocchia']=($record->parrocchia_provenienza);
          $_POST['myruolo']=($record->IdRuolo);
    }

    return;
}

//*********************> RIPRISTINA I VALORI INIZIALI DI TUTTI I CAMPI  <***************
function PulisciCampi(){
    unset($_POST["hdnID"]);
    unset($_POST["cognome"]);
    unset($_POST["nome"]);
    unset($_POST["sesso"]);
    unset($_POST["data_nascita"]);
    unset($_POST["natoa"]);
    unset($_POST["stradario"]);
    unset($_POST["indirizzo"]);
    unset($_POST["numero"]);
    unset($_POST["comune"]);
    unset($_POST["cap"]);
    unset($_POST["prov"]);
    unset($_POST["myemail"]);
    unset($_POST["myquota"]); 
    unset($_POST["myaltraquota"]);        
    unset($_POST["mydataT"]);
    unset($_POST["mydataST"]);
    unset($_POST["myclassi"]);
    unset($_POST["mysezione"]);
    unset($_POST["optPartecipa"]);
    unset($_POST["chkCoro"]);
    unset($_POST['hdnIdParrocchia']);
    unset($_POST["hdnIDParente"]);
    unset($_POST["myruolo"]);
    unset($_POST["azione_salvataggio"]);
return;
}    

// *************** > FUNZIONE RECUPERA IL LIVELLO DI PRIVILEGI PER UN DATO OPERATORE < ***********
function GetRuoliOratorio($ruolo) {
  // setta la stringa $query e estrae i dati
  $query="SELECT IdRuoloOratorio,RuoloOratorio FROM tblruolioratorio";
  $rstRuoli=mysql_query($query);
              
  // popola la lista con i dati estratti dal database
  while ($ruoli=mysql_fetch_array($rstRuoli)) {
      if ($ruoli["IdRuoloOratorio"]==$ruolo) {
          echo ("<option value='".$ruoli["IdRuoloOratorio"]."' selected>".htmlentities($ruoli["RuoloOratorio"])."</option>\n");
      } else {
          echo ("<option value='".$ruoli["IdRuoloOratorio"]."'>".htmlentities($ruoli["RuoloOratorio"])."</option>\n");
      }
  }
          
  return;
}

//*********************> RECUPERA LA QUOTA VERSATA ALL'ORATORIO  <***************
function GetQuota ($scheda_tipo_quota) {
    
    //recupera le quota dal database
    $query="SELECT * FROM tblquoteoratorio WHERE IdQuota=".$scheda_tipo_quota;
    
    $result=mysql_query($query);
    
    if ($result) {
        $quota=mysql_fetch_object($result);
        $myquota=htmlentities($quota->Quota);
    }

return $myquota;
}

//*********************> COSTRUISCE LA TABELLA DELLE QUOTE TESSERAMENTI  <***************
function GetQuoteOratorio() {
    // cerca il tipo di quota associata all'iscritto
    // controllando che non sia una nuova iscrizione
    if ($_POST["hdnID"]!=null) { 
        if ($_POST["myquota"]!=null){
            $tipo_quota=(int)$_POST["myquota"];
        } else {
            $tipo_quota=5; // non pagata
        }
    } else {
        $tipo_quota=5; // non pagata;
    }
    
    //recupera le quote predefinite nel database
    $query="SELECT * FROM tblquoteoratorio";
    
    $result=mysql_query($query);
    
    if ($result) {
        while ($quote=mysql_fetch_object($result)) {
            echo("<tr>");
            
            // serve per tenere selezionato l'opzione di versamento 'gratuito' e 'non pagata'
            if (($quote->IdQuota==3 && $tipo_quota==3) || ($quote->IdQuota==5 && $tipo_quota==5)){
                $checked="checked";
            } else {
                  $checked=null;
            }
            
            // stampa il pulsante radio            
            echo("<th><input type=\"radio\" name =\"myquota\" id=\"myquota\" value=\"".($quote->IdQuota)."\" onClick=\"AbilitaAltraQuota();\" ".$checked." /></th>");
               
            switch ($quote->Quota) {
                case -1: // altro
                    echo ("<th class =\"quota\">Altro</th>");
                    echo("<th>");
                    if ($checked=="checked") {
                        $disabled="enabled";
                    } else {
                          $disabled="disabled";
                    }
                    
                    $classe="myaltraquota_x";
                    
                    print("<input type='text' class=\"".$classe."\" name='myaltraquota' id='myaltraquota' size='10' onblur='ControlloQuota()' onfocus='FuocoCampoQuota()' value='0.00' $disabled /><strong>&nbsp;&euro;</strong></th>");
                break;
                
                case -2: // non pagata
                    echo ("<th class =\"quota\" colspan=2>Non pagata</th>");
                break;
                
                case 0:
                    echo("<th class =\"quota\" colspan=2>Gratuito</th>");
                break;
                
                default:
                    echo("<th class =\"quota\" colspan=2>".($quote->Quota)." &euro;</th>");
                break;
            }
            
            echo("</tr>");
        }
    }
 
return;
}
//*********************> GESTIONE DEI NUMERI DI TELEFONO DELLA RUBRICA (dati dal modulo rubrica) <***************
function GetNomeParrocchia() {
    if ($_POST['hdnIdParrocchia']==null || $_POST['hdnIdParrocchia']=="") {
        $_POST['hdnIdParrocchia']=0;
    }
    
    $query="SELECT * FROM tblparrocchie WHERE IdParrocchia=".$_POST['hdnIdParrocchia'];
    
    $result=mysql_query($query);
    
    if (mysql_num_rows($result)>0) {
        $record=mysql_fetch_object($result);
        $nome_parrocchia=htmlentities($record->Parrocchia);
    } else {
          $nome_parrocchia="**********";
    }

    echo ($nome_parrocchia);
    
    return;
}


//*********************> GESTIONE DEI NUMERI DI TELEFONO DELLA RUBRICA (dati dal modulo rubrica) <*************** 
function SalvaDatiRubrica() {
   // prepara l'array con i campi da salvare
   $campi=array(
      0=>"IDTipoTelefono",
      1=>"PrefissoInt",
      2=>"Prefisso",
      3=>"Numero",
      4=>"TelSMS"
      );
   
   //scompone i dati arrivati dal form
   $DatiRubrica=explode("|",$_POST["DatiRubrica"]);
    
    // elabora i quintetti trovati
    foreach ($DatiRubrica as $quintetto) {
        // li scompone
        $DatiTelefono=explode(";",$quintetto);
        
        // resetta il contatore
        $i=0;
        
        // scompone i valori del quintetto
        foreach ($DatiTelefono as $valori) {
            if ($valori!="") {
                if ($i>0 && $i<4) {
                    $valore[$campi[$i]]=(string)$valori;
                } else {
                    $valore[$campi[$i]]=$valori;
                }
                $i++;
            }
        }
        
        // converte i valori booleani di TelSMS in valori numerici (sic!)
        if ($valore["TelSMS"]=='false') {
            $valore["TelSMS"]=0;
        } else {
            $valore["TelSMS"]=1;
        }
        
        // prepara la query per la ricerca del telefono da salvare
        // controlla se il numero è nuovo e quindi da salvare o è semplicemente da aggiornare
        $query="SELECT IDTipoTelefono,ID FROM tbltelefoni WHERE IDTipoTElefono=".$valore["IDTipoTelefono"]." AND ID=".$_POST["hdnID"];
        $result=mysql_query($query);
        
        if (mysql_num_rows($result)>0) {
            $query="UPDATE tbltelefoni SET Prefisso='".$valore["Prefisso"]."', Numero='".$valore["Numero"]."', TelSMS=".$valore["TelSMS"].", PrefissoInt='".$valore["PrefissoInt"]."' ";
            $query.="WHERE IDTipoTelefono=".$valore["IDTipoTelefono"]." AND ID=".$_POST["hdnID"];
        } else {
            $query="INSERT INTO tblTelefoni (ID,Prefisso,Numero,IDTipoTelefono,TelSMS,PrefissoInt) VALUES(".$_POST['hdnID'].",'".$valore['Prefisso']."','".$valore['Numero']."',".$valore['IDTipoTelefono'].",".$valore['TelSMS'].",'".$valore['PrefissoInt']."')"; 
        } 
      
        // invia la query al db
        $rstAggiornamento=mysql_query($query) || die ($query);
        if (mysql_affected_rows()>0) {
               
        }
    }

    return;
 }
 
 //**************************************************************************************************
 function DeleteDatiRubrica() {
    $TipoTelefono=explode("|",$_POST["DatiRubrica"]);
    
    foreach ($TipoTelefono as $valore) {
        $query="DELETE FROM tbltelefoni WHERE IDTipoTelefono=".$valore." AND ID=".$_POST["hdnID"];  
        mysql_query($query);
    }

    return;
} 
 
// ********************* FUNZIONE PER CONVERTIRE LA DATA DAL FORMATO DI MYSQL A QUELLO ITALIANO ******
function ConvertiData($data) {
    if ($data!=null || $data!="") {
        $data =substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
    }
     return $data;   
} 
      
// ****************** > FUNZIONE POPOLA LISTA TIPI DI VIE < *****************************
function PopolaListaVie($via) {
    // setta la stringa $query e estrae i dati dei tipi di via
    $query="SELECT idstradario,tipo FROM tblstradario";
    $rstVie=mysql_query($query);
              
    // popola la lista con i dati estratti dal database
    while ($vie=mysql_fetch_array($rstVie))
    {
        if ($vie["tipo"]==$via) {
            echo ("<option value='".$vie["tipo"]."' selected>".htmlentities($vie["tipo"])."</option>\n");
        } else {
            echo ("<option value='".$vie["tipo"]."'>".htmlentities($vie["tipo"])."</option>\n");
        }
    }
          
    return;
}
      
// ****************** > FUNZIONE POPOLA LISTA CLASSI < *****************************
function PopolaListaClassi($classe) {
  ConnettiDB();
  // setta la stringa $query e estrae i dati dei tipi di via
  $query="SELECT idclasse,classe FROM tblClassi";
  $rstClassi=mysql_query($query);
              
  // popola la lista con i dati estratti dal database
  while ($classi=mysql_fetch_array($rstClassi))
    {
        if ($classi["idclasse"]==$classe) {
            echo ("<option value='".$classi["idclasse"]."' selected>".htmlentities($classi["classe"])."</option>\n");
        } else {
            echo ("<option value='".$classi["idclasse"]."'>".htmlentities($classi["classe"])."</option>\n");
        }
    }
          
    return;
}
// ************************ > FINE FUNZIONE POPOLA LISTA CLASSI < ***********************
      
// ****************** > FUNZIONE GETCLASSE ISCRITTO < *****************************
function GetClasseIscritto($nome_classe) {
    if ($_POST["myclassi"] < 1 || $_POST["myclassi"] == null) {
        $nome_classe="n. d.";
    } else {
        $query="SELECT idclasse,classe FROM tblClassi WHERE idclasse=".$_POST["myclassi"];
        $rstClassi=mysql_query($query);
        $nome_classe=mysql_fetch_object($rstClassi);
        $nome_classe=htmlentities($nome_classe->classe);
    }
    
    return $nome_classe;
}
// ************************ > FINE FUNZIONE GETCLASSE ISCRITTO < ***********************
      
// ****************** > FUNZIONE GET SEZIONE ISCRITTO < *****************************
function GetSezioneIscritto($nome_sezione) {

    if ($_POST["mysezione"] < 1 || $_POST["mysezione"] == null) {
        $nome_sezione="n. d.";
    } else {
        $query="SELECT idsezione,sezione FROM tblSezioni WHERE IdSezione=".$_POST["mysezione"];
        $rstSezioni=mysql_query($query);
        $nome_sezione=mysql_fetch_object($rstSezioni);
        $nome_sezione=htmlentities($nome_sezione->sezione);
    }    
          
    return $nome_sezione;
}
      
// ************************ > FINE FUNZIONE GETSEZIONE ISCRITTO < ***********************
      
// ****************** > FUNZIONE POPOLA LISTA SEZIONI < *****************************
function PopolaListaSezioni($sezione) {
    // setta la stringa $query e estrae i dati dei tipi di via
    $query="SELECT idsezione,sezione FROM tblSezioni";
    $rstSezioni=mysql_query($query);
              
    // popola la lista con i dati estratti dal database
    while ($sezioni=mysql_fetch_array($rstSezioni))
      {
          if ($sezioni["idsezione"]==$sezione) {
              echo ("<option value='".$sezioni["idsezione"]."' selected>".htmlentities($sezioni["sezione"])."</option>\n");
          } else {
              echo ("<option value='".$sezioni["idsezione"]."'>".htmlentities($sezioni["sezione"])."</option>\n");
          }
      }
          
    return;
}
// ************************ > FINE FUNZIONE POPOLA LISTA SEZIONI < ***********************
      
// ****************** > FUNZIONE TABELLA RUBRICA TELEFONICA< *****************************
function GetRubricaTelefonica($errore) {

    // legge nel db i tipi di telefono ammessi
    $query="SELECT idtipotelefono,tipotelefono FROM tbltipitelefono";
    $rstTipo=mysql_query($query);  
    
    // controlla che nel db vi siano tipi di telefono
    if (mysql_num_rows($rstTipo)<1){
        $errore=1;
        return $errore;
    } 
    
    // legge i telefoni associati all'iscritto
    $query_telefono="SELECT idtelefono,idtipotelefono,id,prefisso,numero,telsms,PrefissoInt FROM tbltelefoni WHERE id='".$_POST['hdnID']."' ORDER BY idtipotelefono ASC";
    $rstTelefono=mysql_query($query_telefono);
  
    $telefono = mysql_fetch_object($rstTelefono); 
  
    // costruisce la tabella
    while ($tipo=mysql_fetch_object($rstTipo)) {
            if (($tipo->idtipotelefono)==($telefono->idtipotelefono)) {
                $prefisso=($telefono->prefisso);
                $numero= ($telefono->numero);
                if (($telefono->telsms)==1) {
                    $checked="checked";
                } else {
                    $checked="";
                }
                $prefisso_intnaz=($telefono->PrefissoInt);
                
                //avanza di uno 
                $telefono = mysql_fetch_object($rstTelefono); 
            } else {
                $prefisso="";
                $numero= "";
                $prefisso_intnaz="";
                $checked="";
            }

            // calcola la posizione della riga e la colora diversamente
            $i++;
            if ($i % 2) {
                $classe="cella_phone_dispari";
            } else {
                $classe="cella_phone_pari";
            }
              
             //costruisce la tabella con i dati raccolti 
            echo ("<tr>");

            echo ("<th class=\"".$classe."\">\n");
            echo ("<input type=\"checkbox\" name=\"SelNumero\" value=\"$i\" />\n");
            echo ("</th>\n");
            
            echo ("<th class=\"".$classe."\">\n");
            echo ($tipo->tipotelefono)."\n";
            echo ("</th>\n");
              
            echo ("<th class=\"".$classe."\">\n");
            echo ("<input type=\"text\" value=\"".$prefisso_intnaz."\"name=\"prefisso_intnaz\" id=\"prefisso_intnaz\" size=\"4\" onblur=\"ControlloNrTel('pref_int',$i);\" onfocus=\"InputNrTel('pref_int',$i)\" class =\"phone\" \>\n");
            echo ("</th>\n");
              
            echo ("<th class=\"".$classe."\">\n");
            echo ("<input type=\"text\" value=\"".$prefisso."\" name=\"prefisso_naz\" id=\"prefisso_naz\" size=\"4\" onblur=\"ControlloNrTel('pref_naz',$i);\" onfocus=\"InputNrTel('pref_naz',$i)\" class =\"phone\" \>\n");
            echo ("</th>\n");
              
            echo ("<th class=\"".$classe."\">\n");
            echo ("<input type=\"text\" value=\"".$numero."\" name=\"numero_phone\" id=\"numero_phone\" onblur=\"ControlloNrTel('nr_phone',$i);\" onfocus=\"InputNrTel('nr_phone',$i)\" class=\"phone\" size=\"19\" \>");
            echo ("</th>\n");
              
            echo ("<th class=\"".$classe."\">\n");
            echo ("<input type=\"checkbox\" name=\"SelSms\" onClick=\"ControllaChkSmS($i);\" ".$checked." />\n");
            echo ("</th>\n");
              
            echo ("</tr>\n");
    }
    $errore=0;
    return $errore;
}
      
// ****************** > FUNZIONE POPOLA LISTBOX CON GRADI PARENTELA < *****************************
function GetGradoParentela()
{
    $query=mysql_query("SELECT * FROM tblgradiparentela ORDER BY IdGradoParentela");
    
    while($result=mysql_fetch_object($query))
    {
      echo "<option value='".htmlentities($result->IdGradoParentela)."'>".htmlentities($result->GradoParentela)."</option>\n";
    }
    
    return;
}

// ****************** > FUNZIONE POPOLA LISTBOX CON RUOLO PARENTELA < *****************************
function GetRuolo()
{
    $query=mysql_query("SELECT * FROM tblruoloparentela ORDER BY IdRuoloParentela");
    
    while($result=mysql_fetch_object($query))
    {
      echo "<option value='".htmlentities($result->IdRuoloParentela)."'>".htmlentities($result->RuoloParentela)."</option>\n";
    }
                
    return;
}

// ****************** > FUNZIONE GET PARENTELA INFO PER SEZIONE INFO< *****************************
function GetParentelaInfo() {
    
    // serve per quando si fa una nuova iscrizione per non dare errore nella costruzione della tabella
    if (!isset($_POST["hdnID"]) || $_POST["hdnID"]==null) {
        echo "n. d.";
        return;
    }

    // cerca l'id della famiglia dell'iscritto
    $query="SELECT IdFamigliare,IdFamiglia FROM tblparentela WHERE IdFamigliare=".$_POST["hdnID"];

    $result=mysql_query($query);
    
    if (mysql_num_rows($result) <1) {
        echo "n. d.";
    } else {
          $famiglia=mysql_fetch_object($result);
          
          // estrae i famigliari dell'iscritto
          $query="SELECT IdFamigliare,IdGradoParentela FROM tblparentela WHERE IdFamiglia=".($famiglia->IdFamiglia)." AND NOT IdFamigliare=".$_POST["hdnID"]." ORDER BY IdGradoParentela";
          $result=mysql_query($query);
          if (mysql_num_rows($result) >0) {
          //inserisce le iconcine dei famigliari
          while ($famigliari=mysql_fetch_object($result)) {
                  //inserisce le faccine in base al grado di parentela
                  switch ($famigliari->IdGradoParentela) {
                      case 2:
                          echo "<img src='./Immagini/mother.png' alt='Mamma' width='30' height='25' />";
                      break;
                  
                      case 3:
                          echo "<img src='./Immagini/father.png' alt='Papà' width='30' height='25' />";
                      break;
                  
                      case 4:
                          echo "<img src='./Immagini/boy.png' alt='Fratello' width='30' height='25' />";
                      break;
                  
                      case 5:
                          echo "<img src='./Immagini/sister.png' alt='Sorella' width='35' height='25' />";
                      break;
                  }
              } 
          } else {
              echo "n. d.";
          }
      }
    return; 
}

// ****************** > FUNZIONE GET INFO PARENTELA PER TABELLA< *****************************
function CreaTabellaFamigliari()
{
    $i=0; // variabile di servizio per costruire i colori della tabella
   
    // serve per quando si fa una nuova iscrizione per non dare errore nella costruzione della tabella
    if (!isset($_POST["hdnID"]) || $_POST["hdnID"]==null) {
        echo "<tr>";
        echo "<th class='cella_parentela_dispari' colspan='7'>nessun dato disponbile</th>";
        echo "</tr>";
        return;
    }   
    
    // controlla se l'iscritto è già associato a una famiglia 
    $query="SELECT IdFamigliare,IdFamiglia,IdGradoParentela FROM tblparentela WHERE IdFamigliare=".$_POST["hdnID"];
    
    $result=mysql_query($query);
    
    // se l'iscritto non è associato a nessuna famiglia esce dalla funzione
    if (mysql_num_rows($result) < 1) {
        echo "<tr>";
        echo "<th class='cella_parentela_dispari' colspan='7'>nessun dato disponbile</th>";
        echo "</tr>";
        return;
    }

    // recupera l'id della famiglia associata all'iscritto
    $famiglia=mysql_fetch_object($result);
    $id_famiglia=$famiglia->IdFamiglia;
    
    // recupera il grado di parentela dell'iscritto
    $id_grado_parentela_iscritto=$famiglia->IdGradoParentela;
    
    // ricerca, estrazione e controllo dei dati dei famigliari dell'iscritto
    $query="SELECT tblparentela.IdFamigliare,tblparentela.IdGradoParentela,tblparentela.IdFamiglia,
            tblgradiparentela.IdGradoParentela,tblgradiparentela.GradoParentela 
            FROM (tblparentela INNER JOIN tblgradiparentela ON tblparentela.IdGradoParentela=tblgradiparentela.IdGradoParentela) 
            WHERE tblparentela.IdFamiglia=".$id_famiglia." ORDER BY tblparentela.IdGradoParentela";
    
    $result_famigliari=mysql_query($query); 
        
    if (mysql_num_rows($result_famigliari) <= 1) {
        echo "<tr>";
        echo "<th class='cella_parentela_dispari' colspan='7'>nessun dato disponbile</th>";
        echo "</tr>";
        return;
    } 
    
    while ($dati=mysql_fetch_object($result_famigliari)) {    
      if ($_POST["hdnID"]!=($dati->IdFamigliare)) { // evita di visualizzare l'iscritto nella tabella    

        //utilizzo dei valori estratti per ricostruire i dati dei parenti dell'iscritto
        $query="SELECT catechismi.ID,catechismi.Cognome,catechismi.Nome,catechismi.Sesso,catechismi.Classe,catechismi.DataScadenzaTessera,tblclassi.IdClasse,tblclassi.Classe 
                FROM catechismi INNER JOIN tblclassi ON catechismi.Classe=tblclassi.IdClasse 
                WHERE catechismi.ID=".($dati->IdFamigliare);
        
        $result_parente=mysql_query($query);
        if (mysql_num_rows($result) == 0) {
            echo "<tr>";
            echo "<th class='cella_parentela_dispari' colspan='7'>nessun dato disponbile</th>";
            echo "</tr>";
            return;
        } 
        
        $dati_parente=mysql_fetch_object($result_parente);
        
            $i++;
            if ($i % 2) {
              $classe="cella_parentela_dispari";
            } else {
                $classe="cella_parentela_pari";
            }
                
            echo "<tr>\n"; 
                
            echo "<th class=\"$classe\">\n";
            echo "<input type=\"checkbox\" name=\"selparente\" value=\"".$id_famiglia."\" /> \n";
            echo "</th>\n";
                
            echo "<th class=\"$classe\" name=\"cella_id\">\n";
            echo htmlentities($dati->IdFamigliare)."\n";
            echo "</th>\n";
                
            echo "<th class=\"$classe\">\n";
            echo htmlentities($dati_parente->Cognome)." ".htmlentities($dati_parente->Nome)."\n";
            echo "</th>\n";

            echo "<th class=\"$classe\">\n";
            
            if ($id_grado_parentela_iscritto==2 || $id_grado_parentela_iscritto==3) {
                if ($dati_parente->Sesso == "F") {
                    if ($dati->IdGradoParentela ==2) {
                        echo ("Moglie");
                    } else {
                        echo ("Figlia");
                    }
                } else {
                    if ($dati->IdGradoParentela ==3) {
                        echo ("Marito");
                    } else {
                        echo ("Figlio");
                    }
                }
            } else {
                  echo htmlentities($dati->GradoParentela)."\n";
            }
                 
            echo "</th>\n";
                
            echo "<th class=\"$classe\">\n";
            if ((int)substr(($dati_parente->DataScadenzaTessera),0,4)==(int)date('Y') || (int)substr(($dati_parente->DataScadenzaTessera),0,4)==(int)date('Y')+1) {
                echo "<img src='./Immagini/check.png' alt='icona' width='15' height='13' />\n";
            } else {
                echo "<img src='./Immagini/cross3.png' alt='icona' width='14' height='12' />\n";
            }
            
            echo "</th>\n";
                
            echo "<th class=\"$classe\">\n";
            echo htmlentities($dati_parente->Classe)."\n";
            echo "</th>\n";
                
            echo "<th class=\"$classe\">\n";
            echo htmlentities($dati->RuoloParentela)."\n";
            echo "</th>\n";
                
            echo "</tr>\n";
      }
    }
    return;
}

// ****************** > FUNZIONE SALVA DATI PARENTELA < *****************************
function SalvaDatiParentela($errore){
    // errori:
    // 0 = ok dati salvati
    // 1 = il parente è già stato associato
    // 2 = salvataggio annullato: è stata riscontrata un'incongruenza dei dati inviati al server
    // 3 = salvataggio annullato: problemi con la query inviata al server
    
    $errore=0;
    
    //OTTIENE IL SESSO DELL'ISCRITTO E DEL PARENTE SELEZONATO
    //(potrebbero sorgere imbarazzanti gradi di parentela fra... gay o lesbiche oppure incestuose!!!)
    
    // Sesso e data di nascita dell'iscritto
    $query="SELECT Sesso,Data_di_nascita FROM catechismi WHERE ID=".$_POST["hdnID"];
    $result=mysql_query($query);
    $sesso=mysql_fetch_object($result);
    
    $sesso_iscritto=($sesso->Sesso);
    $data_iscritto=substr(($sesso->Data_di_nascita),0,4);
    
    // Sesso e data di nascita del parente selezionato
    $query="SELECT Sesso,Data_di_nascita FROM catechismi WHERE ID=".$_POST["hdnIDParente"];
    $result=mysql_query($query);
    $sesso=mysql_fetch_object($result);
    
    $sesso_parente=($sesso->Sesso);
    $data_parente=substr(($sesso->Data_di_nascita),0,4);

    // controllo sesso parente con grado di parentela scelto
    if ($sesso_parente=="M") {
        switch ($_POST["GradoParentela"]) {
            case 2: // mamma
                $errore=2;
                return $errore;
            break;
        
            case 5: // sorella
                $errore=2;
                 return $errore;
            break;
            
            case 6: // moglie
                $errore=2;
                 return $errore;
            break;
            
            case 9: // figlia
                $errore=2;
                 return $errore;
            break;
        }
    } elseif ($sesso_parente=="F"){
          switch ($_POST["GradoParentela"]) {
              case 3: // papà
                  $errore=2;
                   return $errore;
              break;
        
              case 4: // fratello
                  $errore=2;
                   return $errore;
              break;
            
              case 7: // marito
                  $errore=2;
                   return $errore;
              break;
            
              case 8: // figlio
                  $errore=2;
                   return $errore; 
              break;
          }
    }
    
    // controlla che il parente nei gradi parentela di mamma e papà non sia più giovane dell'iscritto
    if ((intval($data_parente) > intval($data_iscritto)) || (intval($data_parente) == intval($data_iscritto))) {
        if ($_POST["GradoParentela"]==2 || $_POST["GradoParentela"]==3){
            $errore=2;
            return $errore;
        }
    } else {
        if ($_POST["GradoParentela"]==8 || $_POST["GradoParentela"]==9){
            $errore=2;
            return $errore;
        }
    }
    
    // controlla che le coppie che vengono formate non siano dello stesso sesso... nel caso di marito e moglie (6 moglie, 7 marito)
    if (($sesso_iscritto==$sesso_parente && $_POST["GradoParentela"]==6) || ($sesso_iscritto==$sesso_parente && $_POST["GradoParentela"]==7)) {
        $errore=2;
        return $errore;
    } else {
        // salva il valore originale della scelta dell'utente
        $grado_parentela=$_POST["GradoParentela"];

        // i gradi di parentela registrati nel db sono mamma, papà, fratello e sorella
        switch ($_POST["GradoParentela"]) {
            case 6: // moglie
                $_POST["GradoParentela"]=2; // mamma
                $grado_iscritto=3; // papà || marito
            break;
            
            case 7: // marito
                $_POST["GradoParentela"]=3; // papà
                $grado_iscritto=2; // mamma || moglie
            break;
            
            case 8: // figlio
                $_POST["GradoParentela"]=4; // fratello
                if ($sesso_iscritto=="F") {
                    $grado_iscritto=2;
                } else {
                    $grado_iscritto=3;
                }
            break;
            
            case 9: // figlia
                $_POST["GradoParentela"]=5; //sorella
                if ($sesso_iscritto=="F") {
                    $grado_iscritto=2;
                } else {
                    $grado_iscritto=3;
                }
            break;
            
            default:
               if ($sesso_iscritto=="F") {
                    $grado_iscritto=5;
                } else {
                    $grado_iscritto=4;
                }
            break;
        } 
        
        // imposta l'id della famiglia: se non c'è lo va a creare
            if ($_POST["id_famiglia"]==-1) {
               $_POST["id_famiglia"]=(GetIdFamiglia($idfamiglia));
            }
            
        // CONTROLLA I DATI DELL'ISCRITTO
       // controlla che l'iscritto selezionato non sia già stato associato a una famiglia
        
        mysql_query("START TRANSACTION");
        
        $query="SELECT IdFamiglia,IdGradoParentela FROM tblparentela WHERE IdFamigliare=".$_POST["hdnID"];
        $result=mysql_query($query);
    
        if (mysql_num_rows($result)>0) { // ha trovato l'iscritto già associato in una famiglia
            $dati=mysql_fetch_object($result);
            if ($dati->IdGradoParentela <4 && $grado_parentela<6) { // impedisce che vengano associati ai genitori fratelli, sorelle, papà e mamme
                $errore=2;
            } elseif ($dati->IdGradoParentela >3 && $grado_parentela>5) { //impedisce che vengano associati ai figli mogli e mariti
                  $errore=2;
            } 
          } else { // non ha trovato l'iscritto
              $query_inserimento_dati_iscritto="INSERT INTO tblparentela (IdFamigliare,IdGradoParentela,IdFamiglia) VALUES ('".$_POST["hdnID"]."','".$grado_iscritto."','".$_POST["id_famiglia"]."')";
              $result=mysql_query($query_inserimento_dati_iscritto);
          }
        
        // CONTROLLA I DATI DEL PARENTE
        //controlla che il famigliare selezionato non sia già presente in archivio
        $query="SELECT IdFamigliare,IdFamiglia,IdGradoParentela FROM tblparentela WHERE IdFamigliare=".$_POST["hdnIDParente"];
        
        $result=mysql_query($query);
        
        // recupera l'id famiglia e il grado di parentela del famigliare selezionato
        $dati=mysql_fetch_object($result);
        $idfamiglia_parente=($dati->IdFamiglia);
        $gradoparentela_parente=($dati->IdGradoParentela);
        
        // se non trova nessuna omonimia costruisce la query per l'inserimento dei dati del famigliare
        if (mysql_num_rows($result)<1) {
            //controlla che non si inseriscano più di una mamma (moglie) o papà (marito)
            if (ControlloGenitoriFamiglia()) {
                $errore=2;
            } else {
                // prima di salvare i dati del parente analizza i componenti della famiglia per impedire
                // l'inserimento di figli coetanei o più vecchi dei genitori o comunque con un ridotto margine di età
                // controllo esteso a tutti i componenti della famiglia già inseriti nel database
                $query="SELECT tblparentela.IdFamiglia,tblparentela.IdGradoParentela,tblparentela.IdFamigliare, 
                        catechismi.ID, catechismi.Data_di_nascita 
                        FROM tblparentela INNER JOIN catechismi 
                        ON tblparentela.IdFamigliare=catechismi.ID 
                        WHERE tblparentela.IdFamiglia=".intval($_POST["id_famiglia"]);
                
                $result=mysql_query($query);
                
                while ($mydati=mysql_fetch_object($result)) {
                    $data_famigliare=intval(substr(($mydati->Data_di_nascita),0,4));
                    if (intval($data_parente) <= $data_famigliare) {
                        if ($_POST["GradoParentela"]<4 && $mydati->IdGradoParentela<4) {
                            $errore=2;
                        }
                    } /*elseif ((intval($data_parente)-$data_famigliare <18 && $mydati->IdGradoParentela <4)) {
                        $errore=2;
                    }*/
                }
                
                $query_inserimento_dati_parente="INSERT INTO tblparentela (IdFamigliare,IdGradoParentela,IdFamiglia) VALUES ('".$_POST["hdnIDParente"]."','".$_POST["GradoParentela"]."','".$_POST["id_famiglia"]."')";
                $result=mysql_query($query_inserimento_dati_parente);
            }
        } else { // ha trovato un'omonimia
            if ($idfamiglia_parente==$_POST["id_famiglia"]) { //il famigliare è già associato alla famiglia
                $errore=1;
            } else { // il famigliare è associato a un'altra famiglia
                  // sposta tutti i parenti del famigliare dell'iscritto: crea così un'unica famiglia
                  $query="UPDATE tblparentela SET IdFamiglia=".$_POST["id_famiglia"]." WHERE IdFamiglia=".$idfamiglia_parente;
                  $result=mysql_query($query);
                  
                  // cancella la vecchia famiglia del famigliare dell'iscritto
                  $query="DELETE FROM tblfamiglie WHERE IdFamiglia=".$idfamiglia_parente;
                  $result=mysql_query($query);
            }
        } 
        
        // SE TUTTO E' A POSTO RISOLVE LA TRANSAZIONE (FINALMENTE...!!!)
        if ($errore>0) {
            mysql_query("ROLLBACK");
        } else {
            mysql_query("COMMIT");
        }
    }

    return $errore;
}

// ****************** > FUNZIONE CREA ID FAMIGLIA < *****************************
function GetIdFamiglia($idfamiglia) {
    // controlla se l'iscritto ha già un id di famiglia
    $query="SELECT IdFamigliare,IdFamiglia FROM tblparentela WHERE IdFamigliare=".$_POST["hdnID"];
        
    $result=mysql_query($query);
    if (mysql_num_rows($result)<1) {
        // recupera l'ultimo id inserito
        $query="SELECT Max(IdFamiglia) as UltimoId FROM tblfamiglie";
        $result=mysql_query($query);
        $dati=mysql_fetch_object($result);
        
        // setta l'id della famiglia
        $idfamiglia=(($dati->UltimoId)+1);

        // lo va a salvare
        $query="INSERT INTO tblfamiglie (IdFamiglia) VALUES('$idfamiglia')";
        $result=mysql_query($query);
        
    } else {
        $dati=mysql_fetch_object($result);
        $idfamiglia=($dati->IdFamiglia);
    }
    
    
    return $idfamiglia;
}

// ********* > FUNZIONE CONTROLLA SE IN UN DATA FAMIGLIA CI SONO GIA' MAMMA E PAPA' (MOGLIE E MARITO) < *******
function ControlloGenitoriFamiglia() {
    // controlla se il grado di parentela scelto per il parente esiste già per quella famiglia
    if ($_POST["GradoParentela"]==2 || $_POST["GradoParentela"]==3){    
        $query ="SELECT IdGradoParentela,IdFamiglia FROM tblparentela WHERE IdFamiglia=". $_POST["id_famiglia"]. " AND IdGradoParentela=".$_POST["GradoParentela"];
    
        $result=mysql_query($query);

        if (mysql_num_rows($result)>0) {
            return true;
        }
    }
    
    return false;
}

// ****************** > FUNZIONE CANCELLA DATI PARENTELA < *****************************
function CancellaDatiParentela() {
    $IdFamigliare=explode("|",$_POST["id_famiglia"]);
      
    foreach ($IdFamigliare as $valore) {
        $query="DELETE FROM tblparentela WHERE IdFamigliare=".$valore;  
        mysql_query($query);
    }

    return;
}

// ****************** > FUNZIONE CONTROLLA MODIFICHE AI DATI (prima di chiudere la scheda iscritto) < *************************
function ControllaScheda($variazione) {
    $DatiVariati=0;
    global $variazione;
    // recupera i dati dal db
    $query ="SELECT * FROM catechismi WHERE ID=".$_POST["hdnID"];
    
    // invia la richiesta dati al db
    $result=mysql_query($query);
    
    // recupera le informazioni inviate dal server Mysql
    $record=mysql_fetch_array($result);
    
    // controlla se i dati sono stati cambiati              
    if (htmlentities($_POST["cognome"])!=htmlentities($record["Cognome"])) {
        $DatiVariati++;
        $variazione="| cognome | ";
    }

    if (htmlentities($_POST["nome"])!=htmlentities($record["Nome"])) {
        $DatiVariati++;
        $variazione.="| nome | ";
    }
   
    if ($_POST["sesso"]!=htmlentities($record["Sesso"])) {
        $DatiVariati++;
        $variazione.="| sesso | ";
    }    
     
    if (isset($_POST["data_nascita"])) {
        if ($_POST["data_nascita"]!= "" || $_POST["data_nascita"] != null) { 
            $scheda_dataN=substr($_POST["data_nascita"],6,4)."-".substr($_POST["data_nascita"],3,2)."-".substr($_POST["data_nascita"],0,2);
        } else {
            $scheda_dataN=null;
        }
        
        if ($scheda_dataN != substr(htmlentities($record["Data_di_nascita"]),0,10)) {
            $DatiVariati++;
            $variazione.="| data di nascita | ";
        }    
    }
    
    if (htmlentities($_POST["natoa"])!=htmlentities($record["Luogo_di_nascita"])) {
        $DatiVariati++;
        $variazione.="| nato a | ";
    }   
      
    if ($_POST["stradario"]!=htmlentities($record["Tipo_via"])) {
        $DatiVariati++;
        $variazione.="| tipo via | ";
    }   
    
    if (htmlentities($_POST["indirizzo"])!=htmlentities($record["Via"])) {
        $DatiVariati++;
        $variazione.="| via | ";
    }
     
    if (htmlentities($_POST["numero"])!=htmlentities($record["numero_civico"])) {
        $DatiVariati++;
        $variazione.="| numero civico | ";
    }

    if (htmlentities($_POST["comune"])!=htmlentities($record["Citt"])) {
        $DatiVariati++;
        $variazione.="| citt&agrave; | ";
    }    
     
    if ($_POST["cap"]!=htmlentities($record["CAP"])) {
        $DatiVariati++;
        $variazione.="| cap | ";
    }    
   
    if ($_POST["prov"]!=htmlentities($record["Provincia"])) {
        $DatiVariati++;
        $variazione.="| provincia | ";
    }    

    if ($_POST["myemail"]!=htmlentities($record["email"])) {
        $DatiVariati++;
        $variazione.="| email | ";
    }
    
    if ($_POST["hdnIdParrocchia"]!=htmlentities($record["Parrocchia_Provenienza"])) {
        $DatiVariati++;
        $variazione.="| parrocchia | ";
    }
    
    //if ($_POST["chkspedizione"]!=htmlentities($record[""])) {
    //    $DatiVariati++;
    //}
    
    if ($_POST["myquota"]!=htmlentities($record["TipoQuota"])) {
        if ($_POST["mydataT"]!=null || $_POST["mydataT"]!=""){
          $DatiVariati++;
          $variazione.="| Quota Oratorio | ";
        }
    }
    
    if (isset($_POST["myaltraquota"])) {
        if ($_POST["myaltraquota"]!=htmlentities($record["QuotaOratorio"])) {
            $DatiVariati++;
            if (strpos($variazione,"Quota Oratorio")===false) {
                $variazione.="| Quota Oratorio | ";
            }
        }
    }
    
    if (isset($_POST["mydataT"])) {
        switch ($_POST["mydataT"]) {
            case null:
                $scheda_dataT=null; 
                $_POST["myquota"]=5;// se non c'è data di tesseramento pone allo stato di non pagato la quota dell'oratorio
            break;
        
            case 'Oggi':
                $scheda_dataT=date('Y-m-d');
            break;
            
            default:
                $scheda_dataT=substr($_POST["mydataT"],6,4)."-".substr($_POST["mydataT"],3,2)."-".substr($_POST["mydataT"],0,2);
            break;
        }
        if ((string)$scheda_dataT != (string)substr(htmlentities($record["DataTesseramento"]),0,10)) {
            $DatiVariati++;
            $variazione.="| data tesseramento | ";
        }    
    }
    
    if ($_POST["myclassi"]!=htmlentities($record["Classe"])) {
        $DatiVariati++;
        $variazione.="classe | ";
    }
    
    if ($_POST["mysezione"]!=htmlentities($record["Sezione"])) {
        $DatiVariati++;
        $variazione.="| sezione | ";
    }

    if ($_POST["optPartecipa"]!=htmlentities($record["Presenza"])) {
        $DatiVariati++;
        $variazione.="| partecipazione | ";
    }
     
    if (isset($_POST["chkCoro"])) {
        $_POST["chkCoro"]="True";
    } else {
        $_POST["chkCoro"]="False";
    }
    
    if ($_POST["chkCoro"]!=htmlentities($record["Coro"])) {
        $DatiVariati++;
        $variazione.="| coro | ";
    }       
    
    if ($_POST["myruolo"]!=htmlentities($record["IdRuolo"])) {
        $DatiVariati++;
        $variazione.="| ruolo | ";
    }
   
    // valuta l'esito dei controlli
    if ($DatiVariati > 0) {
        return true;
    } else {
        return false;
    }
}

// ****************** > FUNZIONE STAMPA AVVISI E ERRORI < *************************
function StampaMessaggio($messaggio,$errore,$icona) {
    if($errore!=0){
        print "<div id=\"stampamessaggio\">\n";
        print "<table class=\"tabellamessaggi\">\n<tr>\n<th class=\"testo\">\n";
        
        switch ($icona) {
            case 1:
                 print "<img src=\"./Immagini/check.png\" alt=\"\" width=\"35\" height=\"30\" /></th>\n";
            break;
    
            case 2:
                 print "<img src=\"./Immagini/cross.png\" alt=\"\" width=\"35\" height=\"30\" /></th>\n";
            break;
        }
   
        print "<th class=\"testomessaggio\">".$messaggio."</th>\n";
        print "<th class=\"testo\"><input type=\"button\" name=\"chiudimessaggio\" id=\"ChiudiMessaggio\" value=\"ok\" onClick=\"ChiudiMessaggio();\" /></th>\n";
        print "</tr>\n</table>\n"; 
        print "</div>\n";
    }
    return;
}
// ****************** > FUNZIONE RECUPERA RUOLO DELL'ISCRITTO < *************************
function GetInfoRuolo() {

    $query ="SELECT * FROM tblruolioratorio WHERE IdRuoloOratorio=".$_POST['myruolo'];
    
    $result= mysql_query($query);
    
    $row=mysql_fetch_object($result);
    
    echo (htmlentities($row->RuoloOratorio));
    
    return;

}

?>
