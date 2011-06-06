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
            $errore = SalvaDatiParentelaNuovo();
            
            switch ($errore) {
                case 1:
                    $messaggio="Salvataggio annullato!<br />Il familiare selezionato &egrave; gi&agrave; associato.";
                    $icona=2; 
                break;
                
                case 2:
                    $messaggio="Salvataggio annullato!<br />Errata scelta del grado di parentela.";
                    $icona=2; 
                break;
                
                case 3:
                    $messaggio="Salvataggio annullato!<br />Papà gi&agrave; presente in questa famiglia.";
                    $icona=2; 
                break;
                
                case 3.1:
                    $messaggio="Salvataggio annullato!<br />Mamma gi&agrave; presente in questa famiglia.";
                    $icona=2; 
                break;
                
                case 4:
                    $messaggio="Salvataggio annullato! <br />Stai cercando di inserire genitori pi&ugrave; giovani dei figli...!";
                    $icona=2; 
                break;
                
                case 5:
                    $messaggio="Salvataggio annullato!<br />I nonni non sono ammessi nei nuclei famigliari.";
                    $icona=2; 
                break;
                
                case 6:
                    $messaggio="Salvataggio annullato!<br />Gli zii non sono ammessi nei nuclei famigliari.";
                    $icona=2; 
                break;
                
                case 7:
                    $messaggio="Salvataggio annullato!<br />Imbarazzante scelta del grado di parentela...";
                    $icona=2; 
                break;
                
                case 8:
                    $messaggio="Salvataggio annullato!<br />Genitore coetaneo del figlio!";
                    $icona=2; 
                break;
                
                case 9:
                    $messaggio="Salvataggio annullato!<br /> Problemi con la query inviata al server.";
                    $icona=2; 
                break;
                
                case 10:
                    $messaggio="Salvataggio annullato!<br /> Marito/Moglie gi&agrave; presente.";
                    $icona=2; 
                break;
            }
             unset ($_POST["CognomeParente"]);
             unset ($_POST["id_famiglia_nucleo_A"]);
        break;
        
        case "salva_rubrica":
            SalvaDatiRubrica(); 
        break;

        case "aggiungi_ruolo":
            $errore=AggiungiRuolo();
            
            switch ($errore) {
                case 1:
                    $messaggio="E' stato raggiunto il numero massimo (7) di ruoli consentiti!";
                    $icona=2;
                break;
                
                case 2:
                    $messaggio="Questo ruolo &egrave; gi&agrave; stato associato";
                    $icona=2; 
                break;
            }
            
        break;
        
        case "cancella_ruolo":
            CancellaRuolo();
        break;
        
        default:
            $errore=0;
        break;
     } 
   
   if ($errore==0)  {
      RicercaIscritti();
   } 
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
* versione 1.7 - 14/01/11: migliorato controllo data tesseramento                       
**********************************************************************************/
 function SalvaScheda() {
       // variabili di servizio
       global $errore,$messaggio,$icona;
       
       // imposta le variabili con i valori dei campi 
        $scheda_id=$_POST["hdnID"];
        $scheda_cognome=$_POST["cognome"];
        $scheda_nome=$_POST["nome"];
        $scheda_sesso=$_POST["sesso"];
        $scheda_dataN=$_POST["data_nascita"];
        $scheda_luogoN=$_POST["natoa"];
        $scheda_tipo_via=$_POST["stradario"];
        $scheda_via=$_POST["indirizzo"];
        $scheda_numero_civico=$_POST["numero"];
        $scheda_comune=$_POST["comune"];
        $scheda_cap=$_POST["cap"];
        $scheda_provincia=$_POST["prov"];
        $scheda_email=$_POST["myemail"];
        $scheda_parrocchia=$_POST["hdnIdParrocchia"];
        $scheda_spedizione=$_POST["chkspedizione"];
        $scheda_tipo_quota=$_POST["myquota"];
        $scheda_myaltraquota=$_POST["myaltraquota"];
        $scheda_dataT=$_POST["mydataT"];
        $scheda_dataST=$_POST["mydataST"]; // data di scadenza della tessera
        $scheda_classe=$_POST["myclassi"];
        $scheda_sezione=$_POST["mysezione"];
        $scheda_partecipazione=$_POST["optPartecipa"];
        $scheda_coro=$_POST["chkCoro"];
        //$scheda_ruolo=$_POST["myruolo"];
        
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
        $timestamp_primo_settembre=mktime(0,0,0,8,31,date('Y')); // timestamp del primo settembre dell'anno corrente 
       
        if ($scheda_dataT != "" || $scheda_dataT != null) {
            if ($scheda_dataT=="Oggi") {
                $scheda_dataT=date('Y-m-d');
                $timestamp_data_tesseramento=mktime(0,0,0,date('m'),date('d'),date('Y'));
            } else {
                $timestamp_data_tesseramento=mktime(0,0,0,(int)substr($scheda_dataT,3,2),(int)substr($scheda_dataT,0,2),(int)substr($scheda_dataT,6,4));
                $timestamp_primo_settembre_anno_passato=mktime(0,0,0,8,31,(date('Y')-1)); // timestamp del primo settembre dell'anno passato
                // blocca il tentativo di inserire tesseramenti fuori dall'intervallo:
                // 1 settembre dell'anno prima - 31 agosto anno corrente
                if (($timestamp_data_tesseramento > $timestamp_primo_settembre_anno_passato) && ($timestamp_data_tesseramento <= $timestamp_primo_settembre)) {
                    $scheda_dataT=substr($scheda_dataT,6,4)."-".substr($scheda_dataT,3,2)."-".substr($scheda_dataT,0,2);
                }
                else {
                    $errore=1;
                    $messaggio="Attenzione! Controlla la data di tesseramento: l'anno che hai inserito potrebbe non essere valido";
                    $icona=2;
                    return;
                }
            }
            //calcola la data di scadenza della tessera
            if ($timestamp_data_tesseramento > $timestamp_primo_settembre) { //siamo dopo il primo settembre aumenta di 1 anno
                $scheda_dataST=(date('Y')+1)."-12-31";
            } else { // siamo prima del primo settembre
                  $scheda_dataST=date('Y')."-12-31";  
            }
        }
        
        // controlla se i dati sono di un nuovo iscritto o da aggiornare e costruisce le query
        if ($scheda_id != 0 || $scheda_id !=null){
            $query = "UPDATE Catechismi SET Cognome='".$scheda_cognome."', Nome='".$scheda_nome."', Sesso='".$scheda_sesso."'";
                 
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
 
               $query.=", Classe=".$scheda_classe.", Sezione=".$scheda_sezione.", Presenza=".$scheda_partecipazione.", Coro='".$scheda_coro."'";//.", IdRuolo=".$scheda_ruolo;
               $query.=" WHERE id=".$scheda_id;
        } else {
            // prima di salvare controlla se c'è una omonimia sui campi cognome, nome, sesso e data di nascita
            $query="SELECT Cognome,Nome,Sesso,Data_di_nascita FROM Catechismi WHERE Cognome='".$scheda_cognome."' and Nome='".$scheda_nome."' and Sesso='".$scheda_sesso."'";
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
               $query="INSERT INTO Catechismi (Cognome,Nome,Sesso";
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
                  //$insert.=",IdRuolo";
                  //$value.=", $scheda_ruolo";
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
 * segna il giorno di cancellazione e chi ha cancellato) */

function DeleteDatiScheda() {
    global $errore,$messaggio,$icona;
    
    $operatore=$_POST["login"]; // legge l'user id dell'operatore

    $data_cancellazione=date('Y-m-d'); // setta la data di cancellazione
    
    $query="UPDATE Catechismi SET Cancellato=True,DataCancellazione='$data_cancellazione',OperatoreCancellazione='$operatore' WHERE ID=".$_POST["hdnID"];
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
    $query ="SELECT ID,cognome,nome,Data_di_nascita,luogo_di_nascita,sesso,tipo_via,via,numero_civico,citt as citta, cap,provincia,citt,email,datatesseramento,quotaoratorio,TipoQuota,classe,sezione,presenza,coro,parrocchia_provenienza,DataScadenzaTessera FROM Catechismi ";

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
            $query.="WHERE ID=".$_POST["hdnID"]." AND Cancellato=False";
            $parola_chiave=$_POST["hdnID"];
        }
    } elseif ($_POST["txtCognome"]!=""){
        $query.="WHERE cognome LIKE'".$_POST["txtCognome"]."%' AND nome LIKE'".$_POST["txtNome"]."%' AND Cancellato=False";
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
function GetRuoliOratorio() {
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
            
            //tiene selezionato l'opzione relativa al tipo di quota pagata
            switch ($tipo_quota) {
                case 1:
                    if (($quote->IdQuota==1)) {
                        $checked="checked";
                    }
                    else {
                        $checked=null;
                    }
                break;
            
                case 2:
                    if (($quote->IdQuota==2)) {
                        $checked="checked";
                    }
                    else {
                        $checked=null;
                    }
                break;
                
                case 3:
                    if (($quote->IdQuota==3)) {
                        $checked="checked";
                    }
                    else {
                        $checked=null;
                    }
                break;
                
                case 4:
                    if (($quote->IdQuota==4)) {
                        $checked="checked";
                    }
                    else {
                        $checked=null;
                    }
                break;
                
                case 5:
                    if (($quote->IdQuota==5)) {
                        $checked="checked";
                    }
                    else {
                        $checked=null;
                    }
                break;
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
                    
                    if ($tipo_quota==4) {
                        print("<input type='text' class=\"".$classe."\" name='myaltraquota' id='myaltraquota' size='10' onblur='ControlloQuota()' onfocus='FuocoCampoQuota()' value=".$_POST["myaltraquota"]."  /><strong>&nbsp;&euro;</strong></th>");
                    } else {
                        print("<input type='text' class=\"".$classe."\" name='myaltraquota' id='myaltraquota' size='10' onblur='ControlloQuota()' onfocus='FuocoCampoQuota()' value=0.00  /><strong>&nbsp;&euro;</strong></th>");
                    }
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
        $query="SELECT IDTipoTelefono,ID FROM tblTelefoni WHERE IDTipoTElefono=".$valore["IDTipoTelefono"]." AND ID=".$_POST["hdnID"];
        $result=mysql_query($query);
        
        if (mysql_num_rows($result)>0) {
            $query="UPDATE tblTelefoni SET Prefisso='".$valore["Prefisso"]."', Numero='".$valore["Numero"]."', TelSMS=".$valore["TelSMS"].", PrefissoInt='".$valore["PrefissoInt"]."' ";
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
        $query="DELETE FROM tblTelefoni WHERE IDTipoTelefono=".$valore." AND ID=".$_POST["hdnID"];  
        mysql_query($query);
    }

    return;
} 
 
// ********************* FUNZIONE PER CONVERTIRE LA DATA DAL FORMATO DI MYSQL A QUELLO ITALIANO ******
function ConvertiData($data) {
    
    if ($data!=null || $data!="") {
        if (strpos($data,"/")!=2) {
            $data =substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
        }
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
    $query_telefono="SELECT idtelefono,idtipotelefono,id,prefisso,numero,telsms,PrefissoInt FROM tblTelefoni WHERE id='".$_POST['hdnID']."' ORDER BY idtipotelefono ASC";
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
      echo "<option value='".((int)$result->IdGradoParentela-1)."'>".htmlentities($result->GradoParentela)."</option>\n";
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
    $query="SELECT IdFamiglia,IdGradoParentela FROM tblparentela WHERE IdFamigliare=".$_POST["hdnID"];

    $result=mysql_query($query);
    
    if (mysql_num_rows($result) <1) {
        echo "n. d.";
    } else {
          $famiglia=mysql_fetch_object($result);
          $grado_parentela=$famiglia->IdGradoParentela;
          
          // estrae i famigliari dell'iscritto
          $query="SELECT tblparentela.IdFamigliare,tblparentela.IdGradoParentela, Catechismi.Cognome,Catechismi.Nome
                  FROM tblparentela INNER JOIN Catechismi
                  ON tblparentela.IdFamigliare = Catechismi.ID
                  WHERE IdFamiglia=".($famiglia->IdFamiglia)." 
                  AND NOT IdFamigliare=".$_POST["hdnID"]." 
                  ORDER BY IdGradoParentela";
          
          $result=mysql_query($query);
          if (mysql_num_rows($result) >0) {
          
          //inserisce le iconcine dei famigliari
          echo "<table cellpadding=\2\" class=\"famigliari_iscritto\"><tr>";
          while ($famigliari=mysql_fetch_object($result)) {
                  $i++;
                  if($i > 6) {
                      echo "</tr><tr>";
                      $i=1;
                  }
                  
                  $title=htmlentities($famigliari->Cognome)." ".htmlentities($famigliari->Nome);
                  
                  //inserisce le faccine in base al grado di parentela
                  switch ($famigliari->IdGradoParentela) {
                      case 1:
                          if ($grado_parentela < 3 && $famigliari->IdGradoParentela < 3) {
                              $title.=" (moglie)";
                          } else {
                              $title.=" (mamma)";
                          }
                          echo "<th class=\"celle_famigliari_iscritto_mamma\"><img src='./Immagini/omino.png' title=\"$title\" width='15' height='20' /></th>";
                          
                      break;
                  
                      case 2:
                          if ($grado_parentela < 3 && $famigliari->IdGradoParentela < 3) {
                              $title.=" (marito)";
                          } else {
                              $title.=" (pap&agrave;)";
                          }
                          echo "<th class=\"celle_famigliari_iscritto_padre \"><img src='./Immagini/omino.png' title=\"$title\" width='15' height='20' /></th>";
                      break;
                  
                      case 3:
                          if ($grado_parentela < 3 && $famigliari->IdGradoParentela > 2) {
                              $title.=" (figlio)";
                          } else {
                              $title.=" (fratello)";
                          }
                          echo "<th class=\"celle_famigliari_iscritto_fratello \"><img src='./Immagini/omino.png' title=\"$title\" width='15' height='20' /></th>";
                      break;
                  
                      case 4:
                          if ($grado_parentela < 3 && $famigliari->IdGradoParentela > 2) {
                              $title.=" (figlia)";
                          } else {
                              $title.=" (sorella)";
                          }
                          echo "<th class=\"celle_famigliari_iscritto_sorella \"><img src='./Immagini/omino.png' title=\"$title\" width='15' height='20' /></th>";
                      break;
                  }
              } 
          echo "</tr></table>";
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
            tblgradiparentela.GradoParentela 
            FROM (tblparentela INNER JOIN tblgradiparentela ON tblparentela.IdGradoParentela=tblgradiparentela.IdGradoParentela-1) 
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
        $query="SELECT Catechismi.ID,Catechismi.Cognome,Catechismi.Nome,Catechismi.Sesso,Catechismi.Classe,Catechismi.DataScadenzaTessera,tblClassi.IdClasse,tblClassi.Classe 
                FROM Catechismi INNER JOIN tblClassi ON Catechismi.Classe=tblClassi.IdClasse 
                WHERE Catechismi.ID=".($dati->IdFamigliare);
        
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
            
            if ($id_grado_parentela_iscritto == 1 || $id_grado_parentela_iscritto == 2) {
                if ($dati_parente->Sesso == "F") {
                    if ($dati->IdGradoParentela ==1) {
                        echo ("Moglie");
                    } else {
                        echo ("Figlia");
                    }
                } else {
                    if ($dati->IdGradoParentela ==2) {
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
    
    // ottiene il numero dei componenti della famiglia
    $IdElementiFamiglia=(($IdFamigliare[count($IdFamigliare)-1])+1);  
    
    // ottiene l'id della famiglia
    $IdFamiglia=$IdFamigliare[count($IdFamigliare)-2];
    
    // toglie gli ultimi due elementi dell'array poiché sono riferiti a idfamiglia e nr componenti famiglia
    unset ($IdFamigliare[count($IdFamigliare)-1]);
    unset ($IdFamigliare[count($IdFamigliare)-1]);
    
    // cancella i dati dall'archivio
    if ($IdElementiFamiglia < 3) {
        $query="DELETE FROM tblparentela WHERE IdFamiglia=".$IdFamiglia;
        mysql_query($query); 
    } else {
          foreach ($IdFamigliare as $valore) {
              $query="DELETE FROM tblparentela WHERE IdFamigliare=".$valore;  
              mysql_query($query);
          }
    }
    
    return;
}

// ****************** > FUNZIONE CONTROLLA MODIFICHE AI DATI (prima di chiudere la scheda iscritto) < *************************
function ControllaScheda($variazione) {
    $DatiVariati=0;
    global $variazione;
    // recupera i dati dal db
    $query ="SELECT * FROM Catechismi WHERE ID=".$_POST["hdnID"];
    
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
    
    /*if (isset($_POST["myaltraquota"])) {
        if ($_POST["myaltraquota"]!=htmlentities($record["QuotaOratorio"])) {
            $DatiVariati++;
            if (strpos($variazione,"Quota Oratorio")===false) {
                $variazione.="| Quota Oratorio | ";
            }
        }
    }*/
    
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
        //$DatiVariati++;
        //$variazione.="| ruolo | ";
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
// ****************** > FUNZIONE PER RECUPERARE I RUOLI DELL'ISCRITTO < *************************
function GetInfoRuolo() {

    if (isset($_POST["hdnID"])) {
        $query ="SELECT tblanimatori.ID,tblanimatori.IDRuolo, tblruolioratorio.Sigla
                 FROM tblanimatori
                 INNER JOIN tblruolioratorio
                 ON tblanimatori.IDRuolo = tblruolioratorio.IdRuoloOratorio
                 WHERE tblanimatori.ID =".$_POST['hdnID']."
                 ORDER BY tblanimatori.IdRuolo";
    
        $result= mysql_query($query);
        // costruisce i ruoli dell'iscritto
        while ($row=mysql_fetch_object($result)) {
            if ($ruoli==null) {
                $ruoli.=$row->Sigla."/";
            } else {
                $posizione=strstr($ruoli,$row->Sigla); //evita di ripetere la stessa sigla del ruolo
                if ($posizione==false || $posizione==null) {
                      $i++;
                      if ($i>3) {
                        $ruoli=substr($ruoli,0,strlen($ruoli)-1)."<br />".$row->Sigla."/";
                        $i=0;
                      } else {
                            $ruoli.=$row->Sigla."/";
                      }
                }
            }
        }
        
        if ($ruoli!=null || $ruoli!="") {
          echo "<a href=\"javascript:LegendaRuoli(0);\">".substr($ruoli,0,strlen($ruoli)-1)."</a>";
        } else {
            echo "**********";
        }
    } else {
        echo ("n. d.");
    }
    
    return;

}

// ****************** > FUNZIONE GET INFO PARENTELA PER TABELLA< *****************************
function CreaTabellaRuoli()
{
    // variabile di servizio per costruire i colori della tabella
    $i=0; 
    // variabile per controllare la costruzione della tabella
    $indice=0;
    // variabile di servizio che indicati quanti elementi al massimo può contenere la tabella ruoli
    $nrelementi_tabella = 7; 
    
    // serve quando si fa una nuova iscrizione per non dare errore nella costruzione della tabella
    if (!isset($_POST["hdnID"]) || $_POST["hdnID"]==null) {
        return;
    }   
    
    $query= "SELECT tblanimatori.IDAnimatore,tblruolioratorio.RuoloOratorio,tblClassi.Classe,tblSezioni.Sezione
            FROM tblanimatori INNER JOIN tblruolioratorio
            ON tblanimatori.IDRuolo = tblruolioratorio.IdRuoloOratorio
            INNER JOIN tblClassi
            ON tblanimatori.IDClasse = tblClassi.IDClasse
            INNER JOIN tblSezioni
            ON tblanimatori.IDSezione = tblSezioni.IDSezione
            WHERE tblanimatori.ID =".$_POST["hdnID"]."
            ORDER BY tblruolioratorio.IDRuoloOratorio ASC, tblClassi.IDClasse, tblSezioni.IDSezione ASC";
    
    $result =mysql_query($query);
    
    //CALCOLA QUANTE "PAGINE" DI TABELLA SERVONO IN BASE AL NUMERO MAX DI ELEMENTI E AL NUMERO DI RUOLI ASSEGNATI
    $nr_ruoli=mysql_num_rows($result); //trova il numero di ruoli assegnati all'iscritto
    
    // trova il numero "grezzo" delle pagine che servono
    $valore=($nr_ruoli/$nrelementi_tabella);
     
    if ($valore-(int)$valore >0) {  
        $nrpagine=(int)$valore+1;
    } else {
        $nrpagine=(int)$valore;
    }
    
    // se il numero di pagine da pubblicare è minore di 1... 
    if ($nrpagine < 1) { 
        $nrpagine = 1;
    }    
    
    // setta la pagina corrente 
    if (isset($_POST["nrpagina_tabella_ruoli"]) && $_POST["nrpagina_tabella_ruoli"] >0) { 
        if ($_POST["nrpagina_tabella_ruoli"]>$nrpagine) {
            $_POST["nrpagina_tabella_ruoli"]=$nrpagine;
        }
           
        $nrpagina_corrente=$_POST["nrpagina_tabella_ruoli"];
    } else {
         $nrpagina_corrente=1;
    }
    
     //visualizza il navigatore di pagine
    echo "\n";
    echo "<div id=\"div_nrpagine\">\n";
    echo "|<img src=\"./Immagini/indietro.png\" alt=\"icona back\" width=\"25\" height=\"25\" onClick=\"ChangePageTabellaRuoli($nrpagina_corrente-1,$nrpagine);\" />"; 
    echo "&nbsp;&nbsp;pagina $nrpagina_corrente/$nrpagine&nbsp;&nbsp;";
    echo "<img src=\"./Immagini/avanti.png\" alt=\"icona back\" width=\"25\" height=\"25\" onClick=\"ChangePageTabellaRuoli($nrpagina_corrente+1,$nrpagine);\" />"; 
    echo "|"; //elementi cestinati: $nr_ruoli |\n";
    echo "</div>\n";
    
    /* trova la posizione, all'interno dei records trovati, dalla quale partire
         * per visualizzare gli elementi cancellati nel caso ci siano più pagine */ 
        if ($_POST["nrpagina_tabella_ruoli"]>1) {

            $posizione=($_POST["nrpagina_tabella_ruoli"]-1)*$nrelementi_tabella;
            
            mysql_data_seek($result,$posizione);
        }
    
    //COSTRUISCE LA TABELLA
    while ($row=mysql_fetch_object($result))
    {
        // controlla che nella costruzione della tabella non venga superato il numero degli elementi consentiti
        $indice++;
        if ($indice > $nrelementi_tabella) {
            break;
        }
            
        $i++; //indice per stabilire i colori delle righe della tabella
        if ($i % 2) {
            $classe="cella_ruoli_dispari";  
        } else {
            $classe ="cella_ruoli_pari";
        }
        
        print "<tr> \n";
        print "<th class=$classe><input type=\"checkbox\" name=\"SelRuolo\" value=\"".$row->IDAnimatore."\" /> </th>\n";
        print "<th class=$classe>".htmlentities($row->RuoloOratorio)."</th> \n";
        print "<th class=$classe>".htmlentities($row->Classe)."</th> \n";
        print "<th class=$classe>".htmlentities($row->Sezione)."</th> \n";
        print "</tr> \n";
    }    
    
    return;
}

// ****************** > FUNZIONE GET LEGENDA RUOLI< *****************************
function GetLegendaRuoli()
{
    $i=0; // variabile di servizio per costruire i colori della tabella
    
    $query="SELECT * FROM tblruolioratorio ORDER BY IdRuoloOratorio ASC";
    
    $result=mysql_query($query);
    
    echo "<table id=\"tabellalegendaruoli\" class=\"layout_tabella_legenda_ruoli\">";
    echo "<tr>";
    echo "<th>SIGLA</th>";
    echo "<th>RUOLO</th>";
    echo "</tr>";
    
    while ($row=mysql_fetch_object($result)) {
        $i++;
        if ($i!=1) { 
            if ($i % 2) {
                $classe="cella_ruoli_dispari";
            } else {
                $classe ="cella_ruoli_pari";
            }

            echo "<tr>";
            echo "<th class=$classe>".$row->Sigla."</th>";
            echo "<th class=$classe>".$row->RuoloOratorio."</th>";
            echo "</tr>";
        }
    }
    
    echo "</table>";
    return;
}

// ****************** > FUNZIONE AGGIUNGI RUOLI ALL'ISCRITTO< *****************************
function AggiungiRuolo()
{
  // errore 1 = superato il numero consentito di ruoli in oratorio
  // errore 2 = ruolo già associato
    
  $errore=0;
  
  $query = "SELECT * FROM tblanimatori WHERE ID=".$_POST["hdnID"];

  
  $result=mysql_query($query);
  
  /*if (mysql_num_rows($result)+1>7) {
    $errore=1;
    return $errore;
  }
  */
  while($row=mysql_fetch_object($result)) {
      if ($row->IDRuolo == $_POST['myruolo'] && $row->IDClasse==$_POST['myruoloclasse'] && $row->IDSezione==$_POST['myruologruppo']) {
          $errore=2;
          return $errore;
      }
  }
  
  mysql_query("START TRANSACTION");
  
  $query="INSERT INTO tblanimatori (ID,IDRuolo,IDClasse,IDSezione)
          VALUES ('".$_POST['hdnID']."','".$_POST['myruolo']."','".$_POST['myruoloclasse']."','".$_POST['myruologruppo']."')";
  
  $result=mysql_query($query);
  
  mysql_query("COMMIT");
  
  return $errore;
}

// ****************** > FUNZIONE AGGIUNGI RUOLI ALL'ISCRITTO< *****************************
function CancellaRuolo()
{
    $errore=0;
    
    //scompone i dati arrivati dal form
    $DatiDaCancellare=explode("|",$_POST["dati_check_ruoli"]);
    
    foreach ($DatiDaCancellare as $valore) {
        $query="DELETE FROM tblanimatori WHERE IDAnimatore=".$valore;  
        mysql_query($query);
    }
    return;
}

// ****************** > FUNZIONE SALVA DATI PARENTELA < *****************************
function SalvaDatiParentelaNuovo()
{
    // variabili
    $errore=0; // indica il tipo di errore nella gestione dell'inserimento del famigliare da aggiungere
    $id_nucleo_A=(int)$_POST["hdnIDParente"];// identificativo del famigliare da aggiungere
    
    // analizza il grado di parentela arrivato dal form (i valori permessi sono 1,2,3,4)
    if ((int)$_POST["GradoParentela"] >4) {
        $grado_nucleo_A=(int)$_POST["GradoParentela"]-4;
    } else {
        $grado_nucleo_A=(int)$_POST["GradoParentela"];
    }
    
    // memorizza la scelta fatta in origine dall'operatore
    // serve per stabilire se necessario il grado di parentela del componente
    // del nucleo B
    $scelta_selezionata=(int)$_POST["GradoParentela"];
    
    // identificativo dell'iscritto della scheda aggiunta
    $id_nucleo_B=(int)$_POST["hdnID"];
    
    //****** CONTROLLI SUL COMPONENTE DA AGGIUNGERE (NUCLEO A) ******
    //recupera il sesso e la data di nascita
    $query="SELECT Sesso,Data_di_nascita FROM Catechismi WHERE ID =".$id_nucleo_A;
            
    $nucleoA=mysql_query($query);
    
    $rowA=mysql_fetch_object($nucleoA);
    
    $sesso_nucleo_A=$rowA->Sesso;
    $data_nucleo_A=(int)substr($rowA->Data_di_nascita,0,4);
    
    // controlla che sia stato scelto correttamente
    //il grado di parentela in relazione al sesso
    switch ($sesso_nucleo_A) {
        case "F":
            if ($grado_nucleo_A==2 || $grado_nucleo_A==3) {
                $errore=2;
                return $errore;
            }
        break;
        
        case "M":
            if ($grado_nucleo_A==1 || $grado_nucleo_A==4) {
                $errore=2;
                return $errore;
            }
        break;
    }
    
    // controlla se il famigliare da aggiungere è già associato a una famiglia
    // in caso affermativo ne estrae i componenti con le necessarie info
    $query= "SELECT tblparentela.IdFamigliare,Catechismi.Cognome,Catechismi.Nome,Catechismi.Data_di_nascita,
                    Catechismi.Sesso,tblparentela.IdFamiglia,tblparentela.IdGradoParentela
             FROM tblparentela
             INNER JOIN Catechismi
             ON tblparentela.IdFamigliare = Catechismi.ID
             WHERE tblparentela.IdFamiglia=(SELECT IdFamiglia FROM tblparentela WHERE IdFamigliare=".$id_nucleo_A.")";

    $nucleoA=mysql_query($query);
    
    
    //CONTROLLA SE ESISTE GIÀ UN ID DI FAMIGLIA IN RELAZIONE CON L'ISCRITTO DELLA SCHEDA APERTA, SE NO LO CREA
    if ($_POST["id_famiglia"]==-1) {
        $query="SELECT Max(IdFamiglia) as UltimoId FROM tblparentela";
        $result_id_famiglia=mysql_query($query);
        $dati=mysql_fetch_object($result_id_famiglia);
        
        // setta l'id della famiglia
        $id_famiglia_B =(($dati->UltimoId)+1);
    } else {
          $id_famiglia_B = $_POST["id_famiglia"];
    }

    // ****** CONTROLLI SUL COMPONENTE DELLA SCHEDA APERTA (NUCLEO B) ******
    //recupera le info sui componenti della famiglia dell'iscritto della scheda aperta
    $query="SELECT tblparentela.IdFamigliare,tblparentela.IdFamiglia,tblparentela.IdGradoParentela, 
                   Catechismi.Data_di_nascita,Catechismi.Sesso
            FROM tblparentela
            INNER JOIN Catechismi
            ON tblparentela.IdFamigliare = Catechismi.ID
            WHERE tblparentela.IdFamiglia =".$id_famiglia_B;
            
    $nucleoB=mysql_query($query);
    
    // se non esiste nessun elemento del nucleo B bisogna trovare i dati necessari per i controlli
    if (mysql_num_rows($nucleoB) < 1) {
        $query="SELECT Sesso,Data_di_nascita FROM Catechismi WHERE ID =".$id_nucleo_B;
            
        $nucleoB1=mysql_query($query);
        
        $rowB1=mysql_fetch_object($nucleoB1);
    
        $data_nucleo_B=(int)substr($rowB1->Data_di_nascita,0,4);    
        $sesso_nucleo_B=$rowB1->Sesso;
        
        // stabilisce il grado di parentela in base alla scelta fatta
        // dall'operatore per il componente del nucleo A
        if ($scelta_selezionata < 5 ) {
            switch ($sesso_nucleo_B) {
                case "F":
                    $grado_nucleo_B=4; //figlia/sorella
                break;
                
                case "M":
                    $grado_nucleo_B=3; //figlio/fratello
                break;             
            }
        } else {
            switch ($sesso_nucleo_B) {
                case "F":
                    $grado_nucleo_B=1; //mamma/moglie
                break;
                
                case "M":
                    $grado_nucleo_B=2; //papà/marito
                break;             
            }
        }
    }
   
    // ******* CONTROLLO INCROCIATO DEI COMPONENTI DEI DUE NUCLEI**********    
    if (mysql_num_rows($nucleoA)>0) { // esiste un nucleo A
        while ($rowA=mysql_fetch_object($nucleoA)) { //nucleo A
            $id_nucleo_A=$rowA->IdFamigliare;
            $id_famiglia_A=$rowA->IdFamiglia;
            //preserva la scelta fatta dall'operatore per il grado di parentela
            if ($id_nucleo_A!=$_POST["hdnIDParente"]) {
                $grado_nucleo_A=$rowA->IdGradoParentela;
            } else {
                if ($scelta_selezionata > 4) {
                    $grado_nucleo_A=($scelta_selezionata-4);
                } else {
                    $grado_nucleo_A=$scelta_selezionata;
                }
            }
            
            // serve per controllare che non vengano inseriti fratelli o genitori dei genitori
            // gradi di parentela non ammessi
            $grado_nucleo_A_Zii=$rowA->IdGradoParentela; 
            
            // ottiene il sesso del componente del nucleo A
            $sesso_nucleo_A=$rowA->Sesso;
            
            // ottiene l'anno di nascita del componente del nucleo A
            $data_nucleo_A=(int)substr($rowA->Data_di_nascita,0,4);
                    
            if (mysql_num_rows($nucleoB)>0) { // il nucleo B ha già elementi
                while ($rowB=mysql_fetch_object($nucleoB)) { // nucleo B
                    // ottiene i dati da confrontare con il nucleo A
                    $id_nucleo_B=$rowB->IdFamigliare;
                    $grado_nucleo_B =$rowB->IdGradoParentela;
                    $data_nucleo_B=(int)substr($rowB->Data_di_nascita,0,4);
                    $sesso_nucleo_B=$rowB->Sesso;
                    
                    //controlla che il componente del nucleo A non sia già stato associato al nucleo B
                    if ($id_nucleo_A==$id_nucleo_B) {
                        $errore=1;
                        return $errore;
                    }
            
                    // controlla compatibilità della scelta dell'operatore con i gradi di parentela
                    // già presenti con i due nuclei famigliari
                    if ($scelta_selezionata == 1 || $scelta_selezionata == 2) { // mamma & papà
                        // controlla che non vengano inseriti parenti non ammessi                                
                        if ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B < 3) { // tutti genitori
                            if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                $errore=5;
                                return $errore;
                            } elseif (($grado_nucleo_A_Zii == $grado_nucleo_B) && ($grado_nucleo_A_Zii < 3 || $grado_nucleo_B < 3)) { // genitori già presenti  
                                  if ($grado_nucleo_A_Zii == 1 || $grado_nucleo_B == 1) {
                                      $errore=3.1;
                                      return $errore;
                                  } elseif($grado_nucleo_A_Zii == 2 || $grado_nucleo_B == 2){
                                        $errore=3;
                                        return $errore;
                                  }
                            }
                        } elseif (($grado_nucleo_A_Zii > 2 && $grado_nucleo_B < 3)) { //figli/genitori
                            if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                $errore=2;
                                return $errore;
                            }
                        } elseif ($grado_nucleo_A_Zii > 2 && $grado_nucleo_B > 2) { // figli
                            if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                $errore=2;
                                return $errore;
                            }
                        }
                        
                        // controlla che i genitori non siano più giovani o coetanei dei figli
                            if ($data_nucleo_A > $data_nucleo_B) { //con i componenti del nucleo A più giovani del nucleo B
                                if ($grado_nucleo_A < 3 && $grado_nucleo_B > 2) {
                                    $errore=4;
                                    return $errore;
                                }
                            } elseif ($data_nucleo_A < $data_nucleo_B) { // con i componenti del nucleo A più vecchi del nucleo B
                                  if ($grado_nucleo_A >2 && $grado_nucleo_B < 3) { 
                                      $errore=4;
                                      return $errore;
                                  }
                            } else { // coetanei  
                                  if ($grado_nucleo_A < 3 && $grado_nucleo_B > 2){ 
                                      if ($scelta_selezionata == 5 || $scelta_selezionata == 6) {
                                          $errore =7;
                                          return $errore;
                                      } else {
                                            $errore=8;
                                            return $errore;
                                      }
                                    } elseif ($grado_nucleo_A > 2 && $grado_nucleo_B < 3) {
                                          $errore=8; 
                                          return $errore; 
                                    }
                            }
                    } elseif ($scelta_selezionata == 3 || $scelta_selezionata == 4) { // fratello & sorella
                          // controlla che non vengano inseriti zii  
                          if ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B > 2) { //genitori/figli
                              if ($id_nucleo_A == $_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                  $errore=2;
                                  return $errore;
                              }
                          } elseif ($grado_nucleo_A_Zii > 2 && $grado_nucleo_B < 3) { //figli/genitori
                                if ($id_nucleo_A == $_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){ 
                                    $errore=2;
                                    return $errore;
                                }    
                          } elseif ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B < 3) { //genitori/genitori
                                if ($id_nucleo_A == $_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){  
                                    $errore=6;
                                    return $errore;
                                } elseif (($grado_nucleo_A_Zii == $grado_nucleo_B) && ($grado_nucleo_A_Zii < 3 || $grado_nucleo_B < 3)) { // genitori già presenti  
                                      if ($grado_nucleo_A_Zii == 1 || $grado_nucleo_B == 1) {
                                          $errore=3.1;
                                          return $errore;
                                      } elseif($grado_nucleo_A_Zii == 2 || $grado_nucleo_B == 2){
                                        $errore=3;
                                        return $errore;
                                      }
                                }
                          } 
                    } elseif ($scelta_selezionata == 5 || $scelta_selezionata == 6) { // moglie & marito
                          //controlla che non vengano inseriti due mogli/mariti
                          if ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B < 3) { //tutti e due genitori
                              if ($sesso_nucleo_A == $sesso_nucleo_B) {
                                  $errore=10;
                                  return $errore;
                              }
                          } elseif ($grado_nucleo_A_Zii > 2 && $grado_nucleo_B > 2) { //tutti e due figli
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore=2;
                                    return $errore;
                                }
                          } else { //genitori/figli
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore=2;
                                    return $errore;
                                }
                          }
                    } elseif ($scelta_selezionata == 7 || $scelta_selezionata == 8) { // figlio & figlia
                          if ($grado_nucleo_A_Zii > 2 && $grado_nucleo_B > 2) {
                              if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                  $errore = 2;
                                  return $errore;
                              }
                          } elseif (($grado_nucleo_A_Zii < 3 && $grado_nucleo_B > 2)) {
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore = 2;
                                    return $errore;
                                }
                          } elseif ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B < 3) {
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore = 5;
                                    return $errore;
                                }
                          }
                    }
                }
                
                //bisogna tornare all'inizio del recordset B
                mysql_data_seek($nucleoB,0);
                    
            } else { // NON C'È NESSUN COMPONENTE NEL NUCLEO B
                  // controlla l'inserimento dei genitori
                    if ($scelta_selezionata == 1 || $scelta_selezionata == 2) { // mamma & papà
                        // controlla che non vengano inseriti parenti non ammessi                                
                        if ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B < 3) { // tutti genitori
                            if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                $errore=5;
                                return $errore;
                            } elseif (($grado_nucleo_A_Zii == $grado_nucleo_B) && ($grado_nucleo_A_Zii < 3 || $grado_nucleo_B < 3)) { // genitori già presenti  
                                  if ($grado_nucleo_A_Zii == 1 || $grado_nucleo_B == 1) {
                                      $errore=3.1;
                                      return $errore;
                                  } elseif($grado_nucleo_A_Zii == 2 || $grado_nucleo_B == 2){
                                        $errore=3;
                                        return $errore;
                                  }
                            }
                        } elseif (($grado_nucleo_A_Zii > 2 && $grado_nucleo_B < 3)) { //figli/genitori
                            if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                $errore=2;
                                return $errore;
                            }
                        } elseif ($grado_nucleo_A_Zii > 2 && $grado_nucleo_B > 2) { // figli
                            if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                $errore=2;
                                return $errore;
                            }
                        }
                        
                        // controlla che i genitori non siano più giovani o coetanei dei figli
                            if ($data_nucleo_A > $data_nucleo_B) { //con i componenti del nucleo A più giovani del nucleo B
                                if ($grado_nucleo_A < 3 && $grado_nucleo_B > 2) {
                                    $errore=4;
                                    return $errore;
                                }
                            } elseif ($data_nucleo_A < $data_nucleo_B) { // con i componenti del nucleo A più vecchi del nucleo B
                                  if ($grado_nucleo_A >2 && $grado_nucleo_B < 3) { 
                                      $errore=4;
                                      return $errore;
                                  }
                            } else { // coetanei  
                                  if ($grado_nucleo_A < 3 && $grado_nucleo_B > 2){ 
                                      if ($scelta_selezionata == 5 || $scelta_selezionata == 6) {
                                          $errore =7;
                                          return $errore;
                                      } else {
                                            $errore=8;
                                            return $errore;
                                      }
                                    } elseif ($grado_nucleo_A > 2 && $grado_nucleo_B < 3) {
                                          $errore=8; 
                                          return $errore; 
                                    }
                            }
                    }
                  
                  // controlla inserimento di parentele non ammesse nel nucleo famigliare
                  if ($scelta_selezionata == 3 || $scelta_selezionata == 4) { // fratello & sorella
                          // controlla che non vengano inseriti zii  
                          if ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B > 2) { //genitori/figli
                              if ($id_nucleo_A == $_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                  $errore=2;
                                  return $errore;
                              }
                          } elseif ($grado_nucleo_A_Zii > 2 && $grado_nucleo_B < 3) { //figli/genitori
                                if ($id_nucleo_A == $_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){ 
                                    $errore=2;
                                    return $errore;
                                }    
                          } elseif ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B < 3) { //genitori/genitori
                                if ($id_nucleo_A == $_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){  
                                    $errore=6;
                                    return $errore;
                                } elseif (($grado_nucleo_A_Zii == $grado_nucleo_B) && ($grado_nucleo_A_Zii < 3 || $grado_nucleo_B < 3)) { // genitori già presenti  
                                      if ($grado_nucleo_A_Zii == 1 || $grado_nucleo_B == 1) {
                                          $errore=3.1;
                                          return $errore;
                                      } elseif($grado_nucleo_A_Zii == 2 || $grado_nucleo_B == 2){
                                        $errore=3;
                                        return $errore;
                                      }
                                }
                          } 
                    }
                    
                    // controlla inserimento di moglie e marito
                    if ($scelta_selezionata == 5 || $scelta_selezionata == 6) { // moglie & marito
                          //controlla che non vengano inseriti due mogli/mariti
                          if ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B < 3) { //tutti e due genitori
                              if ($sesso_nucleo_A == $sesso_nucleo_B) {
                                  $errore=7;
                                  return $errore;
                              }
                          } elseif ($grado_nucleo_A_Zii > 2 && $grado_nucleo_B > 2) { //tutti e due figli
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore=2;
                                    return $errore;
                                }
                          } else { //genitori/figli
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore=2;
                                    return $errore;
                                }
                          }
                    }
                    // controlla l'inserimento di figli
                    if ($scelta_selezionata == 7 || $scelta_selezionata == 8) { // figlio & figlia
                          if ($grado_nucleo_A_Zii > 2 && $grado_nucleo_B > 2) {
                              if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                  $errore = 2;
                                  return $errore;
                              }
                          } elseif (($grado_nucleo_A_Zii < 3 && $grado_nucleo_B > 2)) {
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore = 2;
                                    return $errore;
                                }
                          } elseif ($grado_nucleo_A_Zii < 3 && $grado_nucleo_B < 3) {
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore = 5;
                                    return $errore;
                                }
                          }
                    }
            }
        }
    } else { // IL NUCLEO A NON HA ELEMENTI
          if (mysql_num_rows($nucleoB)>0) { // il nucleo B ha già elementi
                while ($rowB=mysql_fetch_object($nucleoB)) { // nucleo B
                    // ottiene i dati da confrontare con il nucleo A
                    $id_nucleo_B=$rowB->IdFamigliare;
                    $grado_nucleo_B =$rowB->IdGradoParentela;
                    $data_nucleo_B=(int)substr($rowB->Data_di_nascita,0,4);
                    $sesso_nucleo_B=$rowB->Sesso;

                    // controlla l'inserimento dei genitori
                    if ($scelta_selezionata == 1 || $scelta_selezionata == 2) { // mamma & papà
                        // controlla che non vengano inseriti parenti non ammessi                                
                        if ($grado_nucleo_A < 3 && $grado_nucleo_B < 3) { // tutti genitori
                            if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                $errore=5;
                                return $errore;
                            } elseif (($grado_nucleo_A == $grado_nucleo_B) && ($grado_nucleo_A < 3 || $grado_nucleo_B < 3)) { // genitori già presenti  
                                  if ($grado_nucleo_A == 1 || $grado_nucleo_B == 1) {
                                      $errore=3.1;
                                      return $errore;
                                  } elseif($grado_nucleo_A == 2 || $grado_nucleo_B == 2){
                                        $errore=3;
                                        return $errore;
                                  }
                            }
                        } elseif (($grado_nucleo_A > 2 && $grado_nucleo_B < 3)) { //figli/genitori
                            if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                $errore=2;
                                return $errore;
                            }
                        } elseif ($grado_nucleo_A > 2 && $grado_nucleo_B > 2) { // figli
                            if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                $errore=2;
                                return $errore;
                            }
                        }
                    }
                  
                  // controlla inserimento di parentele non ammesse nel nucleo famigliare
                  if ($scelta_selezionata == 3 || $scelta_selezionata == 4) { // fratello & sorella
                          // controlla che non vengano inseriti zii  
                          if ($grado_nucleo_A < 3 && $grado_nucleo_B > 2) { //genitori/figli
                              if ($id_nucleo_A == $_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                  $errore=2;
                                  return $errore;
                              }
                          } elseif ($grado_nucleo_A > 2 && $grado_nucleo_B < 3) { //figli/genitori
                                if ($id_nucleo_A == $_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){ 
                                    $errore=6;
                                    return $errore;
                                }    
                          } elseif ($grado_nucleo_A < 3 && $grado_nucleo_B < 3) { //genitori/genitori
                                if ($id_nucleo_A == $_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){  
                                    $errore=6;
                                    return $errore;
                                } elseif (($grado_nucleo_A == $grado_nucleo_B) && ($grado_nucleo_A < 3 || $grado_nucleo_B < 3)) { // genitori già presenti  
                                      if ($grado_nucleo_A_Zii == 1 || $grado_nucleo_B == 1) {
                                          $errore=3.1;
                                          return $errore;
                                      } elseif($grado_nucleo_A == 2 || $grado_nucleo_B == 2){
                                        $errore=3;
                                        return $errore;
                                      }
                                }
                          } 
                    }
                    
                    // controlla inserimento di moglie e marito
                    if ($scelta_selezionata == 5 || $scelta_selezionata == 6) { // moglie & marito
                          //controlla che non vengano inseriti due mogli/mariti
                          if ($grado_nucleo_A < 3 && $grado_nucleo_B < 3) { //tutti e due genitori
                              if ($sesso_nucleo_A == $sesso_nucleo_B) {
                                  $errore=7;
                                  return $errore;
                              }
                          } elseif ($grado_nucleo_A > 2 && $grado_nucleo_B > 2) { //tutti e due figli
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore=2;
                                    return $errore;
                                }
                          } else { //genitori/figli
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore=7;
                                    return $errore;
                                }
                          }
                    }
                    
                    // controlla l'inserimento di figli
                    if ($scelta_selezionata == 7 || $scelta_selezionata == 8) { // figlio & figlia
                          if ($grado_nucleo_A > 2 && $grado_nucleo_B > 2) {
                              if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                  $errore = 2;
                                  return $errore;
                              }
                          } elseif (($grado_nucleo_A < 3 && $grado_nucleo_B > 2)) {
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore = 2;
                                    return $errore;
                                }
                          } elseif ($grado_nucleo_A < 3 && $grado_nucleo_B < 3) {
                                if ($id_nucleo_A==$_POST["hdnIDParente"] && $id_nucleo_B == $_POST["hdnID"]){
                                    $errore = 5;
                                    return $errore;
                                }
                          }
                    }
                    
                     // controlla che i genitori non siano più giovani o coetanei dei figli
                            if ($data_nucleo_A > $data_nucleo_B) { //con i componenti del nucleo A più giovani del nucleo B
                                if ($grado_nucleo_A < 3 && $grado_nucleo_B > 2) {
                                    $errore=4;
                                    return $errore;
                                }
                            } elseif ($data_nucleo_A < $data_nucleo_B) { // con i componenti del nucleo A più vecchi del nucleo B
                                  if ($grado_nucleo_A >2 && $grado_nucleo_B < 3) { 
                                      $errore=4;
                                      return $errore;
                                  }
                            } else { // coetanei  
                                  if ($grado_nucleo_A < 3 && $grado_nucleo_B > 2){ 
                                      if ($scelta_selezionata == 5 || $scelta_selezionata == 6) {
                                          $errore =7;
                                          return $errore;
                                      } else {
                                            $errore=8;
                                            return $errore;
                                      }
                                    } elseif ($grado_nucleo_A > 2 && $grado_nucleo_B < 3) {
                                          $errore=8; 
                                          return $errore; 
                                    }
                            }
                  }
            } else { // NESSUN ELEMENTO COMPONE SIA IL NUCLEO A SIA IL NUCLEO B
                  
                  // controlla inserimento di moglie e marito
                    if ($scelta_selezionata == 5 || $scelta_selezionata == 6) { // moglie & marito
                        //controlla che non vengano inseriti due mogli/mariti
                        if ($sesso_nucleo_A == $sesso_nucleo_B) {
                            $errore=7;
                            return $errore;
                        }
                    }
                  
                  // controlla che i genitori non siano più giovani o coetanei dei figli
                            if ($data_nucleo_A > $data_nucleo_B) { //con i componenti del nucleo A più giovani del nucleo B
                                if ($grado_nucleo_A < 3 && $grado_nucleo_B > 2) {
                                    $errore=4;
                                    return $errore;
                                }
                            } elseif ($data_nucleo_A < $data_nucleo_B) { // con i componenti del nucleo A più vecchi del nucleo B
                                  if ($grado_nucleo_A >2 && $grado_nucleo_B < 3) { 
                                      $errore=4;
                                      return $errore;
                                  }
                            } else { // coetanei  
                                  if ($grado_nucleo_A < 3 && $grado_nucleo_B > 2){ 
                                      if ($scelta_selezionata == 5 || $scelta_selezionata == 6) {
                                          $errore =7;
                                          return $errore;
                                      } else {
                                            $errore=8;
                                            return $errore;
                                      }
                                    } elseif ($grado_nucleo_A > 2 && $grado_nucleo_B < 3) {
                                          $errore=8;
                                          return $errore;
                                    }
                            }
            }
    }
    
    // VA (finalmente...!!) A SALVARE I DATI IN TABELLA
    if (mysql_num_rows($nucleoA) > 0 && mysql_num_rows($nucleoB) > 0) {
        // stabilisce quale famiglia deve modificare
        if ($id_famiglia_A < $id_famiglia_B) {
            $id_famiglia = $id_famiglia_A;
            $famiglia_id = $id_famiglia_B;
        } else {
            $id_famiglia = $id_famiglia_B;
            $famiglia_id = $id_famiglia_A;
        }
    
        // manda a mysql i dati
        $query ="UPDATE tblparentela SET IdFamiglia = '".$id_famiglia."' WHERE IdFamiglia = '".$famiglia_id."'";
        $salvataggio=mysql_query($query) || die ($query);
        
    } elseif (mysql_num_rows($nucleoA) > 0 && mysql_num_rows($nucleoB) == 0){
          $query="INSERT INTO tblparentela (IdFamigliare,IdGradoParentela,IdFamiglia) VALUES('$id_nucleo_B','$grado_nucleo_B','$id_famiglia_A')";
          $salvataggio=mysql_query($query) || die ($query);
    
    } elseif (mysql_num_rows($nucleoA) == 0 && mysql_num_rows($nucleoB) > 0){
          $query="INSERT INTO tblparentela (IdFamigliare,IdGradoParentela,IdFamiglia) VALUES('$id_nucleo_A','$grado_nucleo_A','$id_famiglia_B')";
          $salvataggio=mysql_query($query) || die ($query);
   
    } else {
          $query="INSERT INTO tblparentela (IdFamigliare,IdGradoParentela,IdFamiglia) VALUES('$id_nucleo_B','$grado_nucleo_B','$id_famiglia_B')";
          $salvataggio=mysql_query($query) || die ($query);
          
          $query="INSERT INTO tblparentela (IdFamigliare,IdGradoParentela,IdFamiglia) VALUES('$id_nucleo_A','$grado_nucleo_A','$id_famiglia_B')";
          $salvataggio=mysql_query($query) || die ($query);
    }
    
   
    return $errore;    
}

// ****************** > FUNZIONE RECUPERA FAMIGLIARI DEL NUCLEO A < *****************************
function GetFamigliariParentelaNucleoA() {
    
    if (isset($_POST["id_famiglia_nucleo_A"]) && $_POST["id_famiglia_nucleo_A"] !="") {
        $query ="SELECT tblparentela.IdGradoParentela,Catechismi.Cognome,Catechismi.Nome
                FROM tblparentela INNER JOIN Catechismi
                ON tblparentela.IdFamigliare = Catechismi.ID
                WHERE tblparentela.IdFamiglia =".$_POST["id_famiglia_nucleo_A"]. "
                ORDER BY tblparentela.IdGradoParentela";
        
        $result = mysql_query($query);
        
        echo "<table id=\"tabella_parentela_nucleo_A\"><tr>";
        
        while ($row=mysql_fetch_object($result)) {
            switch ($row->IdGradoParentela) {
                case 1:
                    $classe="cella_parentela_nucleo_A_mamma";
                break;
                
                case 2:
                    $classe="cella_parentela_nucleo_A_padre";
                    $title ="";
                break;
                
                case 3:
                    $classe="cella_parentela_nucleo_A_fratello";
                break;
                
                case 4:
                    $classe="cella_parentela_nucleo_A_sorella";
                break;
            }
            
            echo "<th class=$classe><img src='./Immagini/omino.png' title='".htmlentities($row->Cognome)." ".htmlentities($row->Nome)."' width='16' height='21' /></th>";

        }
        echo "</tr></table>";
        
    } else {
          echo "&nbsp;";
    }
    
    unset ($_POST["id_famiglia_nucleo_A"]);
    
    return;
}
?>
