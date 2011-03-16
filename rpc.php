<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// controllo di avere una stringa da cercare
if(isset($_POST['queryString'])) {
	// control sql injection
	$queryString = mysql_real_escape_string($_POST['queryString']);
	if(strlen($queryString) >0) {
		$query = mysql_query("SELECT ID, Cognome, Nome, BarCode FROM Catechismi WHERE Cognome LIKE '$queryString%' AND cancellato='False' ORDER BY Cognome,Nome LIMIT 10");
		if($query) {
      while ($row = mysql_fetch_object($query)) {
              echo '<li onClick="fill(\''.htmlentities($row->ID).'|'.addslashes(htmlentities($row->Cognome)).'|'.htmlentities($row->Nome).'|'.htmlentities($row->BarCode).'\');">'.stripslashes(htmlentities($row->Cognome)).' '.htmlentities($row->Nome).'</li>';
      }
		} else {
			echo ("ERRORE: la ricerca nel database non ha dato risultati.");
		}
		
	} // There is a queryString.
} else {
	echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>