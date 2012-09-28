<?php
// smista le azioni da svolgere
switch ($_POST['azione']) {
  case 'elabora':
      if ($_POST['hdnClassi']!="" || $_POST['hdnClassi']!=null) {
          $classi=explode("|",$_POST['hdnClassi']);
      }
      
      if ($_POST['hdnGruppi']!="" || $_POST['hdnGruppi']!=null) {
          $gruppi=explode("|",$_POST['hdnGruppi']);
      }
      
      if ($_POST['hdnRuoli']!="" || $_POST['hdnRuoli']!=null) {
          $ruoli=explode("|",$_POST['hdnRuoli']);
      }
      
      if ($_POST['hdnTesseramenti']!="" || $_POST['hdnTesseramenti']!=null) {
          $tesseramenti=explode("|",$_POST['hdnTesseramenti']);
      }
      
      if ($_POST['hdnPartecipazione']!="" || $_POST['hdnPartecipazione']!=null) {
          $partecipazione=explode("|",$_POST['hdnPartecipazione']);
      }
     
      $coro=$_POST['hdnAltro'];
      
  break;
  
  case 'riepilogo':
      if ($_POST['hdnListaDestinatari']!="" || $_POST['hdnListaDestinatari']!=null) {
          $ListaDestinatari=explode("|",$_POST['hdnListaDestinatari']);
      }
  break;
  
  case 'togateway':
      // recupera la lista dei destinatari
      if ($_POST['hdnListaDestinatari']!="" || $_POST['hdnListaDestinatari']!=null) {
          $ListaDestinatari=explode("|",$_POST['hdnListaDestinatari']);
      }
      
      // ottiene il nome del mittente
      $from=$_POST['from'];

      // istanzia la classe per l'invio degli sms
      $sms=new mobytSms($account, $pw_gtw);
      
      // setta il mittente
      $sms->setFrom($from);
      
      // richiede al gateway Mobyt di inviare le notifiche di avvenuta ricezione
      if ($abilita_notifiche) {
          $sms->setQualityAutoNotify();
      }
      
     /* Invia l'sms ai destinatari selezionati
      ******************************** ATTENZIONE *************************************************
      * Il gateway Mobyt considera una spedizione batch una lista con almeno due destinatari.
      * In caso contrario non prenderà in carico la spedizione. Sono state predisposte due funzioni
      * nella classe mobytSms per evitare errori di spedizione: sendSms() e sendMultiSms().
      * 
      * Secondo le specifiche Mobyt non si possono superare per spedizione:
      *     - 5000  destinatari utilizzando il metodo tx HTTP/POST
      *     - 25000 destinatari utilizzanddo il metedo tx FTP
      *     
      * I messaggi eccedenti non verranno presi in carico per la spedizione            
      *  
      * La variabile "$max_messaggi_inviabili" è configurata nel file accesso_gtw_sms                                    
      ***********************************************************************************************
      */

      /* * prepara la lista da inviare al gateway secondo le specifiche Mobyt. Sgrunt...!
         * Bisogna passare delle stringhe. A risgrunt...! Altrimenti anche se invia il messaggio
         * ritorna un errore!!!
      */      
      
      
      // manda al gateway      
      if (sizeof($ListaDestinatari)>$max_messaggi_inviabili) {
          //GestioneErrori(1);
      } elseif (sizeof($ListaDestinatari)<2){
         // si invia soltanto il valore del primo elemento dell'array $ListaDestinatari
         $result = $sms->sendSms($ListaDestinatari[0], $_POST['sms_text2']);
      } else {
         // si invia tutto l'array $ListaDestinatari
         $result = $sms->sendMultiSms($ListaDestinatari, $_POST['sms_text2']);
      }
  break;
}

//*******************************************************************************************
// funzione per filtrare i dati dal database
//*******************************************************************************************
function FiltraDati($classi,$gruppi,$ruoli,$tesseramenti,$partecipazione,$coro) {
    global $lista_totale; // è il numero di quanti sono stati trovati dalla ricerca parametrica
    
    
    //calcola quando scade la tessera
    $anno_scadenza_tessera=date("Y");
  
    /* Ricordarsi che l'anno nuovo per l'oratorio inizia il 1° settembre. 
    if (date("m")<9){ // se siamo prima di settembre
        $anno_scadenza_tessera=date("Y");
    } else { // se siamo dopo settembre
        //$anno_scadenza_tessera=date("Y")+1;
    }*/

    /********************************************************************************************************
     * ho dovuto fare in due tempi la ricerca in base ai metodi stabiliti:
     * per classi, gruppi, ruoli, tesseramenti, partecipazione alle attività e al coro.
     * Si potranno mandare sms alle classi selezionate e ai loro rispettivi animatori in una mandata sola
     * (senza cioè preparare o rifare i messaggi per gli animati e per i loro animatori).
     * Quindi due interrogazioni al db separate da accorpare in fase di impaginazione.
     * Sarà possibile filtrare i ruoli per classi distinte.
     *********************************************************************************************************/                   
    
    // prepara la base della query per i ruoli
    $query="SELECT DISTINCTROW Catechismi.ID,Catechismi.Cognome,Catechismi.Nome,Catechismi.Classe,
                          Catechismi.Sezione,Catechismi.Presenza,
                          Catechismi.Coro,Catechismi.DataScadenzaTessera,tblTelefoni.PrefissoInt,
                          tblTelefoni.Prefisso,tblTelefoni.Numero,tblClassi.Sigla
                          FROM Catechismi 
                          INNER JOIN tblTelefoni ON Catechismi.ID=tblTelefoni.ID 
                          INNER JOIN tblClassi ON tblClassi.IDClasse=Catechismi.Classe ";
                          
                          
     // controlla se l'utente vuole filtrare i dati per ruoli       
    if (sizeof($ruoli)>0) {
        $query_ruoli="SELECT DISTINCTROW Catechismi.ID,Catechismi.Cognome,Catechismi.Nome,Catechismi.Classe,
                          Catechismi.Sezione,Catechismi.Presenza,tblruolioratorio.Sigla,
                          Catechismi.Coro,Catechismi.DataScadenzaTessera,tblTelefoni.PrefissoInt,
                          tblTelefoni.Prefisso,tblTelefoni.Numero
                          FROM Catechismi 
                          INNER JOIN tblTelefoni ON Catechismi.ID=tblTelefoni.ID  
                          INNER JOIN tblanimatori ON Catechismi.ID=tblAnimatori.ID 
                          INNER JOIN tblruolioratorio ON tblanimatori.IDRuolo=tblruolioratorio.IdRuoloOratorio ";
                          
        for ($i=0;$i<sizeof($ruoli);$i++) {
            if ($i==0) {
                $query_ruoli.="WHERE (tblAnimatori.IDRuolo=".$ruoli[$i];
            } else {
                $query_ruoli.=" OR tblAnimatori.IDRuolo=".$ruoli[$i];
            }
        }
        $query_ruoli.=")";
    
        if (sizeof($classi)>0){
          for ($i=0;$i<sizeof($classi);$i++) {
              if ($i==0) {
                  $query_ruoli.=" AND (tblAnimatori.IDClasse=".$classi[$i];
              } else {
                  $query_ruoli.=" OR tblAnimatori.IDClasse=".$classi[$i];
              }
          }
          
          $query_ruoli.=")";
        }
    }
   
    // controlla che ci siano gli elementi da poter filtrare e setta la variabile di serivizio $campi per indicare
    // che l'utente ha scelto di filtrare i dati in base alla classe
    if (sizeof($classi)>0){
        $campi++;
        for ($i=0;$i<sizeof($classi);$i++) {
            if ($campi==1) {
                if ($i==0) {
                    $query.="WHERE (Catechismi.Classe=".$classi[$i];
                } else {
                    $query.=" OR Catechismi.Classe=".$classi[$i];
                }
            } else {
                if ($i==0) {
                    $query.=" OR (Catechismi.Classe=".$classi[$i];
                } else {
                    $query.=" OR Catechismi.Classe=".$classi[$i];
                }
            }
        }
        $query.=")";
    }
   
    // controlla se l'utente vuole filtrare i dati anche con il campo gruppi (nel db è indicato come sezione)
    if (sizeof($gruppi)>0){
        $campi++;
        for ($i=0;$i<sizeof($gruppi);$i++) {
            if ($campi==1){
                if ($i==0) {
                    $query.="WHERE (Catechismi.Sezione=".$gruppi[$i];
                    $query_ruoli.=" AND (Catechismi.Sezione=".$gruppi[$i];
                } else {
                    $query.=" OR Catechismi.Sezione=".$gruppi[$i];
                    $query_ruoli.=" OR (Catechismi.Sezione=".$gruppi[$i];
                }
            } else {
                if ($i==0) {
                    $query.=" AND (Catechismi.Sezione=".$gruppi[$i];
                    $query_ruoli.=" AND (Catechismi.Sezione=".$gruppi[$i];
                } else {
                    $query.=" OR Catechismi.Sezione=".$gruppi[$i];
                    $query_ruoli.=" OR (Catechismi.Sezione=".$gruppi[$i];
                }
            }
        }
        $query.=")";
         $query_ruoli.=")";
    }
    
    // controlla se l'utente vuole filtrare i dati anche con il campo tesseramenti
    if (sizeof($tesseramenti)==1){
        $campi++;
        if ($campi==1){
            if ($tesseramenti[0]=="1") {
                $query.="WHERE (YEAR(Catechismi.DataScadenzaTessera)>=".$anno_scadenza_tessera;
                $query_ruoli.=" AND (YEAR(Catechismi.DataScadenzaTessera)>=".$anno_scadenza_tessera;
            } else {
                $query.="WHERE (ISNULL(YEAR(Catechismi.DataScadenzaTessera)) OR YEAR(Catechismi.DataScadenzaTessera)<".$anno_scadenza_tessera;
                $query_ruoli.=" AND (ISNULL(YEAR(Catechismi.DataScadenzaTessera)) OR YEAR(Catechismi.DataScadenzaTessera)<".$anno_scadenza_tessera;
            }
        } else {
            if ($tesseramenti[0]=="1") {
                $query.=" AND (YEAR(Catechismi.DataScadenzaTessera)>=".$anno_scadenza_tessera;
                $query_ruoli.=" AND (YEAR(Catechismi.DataScadenzaTessera)>=".$anno_scadenza_tessera;
            } else {
                $query.=" AND (ISNULL(YEAR(Catechismi.DataScadenzaTessera)) OR YEAR(Catechismi.DataScadenzaTessera)<".$anno_scadenza_tessera;
                $query_ruoli.=" AND (ISNULL(YEAR(Catechismi.DataScadenzaTessera)) OR YEAR(Catechismi.DataScadenzaTessera)<".$anno_scadenza_tessera;
            }
        }
        $query.=")";
        $query_ruoli.=")";
    }

    
    // controlla se l'utente vuole filtrare i dati anche con il campo patecipazione (nel db è chiamato come presenza)
    if (sizeof($partecipazione)>0){
        $campi++;
        for ($i=0;$i<sizeof($partecipazione);$i++) {
            if ($campi==1){
                if ($i==0) {
                    $query.="WHERE (Catechismi.Presenza=".$partecipazione[$i];
                    $query_ruoli.=" AND (Catechismi.Presenza=".$partecipazione[$i];
                } else {
                    $query.=" OR Catechismi.Presenza=".$partecipazione[$i];
                    $query_ruoli.=" OR Catechismi.Presenza=".$partecipazione[$i];
                }
            } else {
                if ($i==0) {
                    $query.=" AND (Catechismi.Presenza=".$partecipazione[$i];
                    $query_ruoli.=" AND (Catechismi.Presenza=".$partecipazione[$i];
                } else {
                    $query.=" OR Catechismi.Presenza=".$partecipazione[$i];
                    $query_ruoli.=" OR Catechismi.Presenza=".$partecipazione[$i];
                }
            }
        }
        $query.=")";
        $query_ruoli.=")";
    }
    
    // controlla se l'utente vuole filtrare i dati anche con il campo coro
    if ($coro) {
        $campi++;
        if ($campi==1) {
            $query.="WHERE (Catechismi.Coro='True')";
            $query_ruoli.=" AND (Catechismi.Coro='True')";
        } else {
             $query.=" AND (Catechismi.Coro='True')";
             $query_ruoli.=" AND (Catechismi.Coro='True')";
        }
    }
    
    // aggiunge quale telefono è abilitato a ricevere gli sms e quale tipo di ordinamento devono avere i dati
    $query.="  AND (tblTelefoni.TelSMS=1) ORDER BY Catechismi.Classe,Catechismi.Cognome,Catechismi.Nome";
    $query_ruoli.="  AND (tblTelefoni.TelSMS=1) ORDER BY Catechismi.Classe,Catechismi.Cognome,Catechismi.Nome";
    
    if (sizeof($ruoli)>0) {
        $result_ruoli=mysql_query($query_ruoli);
        
        if ($result_ruoli) {
            $lista_totale=mysql_num_rows($result_ruoli);
        }
        
        while ($row_ruoli=mysql_fetch_object($result_ruoli)) {
            if ($row_ruoli->PrefissoInt==null || $row_ruoli->PrefissoInt=="") {
                $prefisso_internazionale="+39";
            } else {
                 $prefisso_internazionale=$row_ruoli->PrefissoInt;
            }
            
            // serve per allineare i dati trovati
            $nominativo=$row_ruoli->Cognome."&nbsp".$row_ruoli->Nome;
            if (strlen($nominativo)< 38) {
                $nominativo.=str_repeat("&nbsp;",38-strlen($nominativo));
            }
            
            echo "<option value=\"".$prefisso_internazionale.$row_ruoli->Prefisso.$row_ruoli->Numero."\">".
                                    $row_ruoli->Sigla."&nbsp;&nbsp;".
                                    $nominativo.
                                    $prefisso_internazionale.$row_ruoli->Prefisso.$row_ruoli->Numero;
              
        }
        // controlla se l'utente vuole soltanto i ruoli di una determinata classe
        if (!$_POST['chkDellaClasse'] && sizeof($classi)!=0) {
            $result=mysql_query($query);
            
            if ($result) {
                $lista_totale+=mysql_num_rows($result);
            }
            
            while ($row=mysql_fetch_object($result)) {
                if ($row_ruoli->PrefissoInt==null || $row_ruoli->PrefissoInt=="") {
                $prefisso_internazionale="+39";
                } else {
                 $prefisso_internazionale=$row_ruoli->PrefissoInt;
                }
                
                // serve per allineare i dati trovati
                $nominativo=$row->Cognome."&nbsp".$row->Nome;
                if (strlen($nominativo)< 38) {
                    $nominativo.=str_repeat("&nbsp;",38-strlen($nominativo));
                }
                
                echo "<option value=\"".$prefisso_internazionale.$row->Prefisso.$row->Numero."\">".
                                        $row->Sigla.str_repeat("&nbsp;",3).
                                        $nominativo.
                                        $prefisso_internazionale.$row->Prefisso.$row->Numero;
            }
        }
        
    } else {
          $result=mysql_query($query);
           
           if ($result) {
                $lista_totale+=mysql_num_rows($result);
            }
          
          while ($row=mysql_fetch_object($result)) {
             if ($row_ruoli->PrefissoInt==null || $row_ruoli->PrefissoInt=="") {
                $prefisso_internazionale="+39";
                } else {
                 $prefisso_internazionale=$row_ruoli->PrefissoInt;
                }
                 // serve per allineare i dati trovati
                $nominativo=$row->Cognome.str_repeat("&nbsp;",1).$row->Nome;
                if (strlen($nominativo)< 38) {
                    $nominativo.=str_repeat("&nbsp;",38-strlen($nominativo));
                }
                
                
                echo "<option value=\"".$prefisso_internazionale.$row->Prefisso.$row->Numero."\">".
                                        $row->Sigla.str_repeat("&nbsp;",3).
                                        $nominativo.
                                        $prefisso_internazionale.$row->Prefisso.$row->Numero;
          }
    }
    
    return;
}

//*******************************************************************************************
// funzione per popolare scelta ruoli
//*******************************************************************************************
function PopolaRuoli() {
    $query="SELECT * FROM tblruolioratorio";
    
    $result=mysql_query($query);
    
    if ($result) {
        echo "<table id=\"tblRuoli\" border=0>\n";
        while ($row=mysql_fetch_object($result)) {
            if ($row->IdRuoloOratorio!=1) {
                echo "<tr>";
                echo "<td>";
                echo "<input type=\"checkbox\" name=\"chkRuolo\" onclick=\"Abilita('chkDellaClasse');\" value=\"".$row->IdRuoloOratorio."\" />&nbsp;".$row->RuoloOratorio;
                echo "</td>";
                echo "</tr>";
            }
        }
        
        // possibilità di scegliere ruoli per classe
        echo "<tr>";
        echo "<td>";
        echo "<hr />";
        echo "<input type=\"checkbox\" name =\"chkDellaClasse\" id=\"chkDellaClasse\" disabled />&nbsp;<span style=\"color:green;font-weight:bold;\">della classe...</span>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "nessun ruolo &grave; configurato in archivio";
        echo "</table>";
    }

    return;
}

//*******************************************************************************************
// funzione per popolare scelta classi
//*******************************************************************************************
function PopolaClassi () {
    $colore[1]="brown";
    $colore[2]="red";
    $colore[3]="purple";
    $colore[4]="blue";
    $colore[5]="black";
    $colore[6]="green";
    
    $query="SELECT * FROM tblClassi";
    
    $result =mysql_query($query);

    if ($result) {
        echo "<table id=\"tblClassi\" border=0>\n";
        
        // popola l'array classi con i valori salvati nel db. Ha la chiave (indice)
        // con l'id della classe e il valore con il nome della classe.
        // Tutto questo per impaginare su due colonne i valori della tabella tblClassi
        while ($row=mysql_fetch_object($result)) {
          $classi[$row->IDClasse]=$row->Classe;
        }  
        
        reset($classi); // riporta all'inizio il puntatore dell'array classi
        
        // la funzione each estrae i dati salvati nell'array classi restituendo un altro array
        // per recuperare la chiave dell'array (l'id della classe) e il suo valore ho utilizzato
        // la seguente sintassi: $nome_array['key'] e $nome_array['value'].
        while ($valori=each($classi)) {
              echo "<tr>";
              // analizza quanti elementi ci sono nell'array $classi
              // se maggiore di 8 lo impagina su due colonne
              if (sizeof($classi)>8){ 
                  echo "<td>";
                  if ($valori) {
                      $valore = $valori['value'];
                      
                      // stabilisce i colori in base al gruppo di classi
                      if ($valori['key']==1) {
                          $indice_colore=1;
                      } elseif ($valori['key']>1 && $valori['key']<7) {
                          $indice_colore=2;
                      } elseif ($valori['key']>6 && $valori['key']< 10) {
                          $indice_colore=3;
                      } elseif ($valori['key']>9 && $valori['key']<15){
                          $indice_colore=4;
                      } elseif ($valori['key']>14 && $valori['key']< 17) {
                          $indice_colore=5;
                      } else {
                          $indice_colore=6;
                      }
                      
                      if ($valori['key']!=17) { // 17 è l'indice in tabella che corrisponde al valore '*******'
                          echo "<input type=\"checkbox\" name=\"chkClasse\" value=\"".$valori['key']."\" />&nbsp;<span style=\"color:".$colore[$indice_colore].";\">".$valore."</span>";
                      }                  }
                  echo "</td>";
                  
                  echo "<td>";
                  $valori=each($classi);
                  if ($valori) {
                      $valore = $valori['value'];

                      // stabilisce i colori in base al gruppo di classi
                      if ($valori['key']==1) {
                          $indice_colore=1;
                      } elseif ($valori['key']>1 && $valori['key']<7) {
                          $indice_colore=2;
                      } elseif ($valori['key']>6 && $valori['key']< 10) {
                          $indice_colore=3;
                      } elseif ($valori['key']>9 && $valori['key']<15){
                          $indice_colore=4;
                      } elseif ($valori['key']>14 && $valori['key']< 17) {
                          $indice_colore=5;
                      } else {
                          $indice_colore=6;
                      }
                      
                      if ($valori['key']!=17) { // 17 è l'indice in tabella che corrisponde al valore '*******'
                          echo "<input type=\"checkbox\" name=\"chkClasse\" value=\"".$valori['key']."\" />&nbsp;<span style=\"color:".$colore[$indice_colore].";\">".$valore."</span>";
                      }
                  }
                  echo "</td>";
              }
              else {
                  echo "<td>";
                  if ($valori) {
                      $valore = $valori['value'];
                      
                      // stabilisce i colori in base al gruppo di classi
                      if ($valori['key']==1) {
                          $indice_colore=1;
                      } elseif ($valori['key']>1 && $valori['key']<7) {
                          $indice_colore=2;
                      } elseif ($valori['key']>6 && $valori['key']< 10) {
                          $indice_colore=3;
                      } elseif ($valori['key']>9 && $valori['key']<15){
                          $indice_colore=4;
                      } elseif ($valori['key']>14 && $valori['key']< 17) {
                          $indice_colore=5;
                      } else {
                          $indice_colore=6;
                      }
                      if ($valori['key']!=17) { // 17 è l'indice in tabella che corrisponde al valore '*******'
                          echo "<input type=\"checkbox\" name=\"chkClasse\" value=\"".$valori['key']."\" />&nbsp;<span style=\"color:".$colore[$indice_colore].";\">".$valore."</span>";
                      }                  }
                  echo "</td>";
              }
                  echo "</tr>\n";
          }
        // possibilità di scegliere tutte le classi in una soluzione unica
        echo "<tr>";
        echo "<td>";
        echo "<hr />";
        echo "<input type=\"checkbox\" id =\"chkTutteLeClassi\" onclick=\"selTutteLeClassi();\" />&nbsp;<span style=\"color:green;font-weight:bold;\">Tutte le classi...</span>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "nessun ruolo &grave; configurato in archivio";
        echo "</table>";
    }
    return;
}

function TrovaClasse($classi) {
  if (sizeof($classi)>0) {
      $query="SELECT Sigla FROM tblClassi ";
      for ($i=0;$i<sizeof($classi);$i++) {
          if ($i==0) {
              $query.=" WHERE IDClasse=".$classi[$i];
          } else {
              $query.=" OR IDClasse=".$classi[$i];
          }
      }

      $result=mysql_query($query);
  
      while ($row=mysql_fetch_object($result)) {
          $classe.=$row->Sigla."&nbsp;";
      }
 } else {
    $classe="Tutte le classi ";
 }

  return $classe;
}

function TrovaGruppo($gruppi) {
  if (sizeof($gruppi)>0) {
      $query="SELECT Sezione FROM tblSezioni ";
      for ($i=0;$i<sizeof($gruppi);$i++) {
          if ($i==0) {
              $query.=" WHERE IDSezione=".$gruppi[$i];
          } else {
              $query.=" OR IDSezione=".$gruppi[$i];
          }
      }

      $result=mysql_query($query);
  
      while ($row=mysql_fetch_object($result)) {
          $gruppo.="&nbsp;".substr(strtoupper($row->Sezione),0,3);
      }
 } 

  return $gruppo;
}

function TrovaRuolo($ruoli) {
  if (sizeof($ruoli)>0) {
      $query="SELECT Sigla FROM tblRuoliOratorio";
      for ($i=0;$i<sizeof($ruoli);$i++) {
          if ($i==0) {
              $query.=" WHERE IdRuoloOratorio=".$ruoli[$i];
          } else {
              $query.=" OR IdRuoloOratorio=".$ruoli[$i];
          }
      }

      $result=mysql_query($query);
  
      while ($row=mysql_fetch_object($result)) {
          $ruolo.=$row->Sigla."&nbsp;";
      }
 } 

  return $ruolo;
}

// funzione per costruire i parametri utilizzati per la ricerca utenti a cui inviare gli sms
function ParametriRicerca($classi,$gruppi,$ruoli,$tesseramenti,$partecipazione,$coro) {
if ($_POST['chkDellaClasse']) {
            $ruolo=TrovaRuolo($ruoli);
            if ($ruolo!="") {
                $ricerca_per=$ruolo." |";
            }
            
            $classe=TrovaClasse($classi);
              if ($classe!="") {
                  $ricerca_per.="&nbsp;".$classe." |";
              }
              
          } else {
              $classe=TrovaClasse($classi);
              if ($classe!="") {
                  $ricerca_per=$classe." |";
              }
              
              $ruolo=TrovaRuolo($ruoli);
              if ($ruolo!="") {
                  $ricerca_per.=$ruolo." |";
              }
          
          }
          
          $gruppo=TrovaGruppo($gruppi);  
          if ($gruppo!="") {
              $ricerca_per.=$gruppo." |";
          }

          switch (sizeof($tesseramenti)) {
              case 1:
                  if ($tesseramenti[0]==1) {
                      $ricerca_per.=" TESS |";                 
                  } else {
                      $ricerca_per.=" NO TESS |";
                  }
              break;
              
              case 2:
                  $ricerca_per.=" TESS NO TESS |";
              break;
          }
          
          if (sizeof($partecipazione)!=0) {
              for ($i=0;$i<sizeof($partecipazione);$i++) {
                    switch ($partecipazione[$i]) {
                        case 1:
                            $ricerca_per.=" REG ";
                        break;
                  
                        case 2:
                            $ricerca_per.=" NESS ";
                        break;
              
                        case 3:
                            $ricerca_per.=" SAL ";
                        break;
                    }
                }
                $ricerca_per.=" | ";
          }
          
          
          if ($coro) {
              $ricerca_per.=" CORO |";
          }
          
          return $ricerca_per;  
}

?>
