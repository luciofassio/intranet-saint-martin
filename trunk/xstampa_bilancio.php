<?php
require('accesso_db.inc');

ob_clear;

ConnettiDB();

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
/****************************************************************
 * FUNZIONE PER RECUPERARE I DATI SINTETICI
 ****************************************************************/
function GetDatiSintetici(){
    // VISUALIZZA LE ENTRATE
    echo "<table id=\"tblDatiSinteticiEntrate\">";
    echo "<tr>";
    echo "<th colspan=\"3\">ENTRATE</th>";
    echo "</tr>";
    
    if ($_POST["typeContabilita"]==1) {
        $filtro=" GROUP BY tblcontabilita.IdCapitolo HAVING tblcontabilita.Operazione='E'";
    } else {
        $filtro=" GROUP BY tblcontabilita.IdCapitolo,tblcontabilita.Contabilita HAVING tblcontabilita.Contabilita='ER' AND tblcontabilita.Operazione='E'";
    }
    
    $query="SELECT tblcontabilita.DataOperazione,tblcontabilita.Operazione,tblcontabilita.Contabilita,
            SUM(tblcontabilita.Importo) AS SubTotale,tblcapitolicontabilita.Capitolo,tblcapitolicontabilita.SiglaCapitolo
            FROM tblcontabilita
            INNER JOIN tblcapitolicontabilita
            ON tblcontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo".
            $filtro."
            AND YEAR(tblcontabilita.DataOperazione)='".$_POST["annoBilancio"]."'
            ORDER BY tblcapitolicontabilita.SiglaCapitolo";
    
    $result=mysql_query($query);
     
    if ($result) {
        $righe=mysql_num_rows($result);
        if ($righe >0) {
            while ($row=mysql_fetch_object($result)) {
                $grantotale_entrate+=$row->SubTotale;
                echo "<tr>";
                echo "<td class=\"sx\">".htmlentities($row->SiglaCapitolo)."</td>";
                echo "<td class=\"sx\">".htmlentities($row->Capitolo)."</td>";
                echo "<td class=\"dx\">".FormattaValuta($row->SubTotale)." &euro;</td>";
                echo "</tr>";
            }
            echo "<tr>";
            echo "<td class=\"sx\">&nbsp;</td>";
            echo "<td class=\"sx\"><span style=\"font-variant:small-caps; font-weight:bold;\">totale entrate</span></td>";
            echo "<td class=\"dx\"><span style=\"font-variant:small-caps; font-weight:bold;\">".FormattaValuta($grantotale_entrate)." &euro;</span></td>";
            echo "</tr>";
        }else {
            echo "<tr><td class=\"sx\" colspan=\"3\">Nessuna attivit&agrave; &egrave; iscritta nelle Entrate"; 
        }
    } 
    echo "</table>";
    echo "<p style=\"page-break-before: always; margin-top:460px\" />";
    // VISUALIZZA LE USCITE
    echo str_repeat("<br />",3)."<table id=\"tblDatiSinteticiUscite\">";
    echo "<tr>";
    echo "<th colspan=\"3\">USCITE</th>";
    echo "</tr>";
    
    if ($_POST["typeContabilita"]==1) {
        $filtro=" GROUP BY tblcontabilita.IdCapitolo HAVING tblcontabilita.Operazione='U'";
    } else {
        $filtro=" GROUP BY tblcontabilita.IdCapitolo,tblcontabilita.Contabilita HAVING tblcontabilita.Contabilita='ER' AND tblcontabilita.Operazione='U'";
    }
    
    $query="SELECT tblcontabilita.DataOperazione,tblcontabilita.Operazione,tblcontabilita.Contabilita, 
            SUM(tblcontabilita.Importo) AS SubTotale,tblcapitolicontabilita.Capitolo,tblcapitolicontabilita.SiglaCapitolo
            FROM tblcontabilita
            INNER JOIN tblcapitolicontabilita
            ON tblcontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo".
            $filtro."
            AND YEAR(tblcontabilita.DataOperazione)='".$_POST["annoBilancio"]."'
            ORDER BY tblcapitolicontabilita.SiglaCapitolo";
    
    $result=mysql_query($query);
     
    if ($result) {
        $righe=mysql_num_rows($result);
        if ($righe >0) {
            while ($row=mysql_fetch_object($result)) {
                $grantotale_uscite+=($row->SubTotale)*-1;
                echo "<tr>";
                echo "<td class=\"sx\">".htmlentities($row->SiglaCapitolo)."</td>";
                echo "<td class=\"sx\">".htmlentities($row->Capitolo)."</td>";
                echo "<td class=\"dx\">".FormattaValuta($row->SubTotale)." &euro;</td>";
                echo "</tr>";
            }
            echo "<tr>";
            echo "<td class=\"sx\">&nbsp;</td>";
            echo "<td class=\"sx\"><span style=\"font-variant:small-caps; font-weight:bold;\">totale uscite</span></td>";
            echo "<td class=\"dx\"><span style=\"font-variant:small-caps; font-weight:bold;\">".FormattaValuta($grantotale_uscite)." &euro;</span></td>";
            echo "</tr>";
        }else {
            echo "<tr><td class=\"sx\" colspan=\"3\">Nessuna passivit&agrave; &egrave; iscritta nelle Uscite"; 
        }
    }
    echo "</table>";
    
    echo str_repeat("<br />",4);
    
    echo "<table id=\"riepilogobilancio\">";
    echo "<tr>";
    echo "<th colspan =\"3\">riepilogo bilancio</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class=\"sx\">Totale Entrate</td>"."<td class=\"dx\">".FormattaValuta($grantotale_entrate)." &euro;</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class=\"sx\">Totale Uscite</td>"."<td class=\"dx\">".FormattaValuta($grantotale_uscite)." &euro;</td>";

    echo "</tr>";
    echo "<tr>";
    
    $differenza=($grantotale_entrate-$grantotale_uscite);
    if ($differenza >0) {
        echo "<td class=\"sx\">"."Il Bilancio registra un avanzo di</td>"."<td class=\"dx\"><span style =\"font-size:16pt; font-family: Arial Black; \">"."+".FormattaValuta($differenza)." &euro;</span></td>";
    } else {
        echo "<td class=\"sx\">"."Il Bilancio registra un disavanzo di</td>"."<td class=\"dx\"><span style =\"font-size:16pt; font-family: Arial Black; \">"."-".FormattaValuta($differenza)." &euro;</span></td>";
    }
    echo "</tr></table>";
    return;     

}

/****************************************************************
 * FUNZIONE PER RECUPERARE I DATI SINTETICI
 ****************************************************************/
function GetDatiAnalitici(){
    // RECUPERA E VISUALIZZA LE ENTRATE
    // prepara il filtro
    if ($_POST["typeContabilita"]==1) {
        $filtro=" GROUP BY tblcontabilita.IdCapitolo HAVING tblcontabilita.Operazione='E' ";
    } else {
        $filtro=" GROUP BY tblcontabilita.IdCapitolo,tblcontabilita.Contabilita HAVING tblcontabilita.Contabilita='ER' AND tblcontabilita.Operazione='E' ";
    }
    
    //ottiene il numero di voci per capitolo
    $query="SELECT tblcontabilita.Operazione,tblcapitolicontabilita.SiglaCapitolo,Sum(tblcontabilita.Importo) as TotaleImportoVoci
            FROM tblcontabilita
            INNER JOIN tblcapitolicontabilita
            ON tblcontabilita.IdCapitolo = tblcapitolicontabilita.IdCapitolo".
            $filtro."
            ORDER BY tblcapitolicontabilita.SiglaCapitolo";
      
    $result=mysql_query($query);
    
    // popola l'array con chiave la sigla del capitolo e valore il numero delle voci calcolate da mysql
    while ($row=mysql_fetch_object($result)) {
        $nrvoci[$row->SiglaCapitolo] = ($row->TotaleImportoVoci);
    }      
    
    echo "<table id=\"tblDatiAnaliticiEntrate\">";
    echo "<tr>";
    echo "<th colspan=\"4\">ENTRATE</th>";
    echo "</tr>";
    
    if ($_POST["typeContabilita"]==1) {
        $filtro=" GROUP BY tblcontabilita.IdVoce HAVING tblcontabilita.Operazione='E' ";
    } else {
        $filtro=" GROUP BY tblcontabilita.IdVoce,tblcontabilita.Contabilita HAVING tblcontabilita.Contabilita='ER' AND tblcontabilita.Operazione='E' ";
    }
    
    $query="SELECT tblcontabilita.DataOperazione,tblcontabilita.Operazione,tblcontabilita.Contabilita,
            SUM(tblcontabilita.Importo) AS SubTotale,tblcapitolicontabilita.Capitolo,tblcapitolicontabilita.SiglaCapitolo,
            tblvocicontabilita.Voce
            FROM tblcontabilita
            INNER JOIN tblcapitolicontabilita
            ON tblcontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo
            INNER JOIN tblvocicontabilita
            ON tblcontabilita.IdVoce=tblvocicontabilita.IdVoci".
            $filtro."
            AND YEAR(tblcontabilita.DataOperazione)='".$_POST["annoBilancio"]."'
            ORDER BY tblcapitolicontabilita.SiglaCapitolo";
      
      $result=mysql_query($query);
      
      if ($result) {
          $righe=mysql_num_rows($result);
          if ($righe>0) {
              $voci=0;
              $current_chapter="";
              
              while ($row=mysql_fetch_object($result)) {
                  $grantotale_entrate+=($row->SubTotale);
                  $voci++;
                  
                  if ($current_chapter!=$row->SiglaCapitolo) { //il capitolo è cambiato?
                      echo "<tr>";
                      echo "<td class=\"sx_capitolo\">".htmlentities($row->SiglaCapitolo)."</td>";
                      echo "<td colspan=\"2\" class=\"sx_capitolo\">".htmlentities($row->Capitolo)."</td>";
                       echo "<td class=\"dx_capitolo\">".FormattaValuta($nrvoci[$row->SiglaCapitolo])." &euro;</td>";
                      echo "</tr>";
                      $current_chapter=$row->SiglaCapitolo;
                  } 

                      echo "<tr>";
                      echo "<td class=\"sx\">&nbsp;</td>"; //cella vuota per la sigla del capitolo
                      echo "<td class=\"sx\">&nbsp;</td>"; // cella vuota per il capitolo
                      echo "<td class=\"sx_voce_analitico\">&rArr; ".htmlentities($row->Voce)."</td>";
                      echo "<td class=\"dx_voce_analitico\">".FormattaValuta($row->SubTotale)." &euro;</td>";
                      echo "</tr>";
                      $totale_capitolo+=($row->SubTotale);
              }
              echo "<tr><td colspan=\"4\" class=\"sx\">&nbsp;"; 
              echo "<tr>";
              echo "<td class=\"sx\">&nbsp;</td>";
              echo "<td class=\"sx\"><span style=\"font-weight:bold;\">Totale Entrate</span></td>";
              echo "<td class=\"sx_capitolo\">&nbsp;</td>";
              echo "<td class=\"dx_capitolo\"><span style=\"font-weight:bold;\">".FormattaValuta($grantotale_entrate)." &euro;</span></td>";
              echo "</tr>";
          } else {
              echo "<tr><td class=\"sx\" colspan=\"4\">Nessuna passivit&agrave; &egrave; iscritta nelle Entrate"; 
          }
      }
      echo "</table>";
      
      echo "<p style=\"page-break-before: always; margin-top:460px\" />";
    
    
    
     //RECUPERA E VISUALIZZA LE USCITE
    // prepara il filtro
    if ($_POST["typeContabilita"]==1) {
        $filtro=" GROUP BY tblcontabilita.IdCapitolo HAVING tblcontabilita.Operazione='U' ";
    } else {
        $filtro=" GROUP BY tblcontabilita.IdCapitolo,tblcontabilita.Contabilita HAVING tblcontabilita.Contabilita='ER' AND tblcontabilita.Operazione='U' ";
    }
    
    //ottiene il numero di voci per capitolo
    $query="SELECT tblcontabilita.Operazione,tblcapitolicontabilita.SiglaCapitolo,Sum(tblcontabilita.Importo) as TotaleImportoVoci
            FROM tblcontabilita
            INNER JOIN tblcapitolicontabilita
            ON tblcontabilita.IdCapitolo = tblcapitolicontabilita.IdCapitolo".
            $filtro."
            ORDER BY tblcapitolicontabilita.SiglaCapitolo";
      
    $result=mysql_query($query);
    
    // popola l'array con chiave la sigla del capitolo e valore il numero delle voci calcolate da mysql
    while ($row=mysql_fetch_object($result)) {
        $nrvoci[$row->SiglaCapitolo] = ($row->TotaleImportoVoci)*-1;
    }      
    
    echo str_repeat("<br />",3);
    echo "<table id=\"tblDatiAnaliticiUscite\">";
    echo "<tr>";
    echo "<th colspan=\"4\">USCITE</th>";
    echo "</tr>";
    
    if ($_POST["typeContabilita"]==1) {
        $filtro=" GROUP BY tblcontabilita.IdVoce HAVING tblcontabilita.Operazione='U' ";
    } else {
        $filtro=" GROUP BY tblcontabilita.IdVoce,tblcontabilita.Contabilita HAVING tblcontabilita.Contabilita='ER' AND tblcontabilita.Operazione='U' ";
    }
    
    $query="SELECT tblcontabilita.DataOperazione,tblcontabilita.Operazione,tblcontabilita.Contabilita,
            SUM(tblcontabilita.Importo) AS SubTotale,tblcapitolicontabilita.Capitolo,tblcapitolicontabilita.SiglaCapitolo,
            tblvocicontabilita.Voce
            FROM tblcontabilita
            INNER JOIN tblcapitolicontabilita
            ON tblcontabilita.IdCapitolo=tblcapitolicontabilita.IdCapitolo
            INNER JOIN tblvocicontabilita
            ON tblcontabilita.IdVoce=tblvocicontabilita.IdVoci".
            $filtro."
            AND YEAR(tblcontabilita.DataOperazione)='".$_POST["annoBilancio"]."'
            ORDER BY tblcapitolicontabilita.SiglaCapitolo";
      
      $result=mysql_query($query);
      
      if ($result) {
          $righe=mysql_num_rows($result);
          if ($righe>0) {
              $voci=0;
              $current_chapter="";
              
              while ($row=mysql_fetch_object($result)) {
                  $grantotale_uscite+=($row->SubTotale)*-1;
                  $voci++;
                  
                  if ($current_chapter!=$row->SiglaCapitolo) { //il capitolo è cambiato?
                      echo "<tr>";
                      echo "<td class=\"sx_capitolo\">".htmlentities($row->SiglaCapitolo)."</td>";
                      echo "<td colspan=\"2\" class=\"sx_capitolo\">".htmlentities($row->Capitolo)."</td>";
                       echo "<td class=\"dx_capitolo\">".FormattaValuta($nrvoci[$row->SiglaCapitolo])." &euro;</td>";
                      echo "</tr>";
                      $current_chapter=$row->SiglaCapitolo;
                  } 

                      echo "<tr>";
                      echo "<td class=\"sx\">&nbsp;</td>"; //cella vuota per la sigla del capitolo
                      echo "<td class=\"sx\">&nbsp;</td>"; // cella vuota per il capitolo
                      echo "<td class=\"sx_voce_analitico\">&rArr; ".htmlentities($row->Voce)."</td>";
                      echo "<td class=\"dx_voce_analitico\">".FormattaValuta($row->SubTotale)." &euro;</td>";
                      echo "</tr>";
                      $totale_capitolo+=($row->SubTotale);
              }
              echo "<tr><td colspan=\"4\" class=\"sx\">&nbsp;"; 
              echo "<tr>";
              echo "<td class=\"sx\">&nbsp;</td>";
              echo "<td class=\"sx\"><span style=\"font-weight:bold;\">Totale Uscite</span></td>";
              echo "<td class=\"sx_capitolo\">&nbsp;</td>";
              echo "<td class=\"dx_capitolo\"><span style=\"font-weight:bold;\">".FormattaValuta($grantotale_uscite)." &euro;</span></td>";
              echo "</tr>";
          } else {
              echo "<tr><td class=\"sx\" colspan=\"4\">Nessuna passivit&agrave; &egrave; iscritta nelle Uscite"; 
          }
      }
      echo "</table>";
    
    echo "<p style=\"page-break-before: always; margin-top:460px\" />";
    
    echo str_repeat("<br />",2);
    
    echo "<table id=\"riepilogobilancio\">";
    echo "<tr>";
    echo "<th colspan =\"3\">riepilogo bilancio</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class=\"sx\">Totale Entrate</td>"."<td class=\"dx\">".FormattaValuta($grantotale_entrate)." &euro;</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class=\"sx\">Totale Uscite</td>"."<td class=\"dx\">".FormattaValuta($grantotale_uscite)." &euro;</td>";

    echo "</tr>";
    echo "<tr>";
    
    $differenza=($grantotale_entrate-$grantotale_uscite);
    if ($differenza >0) {
        echo "<td class=\"sx\">"."Il Bilancio registra un avanzo di</td>"."<td class=\"dx\"><span style =\"font-size:16pt; font-family: Arial Black; \">"."+".FormattaValuta($differenza)." &euro;</span></td>";
    } else {
        echo "<td class=\"sx\">"."Il Bilancio registra un disavanzo di</td>"."<td class=\"dx\"><span style =\"font-size:16pt; font-family: Arial Black; \">"."-".FormattaValuta($differenza)." &euro;</span></td>";
    }
    echo "</tr></table>";
    
    return;     
}
/****************************************************************
 * FUNZIONE PER RECUPERARE I DATI SINTETICI
 ****************************************************************/
function StampaMovimentiCassa(){

if ($_POST["typeContabilita"]==1) {
        $filtro="";
    } else {
        $filtro=" WHERE tblcontabilita.Contabilita='ER' ";
    }

$query="SELECT tblcontabilita.DataOperazione,tblcontabilita.Contabilita,tblcontabilita.IdCapitolo,tblcontabilita.IdVoce,
        tblcontabilita.Operazione,tblcontabilita.Importo,tblcapitolicontabilita.SiglaCapitolo,tblvocicontabilita.Voce
        FROM tblcontabilita
        INNER JOIN tblcapitolicontabilita
        ON tblcontabilita.IdCapitolo = tblcapitolicontabilita.IdCapitolo
        INNER JOIN tblvocicontabilita
        ON tblcontabilita.IdVoce = tblvocicontabilita.IdVoci
        $filtro
        ORDER BY tblcontabilita.DataOperazione DESC,tblcapitolicontabilita.SiglaCapitolo";
        
$result=mysql_query($query);

if (result) {
    echo "<table id=\"table_movimentazione_cassa\">";
    print "<tr>";
    print "<th width=\"18%\">DATA</th>";
    print "<th width=\"2%\">CT</th>";
    print "<th width=\"5%\">CAP</th>";
    print "<th width=\"40%\">VOCE</th>";
    print "<th width=\"17%\">ENTRATA</th>";
    print "<th width=\"17%\">USCITA</th>";
    print "</tr>";
  
  while ($row=mysql_fetch_object($result)) {
      echo "<tr>";
      echo "<td class=\"sx\" width=\"18%\">".FiltraData($row->DataOperazione,"DaMysql")."</td>";
      echo "<td class=\"sx\" width=\"2%\">".$row->Contabilita."</td>";
      echo "<td class=\"sx\" width=\"5%\">".$row->SiglaCapitolo."</td>";
      echo "<td class=\"sx\" width=\"40%\">".$row->Voce."</td>";
      
      if ($row->Operazione=="E") {
          echo "<td class=\"dx\" width=\"17%\">".FormattaValuta($row->Importo)." &euro;</td>";
          $totale_entrate+=$row->Importo;
      } else {
          echo "<td class=\"dx\" width=\"17%\">&nbsp;</td>";
      }
      
     if ($row->Operazione=="U") {
          echo "<td class=\"dx\" width=\"17%\">-".FormattaValuta($row->Importo)." &euro;</td>";
           $totale_uscite+=$row->Importo;
      } else {
          echo "<td class=\"dx\" width=\"17%\">&nbsp;</td>";
      }
      
      echo "</tr>";
      $saldo+=$row->Importo;
      
  }
  
  print "<tr>";
  print "<td>&nbsp</td>";
  print "<td>&nbsp</td>";
  print "<td>&nbsp</td>";
  print "<td class=\"sx_capitolo\"><span style=\"font-weight:bold;\">TOTALE</span></td>";
  print "<td class=\"dx\"><span style=\"font-weight:bold;\">".FormattaValuta($totale_entrate)."</span></td>";
  print "<td class=\"dx\"><span style=\"font-weight:bold;\">-".FormattaValuta($totale_uscite)."</span></td>";
  print "</tr>";
  print "<tr>";
  print "<td>&nbsp</td>";
  print "<td>&nbsp</td>";
  print "<td>&nbsp</td>";
  print "<td class=\"sx_capitolo\"><span style=\"font-weight:bold;\">SALDO</span></td>";
  if ($saldo >0 ) {
      print "<td class=\"dx\"><span style=\"font-weight:bold;\">".FormattaValuta($saldo)."</span></td>";
      print "<td class=\"sx\">&nbsp</td>";
  } else {
      print "<td class=\"sx\">&nbsp</td>";
      print "<td class=\"dx\"><span style=\"font-weight:bold;\">-".FormattaValuta($saldo)."</span></td>";
  }
        
  print "</tr>";
  echo "</table>";
  echo "<p style=\"page-break-before: always; margin-top:460px\" />";
}


return;


}
//**************************************************************************************
//funzione per filtrare la valuta arrivata da Mysql
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
    } else {
        $controllo[1]="00";
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
        //$controllo[0]="-".$controllo[0];
    }
   
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
?>

<html>
<head>
<title>Bilancio</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >

<style>

/* REGOLE 'SELETTORI' */
body {
	font-family: arial;
	font-size: 9pt;
	line-height: 0.99em;
}

h1 {
	font-size:x-large;
	font-weight: bold;
	text-align:center;
}

h2 {
	font-size:medium;
	text-align:center;
}

h3 {
   text-align: left;
   padding:0.5em;
}

h4 {
   text-align: center;
}

img {
  vertical-align: middle;
}

li {
	margin-top: 0.5em;	
	margin-bottom: 0.5em;	
}

p {
	font-size:12pt;
	font-weight:bold;
}

p.documento_elaborato {
	font-size:10pt;
	font-weight:bold;
	text-align:left;
	margin-top:3em;
}

p.errore {
	font-size:12pt;
	line-height:2em;
}

table {
	font-family: arial;
	font-size: 10pt;
	line-height: 1em;	
	width:100%;
	border:1px dotted grey;
	-moz-border-radius: 7px; 
}

th {
  font-variant: small-caps;
  text-align:center;
  font-weight:bold;
  font-size:12pt;
  border-bottom:1px dotted grey;
  padding:0.5em;
}

td.sx{
  font-size:12pt;
  text-align:left;
  padding:0.5em;
  border-bottom:1px dotted grey;
}

td.dx{
  font-size:12pt;
  text-align:right;
  padding:0.5em;
  border-bottom:1px dotted grey;
}

td.sx_capitolo {
  font-size:12pt;
  text-align:left;
  padding:0.4em;
  border-bottom:1px dotted grey;
}

td.dx_capitolo {
  font-size:12pt;
  text-align:right;
  padding:0.4em;
  border-bottom:1px dotted grey;
  font-weight:bold;
  /*background:#D3D3D3;*/
}

td.sx_voce_analitico {
  font-size:10pt;
  border-bottom:1px dotted grey;
  padding:0.3em;
  font-style: italic;
}

td.dx_voce_analitico {
  text-align:right;
  font-size:10pt;
  border-bottom:1px dotted grey;
  padding:0.3em;
  font-style: italic;
}

td.sx_riga{
  font-size:12pt;
  text-align:left;
  padding:0.5em;
  border-bottom:2px dotted black;
}

ul {
	line-height: 1.15em;
	text-align: justify; 
}

/* REGOLE PER ID OGGETTI */

#contenutopagina {
	width: 90%;
	height: 85%;
	text-align: left;
	padding:0px 0px 0px 0px;
	margin: 0 auto;
}

#intestazione_pagina {
	text-align: left;
  border-bottom: 3px dotted grey;
  padding-bottom: 7px;
  padding-top: 30px;
  /*background: #E0E0E0;
  -moz-border-radius: 14px; */
}

</style>

</head>
<body>
    <div id ="contenuto_pagina">
        <div id="intestazione_pagina">
            <img src="./Immagini/logoratorio.png" id="logo_oratorio" width ="40" height="40" alt="logo oratorio" />
            <strong>Oratorio Saint Martin - Viale Europa, 1 - 11100 Aosta - Tel. 0165/554234 </strong>
        </div>
    
        <h2>
        <?php 
            switch ($_POST["typeContabilita"]){
                case 1:
                    $contabilita="BILANCIO ORATORIO";
                break;
            
                case 2:
                    $contabilita="BILANCIO ESTATE RAGAZZI";
                break;
                
            }
            echo $contabilita."&nbsp;".$_POST["annoBilancio"]; ?></h2>
        <h4>elaborato <?php echo GetData($Data); ?></h4>    
        
        
        <br />
            <?php
                  switch ($_POST["tipoBilancio"]) {
                      case 1:
                          echo "<h3>SINTETICO</h3>";
                          GetDatiSintetici();
                      break;
                      
                      case 2:
                          echo "<h3>ANALITICO</h3>";
                          GetDatiAnalitici();
                      break;
                      
                      case 3:
                          echo "<h3>MOVIMENTI DI CASSA</h3>";
                          StampaMovimentiCassa();
                      break;
                  }
            ?>
            
    </div>
   
</body>
</html>