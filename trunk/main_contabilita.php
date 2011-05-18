<?php
// Main page della contabilità dell'oratorio
// Sviluppatore: Marco Fogliadini
// Agosto 2009
// Versione pagina: 1.0

require('accesso_db.inc');
require("funzioni_generali.inc");

session_start();
$postback = $_POST['postback'];
$host  = $_SERVER['HTTP_HOST'];

/*$_SESSION['authenticated_user']=true; // da togliere quando la pagina sarà sul server di produzione*/

// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}
$idoperatore = $_SESSION['authenticated_user_id'];
ConnettiDB();

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
?>
<html>
    <head>
        <title>Oratorio Saint-Martin / Contabilit&agrave;</title>
    
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <link href="./css/style_contabilita_ff.css" rel="stylesheet" type="text/css" />
  
        <script type="text/javascript" src="./js/funzioni.js"></script> 
        
    </head>

    <body>
        <!-- inizio sezione intestazione. Questa sezione contiene il logo dell'oratorio -->
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
  
        <div id="scritta_contabilita">
            contabilit&agrave; > men&ugrave; principale
        </div>

        <!-- fine sezione intestazione. -->
  
        <!-- INIZIO SEZIONE MENU -->
        <div id="mainmenu">
            <div id="gestionecapitoli">      
                <img src ="./Immagini/capitoli.gif" alt="" />
            </div>
      
            <div id="scrittagestionecapitoli">      
                <a href="">Gestione Capitoli</a>
            </div>
     
            <div id="gestionevoci">      
                <img src ="./Immagini/contabilita.png" width="110" height="140" alt="" />
            </div>
      
            <div id="scrittagestionevoci">      
                <a href="xcontabilita.php">Gestione Cassa</a>
            </div>
     
            <div id="stampamovimenti">      
                <img src ="./Immagini/stampante.gif" width="130" height="110" alt="" />
            </div>
      
            <div id="scrittastampamovimenti">      
                <a href="">Stampa Movimenti</a>
            </div>
      
            <div id="gestionebilancio">      
                <img src ="./Immagini/contabilita.gif" width="130" height="130" alt="" />
            </div>
  
            <div id="scrittagestionebilancio">      
                <a href="">Gestione Bilancio</a>
            </div>
        </div>
  
        <div id="barrabottom">
            &nbsp;
        </div>
   
        <div id="homepage">
            | <a href="homepage.php">home page</a> |
        </div>
   
        <div id="versione">
            versione 1.0
        </div>
   
        <div id="mydata">
            <?php echo ($Data);?>
        </div>
    </body>
</html>