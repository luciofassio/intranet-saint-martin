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

$idoperatore = $_SESSION['authenticated_user_id'];
ConnettiDB();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
   <title>Oratorio Saint-Martin / Home Page</title>
    
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
  <?php SelectCSS(""); ?>   

	<script type="text/javascript" src="./js/funzioni.js"></script>
   
  
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
  
        <div id="scritta_location">
            home page > men&ugrave; principale
        </div>
  
        <div id="crediti">
            <?php
                print ("&copy; 2008-".date('Y')." | "); 
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
        $row = mysql_fetch_object($result);
     ?>
     <div id="myoperatore">
          | operatore connesso: <strong><?php echo htmlentities($row->Nome).' '.htmlentities($row->Cognome) ?></strong > | 
    </div> 
    
<!-- FINE SEZIONE OPERATORE CONNESSO -->

<!-- costruzione della tabella del menu in base ai privilegi dell'operatore -->
    <div id="mymenuhome">
    <table border="0" width="100%">
    
    <?php
        $result = GetApplicazioni($idoperatore);
        $row = mysql_fetch_array($result);
        while($row){
    ?>
    
        <tr>
            <td class="cellaimmagini"><img src="<?php	echo htmlentities($row['immagine']); ?>" alt="<?php	echo htmlentities($row['immagine_testo']); ?>"  height="70" width="70" /></td>
            <td class="celladescrizione"><a href="<?php echo htmlentities($row['url']);?>"><strong><?php echo htmlentities($row['nome']);?></strong></a></td>

        <?php
	         $row = mysql_fetch_array($result);
	         if (!$row) {
		          break;	
        ?>
            <td class="cellaimmagini">&nbsp;</td>
            <td class="celladescrizione">&nbsp;</td>
        </tr>

        <?php
	         }
        ?>
            <td class="cellaimmagini"><img src="<?php	echo htmlentities($row['immagine']); ?>" alt="<?php	echo htmlentities($row['immagine_testo']); ?>"  height="70" width="70" /></td>
            <td class="celladescrizione"><a href="<?php echo htmlentities($row['url']);?>"><strong><?php echo htmlentities($row['nome']);?></strong></a></td>
        </tr>
    
        <?php
	         $row = mysql_fetch_array($result);
           }
        ?>              	
    </table>
  </div>
    <!-- DIV CHE APRE FINESTRA CON REALEASE E CREDITI DEL PROGRAMMA -->
    <div id="release_credits">
        <div id="image_crediti">
            <img src="./Immagini/ruolo.png" width="100" heigth="90" alt="">
        </div>
        
        <div id="crediti_scritta">
          <p class="scritta_crediti_titolo">
              GESTIONE ORATORIO
          </p>
          
          <p>
              <ul>
                  <li><strong>Release:</strong> 2.0.x</li>
                  <li><strong>Copyright:</strong> <?php print("&copy; 2008-".date('Y'));?></li>
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
                  An unauthorized copy of this software is forbidden
              </p>
          
              <p class="bottone_chiudi_crediti">
                  <input type="button" id="ChiudiCrediti" value="Chiudi" onClick="ReleaseCredits('chiudi');" />  
             </p>        
        </div>
    </div>

</body>

</html>
