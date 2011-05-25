<?php

ConnettiDB();

// smista le operazioni da svolgere
switch ($_POST["azione"]) {
    case "addVoceCapitolo":
       AddVoceCapitolo();
    break;
    
    case "ModificaCapitolo":
        ModificaCapitolo();
    break;
    
    case "ModificaVoce":
        ModificaVoce();
    break;
    
    case "CancellaCapitoloVoce":
        CancellaCapitoloVoce();
    break;
    
    case "SalvaOperazioneEC":
        SalvaOperazione("insert");
    break;
    
    case "ModificaOperazioneEC":
        ModificaOperazione();
    break;
    
    case "AggiornaOperazioneEC":
        SalvaOperazione("update");
    break;
    
    case "EliminaOperazioneEC":
        EliminaOperazione();
    break;
}


//**************************************************************************************
//funzione per eliminare su Mysql i dati dell'EstrattoConto
function EliminaOperazione() {
    $_POST["mymodificaoperazione"]=substr($_POST["mymodificaoperazione"],0,strlen($_POST["mymodificaoperazione"])-1);
    
    $movimentazione=explode("|",$_POST["mymodificaoperazione"]);
    
    foreach ($movimentazione as $valore) {
        $query="DELETE FROM tblcontabilita WHERE IdContabilita=".$valore;  
        mysql_query($query) || die ($query);
    }
    ResetVociCapitoli();
    return;
}
//**************************************************************************************
//funzione per salvare su Mysql i dati dell'EstrattoConto
function ModificaOperazione() {
    $query="SELECT * FROM tblcontabilita WHERE IdContabilita=".$_POST["mymodificaoperazione"];

    $result=mysql_query($query);
    
    $row=mysql_fetch_object($result);
    
    $_POST["tipo_contabilita"]=$row->Contabilita;
    $_POST["optOperazione"]=$row->Operazione;
    $_POST["filtra_capitolo"]=$row->IdCapitolo;
    $_POST["addVoci"]=$row->IdVoce;

    $valuta=FormattaValuta($row->Importo);
    //$valuta=($row->Importo);
    if (substr($valuta,0,1)=="-"){ // controlla se il numero è negativo. In caso affermativo toglie il trattino
        $_POST["txtImporto"]=substr($valuta,1,strlen($valuta));
    } else {
        $_POST["txtImporto"]=$valuta;    
    }
    
    
    
    $_POST["data_operazione"]=FiltraData($row->DataOperazione,"DaMysql");
    
    return; 
}
//**************************************************************************************
//funzione per salvare su Mysql i dati dell'EstrattoConto
function SalvaOperazione($azione) {
  // ottiene l'id dell'operazione da modificare (solo in modalità update)
  $IdContabilita=$_POST["mymodificaoperazione"];
  
  // ottiene il tipo di contabilità da modificare
  $contabilita=$_POST["tipo_contabilita"];
  
  // ottiene la data dell'operazione da salvare filtrata per mysql 
  $data=FiltraData($_POST["data_operazione"],"PerMysql");

  // ottiene l'id della voce da salvare
  $IdVoce=$_POST["voci"];
  
  // ottiene l'Id del capitolo in base alla voce da salvare in contabilita 
  $query="SELECT IdCapitolo FROM tblvocicontabilita WHERE IdVoci=".$IdVoce;
  $result =mysql_query($query);
  $row=mysql_fetch_object($result);
  $IdCapitolo=$row->IdCapitolo;
  
  // ottiene l'operazione da compiere
  $operazione=$_POST["optOperazione"];
  
  // ottiene l'importo da salvare
  $importo=$_POST["txtImporto"];
  
  // toglie la virgola e i punti dal valore dell'importo
  $cifra=explode(".",$importo);
  $importo=implode($cifra);
  
  $cifra=explode(",",$importo);
  $importo=implode(".",$cifra);
  
  if ($operazione=="U") {
      $importo="-".$importo;
  }
  
  // prepara la query da mandare a Mysql
  switch ($azione) {
      case "insert":
          $query="INSERT INTO tblcontabilita (DataOperazione,Contabilita,IdCapitolo,IdVoce,Operazione,Importo) 
                  VALUES ('$data','$contabilita','$IdCapitolo','$IdVoce','$operazione','$importo')";
      break;
      
      case "update":
          $query="UPDATE tblcontabilita 
                  SET DataOperazione='$data',
                      Contabilita='$contabilita',
                      IdCapitolo='$IdCapitolo',
                      IdVoce='$IdVoce',
                      Operazione='$operazione',
                      Importo='$importo' 
                  WHERE IdContabilita=$IdContabilita";
      break;
  }
  
  
  
  mysql_query("START TRANSACTION");
  
  $result=mysql_query($query) || die($query);
  
  mysql_query("COMMIT");
 
  ResetVociCapitoli();
  return;
}

//**************************************************************************************
//funzione per filtrare la data arrivata da Mysql
function FormattaValuta($valuta) {
    /*  l'array $controllo contiene i valori da formattare
        $controllo[0] -> i numeri prima della virgola
        $controllo[1] -> i numeri dopo la virgola
    */
     // ottiene i due valori da trattare (numeri prima della virgola e decimali)    
    $controllo=explode(".",$valuta);

    // controlla se il numero da formattare contiene decimali e li riduce a due
    if (strlen($controllo[1]>0)) {
        $controllo[1]=substr($controllo[1],0,2);
    }
    
    // controlla se il numero è negativo
    if (substr($controllo[0],0,1)=="-") {
       $controllo[0]=substr($controllo[0],1,strlen($controllo[0]));
        $negativo=true;
    }
    
    // ottiene la lunghezza della cifra
    $LunghezzaCifra=strlen($controllo[0]);
    
    
    // stabilisce quanti punti bisogna inserire nella somma da formattare
    if ($LunghezzaCifra % 3) { // controlla se il risultato della divisione tra la lunghezza della stringa e 3 (tripletta) dà resto 
        $punti=(int)($LunghezzaCifra/3); // l'intero della divisione tra la lunghezza della stringa e 3 dà il numero dei punti da inserire
    } else {
        $punti=((int)($LunghezzaCifra/3))-1; // se la divisione non dà resto toglie un punto. Siamo nel caso di multipli di 3
    }
    
    // costruisce la nuova stringa in base ai punti calcolati
    for ($indice = 1;$indice <= $punti;$indice++){
        $somma_formattata=".".substr($controllo[0],$LunghezzaCifra-(3*$indice),3).$somma_formattata;
    }    
    
    // completa la somma formattata con le eventuali cifre rimaste fuori dalle triplette
     $somma_formattata=substr($controllo[0],0,$LunghezzaCifra-(strlen($somma_formattata)-$punti)).$somma_formattata;
    
    if ($controllo[0]>0) {
        $controllo[0]=$somma_formattata;
    } else {
        $controllo[0]="0";
    }
    
    if ($negativo) {
        $controllo[0]="-".$controllo[0];
    }
   
  /* echo "valuta da trattare: ".$valuta."<br>";
   echo "valore di \$controllo[0]: ".$controllo[0]."<br>";
   echo "lunghezza cifra da trattare: ".$LunghezzaCifra."<br>";
   die();*/
   
    //ricompatta la cifra
    return implode(",",$controllo);
}
//*************************************************************************************
//funzione per filtrare la data arrivata da Mysql
function FiltraData ($mydata,$conversione) {
    
    switch ($conversione) {
        case "DaMysql":
            $anno=substr($mydata,0,4);
            $mese=substr($mydata,5,2);
            $giorno=substr($mydata,8,2);
    
            $datafiltrata=$giorno."/".$mese."/".$anno;
        break;
    
        case "PerMysql":
            $anno=substr($mydata,6,4);
            $mese=substr($mydata,3,2);
            $giorno=substr($mydata,0,2);
    
            $datafiltrata=$anno."-".$mese."-".$giorno;
        break;
    }
    
    return $datafiltrata;
}

//*************************************************************************************
// funzione per costruire la tabella delle operazioni di contabilità
function CostruisciEC ($TipoContabilita) {
    // filtra la contabilita in base al tipo (ER/OR)
    if ($TipoContabilita=="ER") {
        $filtro="HAVING Contabilita='".$TipoContabilita."' ";
    } else {
        $filtro="";
    }    
    
    // ottiene il saldo dell'estratto conto
    $query="SELECT Sum(Importo) AS Saldo
            FROM tblcontabilita
            GROUP BY Contabilita ".$filtro;
    
    
    $result=mysql_query($query);
    
    // somma assieme i saldi delle due famiglie di contabilità (OR e ER)
    while ($row=mysql_fetch_object($result)) {
        $saldo+=($row->Saldo);
    }
    
    // per una migliore lettura formatta la cifra recuperata
   $saldo=FormattaValuta($saldo);
    
    // filtra la contabilita in base al tipo (ER/OR)
    if ($TipoContabilita=="ER") {
        $filtro="WHERE Contabilita='".$TipoContabilita."' ";
    } else {
        $filtro="";
    }    
    
    // ottiene i dati dal database (dati per tabella) filtrati per contabilità
    $query="SELECT * FROM tblcontabilita
            INNER JOIN tblcapitolicontabilita
            ON tblcontabilita.IdCapitolo = tblcapitolicontabilita.IdCapitolo  
            INNER JOIN tblvocicontabilita
            ON tblcontabilita.IdVoce = tblvocicontabilita.IdVoci ".$filtro."
            ORDER BY tblcontabilita.DataOperazione DESC,tblcontabilita.IdCapitolo";
    $result=mysql_query($query);
    
    // stampa la tabella
    print "<tr>";
    print "<td colspan=\"3\" class=\"layoutEC\">";
    print "<input type=\"button\" id=\"btnBilancio\" value=\"Bilancio\" onClick=\"DivBilancio('apri')\" />";
    print "</td>";
                
    print "<td colspan=\"4\" class=\"layoutECdx\">";
    
    if (substr($saldo,0,1)=="-") { // se il saldo è negativo lo scrive in rosso
        print "Saldo: <span style=\"color:red;\">".$saldo." &euro;</span>";
    } else {
        print "Saldo: <span style=\"color:green;\">".$saldo." &euro;</span>";
    }
    print "</td>";
    print "</tr>";
            
    print "<tr>";
    print "<td>&nbsp;</td>";
    print "<td width=\"18%\" class=\"layoutECdispari\">DATA</td>";
    print "<td width=\"2%\" class=\"layoutECpari\">CT</td>";
    print "<td width=\"5%\" class=\"layoutECdispari\">CAP</td>";
    print "<td width=\"40%\" class=\"layoutECpari\">VOCE</td>";
    print "<td width=\"17%\" class=\"layoutECdispari\">ENTRATA &euro;</td>";
    print "<td width=\"17%\" class=\"layoutECpari\">USCITA &euro;</td>";
    print "</tr>";
    
    
    // CALCOLA E VISUALIZZA IL NUMERO DELLE PAGINE IN BASE AL NUMERO DELLE OPERAZIONI VISUALIZZATE
    $nrelementi_pagina=11; // setta il numero massimo di elementi da visualizzare per "pagina" di tabella
    $righe_da_visualizzare=mysql_num_rows($result);
    
    $valore=($righe_da_visualizzare/$nrelementi_pagina); // trova il numero "grezzo" di pagine da visualizzare 
        
    // calcola il numero di pagine da pubblicare    
    if ($valore-(int)$valore >0) {  
        $nrpagine=(int)$valore+1;
    } else {
        $nrpagine=(int)$valore;
    }    
    
    // se il numero di pagine da pubblicare è minore di 1... 
    if ($nrpagine < 1) { 
        $nrpagine = 1;
    }
    
    if (isset($_POST["nrpagina"]) && $_POST["nrpagina"] >0) { // setta la pagina corrente 
        
        if ($_POST["nrpagina"]>$nrpagine) {
            $_POST["nrpagina"]=$nrpagine;
        }
           
        $nrpagina_corrente=$_POST["nrpagina"];
    } else {
         $nrpagina_corrente=1;
    }

    /* trova la posizione, all'interno dei records trovati, dalla quale partire
     * per visualizzare gli elementi cancellati nel caso ci siano più pagine */ 
        if ($_POST["nrpagina"]>1) {

            $posizione=($_POST["nrpagina"]-1)*$nrelementi_pagina;
            
            mysql_data_seek($result,$posizione);
        }
        
        $indice=0; // inizializza la variabile di controllo per il numero degli elementi da pubblicare per pagina

    // stampa la tabella con i dati trovati
    while ($row=mysql_fetch_object($result)) {
        // controlla che nella costruzione della tabella non venga superato il numero degli elementi consentiti
        $indice++;
        if ($indice > $nrelementi_pagina) {
            break;
        }
            
        if ($_POST["mymodificaoperazione"]==$row->IdContabilita){
            $checked="checked";
        } else {
            $checked="";
        }
        
        print "<tr>";
        print "<td class=\"layoutECpari\"><input type =\"checkbox\" value=\"".$row->IdContabilita."\" name=\"seleziona_operazione\" $checked></td>";
        print "<td width=\"18%\" class=\"layoutECdispari\">".FiltraData($row->DataOperazione,"DaMysql")."</td>";
        print "<td width=\"2%\" class=\"layoutECpari\">".$row->Contabilita."</td>";
        print "<td width=\"5%\" class=\"layoutECdispari\">".$row->SiglaCapitolo."</td>";
        print "<td width=\"40%\" class=\"layoutECpari\">".htmlentities($row->Voce)."</td>";
        
        if ($row->Operazione=="E") {
            print "<td width=\"17%\" class=\"layoutECent\">".FormattaValuta($row->Importo)."</td>";
        } else {
            print "<td width=\"17%\" class=\"layoutECent\">&nbsp;</td>";
        }
        
        if ($row->Operazione=="U") {
             print "<td width=\"17%\" class=\"layoutECusc\">".FormattaValuta($row->Importo)."</td>";
        } else {
            print "<td width=\"17%\" class=\"layoutECusc\">&nbsp;</td>";
        }
        
        print "</tr>";
    }    
    
    // riempe con righe vuote l'estratto conto
    if ($righe_da_visualizzare<11 && $nrpagina_corrente<2) {
        if ($righe_da_visualizzare==0){
            $nrelementi_pagina=13;
        } else {
            $nrelementi_pagina=12;
        }
        
    }
        
        for ($indice=1;$indice<($nrelementi_pagina-$righe_da_visualizzare);$indice++) {
            print "<tr>";
            print "<td class=\"layoutECpari\">&nbsp;</td>";
            print "<td width=\"18%\" class=\"layoutECdispari\">&nbsp;</td>";
            print "<td width=\"2%\" class=\"layoutECpari\">&nbsp;</td>";
            print "<td width=\"5%\" class=\"layoutECdispari\">&nbsp;</td>";
            print "<td width=\"40%\" class=\"layoutECpari\">&nbsp;</td>";
            print "<td width=\"17%\" class=\"layoutECent\">&nbsp;</td>";
            print "<td width=\"17%\" class=\"layoutECusc\">&nbsp;</td>";
            print "</tr>";
        }
    
    //visualizza il navigatore di pagine
    print "<tr>\n";
    print "<td colspan=7 class=\"layoutECdx\">\n";
    print "|<img src=\"./Immagini/indietro.png\" alt=\"icona back\" width=\"25\" height=\"25\" onClick=\"ChangePage($nrpagina_corrente-1,$nrpagine);\" />"; 
    print "&nbsp;&nbsp;pagina $nrpagina_corrente/$nrpagine&nbsp;&nbsp;";
    print "<img src=\"./Immagini/avanti.png\" alt=\"icona forward\" width=\"25\" height=\"25\" onClick=\"ChangePage($nrpagina_corrente+1,$nrpagine);\" />|"; 
    print "</td>\n";
    print "</tr>\n";
    
    return;
}

//*************************************************************************************
function CancellaCapitoloVoce() {
   if (isset($_POST["addCapitoli"])) {
      // cancella il capitolo selezionato
      $query="DELETE FROM tblcapitolicontabilita WHERE IdCapitolo=".$_POST["addCapitoli"];
      $result=mysql_query($query);
      
      // cancella le voci collegate al capitolo selezionato
      $query="DELETE FROM tblvocicontabilita WHERE IdCapitolo=".$_POST["addCapitoli"];
      $result=mysql_query($query);

    } else {
        if (isset($_POST["addVoci"])) {
            // cancella la voce selezionata
            $query="DELETE FROM tblvocicontabilita WHERE IdVoci=".$_POST["addVoci"];
            $result=mysql_query($query);
        }
    }
   
    ResetVociCapitoli();
    return;
}

//*************************************************************************************
function ResetVociCapitoli() {
    unset($_POST["addCapitoli"]);
    unset($_POST["txtCapitolo"]);
    unset($_POST["txtSigla"]);
    unset($_POST["txtVoce"]);
    unset($_POST["chkOperazione"]);
    unset($_POST["addVoci"]);
    unset($_POST["azione"]);
    unset($_POST["voci"]);
    unset($_POST["filtra_capitolo"]);
    unset($_POST["optOperazione"]);
    unset($_POST["optOperazione"]);
    unset($_POST["txtImporto"]);
    unset($_POST["data_operazione"]);
    unset($_POST["mymodificaoperazione"]);
    return;
}

//*************************************************************************************
function ModificaCapitolo() {
    if (isset($_POST["addCapitoli"])) {
        $query="UPDATE tblcapitolicontabilita SET SiglaCapitolo='".$_POST["txtSigla"]
        ."', Capitolo='".$_POST["txtCapitolo"]."' "
        ."WHERE IdCapitolo=".$_POST["addCapitoli"];
        $result = mysql_query($query);
    }
    
    ResetVociCapitoli();
    return;
}

//*************************************************************************************
function RecuperaCapitolo() {
    if (isset($_POST["addCapitoli"])) {
            $query="SELECT * FROM tblcapitolicontabilita WHERE IdCapitolo=".$_POST["addCapitoli"];
            $result = mysql_query($query);
    }
    return $result;
}

//*************************************************************************************
function ModificaVoce() {
    if (isset($_POST["addVoci"])) {
        $query="UPDATE tblvocicontabilita SET Voce='".$_POST["txtVoce"]
        ."', IdCapitolo=".$_POST["addCapitoli"].", Movimentazione='".$_POST["chkOperazione"]."' "
        ."WHERE IdVoci=".$_POST["addVoci"];
        $result = mysql_query($query);
    }
    
    ResetVociCapitoli();
    return;
}

//*************************************************************************************
function RecuperaVoce() {
    if (isset($_POST["addVoci"])) {
            $query="SELECT * FROM tblvocicontabilita WHERE IdVoci=".$_POST["addVoci"];
            $result = mysql_query($query);
    }
    return $result;
}

//*************************************************************************************
function AddVoceCapitolo() {
    //controlla i dati che sono arrivati dal form
    if (isset($_POST["addCapitoli"])) { // scelta capitoli da associare alla voce
        $addCapitolo=$_POST["addCapitoli"];    
    }
    
    if (isset($_POST["txtCapitolo"])) { // capitolo
        $capitolo=$_POST["txtCapitolo"];    
    }
    
    if (isset($_POST["txtSigla"])) { // sigla
        $sigla=$_POST["txtSigla"];    
    }
    
    if (isset($_POST["txtVoce"])) { // voce
        $voce=$_POST["txtVoce"];    
    }
    
    if (isset($_POST["chkOperazione"])) { //selezione entrata/uscita
        $movimentazione=$_POST["chkOperazione"];    
    }
 
    // prepara e indirizza la query per il salvataggio dei dati
    if ($capitolo!="" && $sigla!="") { // aggiunge un capitolo
        $query="INSERT INTO tblcapitolicontabilita (SiglaCapitolo,Capitolo) VALUES ('$sigla','$capitolo')";
    } else { // aggiunge una voce
        $query="INSERT INTO tblvocicontabilita (Voce,IdCapitolo,Movimentazione) VALUES ('$voce',$addCapitolo,'$movimentazione')";
    }
    
    mysql_query("START TRANSACTION");
    $result=mysql_query($query);
    mysql_query("COMMIT");
    ResetVociCapitoli();
    return;
}

//*************************************************************************************
function PopolaVoci($stringa){
    
    switch ($stringa) {
        case "E": // filtra le voci in relazione alle entrate
            $query="SELECT * FROM tblvocicontabilita INNER JOIN tblcapitolicontabilita ON tblvocicontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo WHERE Movimentazione LIKE '%E%' ORDER BY SiglaCapitolo,Voce";
        break;
        
        case "F": // filtra le voci in relazione ai capitoli (nel div voci e capitoli)
          $query="SELECT * FROM tblvocicontabilita INNER JOIN tblcapitolicontabilita ON tblvocicontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo WHERE tblvocicontabilita.IdCapitolo=".$_POST["addCapitoli"]." ORDER BY Voce";
          unset ($_POST["FiltroVoce"]);
        break;
        
        case "FEC": // filtra le voci in relazione ai capitoli (nella barra comandi)
          if ($_POST["filtra_capitolo"]==0) {
              $query="SELECT * FROM tblvocicontabilita INNER JOIN tblcapitolicontabilita ON tblvocicontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo WHERE Movimentazione like '%".$_POST["optOperazione"]."%' ORDER BY Voce";
          } else {
              $query="SELECT * FROM tblvocicontabilita INNER JOIN tblcapitolicontabilita ON tblvocicontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo WHERE tblvocicontabilita.IdCapitolo=".$_POST["filtra_capitolo"]." AND Movimentazione like '%".$_POST["optOperazione"]."%' ORDER BY Voce";
          }
        break;
        
        case "U": // filtra le voci in relazione alle uscite
          $query="SELECT * FROM tblvocicontabilita INNER JOIN tblcapitolicontabilita ON tblvocicontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo WHERE Movimentazione LIKE '%U%' ORDER BY SiglaCapitolo,Voce";
        break;
    
        default: // non applica nessun filtro
            $query="SELECT * FROM tblvocicontabilita INNER JOIN tblcapitolicontabilita ON tblvocicontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo ORDER BY SiglaCapitolo,Voce";
            unset ($_POST["myfiltro"]);
        break;
    }
    
    $result = mysql_query($query);
    
    while ($row=mysql_fetch_object($result)) {
        $spazi=str_repeat("&nbsp;",3);
        
        if ($_POST["addVoci"]!=$row->IdVoci){
            $selected="";
        } else {
            $selected="selected";
        }     
         
        echo  "<option value=\"".$row->IdVoci."\" onDblClick=\"ModificaVociCapitoli('voce');\" $selected>" 
              .htmlentities($row->Voce)
              .$spazi
              ."(".$row->SiglaCapitolo
              ."- ".$row->Movimentazione.")"
              ."</option>";     
        
        $spazi="";
    }    
    
    return;
}

//*************************************************************************************
function PopolaCapitoli($stringa){
    
    if (isset($_POST["addVoci"])) {
        unset($_POST["addCapitoli"]);
        
        $query="SELECT tblvocicontabilita.IdCapitolo
                FROM tblvocicontabilita
                INNER JOIN tblcapitolicontabilita
                ON tblvocicontabilita.IdCapitolo = tblcapitolicontabilita.IdCapitolo
                WHERE tblvocicontabilita.IdVoci=".$_POST["addVoci"];
       
          $result_voci=mysql_query($query);
          $row_voce=mysql_fetch_object($result_voci);
                
          $indice=$row_voce->IdCapitolo;
    }
    
    $query="SELECT * FROM tblcapitolicontabilita ORDER BY Capitolo ASC";
        
    if (isset($_POST["addCapitoli"])) {
        $indice=$_POST["addCapitoli"];
    }

    
    $result = mysql_query($query);
    
    while ($row=mysql_fetch_object($result)) {
        switch ($stringa) {
            case "EC":
                if ($_POST["filtra_capitolo"]!=$row->IdCapitolo){
                    echo "<option name=\"optCapitoliEC\" id=\"optCapitoliEC\" value=\"".$row->IdCapitolo."\" onClick=\"FiltraVociEC();\" >" .htmlentities($row->SiglaCapitolo)."\t-\t".htmlentities($row->Capitolo)."</option>";
                } else {
                    echo "<option name=\"optCapitoliEC\" id=\"optCapitoliEC\" value=\"".$row->IdCapitolo."\" onClick=\"FiltraVociEC();\" selected>" .htmlentities($row->SiglaCapitolo)."\t-\t".htmlentities($row->Capitolo)."</option>";
                }
            break;
            
            default:
                if ($indice!=$row->IdCapitolo){
                    echo "<option name=\"optCapitoli\" id=\"optCapitoli\" value=\"".$row->IdCapitolo."\" onClick=\"FiltraVoci()\" onDblClick=\"ModificaVociCapitoli('capitolo');\" >" .htmlentities($row->SiglaCapitolo)."\t-\t".htmlentities($row->Capitolo)."</option>";
                } else {
                    echo "<option name=\"optCapitoli\" id=\"optCapitoli\" value=\"".$row->IdCapitolo."\" onClick=\"FiltraVoci()\" onDblClick=\"ModificaVociCapitoli('capitolo');\" selected>" .htmlentities($row->SiglaCapitolo)."\t-\t".htmlentities($row->Capitolo)."</option>";
                }
            break;
        }
    }    
    
    return;
}
?>
