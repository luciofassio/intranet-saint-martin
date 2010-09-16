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

$IDOperatore = $_SESSION['authenticated_user_id'];

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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Utility per contare i soldi </title>
   
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

   <link href="./css/stylecontasoldi.css" rel="stylesheet" type="text/css" />
   
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
        
        <div id="mybarranavigazione">
        | <a href="homepage.php">home page</a> |
        </div>
         
        <div id="mydata">
            <?php echo(GetData($Data)."\n");?>
        </div>
        
        <div id="scritta_location">
            | sei in: <strong>conta soldi</strong> |
        </div>
    
<!-- FINE SEZIONE INTESTAZIONE -->

<!-- SEZIONE OPERATORE CONNESSO -->
     <?php
        ConnettiDb();
        $result = GetOperatore($IDOperatore); // legge nome e cognome dell'operatore in base al suo ID
        $row = mysql_fetch_object($result);
     ?>
    <div id="myoperatore">
          | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
    
<!-- FINE SEZIONE OPERATORE CONNESSO -->

    <div id="foto">
    	 <img src="Immagini/foto_euro.png" width="290" alt="simbolo euro"/>
  	 	<!-- <img src="Immagini/sfumatura.png" width="500" height="410" alt="sfumatura"/>-->
    </div>
    
    <?php
         if ($errore==0) {
            print  "<script type=\"text/javascript\" src=\"./js/contasoldi.js\"></script>";
         }
    ?>
    
	   <form name="soldicontati" id="soldicontati" method="post" action="">
        <input type="hidden" name="messaggio" id="messaggio" value="" />
        <input type="hidden" name="errore" id="errore" value="" />
    </form>
 </body>
</html>

 <?php
// controlla che javascript non abbia riscontrato errori nel calcolo e/o nell'inserimento dei dati
 /* TABELLA CODICI ERRORI PAGINA CONTASOLDI.PHP
  * errore = 0 pagina vuota
  * errore = 1 pagina ok con valori calcolati
  * errore = 2 errore di calcolo/inserimento
  */   
  
$errore=$_POST["errore"];
$messaggio=$_POST["messaggio"];

switch ($errore) {
    case 1: // i dati sono stati calcolati correttamente
        $icona=1;
        StampaMessaggio($messaggio,$errore,$icona);
    break;
    
    case 2: // errori di calcolo, inserimento dati
        $icona=2;
        StampaMessaggio($messaggio,$errore,$icona);
    break;
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
        
        print "<script type=\"text/javascript\">";
        print "document.getElementById(\"ChiudiMessaggio\").focus()";
        print "</script>";
    }
    return;
}
?>