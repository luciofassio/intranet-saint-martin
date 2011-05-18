<?php 
require('accesso_db.inc');   

session_start();

$host  = $_SERVER['HTTP_HOST'];

// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}

$idoperatore = $_SESSION['authenticated_user_id'];

//connessione al db
ConnettiDb();

global $abilita_pulsante;

switch ($_POST["azione"]) {
    case "recupera":
        RecuperaCancellati();
    break;
    
    case "svuota":
        //SvuotaCestino(); 
    break;
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

/**********************>>>>>>>>>> FUNZIONE PER IL RECUPERO DEGLI ISCRITTI CANCELLATI <<<<<<<<***************************/ 
function RecuperaCancellati(){
    $id=explode("|",$_POST["id_iscritti"]);

    foreach ($id as $valore) {
      $query="UPDATE Catechismi SET Cancellato=False, DataCancellazione=null, OperatoreCancellazione=null WHERE ID=".$valore;
      mysql_query($query);
    }
    
    return;
}

/**********************>>>>>>>>>> FUNZIONE PER LEGGERE QUANTI ISCRITTI CI SONO NEL CESTINO <<<<<<<<**********************/ 
function GetDeleted($abilita_pulsante) {
    $i=1; // variabile per processare i colori delle righe della tabella
    $nrelementi_pagina=14; // setta il numero massimo di elementi visualizzati per pagina
    global $abilita_pulsante; // rende globale la variabile $abilita_pulsante
    $abilita_pulsante ="disabled"; // abilita/disabilita i pulsanti recupera e svuota cestino
    $nrcestinati=0; // inizializza la variabile che conta il numero dei cestinati in arvhivio
    
    // statement sql da mandare a mysql
    $query ="SELECT ID,Cognome,Nome,Cancellato,DataCancellazione,OperatoreCancellazione FROM Catechismi WHERE Cancellato=true ORDER BY DataCancellazione DESC, Cognome,Nome";

    $result =mysql_query($query); // estrae gli iscritti cancellati dal database
    
    $nrcestinati =mysql_num_rows($result); // trova il numero degli iscritti cestinati
    
    
    // CALCOLA E VISUALIZZA IL NUMERO DELLE PAGINE IN BASE AL NUMERO DEGLI ISCRITTI CANCELLATI TROVATI
    $valore=($nrcestinati/$nrelementi_pagina); // trova il numero "grezzo" di pagine da visualizzare 
        
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
    
    //visualizza il navigatore di pagine
    echo "\n";
    echo "<div id=\"div_nrpagine\">\n";
    echo "|<img src=\"./Immagini/indietro.png\" alt=\"icona back\" width=\"25\" height=\"25\" onClick=\"ChangePage($nrpagina_corrente-1,$nrpagine);\" />"; 
    echo "&nbsp;&nbsp;pagina $nrpagina_corrente/$nrpagine&nbsp;&nbsp;";
    echo "<img src=\"./Immagini/avanti.png\" alt=\"icona back\" width=\"25\" height=\"25\" onClick=\"ChangePage($nrpagina_corrente+1,$nrpagine);\" />"; 
    echo "| elementi cestinati: $nrcestinati |\n";
    echo "</div>\n";
    
    
    // VISUALIZZA LA TABELLA CON I RISULTATI OTTENUTI
    echo "\n";
    echo "<div id=\"div_dati_funzioni\">\n";
    echo "<div id=\"imgCestino\">\n";
    echo "<img src=\"./Immagini/trash2.png\" alt=\"icona trash\" width=\"80\" height=\"80\" />\n";
    echo "</div>\n";
    echo "<div id=\"tabella_cestinati\">\n";
    echo "<table id=\"TableDeleted\" class=\"layout_tabella_cestino\">\n";
      
    if ($nrcestinati >0) {
        $abilita_pulsante="enabled";
        
        // visualizza intestazione tabella
        echo "<tr>\n";
        echo "<th>SEL</th>\n";
        echo "<th>COGNOME</th>\n";
        echo "<th>NOME</th>\n";
        echo "<th>ID</th>\n";
        echo "<th>DATA CANC.</th>\n";
        echo "<th>CANCELLATO DA</th>\n";
        echo "</tr>\n";

        /* trova la posizione, all'interno dei records trovati, dalla quale partire
         * per visualizzare gli elementi cancellati nel caso ci siano più pagine */ 
        if ($_POST["nrpagina"]>1) {

            $posizione=($_POST["nrpagina"]-1)*$nrelementi_pagina;
            
            mysql_data_seek($result,$posizione);
        }
        
        $indice=0; // inizializza la variabile di controllo per il numero degli elementi da pubblicare per pagina
        
        while ($row=mysql_fetch_object($result)) {
            
            // controlla che nella costruzione della tabella non venga superato il numero degli elementi consentiti
            $indice++;
            if ($indice > $nrelementi_pagina) {
                break;
            }
            
            // setta il colore della riga in base alla sua posizione (pari o dispari)
            if ($i % 2) {
                $classe="cella_cestino_dispari";
            } else {
                $classe="cella_cestino_pari";
            }
            
            // visualizza la tabella
            echo "<tr>\n";
            echo "<th class=\"$classe\"><input type='checkbox' name=\"SelIscritto\" /></th>\n";
            echo "<th class=\"$classe\">".htmlentities($row->Cognome)."</th>\n";
            echo "<th class=$classe>".htmlentities($row->Nome)."</th>\n";
            echo "<th class=\"$classe\" name=\"cella_id\">".($row->ID)."</th>\n";
            echo "<th class=$classe>".substr(($row->DataCancellazione),8,2)."/".substr(($row->DataCancellazione),5,2)."/".substr(($row->DataCancellazione),0,4)."</th>\n";
            echo "<th class=$classe>".($row->OperatoreCancellazione)."</th>\n";
            echo "</tr>\n";
            $i++;
        }
    } else {
        echo "<tr>\n";
        echo "<th>Ops, il cestino e' vuoto!</th>\n";
        echo "</tr>\n";
    }

    echo "</table>\n</div>\n</div>\n";
    
    return;
}
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Gestione Oratorio / Cestino</title>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> -->

  <script type="text/javascript" src="./js/f_cestino.js"></script>
  <script type="text/javascript" src="./js/jquery-1.2.1.pack.js"></script>
</head>

<style>
#pulsantiera_cestino { /* pulsanti a destra sopra il fondino verde */
  position: absolute;
  top: 85px;
  left: 900px;
}

#div_dati_funzioni{ /* fondino verde chiaro; contiene il contenuto del cestino */
  visibility: visible;
  position: absolute; 
  width: 935px;
  height: 375px;
  top: 115px;
  left: 50px;
  background: #CCFFCC/*#99FF99*/;
  -moz-border-radius: 7px;
}

#tabella_cestinati {
  position: absolute;
  width: 750px;
  top: 10px;
  left: 115px;
}

#TableDeleted {
  border: 1px dotted grey;
  width: 765px;
  /*height: 360px; */
}

th {
  border-bottom: 1px dotted grey;
}

table.layout_tabella_cestino th.cella_cestino_dispari {
  text-align: center;
  font-weight: normal;
  background: #CCFF99;
}

table.layout_tabella_cestino th.cella_cestino_pari {
  text-align: center;
  font-weight: normal;
  background: #CCCC99;
}

#imgCestino {
  position: absolute;
  top: 10px;
  left: 10px;
  width: 80px;
  height: 340px; 
  border-right: 1px dotted grey;
}

#div_nrpagine {
  position: absolute;
  top: 42px;
  /*top: 90px;*/
  /*left: 55px;*/
  left: 320px;
  /*font-weight: bold;*/
  color: purple;
}

img {
  vertical-align: middle;
}

</style>
<body>

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
            | sei in: <strong>cestino</strong> |
        </div>
        
<!-- FINE SEZIONE INTESTAZIONE -->
    
<!-- SEZIONE BARRA DI NAVIGAZIONE -->
    <?php
        $barra_di_navigazione="| <a href='homepage.php'>home page</a> | <a href='xanagrafica.php'>anagrafica</a> |";
        
        echo "<div id='mybarranavigazione'>";
        echo "$barra_di_navigazione";
        echo "</div>"; 
     ?>
<!-- FINE SEZIONE BARRA DI NAVIGAZIONE -->

<!-- SEZIONE OPERATORE CONNESSO -->
      <div id="myoperatore">
        <?php
            $result = GetOperatore($idoperatore); // legge nome e cognome dell'operatore in base al suo ID
            $row = mysql_fetch_object($result);
        ?>
        | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
    
<!-- FINE SEZIONE OPERATORE CONNESSO -->

<!-- SEZIONE TABELLA DEI NOMI CESTINATI -->
            <?php 
                GetDeleted($abilita_pulsante);
            ?>
<!-- FINE SEZIONE TABELLA -->
<!-- SEZIONE PULSANTIERA -->

<div id="pulsantiera_cestino">
    <form name="recupera_cancellati" id="recupera_cestinati" method="post" action="xcestino.php">
        <input type="hidden" name ="id_iscritti" id="id_iscritti" value="" />
        <input type="hidden" name ="azione" id="azione" value="">
        <input type ="hidden" name="nrpagina" id="nrpagina" value="<?php echo ($_POST['nrpagina']);?>" />
        <?php
           
            echo "<input type=\"button\" name=\"recupera\" id=\"btnRecupera\" value=\"Recupera\" onClick=\"javascript:fncRecupera();\" $abilita_pulsante\" /> \n";
       
            if ($_SESSION['access_level'] > 4) {
                echo "<style>#pulsantiera_cestino {left:780px;}</style>\n";
                echo "<input type='button' name='SvuotaCestino' id='svuota_cestino' value='Svuota Cestino' $abilita_pulsante />\n";
            }        
       ?>
    </form>
</div>
<!-- FINE SEZIONE PULSANTIERA -->


</body>

</html>
