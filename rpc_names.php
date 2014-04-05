<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// controllo di avere una stringa da cercare
if(isset($_POST['queryString'])) {
	// control sql injection
	$queryString = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['queryString']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

  if(strlen($queryString) >0) {
		$query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT ID, Cognome, Nome, BarCode FROM Catechismi WHERE Nome LIKE '$queryString%' AND Cancellato=False ORDER BY Nome,Cognome LIMIT 12");
		if($query) {
      while ($row = mysqli_fetch_object($query)) {
              echo '<li onClick="fill(\''.htmlentities($row->ID).'|'.addslashes(htmlentities($row->Cognome)).'|'.htmlentities($row->Nome).'|'.htmlentities($row->BarCode).'\');">'.stripslashes(htmlentities($row->Nome)).' '.htmlentities($row->Cognome).'</li>';
      }
		} else {
			echo ("ERRORE: la ricerca nel database non ha dato risultati.");
		}
		
	} // There is a queryString.
} else {
	echo 'Questa pagina non deve essere acceduta direttamente.';
}
?>