<?php
require('accesso_db.inc');
require("funzioni_generali.inc");
require ("get_data.inc");

session_start();

$host  = $_SERVER['HTTP_HOST'];
// controllo l'autenticazione
if (!isset($_SESSION['authenticated_user'])) {
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/logon.php");
		exit();
}

// Controlla se l'utente ha selezionato il menù principale o un sottomenù
if (isset($_GET["menu_padre"])) {
    $menu_padre=$_GET["menu_padre"];
} else {
    $menu_padre=0;
}

// Identifica l'operatore che si è loggato
$idoperatore = $_SESSION['authenticated_user_id'];


ConnettiDB();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Oratorio Saint-Martin / Home Page</title>
    
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
  <?php SelectCSS("struttura_pagina"); ?>   

<script type="text/javascript" >
  
// funzione per aprire e chiudere il div release&credits nell'home page
function ReleaseCredits(myaction) {
  switch (myaction) {
      case "apri":
          document.getElementById("release_credits").style.visibility= "visible";
      break;
      
      case "chiudi":
          document.getElementById("release_credits").style.visibility= "hidden";
      break;
  }
}

</script>

<style>
#mymenuhome { /* posiziona la tabella con le funzioni */
  position: absolute;
  top: 100px;
  /*left: 260px;*/
  left:100px;
  width:70%;
  /*width: 700px;*/
  text-align: left;
}

#crediti { /* posiziona in pagina il copyright e la voce "Release & Credits" */
  position: absolute;
  top:510px;
  left:50px;
  color: grey;
}

#release_credits { /* div che mostra la versione del software e i suoi crediti */
  visibility: hidden;
  position: absolute;
  top: 110px;
  left:240px;
  /*left: 370px;*/
  width: 500px;
  height: 300px;
  background: white;
  border: 3px solid #FF8C00;
  -moz-border-radius: 7px
}

#image_crediti{
  position: relative;
  width:100px;
  top: 10px;
  left: 10px;
  padding-right: 3px;
  padding-bottom: 120px;
  border-right: 1px dotted grey;
}

#crediti_scritta{
  position: absolute;
  top: 0px;
  left:100px;
}

.scritta_crediti_titolo{ /* regola la scritta Gestione Oratorio */
  font-family: Arial Black, sans-serif;
  font-size: large;
  text-align: center;
  top: 10px;
}

#scritta_forbidden{
  position: absolute;
  top: 220px;
  left: 5px;
  text-align: center;
  width:490px;
  font-weight: bold;
  border-top: 1px dotted grey;
}

#ChiudiCrediti{
  width: 100px;
  left: 200px;
}

#scritta_location_home { /* location=dove ci si trova con le pagine web all'interno del programma */
  position: absolute;
  top: 45px;
  left: 100px;
  color: green;
}
</style>

</head>

<body class="logon">
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
            ORATORIO SAINT-MARTIN
        </div>
        
        <div id="scritta_location_home">
        <?php
            if ($menu_padre==0) {
                echo "home page > men&ugrave; principale";
            } else {
               echo "| <a href=\"homepage.php\">men&ugrave; principale</a> |";   
            }
        ?>
        
        </div>
  
        <div id="crediti">
            <?php
                print ("(P) 2008-".date('Y')." | "); 
            ?>
            <a href="javascript:ReleaseCredits('apri');">Release & Credits</a>
        </div>
  
<!-- fine sezione intestazione. -->
    
<!--  *******************  data ***************************** -->  
    <div id="mydata">
        <?php
            echo(GetData($Data));
        ?>
    </div>
 
<!-- SEZIONE OPERATORE CONNESSO -->
     <?php
        $result = GetOperatore($idoperatore); // legge nome e cognome dell'operatore in base al suo ID
        $row = mysqli_fetch_object($result);
     ?>
     <div id="myoperatore">
          | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
    
<!-- FINE SEZIONE OPERATORE CONNESSO -->

<!-- costruzione della tabella del menu in base ai privilegi dell'operatore -->
    <div id="mymenuhome">
    <table border="0" width="100%">
    
    <?php
        $result = GetApplicazioni($idoperatore,$menu_padre);
        // controlla se l'utente tenta di accedere a un sottomenù senza essere configurato dagli amministratori del sistema
        if (mysqli_num_rows($result)==0){
            $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            header("Location: http://$host$uri/homepage.php");
		        exit();
        }
        
        $row = mysqli_fetch_array($result);
        while($row){
    ?>
    
        <tr>
            <td class="cellaimmagini"><a href="<?php echo htmlentities($row['url']);?>"><img src="<?php	echo htmlentities($row['immagine']); ?>" title="<?php	echo htmlentities($row['immagine_testo']); ?>"  height="70" width="70" /></a></td>
            <td class="celladescrizione"><a href="<?php echo htmlentities($row['url']);?>"><strong><?php echo htmlentities($row['nome']);?></strong></a></td>

        <?php
	         $row = mysqli_fetch_array($result);
	         if (!$row) {
		          break;	
        ?>
            <td class="cellaimmagini">&nbsp;</td>
            <td class="celladescrizione">&nbsp;</td>
        </tr>

        <?php
	         }
        ?>
            <td class="cellaimmagini"><a href="<?php echo htmlentities($row['url']);?>"><img src="<?php	echo htmlentities($row['immagine']); ?>" title="<?php	echo htmlentities($row['immagine_testo']); ?>"  height="70" width="70" /></a></td>
            <td class="celladescrizione"><a href="<?php echo htmlentities($row['url']);?>"><strong><?php echo htmlentities($row['nome']);?></strong></a></td>
        </tr>
    
        <?php
	         $row = mysqli_fetch_array($result);
           }
        ?>              	
    </table>
  </div>
    <!-- DIV CHE APRE FINESTRA CON REALEASE E CREDITI DEL PROGRAMMA -->
    <div id="release_credits">
        <div id="image_crediti">
            <img src="./immagini/ruolo.png" width="100" heigth="90" alt="">
        </div>
        
        <div id="crediti_scritta">
          <p class="scritta_crediti_titolo">
              GESTIONE ORATORIO
          </p>
          
          <p>
              <ul>
                  <li><strong>Release:</strong> 2.0.x</li>
                  <li><strong>License:</strong> Gnu/Gpl v. 2.x</li>
                  <li><strong>Owner:</strong> Oratorio Saint-Martin de Corl&eacute;ans
                  <li><strong>Address:</strong> Viale Europa, 1 - 11100 Aosta (Italy)
                  <li><strong>Developpers:</strong>
                      <ul>
                        <li>Lucio Fassio</li>
                        <li>Marco Fogliadini</li>
                      </ul>
                  </li>
              </ul>
          </p>
        </div>
        <div id="scritta_forbidden">
              <p>
                  &nbsp;
                  <!-- An unauthorized copy of this software is forbidden -->
              </p>
          
              <p class="bottone_chiudi_crediti">
                  <input type="button" id="ChiudiCrediti" value="Chiudi" onClick="ReleaseCredits('chiudi');" />  
             </p>        
        </div>
    </div>

</body>

</html>
