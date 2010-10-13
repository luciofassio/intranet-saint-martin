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

// connessione al database
ConnettiDb();

switch ($_POST["azione"]!="") {
    case "aggiorna":
        AggiornaClassi();
        echo "<div id=\"messaggio\">";
        echo "L'archivio è stato aggiornato con successo!\n";
        echo "</div>";
    break;
}

//funzione che serve per aggiornare le classi
function AggiornaClassi() {
    // setta l'anno corrente
    $anno_corrente=date('Y');

    // recupera l'archivio anagrafico
    $rstIscritti = GetIscritto();

    If ($rstIscritti) {
        while ($row=mysql_fetch_object($rstIscritti)) {
          // recupera l'anno di nascita dell'iscritto
          $anno_nascita_iscritto=(int)substr($row->Data_di_nascita,0,4);
          
          // calcola la classe (scolastica) da aggiornare
          $classe_aggiornata=ClasseProposta($anno_corrente-$anno_nascita_iscritto);
      
          // aggiorna la classe nel database
          $query_aggiornamento="UPDATE Catechismi SET Classe=".$classe_aggiornata.", Sezione=1 WHERE ID=".($row->ID);
          $result_aggiornamento=mysql_query($query_aggiornamento);
      
          // controlla che non vengano restituiti errori da Mysql
          if (mysql_errno() <> 0) {
		          throw new Exception("La routine di aggiornamento ha restituito il seguente errore: ".mysql_errno().": ".mysql_error()."<br/><br/>Statement SQL: ".$query_aggiornamento);
		          exit();
	        }  
      
          // calcola gli iscritti che sono stati aggiornati
          $iscritti_aggiornati++; 
        }
    }
    return;
}
?>

<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Gestione Oratorio / Anagrafica</title>
   <!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script>
// riconosce il browser per caricare i relativi fogli di stile    
var lsBrowser= navigator.appName;
switch (lsBrowser) {
  case "Microsoft Internet Explorer":
    document.write("<link href=\"./css/struttura_pagina.css\" rel=\"stylesheet\" type=\"text/css\" />");
  break;
          
  case "Netscape":
    document.write("<link href=\"./css/struttura_pagina_ff.css\" rel=\"stylesheet\" type=\"text/css\" />");
  break;
}

function fncAggiorna() {
  // per sicurezza chiede conferma se l'utente vuole davvero aggiornare le classi
  if (confirm("Sei sicuro di voler aggiornare le classi?")) {
    //assegna l'azione da far fare a php
    document.getElementById("azione").value="aggiorna";
    //manda a php l'azione da compiere
    document.getElementById("aggiorna_classi").submit();
  }
  return; 
}

</script>

<style>
#pulsante_aggiorna {
  width:50%;
  text-align: center;
  position: absolute;
  top: 220px;
  left: 200px;
}

#bottone_aggiorna {
  width:150px;
}

#messaggio {
  width:50%;
  text-align: center;
  color: red;
  position: absolute;
  top:290px;
  left: 200px;
  font-weight: bold;
}
</style>
</head>
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
            | sei in: <strong>utility: aggiornamento classi</strong> |
        </div>
        
<!-- FINE SEZIONE INTESTAZIONE -->

<!-- SEZIONE BARRA DI NAVIGAZIONE -->
    <?php
        $barra_di_navigazione="| <a href='homepage.php'>home page</a> |";

        echo "<div id='mybarranavigazione'> \n";
        echo "$barra_di_navigazione"."\n";
        echo "</div> \n"; 
     ?>
<!-- FINE SEZIONE BARRA DI NAVIGAZIONE -->

<!-- SEZIONE OPERATORE CONNESSO -->
      <div id="myoperatore">
        <?php
            $result = GetOperatore($idoperatore); // legge nome e cognome dell'operatore in base al suo ID
            $row = mysql_fetch_object($result);
            $_POST["login"]= htmlentities($row->login);
        ?>
        | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
      </div> 
    
<!-- FINE SEZIONE OPERATORE CONNESSO -->

<!-- PULSANTE AGGIORNAMENTO> -->
    <div id="pulsante_aggiorna">
        <input type="button" id="bottone_aggiorna" name="bottone_aggiorna" value="Aggiorna Classi" onClick="fncAggiorna();" />
    </div>
<!-- ******************** -->

<!-- FORM PER COLLEGARSI A PHP -->
<form name="Aggiorna_Classi" id="aggiorna_classi" method="post" action="aggiorna_classi.php">
  <input type="hidden" name="azione" id="azione" value="<?php echo ($_POST["azione"]); ?>" />
</form>

</body>
</html>

<?php
// funzione per recuperare i nominativi da aggiornare
function GetIscritto () {
    $query="SELECT ID,Data_di_nascita,Classe,Sezione FROM Catechismi ORDER BY ID";
    
    $result =mysql_query($query);

    return $result;
}

//funzione per calcolare la classe frequentata dall'iscritto in base all'età
function ClasseProposta($differenzadate) {
      $ValoreClasse=0;
      
      if ($differenzadate < 3) { // frerquenta l'asilo
          $ValoreClasse=0;
      }
      if ($differenzadate < 6 && $differenzadate > 2) { // frerquenta l'asilo
          $ValoreClasse=1;
      }
      
      if ($differenzadate >= 6 && $differenzadate <=18) { // frequenta le scuole elementari/medie/superiori 
          $ValoreClasse=($differenzadate-4); 
      }
      
      if ($differenzadate >=19 && $differenzadate <=29) { // giovane
          $ValoreClasse=15;
      }
      
      if ($differenzadate >=30) { // adulto
          $ValoreClasse=16;
      }
      
  return $ValoreClasse;
}

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
?>
