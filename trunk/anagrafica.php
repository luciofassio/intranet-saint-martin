<?php
	require('accesso_db.inc');	
	require('funzioni_generali.inc');
	session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Gestione Oratorio / Anagrafica</title>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> -->
	<?php SelectCSS("styleanagrafica") ?>
  <script type="text/javascript" src="./js/f_anagrafica.js"></script>
  <script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>
  
<?php 
	// controllo l'autenticazione
	if (!isset($_SESSION['authenticated_user'])) {
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			header("Location: http://$host$uri/logon.php");
			exit();
	}
	$idoperatore = $_SESSION['authenticated_user_id'];
	ConnettiDB();
	$rstOperatore = GetOperatore($idoperatore);
	$rowOperatore = mysql_fetch_object($rstOperatore);

      // Crea l'array $giorni per il calcolo della data
      $giorni=array(
          0=>"domenica",
          1=>"luned"."&igrave;",
		      2=>"marted"."&igrave;",
		      3=>"mercoled"."&igrave;",
		      4=>"gioved"."&igrave;",
		      5=>"venerd"."&igrave;",
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
        $data_server=date('d/m/Y');
           
    /**********************************************************************************
    /*                    ROUTINE PER IL SALVATAGGIO DEI DATI NEL DATABASE            *
    /*                               TABELLA 'CATECHISMI'                             *
   /*                              versione 1.2 - 2/05/09                             *
    /**********************************************************************************/
     if ($_POST["salva_scheda"]=="true") 
     {
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
        $scheda_tesserato=$_POST["chkTesserato"];        
        $scheda_quota=$_POST["myquota"];
        $scheda_dataT=$_POST["dataT"];
        $scheda_classe=$_POST["myclassi"];
        $scheda_sezione=$_POST["mysezione"];
        $scheda_partecipazione=$_POST["optPartecipa"];
        $scheda_coro=$_POST["chkCoro"];
        
        // converte i dati provenienti dai checkbox
        if ($scheda_tesserato) {
            $scheda_tesserato="Si";
        } else {
            $scheda_tesserato="No";
        }
        
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
         
        // data di tesseramento 
        if ($scheda_dataT != "" || $scheda_dataT != null) {
            $scheda_dataT=substr($scheda_dataT,6,4)."-".substr($scheda_dataT,3,2)."-".substr($scheda_dataT,0,2);
        }
                
        // controlla se i dati sono di un nuovo iscritto o da aggiornare e costruisce le query
        if ($scheda_id != 0 || $scheda_id !=null){
            $query = "UPDATE Catechismi SET Cognome='".$scheda_cognome."', Nome='".$scheda_nome."', Sesso='".$scheda_sesso."'";
                 
                if ($scheda_dataN != "" || $scheda_dataN != null) { 
                    $query.=", Data_di_nascita ='".$scheda_dataN."'";
                }
                         
                if ($scheda_luogoN != "" || $scheda_luogoN != null) {
                    $query.=", Luogo_di_nascita='".$scheda_luogoN."'";
                }
                        
                if ($scheda_tipo_via != "" || $scheda_tipo_via != null) {
                    $query.=", Tipo_via='".$scheda_tipo_via."'";
                }
                            
                if ($scheda_via != "" || $scheda_via != null) {
                    $query.=", Via='".$scheda_via."'";
                }

                if ($scheda_numero_civico != "" || $scheda_numero_civico != null) {
                    $query.=", Numero_civico='".$scheda_numero_civico."'";
                }
                
                if ($scheda_comune != "" || $scheda_comune != null) {
                    $query.=", Citt='".$scheda_comune."'";
                }
                          
                if ($scheda_cap != "" || $scheda_cap != null) {
                    $query.=", Cap='".$scheda_cap."'";
                }

                if ($scheda_provincia != "" || $scheda_provincia != null) {
                    $query.=", Provincia='".$scheda_provincia."'";
                }
 
                if ($scheda_email != "" || $scheda_email != null) {
                    $query.=", Email='".$scheda_email."'";
                }
                       
                if ($scheda_parrocchia != "" || $scheda_parrocchia != null) {
                    $query.=", Parrocchia_Provenienza=".$scheda_parrocchia;
                } 

                if ($scheda_tesserato != "" || $scheda_tesserato != null) {
                    $query.=", Oratorio='".$scheda_tesserato."'";
                }

                if ($scheda_quota != "" || $scheda_quota != null) {
                    
                    $query.=", QuotaOratorio=".str_replace(",", ".", $scheda_quota);
                }
                   
                if ($scheda_dataT != "" || $scheda_dataT != null) {
                    $query.=", DataTesseramento='".$scheda_dataT."'";
                }
 
               $query.=", Classe=".$scheda_classe.", Sezione=".$scheda_sezione.", Presenza=".$scheda_partecipazione.", Coro='".$scheda_coro."'";
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
        
                if ($scheda_tesserato !="") {
                  $insert.=",Oratorio";
                  $value.=",'$scheda_tesserato'";
                }
        
                if ($scheda_quota !="") {
                  $insert.=",QuotaOratorio";
                  $value.=",".str_replace(",",".",$scheda_quota);
                }
            
                if ($scheda_dataT !="") {
                  $insert.=",DataTesseramento";
                  $value.=",'$scheda_dataT'";
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
        // al contrario pulisce apre la pagina pulita (ma si può anche modificare...)
        if ($scheda_id!=0 || $scheda_id !=null) {
            $_POST["txtCognome"]=$scheda_cognome;
            $_POST["hdnId"]=$scheda_id;
        } else {
            $_POST["txtCognome"]="";
        } 
    }   
  }
 // controllo inserito per chiudere la scheda dell'iscritto
    if ($_POST["txtCognome"]=="") {
      unset($_POST["txtCognome"]);
      unset($_POST["hdnID"]);
    }
    
    /**********************************************************************************
    /*                        ROUTINE PER LA CONNESSIONE AL DATABASE                  *
    /*               E LA RICERCA DEGLI ISCRITTI NELL'ANAGRAFICA DELL'ORATORIO        *
    /*                               TABELLA 'CATECHISMI'                             *
    /**********************************************************************************/
     
    // controlla che sia stato inserito un cognome per la ricera e setta le variabili
    if (isset($_POST["txtCognome"])) {
        $CognomeRicerca =$_POST["txtCognome"];
        $NomeRicerca =$_POST["txtNome"];
              // imposta la query da sottoporre a Mysql e estrae i dati
              if ($_POST["hdnID"]!="") { 
                  // per far funzionare il suggest ajax tramite id dell'iscritto
                  $query ="SELECT id,cognome,nome,data_di_nascita,luogo_di_nascita,sesso,tipo_via,via,numero_civico,citt as citta, cap,provincia,citt,email,datatesseramento,quotaoratorio,oratorio,classe,sezione,presenza,coro,parrocchia_provenienza FROM Catechismi WHERE id=".$_POST["hdnID"];
              } else {
                  // se l'utente non utilizza il suggest ajax                    
                  $query ="SELECT id,cognome,nome,data_di_nascita,luogo_di_nascita,sesso,tipo_via,via,numero_civico,citt as citta, cap,provincia,citt,email,datatesseramento,quotaoratorio,oratorio,classe,sezione,presenza,coro,parrocchia_provenienza FROM Catechismi WHERE cognome LIKE'".$CognomeRicerca."%' and nome LIKE'".$NomeRicerca."%'";
              }
              
              //die($query);
			  $rstRicerca=mysql_query($query);
              
              /* controlla che l'estrazione dei dati richiesti al database sia andata a buon fine
                 e popola i vari arrays con i dati estratti. 
                 la variabile 'indice' serve anche per verificare il numero dei record estratti
                (recordcount in VB...) */
              
              $indice=0;
              while ($record=mysql_fetch_array($rstRicerca))
              {
                  $indice++;
                  $a_id_anagrafica[$indice]=$record["id"];
                  $a_cognome[$indice]=htmlentities($record["cognome"]);
                  $a_nome[$indice]=htmlentities($record["nome"]);
                  $a_sesso[$indice]=$record["sesso"];
                  $a_data_di_nascita[$indice]=$record["data_di_nascita"];
                  $a_luogo_di_nascita[$indice]=htmlentities($record["luogo_di_nascita"]);
                  $a_tipo_via[$indice]=htmlentities($record["tipo_via"]);
                  $a_via[$indice]=htmlentities($record["via"]);
                  $a_numero_civico[$indice]=$record["numero_civico"];
                  $a_citta[$indice]=htmlentities($record["citta"]);
                  $a_cap[$indice]=$record["cap"];
                  $a_provincia[$indice]=$record["provincia"];
                  $a_email[$indice]=$record["email"];
                  $a_tesseramento[$indice]=$record["oratorio"];
                  $a_quota[$indice]=$record["quotaoratorio"];
                  $a_data_tesseramento[$indice]=$record["datatesseramento"];
                  $a_classe[$indice]=$record["classe"];
                  $a_sezione[$indice]=$record["sezione"];
                  $a_presenza[$indice]=$record["presenza"];
                  $a_coro[$indice]=$record["coro"];
                  $a_parrocchia_provenienza[$indice]=$record["parrocchia_provenienza"];
              }
              
              // conta quante corrispondenze ha trovato nel database
              if ($indice == 0) { 
                  echo '<div class="errore">';
                  echo '<p class="erroreconnessione"><strong>***> MESSAGGIO INVIATO DAL SERVER <***';
                  echo '<br \><br \>ATTENZIONE!';
                  echo '<br \>Non e\' stata trovata nessuna corrispondenza!';
                  echo '<br \>'.$NomeRicerca.' '.$CognomeRicerca.' non esiste in questa banca dati.';
                  echo '<br \><br \>(premi <em>ok</em> per ritornare alla pagina dell\'anagrafica)</p>';
                  echo '<input type="button" value="Ok" id="erroreokbottone" onClick="javascript:ApriAnagrafica();"\>';
                  echo '</div>';
                  die ();
              } elseif ($indice > 1) {
                  echo '<div class="errore">';
                  echo '<p class="erroreconnessione"><strong>***> MESSAGGIO INVIATO DAL SERVER <***';
                  echo '<br \><br \>ATTENZIONE!';
                  echo '<br \>Sono state trovate '.$indice.' persone con il cognome';
                  echo '<br \>che hai inserito o con le lettere che hai digitato.';
                  echo '<br \> Consiglio: perch&eacute; non usi il suggerimento che ti viene dato appena digiti un carattere nel campo Cognome?';
                  echo '<br \><br \>(premi <em>ok</em> per ritornare alla pagina dell\'anagrafica)</p>';
                  echo '<input type="button" value="Ok" id="erroreokbottone" onClick="javascript:ApriAnagrafica();"\>';
                  echo '</div>';
                  die ();
              } 
          
          
          // assegna i valori estratti dal database per popolare le varie liste della pagina
          $citta=$a_citta[$indice];
          $via =$a_tipo_via[$indice];
          $classe=$a_classe[$indice];
          $sezione=$a_sezione[$indice];
          $_POST['hdnID']=$a_id_anagrafica[$indice];
          $_POST['hdnIdParrocchia']=$a_parrocchia_provenienza[$indice];


          // in base all'id della parrocchia trova il nome della parrocchia
          if ($a_parrocchia_provenienza[$indice]>0) {
              $parrocchia_provenienza=mysql_query("SELECT * FROM tblparrocchie WHERE idparrocchia=".$a_parrocchia_provenienza[$indice]);
              if (mysql_num_rows($parrocchia_provenienza)>0) {
                  $parrocchia=mysql_fetch_array($parrocchia_provenienza);
                  $nome_parrocchia=htmlentities($parrocchia["Parrocchia"]);
              }
          } else {
                $nome_parrocchia="**********";
          }
    }
        // ********************* FINE ROUTINE DI CONNESSIONE E RICERCA  *************************************
                         
  
//********************* GESTIONE DEI NUMERI DI TELEFONO DELLA RUBRICA (dati dal modulo rubrica) ***************
// CANCELLAZIONE DI UN NUMERO DALLA RUBRICA 
if (isset($_POST['delete_phone'])) {
  if ($_POST['delete_phone']=="true") {
    if (isset($_POST['hdnIDxTel'])) {
        $id_utente=$_POST['hdnIDxTel'];
        $tipo_telefono=$_POST['tipo_phone'];
    
        $query="DELETE FROM tblTelefoni WHERE id=".$id_utente." AND idtipotelefono=".$tipo_telefono;
        $result=mysql_query($query);
        if (mysql_affected_rows()==0) {
	          echo ("<script type=\"text/javascript\">\n");
            echo ("alert(\"Qualcosa è andato storto. Il numero di telefono non è stato eliminato.\");\n");
            echo ("history.back();\n");
            echo ("</script>\n");
        }
    }
  }
}

// SALVATAGGIO O MODIFICA DEL NUMERO DI TELEFONO IN RUBRICA
if ($_POST['delete_phone']=="false") {
    if (isset($_POST['hdnIDxTel'])) {
        $id_utente=$_POST['hdnIDxTel'];
        $prefisso_internazionale=$_POST['prefisso_intnaz'];
        $prefisso_nazionale=$_POST['prefisso_naz'];
        $numero_phone=$_POST['numero_phone'];
        $tipo_telefono=$_POST['tipo_phone'];
        $change_sms=$_POST['change_sms'];
        $change_tel=$_POST['change_tel'];
        $change_tipo_value=$_POST['change_tipo'];
    
        if (isset($_POST['chkSpedizioneSms'])){
            $sms=$_POST['chkSpedizioneSms'];
        } else {
            $sms=0;
        }
        
        if ($change_tel=="true") {
              // controlla lo status dell'abilitazione alla ricezione degli sms.
              // La pone a false su tutti i numeri di telefono per impedire che ci sia più di un numero abilitato)
              if ($change_sms=="true") {
                  $query="UPDATE tblTelefoni SET telsms=0 WHERE id=".$id_utente;
                  $result=mysql_query($query);
              }
              
              // aggiorna il numero di telefono dell'utente selezionato
              if ($change_tipo_value==$tipo_telefono) { 
                  $query="UPDATE tblTelefoni SET prefisso='".$prefisso_nazionale."', numero='".$numero_phone."', telsms=".$sms." WHERE id=".$id_utente." AND idtipotelefono=".$tipo_telefono;
              } else {
                  $query="UPDATE tblTelefoni SET prefisso='".$prefisso_nazionale."', numero='".$numero_phone."', telsms=".$sms.", idtipotelefono=".$change_tipo_value." WHERE id=".$id_utente." AND idtipotelefono=".$tipo_telefono;
              }
        
              $result=mysql_query($query);
              
              // controlla che l'aggiornamento sia avvenuto con successo
              if (mysql_affected_rows() == 0) {
                  if (!$change_sms=="true") {
                      echo ("<script type=\"text/javascript\">\n");
                      echo ("alert(\"Attenzione! L\'aggiornamento del numero è fallito. Qualcosa è andato storto. Riprovare, se il problema persiste contattare gli amministratori del sistema.\");\n");
                      echo ("</script>\n");
                  }
              }
        } else {
            //controlla che il numero di telefono non sia già inserito in archivio
            $query="SELECT idtelefono, id, prefisso, numero, idtipotelefono,telsms FROM tblTelefoni WHERE id=".$id_utente." AND prefisso=".$prefisso_nazionale." AND numero=".$numero_phone;
            $result=mysql_query($query);
    
            if (mysql_num_rows($result)>0) {
                echo ("<script type=\"text/javascript\">\n");
                echo ("alert(\"Attenzione! Il numero che stai cercando di inserire e\' gia\' presente in rubrica.\");\n");
                echo ("history.back();\n");
                echo ("</script>\n");
            } else {
                // pone a false le abilitazioni agli sms di tutti i numeri di telefono dell'utente selezionato
                // per impedire che ci sia più di un numero abilitato)
                if ($change_sms=="true") {
                    $query="UPDATE tblTelefoni SET telsms=0 WHERE id=".$id_utente;
                    $result=mysql_query($query);
                }
                
                // salva (finalmente) il numero di telefono in archivio
                $query="INSERT INTO tblTelefoni (id,prefisso,numero,idtipotelefono,telsms) VALUES (".$id_utente.",'".$prefisso_nazionale."','".$numero_phone."',".$tipo_telefono.",".$sms.")";
	              $result=mysql_query($query);
	               
                if (mysql_affected_rows()==0) {
                    echo ("<script type=\"text/javascript\">\n");
                    echo ("alert(\"Qualcosa e\' andato storto! Il numero di telefono non e\' stato inserito in rubrica.\");\n");
                    echo ("history.back();\n");
                    echo ("</script>\n");
                }
            }
      }
  }
}  
       // ********************* FUNZIONE PER CONVERTIRE LA DATA DAL FORMATO DI MYSQL A QUELLO ITALIANO ******
          function ConvertiData($data_ita) {
              if (!$data_ita==Null || !$data_ita=="") {
                  $data_ita=substr($data_ita,8,2)."/".substr($data_ita,5,2)."/".substr($data_ita,0,4);
                  return $data_ita;    
              }
          return;
          } 
      
      // ****************** > FUNZIONE POPOLA LISTA TIPI DI VIE < *****************************
      function PopolaListaVie($via) {
              ConnettiDB();
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
      // ************************ > FINE FUNZIONE POPOLA LISTA VIE < ***********************
      
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
      
      // ****************** > FUNZIONE POPOLA LISTA SEZIONI < *****************************
      function PopolaListaSezioni($sezione) {
              ConnettiDB();
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
      
      // ****************** > FUNZIONE POPOLA LISTA TIPI DI TELEFONO< *****************************
      function PopolaListaTipiTelefono() {
              ConnettiDB();
              // setta la stringa $query e estrae i dati dei tipi di telefono
              $query="SELECT idtipotelefono,tipotelefono FROM tbltipitelefono";
              $rstTipo=mysql_query($query);
              
              
              $query_telefono="SELECT idtelefono,idtipotelefono,id,prefisso,numero,telsms FROM tbltelefoni WHERE id='".$_POST['hdnID']."' ORDER BY idtipotelefono ASC";
              $rstTelefono=mysql_query($query_telefono);
              $i=0;
              while ($telefono=mysql_fetch_array($rstTelefono))
              {
                if ($telefono["telsms"]==1) {
                  $telsms[$telefono["idtipotelefono"]]=1;
                }
                $tel[$telefono["idtipotelefono"]]=$telefono["prefisso"].".".$telefono["numero"];
              }
              
              // popola la lista con i dati estratti dal database
              $i=0;
              while ($tipo=mysql_fetch_array($rstTipo))
              {
                  $i++;
                  if ($tel[$i]!="") {
                      if ($telsms[$i]!=1) {
                        echo ("<option value=\"".$tipo["idtipotelefono"]."\" onDblClick=\"fncModificaTel('2')\" onClick=\"fncModificaTel('1')\">".$tipo["tipotelefono"]."&nbsp;&nbsp;[".$tel[$i]."]</option>\n");
                      } else {
                        echo ("<option value=\"".$tipo["idtipotelefono"]."\" onDblClick=\"fncModificaTel('2')\" onClick=\"fncModificaTel('1')\">".$tipo["tipotelefono"]."&nbsp;&nbsp;[".$tel[$i]."]&nbsp;(sms)</option>\n");
                      }
                  } else {
                      echo ("<option value='".$tipo["idtipotelefono"]."' onDblClick=\"fncModificaTel('2')\" onClick=\"fncModificaTel('1')\">".$tipo["tipotelefono"]."</option>\n");
                  }
              }
          
          return;
      }
      // ****************** FINE FUZIONE POPOLA LISTA TIPI DI TELEFONO **************************


?>

</head>

<body onload="CaricamentoIscrizioni()">
   
   <!-- inizio sezione intestazione. Questa sezione contiene il logo, i campi di ricerca, la barra di navigazione e la data -->
   <div id="intestazione"> 
     
      <img src ="./Immagini/logoratorio.png" width="110" height="90" alt="Logo Oratorio Saint-Martin" />
   
      <!-- sezione campi di ricerca -->
    
    <form id="CercaIscritti" name="CercaIscritti" method ="post" action ="anagrafica.php">
      <input type="hidden" name="postback" value="true">
      <div id="campiricerca">
         <fieldset>
            <legend>Cerca iscritti &nbsp;</legend>
               <div id="etichettacognome">
                    <input type="hidden" name="hdnID" id="hdnID" value="<?php echo ($_POST['hdnID']); 
                    ?>" />
                    <label for="txtCognome">Cognome</label>
                    <input type="text" name="txtCognome" id="txtCognome" onkeyup="lookup(this.value);" onblur="fill();" onfocus="ResetCampoCognome()" onkeypress="RilevaTab(event);" autocomplete="off" />
                    &nbsp;
								<div class="suggestionsBox" id="suggestions" style="display: none;">
									 <img src="./Immagini/upArrow.png" style="position: relative; top: -12px; left: 30px;" alt="" />
									<div class="suggestionList" id="autoSuggestionsList">
										&nbsp;
									</div>
								</div>
               </div>     
                <div id ="etichettanome">    
                    <label for="txtNome">Nome</label>
                    <input type="text" name="txtNome" id="txtNome" onfocus="ResetCampoNome()" onblur="ControlloInputNome()" autocomplete="off" />
                    &nbsp;
                    <label for="txtBarCode">Cod. a barre</label>
                    <input type="text" name="txtBarCode" id="txtBarCode" autocomplete="off" />
	                &nbsp;
                    <input type="button" name="caricaPersona" id="caricaPersona" value="cerca iscritti" onClick="btnCercaIscritti();  " disabled />
              </div> <!-- fine etichette -->
         </fieldset>
      </div> <!-- fine campi di ricerca -->
    </form>
    
      <!-- ********************** sezione nome della pagina, operatore e data ********************-->
      <div id="nomepagina">
          | operatore: <strong><?php echo htmlentities($rowOperatore->Nome).' '.htmlentities($rowOperatore->Cognome) ?></strong > | 
      </div> <!-- fine sezione nome della pagina-->
      
      <!-- ******************** sezione barra di navigazione ***************************************-->
      <div id="barranavigazione">
        | <a href="javascript:ApriHomePage();">home page</a> | <a href="javascript:ApriPrenotazioni();">iscrizioni &amp; prenotazioni Er</a> | <a href="javascript: ApriLogon();">esci</a> |
      </div> <!-- fine sezione barra di navigazione -->
   </div> <!-- fine sezione intestazione -->
  	<div id="dataprenotazioni">
        <?php echo($Data); ?>
    </div>
    <input type="hidden" id="dataserver" name="dataserver" value="<?php echo($data_server);?>" \>
    <input type="hidden" id="OldDataTesseramento" name="OldDataTesseramento" value="<?php 
        $data_ita=$a_data_tesseramento[$indice];
        $a_data_tesseramento[$indice]=ConvertiData($data_ita);
        echo($a_data_tesseramento[$indice]);?>" \>
    <div id="rigaorizzontale">
  			<hr />
  	</div>


<!-- ********************** SEZIONE DATI ANAGRAFICI ******************** -->
<form id="SalvaSchedaIscritto" name="SalvaSchedaIscritto" method="post" action="anagrafica.php">
  <input type="hidden" name="hdnID" id="hdnIDx" value="<?php echo($_POST['hdnID']); ?>" />
    <div id="datianagrafici">
        <fieldset class="dati">
            <legend>Dati Anagrafici&nbsp;</legend>
                <div id="imgnomecognome">
                    <img src="./Immagini/nomecognome.png" alt="icona persone" width="50" height="50" />
                </div>
                <div id="nomecognome">
                    <label for="cognome"><strong>Cognome</strong></label>
                    <input type="text" name="cognome" id="cognome" onblur="ControlloCognome()" onfocus="FuocoCampoCognome()"  value="<?php print ($a_cognome[$indice]); ?>" class="campoestratto" size="17"/>
                    &nbsp;&nbsp;
                    <label for="nome"><strong>Nome</strong></label>
                    <input type="text" name="nome" id="nome" onblur="ControlloNome()" onfocus="FuocoCampoNome()" value="<?php print ($a_nome[$indice]); ?>" class="campoestratto" size="17" />
                    &nbsp;
                    <strong>Sesso</strong> <strong>M</strong><input type="radio" name="sesso" id="sesso" value="M" 
                    <?php 
                    if ($a_sesso[$indice]=="M") {
                      echo("checked");
                    } ?> />
                    
                    <strong>F</strong><input type="radio" name="sesso" id="sessof" value="F" 
                    <?php 
                    if ($a_sesso[$indice]=="F") {
                      echo("checked");
                    } ?> />
                 </div>                                                        
                   
                    <div id="datanascita">
                        <label for="data_nascita" id="dataNa"><strong>Data di nascita</strong></label>
                        <input type="text" name="data_nascita" id="dataN" onblur="ControlloDataNascita()" onfocus="FuocoCampoDataNascita()" value="<?php 
                                               $data_ita=$a_data_di_nascita[$indice];
                                               $a_data_di_nascita[$indice]=ConvertiData($data_ita);
                                               echo ($a_data_di_nascita[$indice]); 
                                           ?>"  class="campoestratto" size="8" />
                        &nbsp;
                        <label for="luogo" id="luogo"><strong>Nato/a a</strong></label>
                        <input type="text" name="natoa" id="natoa" size="35" onblur="ControlloNato()" onfocus="FuocoCampoNatoA()" value="<?php echo ($a_luogo_di_nascita[$indice]); ?>" class="campoestratto" />
                    </div>
                    
                    <div id="imgdatanascita">
                    	<img src="./Immagini/peluche.png" alt="icona peluche" width="50" height="50" />                 	
                 	</div>   
                    	
                    <div id="imgindirizzo">
                    	<img src="./Immagini/indirizzo.png" alt="icona peluche" width="50" height="50" /> 
                    </div>
                    
                    <div id="div_indirizzo">
                        <label for="stradario" id="stradario"><strong>Indirizzo</strong></label>
                        <select name="stradario" id="miostradario" onfocus ="FuocoCampoVia()" onblur="ControlloVia()" class="campoestratto">
                            <?php 
                                //richiama funzione per popolare la lista dei tipi di via
                                PopolaListaVie($via);
                            ?>
                        </select>
                        <input type="text" name="indirizzo" id="indirizzo" size="38" onblur="ControlloIndirizzo()" onfocus="FuocoCampoIndirizzo()" value ="<?php print($a_via[$indice]); ?>" class="campoestratto" />
                        &nbsp;
                        <label for="numero"><strong>Nr.</strong></label>
                        <input type="text" name="numero" id="numero" size="4" onblur="ControlloNumero()" onfocus="FuocoCampoNr()" value ="<?php print($a_numero_civico[$indice]); ?>" class="campoestratto" />
                    </div>
                    
                    <div id="citta">
                        <input type="hidden" name="hdnIdcomunex" id="hdnIdcomune" value="<?php echo ($_POST["hdnIdcomune"]); ?>" />
                        <label for="comune" id="comune"><strong>Citt&agrave;/Comune</strong></label>
                       <input type="text" name="comune" id="miocomune" onkeyup="lookup_comuni(this.value);" onblur="fill_comuni();" onfocus="FuocoCampoComune()" onkeypress="RilevaTab(event);" class ="campoestratto" autocomplete="off" 
                       value= "<?php echo ($citta); ?>" size="25" \>
                       <div class="suggestionsBox" id="suggestions_comuni" style="display: none;">
        									<div class="suggestionList" id="autoSuggestionsListComuni">
				          						&nbsp;
									       </div>
								       </div>
                    </div>   
                      
                    <div id="div_cap">
                        <label for="cap"><strong>Cap</strong></label>
                        <input type="text" name="cap" id="cap" size="4" onblur="ControlloCap()" onfocus="FuocoCampoCap()" value ="<?php print($a_cap[$indice]); ?>"/ class="campoestratto" >
                        &nbsp;
                        <label for="prov"><strong>Prov.</strong></label>
                        <input type="text" name="prov" id="prov" size="4" onblur="ControlloProv()" onfocus="FuocoCampoProv()" value ="<?php print(strtoupper($a_provincia[$indice])); ?>" class="campoestratto" />
                    </div>

                    <div id="email">
                        <label for="email"><strong>E-mail</strong></label>
                        <input type="text" name="myemail" id="myemail" size="77" onblur="ControlloEmail()" onfocus="FuocoCampoEmail()" value ="<?php print($a_email[$indice]); ?>" class="campoestratto"  />
                    </div>
                    
                    <div id="parrocchia">
                      <label for="parrocchia" id="idparrocchia"><strong>Parrocchia di provenienza</strong></label>
                      <input type="hidden" name="hdnIdParrocchia" id="hdnIdParrocchia" value="<?php echo ($_POST['hdnIdParrocchia']); ?>" />
                      <input type="text" name="parrocchia" id="miaparrocchia" onkeyup="lookup_parrocchie(this.value);" onblur="fill_parrocchie();" onfocus="FuocoCampoParrocchia()" onkeypress="RilevaTab(event);" class ="campoestratto" autocomplete="off" value= "<?php echo ($nome_parrocchia); ?>" size="57" \>
                      <div class="suggestionsBoxParrocchie" id="suggestions_parrocchie" style="display: none;">
        									<div class="suggestionList" id="autoSuggestionsListParrocchie">
				          						&nbsp;
									       </div>
								      </div>
                    </div>
                   
                    <div id="imgtelefono">
                        <img src="./Immagini/telefono.png" alt="icona telefono" width="40" height="40" />
                        <input type="button" id="btnTelefono" value="aggiungi numero alla rubrica" onclick="AggiungiNrTelefono()" class="buttonrubrica" />
                    </div>
                    
                    <div id="spedmateriale">
                      <input type="checkbox" name="chkspedizione" id="spedizione" checked="checked" />
                      <label for="chkIscrizione" id="chkIscrizione">Spedizione materiale informativo iniziative Oratorio</label>
                    </div>
        </fieldset>
    </div>
    <div id="myTesseramenti">
        <fieldset class="tesseramento">
            <legend>Tesseramenti&nbsp;</legend>
            <div id="chkTesseramenti">
                <input type="checkbox" name="chkTesserato" id="chkTesserato" 
                <?php 
                    if ($a_tesseramento[$indice]=="Si") {
                        echo ("checked");
                    }
                ?> onclick="AbilitaTesseramenti()"/>
                <label for="chkTessera" id="chkTessera"><strong>Tesserato</strong></label>
                &nbsp;
                <label for="quota" id="quota"><strong>Quota</strong></label>
                
                <?php 
                    //$a_quota[$indice].=",00";
                    if ($a_tesseramento[$indice]=="Si") {
                       print("<input type='text' name='myquota' id='myquota' class='quota' size='10' onblur='ControlloQuota()'' onfocus='FuocoCampoQuota()' value='$a_quota[$indice]' enabled />");
                    } else {
                       print("<input type='text' name='myquota' id='myquota' class='quotax' size='10' onblur='ControlloQuota()'' onfocus='FuocoCampoQuota()' value='$a_quota[$indice]' disabled />");
                    }
                ?>
                <strong>&euro;</strong>
            </div>
            <div id="DTesseramento">
                <label for="dataT" id="dataT"><strong>Data Tesseramento</strong></label>
                <?php 
                    // abilita o disabilita il campo data tesseramento in base al valore del check tesseramento
                    if ($a_tesseramento[$indice]=="Si") {
                        print("<input type='text' name='dataT' id='mytesseramento' class='campitesseramenti' size='12' onblur='ControlloDataTesseramento()' onfocus='FuocoCampoDataTesseramento()' value='$a_data_tesseramento[$indice]' enabled \>");
                    } else {
                        print("<input type='text' name='dataT' id='mytesseramento' class='campitesseramentix' size='12' onblur='ControlloDataTesseramento()' onfocus='FuocoCampoDataTesseramento()' value='$a_data_tesseramento[$indice]' disabled \>");
                    }
                ?>
                  
            </div>
            <div id="sconto">
              <label for="sconti" id="sconti"><strong>Sconto</strong></label>
              <input type="text" name="miosconto" id="miosconto" size="4" onblur="" onfocus=""  disabled="disabled" value="0"/>
              &nbsp;<strong>%</strong> &nbsp;
              <input type="checkbox" name="chkConsumazioni" id="chkConsumazioni" disabled="disabled"/>
              <label for="consumazioni" id="consumazioni"><strong>Cons. limitate</strong></label>
            </div>
            <div id="temperaneo">
                <input type="button" name="btnTemperaneo" value="Tesseramento temporaneo" disabled="disabled">
            </div>
        </fieldset>        
    </div>
    <div id="classecatechismo">
        <fieldset class="catechismoclassi">
            <legend>Classi catechismo e coro&nbsp;</legend>
                
                <label for="classecatechismi" id="classecatechismi"><strong>Classe</strong></label>
                <select class="campoestratto" name="myclassi" id="myclassi" onfocus="FuocoClasseCatechismo()" onblur="ControlloClasseCatechismo()" onclick="ControlloClasseFrequentata()">
                    <option value="0">*******</option>
                      <?php  PopolaListaClassi($classe); ?>           
                </select>
               
            <div id="sezionecatechismo">
                <label for="sezionecatechismi" id="sezionecatechismi"><strong>Sezione</strong></label>
                <select name="mysezione" id="mysezione" onfocus="FuocoSezioneCatechismo()" onblur="ControlloSezioneCatechismo()" class="campoestratto">
                    <?php  PopolaListaSezioni($sezione); ?>    
                </select>
            </div> 
            
            <div id="partecipazione">
                <input type="radio" name="optPartecipa" id="optPartecipa" value="1" <?php 
                if ($a_presenza[$indice]==1) {
                  echo("checked='checked'");
                }
                ?> />
                <label for="partecipacatechismo" id="partecipacatechismo"><strong>Partecipa regolarmente</strong></label>
            </div>
            
            <div id="nonpartecipazione">
                <input type="radio" name="optPartecipa" id="optPartecipa" value="2" <?php 
                if ($a_presenza[$indice]==2) {
                  echo("checked='checked'");
                }
                ?>/>
                <label for="nonpartecipacatechismo" id="nonpartecipacatechismo"><strong>Non partecipa</strong></label>
            </div>
            
            <div id="partecipazionesaltuaria">
                <input type="radio" name="optPartecipa" id="optPartecipa" value="3" <?php 
                if ($a_presenza[$indice]==3) {
                  echo("checked='checked'");
                }
                ?>/>
                <label for="partecipasaltuaria" id="partecipasaltuaria"><strong>Partecipa saltuariamente</strong></label>
            </div>
            
            <div id="coro">
                <input type="checkbox" name="chkCoro" id="chkCoro" value="1" <?php 
                if ($a_coro[$indice]=="True") {
                    echo("checked='checked'");
                } ?> />
                <label for="partecipacoro" id="partecipacoro"><strong>Coro</strong></label>
            </div>
            
            
            <div id="pulsantiera">
                <input type="button" name="privacy" value="privacy" onclick="stampa_privacy('<?php echo ($_POST["hdnID"]); ?>')" >
                
                
                <input type="hidden" id="salva_scheda" name="salva_scheda" value="false" />
                <input type="button" name="salvadati" id="btnsalvadati" value="salva i dati" onClick="fncSalvaScheda();" />
                
                <div id="div_btnchiudischeda">
                  <input type="button" name="chiudischeda" id="btnchiudischeda" value="chiudi scheda iscritto" onclick="ChiudiIscritto();" 
                      <?php 
                          if (isset($_POST["hdnID"]))
                          {
                              echo("enabled");
                          } else
                          {
                              echo("disabled");
                          }                      
                      ?>
                   />
                </div>
            </div>
        </fieldset>
    </div>
</form>

<!-- SEZIONE AGGIUNGI TELEFONO ALLA RUBRICA ORATORIO -->
<form id="SalvaNumeroTel" method="post" action="anagrafica.php">
    <div id="divTelefono">
        <div id="testatina">
           RUBRICA TELEFONO
         </div>
         
         <div id="lettera">
            <?php 
                echo(substr($a_cognome[$indice],0,1));
            ?>
         </div>
         <div id="who_is">
            <input type="hidden" name="hdnIDxTel" value="<?php  echo ($_POST['hdnID']);?>"\>
            <?php 
                echo ($a_nome[$indice].' '.$a_cognome[$indice]);
            ?>
            <hr \>
         </div> 
         <div id="campi_phone">
            <label for="prefisso_intnaz"><strong>Pref. Int.</strong></label>
            <input type="text" name="prefisso_intnaz" id="prefisso_intnaz" size="4" value="+39" onblur="ControlloNrTel('pref_int');" onfocus="InputNrTel('pref_int')" class ="phone" \>
            <br \>
            <p>
              <label for="prefisso_naz"><strong>Pref.</strong></label>
              <input type="text" name="prefisso_naz" id="prefisso_naz" size="4" onblur="ControlloNrTel('pref_naz');" onfocus="InputNrTel('pref_naz')" class="phone" \>
              
              <label for="numero_phone"><strong>Nr.</strong></label>
              <input type="text" name="numero_phone" id="numero_phone" onblur="ControlloNrTel('nr_phone');" onfocus="InputNrTel('nr_phone')" class="phone" size="19" \>
            </p>
             <p>
                <label for="tipo_phone"><strong>Tipo</strong></label>
                <br \>
                <select name="tipo_phone" id="tipo_phone" size="10" style ="width: 100%; font-weight: bold;">
                    <?php 
                        PopolaListaTipiTelefono()
                    ?>
                </select>
            </p>
            
            <p>
                <label for="chkSpedizioneSms" id="spedizione_sms">
                    <input type="checkbox" name="chkSpedizioneSms" id="chkSpedizioneSms" value="0" onclick="fncModificaSms();" \>
                    <strong>Spedizione sms iniziative</strong>
                </label>
            </p>
         </div>
         <div id="btnPhone">
            <input type="hidden" name ="txtCognome" id ="change_txtCognome" value="<?php  echo ($_POST["txtCognome"]); ?>"\>
            <input type="hidden" name ="change_tel" id ="change_tel" value="false" \>
            <input type="hidden" name="change_sms" id="change_sms" value="false" \>
            <input type="hidden" name="change_tipo" id="change_tipo" value="0" \>
            <input type="hidden" name="delete_phone" id="erase_phone" value="false">
            <input type="button" value="Salva" onclick="fncSalvaTelefono();" name="salva_numero" id="salva_numero" \> &nbsp;
            <input type="button" value="Cancella" onclick="CancellaTelefono()" \> &nbsp;
            <input type="button" value="Chiudi" onclick="OkNrTelefono()" \>
        </div>
        <?php 
            if (isset($_POST['hdnIDxTel'])) {
                echo ("<script type=\"text/javascript\">\n");
                echo ("document.getElementById('myTesseramenti').style.visibility='hidden';\n");
                echo ("document.getElementById('classecatechismo').style.visibility='hidden';\n");
                echo ("document.getElementById('divTelefono').style.visibility ='visible';\n");
                echo ("document.getElementById('btnTelefono').disabled=true"."\n");
                echo ("document.getElementById('prefisso_naz').focus()\n"); 
                echo ("</script>\n");
            } else {
                echo ("<script type=\"text/javascript\">\n");
                echo ("document.getElementById('myTesseramenti').style.visibility='visible';\n");
                echo ("document.getElementById('classecatechismo').style.visibility='visible';\n");
                echo ("document.getElementById('divTelefono').style.visibility ='hidden';\n");
                echo ("document.getElementById('btnTelefono').disabled=false"."\n"); 
                echo ("</script>\n");
            }
        ?>
    </div>
</form>
</body>

</html>
