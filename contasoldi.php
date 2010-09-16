<?php
session_start();
$host  = $_SERVER['HTTP_HOST'];
// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
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
    <!-- inizio sezione intestazione. Questa sezione contiene il logo dell'oratorio -->
   <div id="intestazione">   
      <img src ="./Immagini/logoratorio.png" width="130" height="110" alt="Logo Oratorio Saint-Martin" />
  </div>

<!-- *******************    saluto     *******************************-->
  <div id="saluto">
    <p>
        Benvenuto nell'utility che ti permette di contare i soldi ricevuti dall'<strong>Oratorio Saint-Martin de Corl&eacute;ans</strong>.
        Inserire i pezzi dei vari tagli negli appositi campi e cliccare su <cite><strong>calcola</strong></cite> per ottenerne la somma. Cliccando sul bottone
        <cite><strong>pulisci</strong></cite> riporterai la maschera al punto di partenza.
    </p>
  </div>
<!-- ******************************************************************-->

	  <div id="barranavigazione">
        | <a href="homepage.php">home page</a> |
      </div> <!-- fine sezione barra di navigazione -->


	<div id="rigaorizzontale">
  			<hr />
  	</div>

    <div id="foto">
    	 <img src="Immagini/foto_euro.png" width="290" alt="simbolo euro"/>
  	 	 <img src="Immagini/sfumatura.png" width="406" height="410" alt="sfumatura"/>
    </div>
    
    <script type="text/javascript" src="./js/contasoldi.js"></script>
	
	<div id="rigaorizzontale2">
  			<hr />
  	</div>
 
 </body>
</html>
