<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// controllo di avere una stringa da cercare
if(isset($_POST['queryString'])) {
	   // contro sql injection
    $queryString = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['queryString']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    
    if(strlen($queryString) >0) {
        //$query = mysql_query("SELECT ID, Cognome, Nome FROM Catechismi WHERE Cognome LIKE '$queryString%' AND Cancellato=False ORDER BY Cognome,Nome LIMIT 10");
        $query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT Catechismi.ID, Catechismi.Cognome, Catechismi.Nome, tblparentela.IdFamiglia, tblparentela.IdFamigliare, tblparentela.IdGradoParentela
                FROM Catechismi LEFT JOIN tblparentela ON Catechismi.ID=tblparentela.IdFamigliare
                WHERE Cognome LIKE '$queryString%' AND Cancellato=False 
                ORDER BY Catechismi.Cognome,Catechismi.Nome,tblparentela.IdGradoParentela 
                LIMIT 12");
        
		if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
			echo("rpc_parentela: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
			exit();
		}  
        if ($query) {  
            while ($row = mysqli_fetch_object($query)) {
                switch($row->IdGradoParentela) {
                    case 1:
                        echo "<span style=\"color: red; font-weight: bold;\">";
                        echo '<li onClick="fill_parentela(\''.htmlentities($row->ID).'|'.addslashes(htmlentities($row->Cognome)).'|'.htmlentities($row->Nome).'|'.($row->IdFamiglia).'\');">'.stripslashes(htmlentities($row->Cognome)).' '.htmlentities($row->Nome).'</li>'; 
                        echo "</span>";                        
                    break;
                    
                    case 2:
                        echo "<span style=\"color: mediumblue; font-weight: bold;\">";
                        echo '<li onClick="fill_parentela(\''.htmlentities($row->ID).'|'.addslashes(htmlentities($row->Cognome)).'|'.htmlentities($row->Nome).'|'.($row->IdFamiglia).'\');">'.stripslashes(htmlentities($row->Cognome)).' '.htmlentities($row->Nome).'</li>'; 
                        echo "</span>";                        
                    break;
                    
                    case 3:
                        echo "<span style=\"color: dodgerblue; font-weight: bold;\">";
                        echo '<li onClick="fill_parentela(\''.htmlentities($row->ID).'|'.addslashes(htmlentities($row->Cognome)).'|'.htmlentities($row->Nome).'|'.($row->IdFamiglia).'\');">'.stripslashes(htmlentities($row->Cognome)).' '.htmlentities($row->Nome).'</li>'; 
                        echo "</span>";                        
                    break;
                    
                    case 4:
                        echo "<span style=\"color: magenta; font-weight: bold;\">";
                        echo '<li onClick="fill_parentela(\''.htmlentities($row->ID).'|'.addslashes(htmlentities($row->Cognome)).'|'.htmlentities($row->Nome).'|'.($row->IdFamiglia).'\');">'.stripslashes(htmlentities($row->Cognome)).' '.htmlentities($row->Nome).'</li>'; 
                        echo "</span>";                        
                    break;
                    
                    default:
                        echo '<li onClick="fill_parentela(\''.htmlentities($row->ID).'|'.addslashes(htmlentities($row->Cognome)).'|'.htmlentities($row->Nome).'|'.($row->IdFamiglia).'\');">'.stripslashes(htmlentities($row->Cognome)).' '.htmlentities($row->Nome).'</li>'; 
                    break;
                }
            }
        }
        
      } else {
			   echo ("ERRORE: la ricerca nel database non ha dato risultati.");
    }
} else { // There is a queryString.
	   echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>