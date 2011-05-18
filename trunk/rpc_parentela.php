<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// controllo di avere una stringa da cercare
if(isset($_POST['queryString'])) {
	   // contro sql injection
    $queryString = mysql_real_escape_string($_POST['queryString']);
    
    if(strlen($queryString) >0) {
        //$query = mysql_query("SELECT ID, Cognome, Nome FROM Catechismi WHERE Cognome LIKE '$queryString%' AND Cancellato=False ORDER BY Cognome,Nome LIMIT 10");
        $query = mysql_query("SELECT Catechismi.ID, Catechismi.Cognome, Catechismi.Nome, tblparentela.IdFamiglia, tblparentela.IdFamigliare, tblparentela.IdGradoParentela
                FROM Catechismi LEFT JOIN tblparentela ON Catechismi.ID=tblparentela.IdFamigliare
                WHERE Cognome LIKE '$queryString%' AND Cancellato=False 
                ORDER BY Catechismi.Cognome,Catechismi.Nome,tblparentela.IdGradoParentela 
                LIMIT 12");
        
		if (mysql_errno() <> 0) {
			echo("rpc_parentela: ".mysql_errno().":".mysql_error()."<br/><br/>".$sql);
			exit();
		}  
        if ($query) {  
            while ($row = mysql_fetch_object($query)) {
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