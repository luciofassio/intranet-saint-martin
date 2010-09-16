<?php
require("funzioni_generali.inc");
require ("get_data.inc");
define("mess01", "Il tuo identificativo non è valido. <br>Controlla lo stato del blocco delle maiuscole e le tue credenziali");
$postback = null;
$mess= null;

session_start();
$host  = $_SERVER['HTTP_HOST'];
require('accesso_db.inc');

$postback = $_POST['postback'];
// Test if this is a postback or not.
if ($postback == true) {
	ConnettiDB();
	if ($_POST['txtNomeUtente'] != "") {
		$result = GetUtente($_POST['txtNomeUtente'], $_POST['txtPassword']);
		if (mysql_num_rows($result) > 0)	{
			$_SESSION['authenticated_user'] = true;
			$row = mysql_fetch_array($result);
			$_SESSION['authenticated_user_id'] = $row['idoperatore'];
			$_SESSION['access_level']=$row['livello'];
			$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			header("Location: http://$host$uri/homepage.php");
			exit();
		}
		else {
			$mess = mess01;
		}
	}
	else {
		$mess = mess01;
	}	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

   <html xmlns="http://www.w3.org/1999/xhtml">
      
<head>
	<title>Oratorio Saint-Martin / Log On</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php SelectCSS(""); ?>   
	<script type="text/javascript" src="./js/funzioni.js"></script>
</head>
<body onload="CaricamentoLogin()" class="logon">
 
 
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
            log on
        </div>
        <!-- fine sezione intestazione. -->
 
 <!-- *******************  saluto *******************************-->
        <div id="scritta_saluto">
            <p>
                Benvenuto nel sito di gestione dell'<strong><span style="color: #FF8C00;">Oratorio Saint-Martin de Corl&eacute;ans</span></strong>
            </p>
        </div>

<!--  *******************  data ***************************** -->  
  <div id="mydata">
    <?php
        echo(GetData($Data));
    ?>
  </div>
 
<!-- ********************** menu home page ******************* --> 
    <form name="Entra" action="logon.php" id="Entra" method="post" class ="verificautente"> 
        <input type="hidden" name="postback" value="true">
        
        <div id="datilogin">
            <fieldset id="datiloginfs">
                <legend>Inserisci...&nbsp;</legend>
            </fieldset>
                
            <div id="nomeutente">
                <img src="./Immagini/utente.gif" style="float:left; margin-right:15px" alt="Nome utente"  height="70" width="70" />
			          <label for="txtNomeUtente" class="posizione">Nome utente</label>	
			          <input type="text" name="txtNomeUtente" id="txtNomeUtente" value="<?php echo $_POST['txtNomeUtente'] ?>" autocomplete="off" />
		        </div>
           		
            <div id="password">
                <img src="./Immagini/icon_login.gif" style="float:left; margin-right:15px" alt="Password" height="70" />	
			          <label for="txtPassword" class="posizione">Password</label>
			          <input type="password" name="txtPassword" id="txtPassword" />
            </div>
        
            <div id="bottonentra">
                <input type="submit" value ="Entra"  onclick="return ConvalidaLogin()" />
            </div>
      </div>
      
      <p id="errore_login"><?php echo $mess ?></p>
        
    </form>
</body>
</html>
