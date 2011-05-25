<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// controllo di avere una stringa da cercare
if(isset($_POST['queryString'])) {
	// control sql injection
	$queryString = mysql_real_escape_string($_POST['queryString']);

  if(strlen($queryString) >0) {
		$query = mysql_query("SELECT ID, Cognome, Nome, BarCode FROM Catechismi WHERE Nome LIKE '$queryString%' AND Cancellato=False ORDER BY Nome,Cognome LIMIT 10");
		if($query) {
      while ($row = mysql_fetch_object($query)) {
              echo '<li onClick="fill(\''.htmlentities($row->ID).'|'.addslashes(htmlentities($row->Nome)).'|'.htmlentities($row->Cognome).'|'.htmlentities($row->BarCode).'\');">'.stripslashes(htmlentities($row->Nome)).' '.htmlentities($row->Cognome).'</li>';
      }
		} else {
			echo ("ERRORE: la ricerca nel database non ha dato risultati.");
		}
		
	} // There is a queryString.
} else {
	echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>