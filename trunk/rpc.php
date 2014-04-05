<?php
session_start();
require('accesso_db.inc');
ConnettiDB();

// controllo di avere una stringa da cercare
if(isset($_POST['queryString'])) {
	// control sql injection
	$queryString = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['queryString']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	if(strlen($queryString) >0) {
		$query = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT ID, Cognome, Nome, BarCode FROM Catechismi WHERE Cognome LIKE '$queryString%' AND Cancellato=False ORDER BY Cognome,Nome LIMIT 12");
		if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) <> 0) {
			echo("rpc_catechismi: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)).":".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br/><br/>".$sql);
			exit();
		}  
		if($query) {
			while ($row = mysqli_fetch_object($query)) {
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